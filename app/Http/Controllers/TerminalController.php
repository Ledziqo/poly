<?php

namespace App\Http\Controllers;

use App\Models\AiSignal;
use App\Models\BotDecisionLog;
use App\Models\Market;
use App\Models\MarketOutcome;
use App\Models\Portfolio;
use App\Models\Position;
use App\Models\Trade;
use App\Services\Trading\PortfolioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TerminalController extends Controller
{
    public function __construct(private readonly PortfolioService $portfolios)
    {
    }

    public function dashboard(): View|\Illuminate\Http\Response
    {
        try {
            $portfolio = $this->portfolios->refresh($this->portfolios->defaultPortfolio());

            return view('dashboard', [
                'portfolio' => $portfolio->load('settings'),
                'opportunities' => $this->opportunityQuery()->limit(8)->get(),
                'positions' => $portfolio->positions()->where('status', 'open')->with('outcome.market')->latest()->limit(8)->get(),
                'decisions' => BotDecisionLog::with('outcome.market')->latest('decided_at')->limit(10)->get(),
                'markets' => Market::with('outcomes')->where('active', true)->where('closed', false)->orderByDesc('volume')->limit(8)->get(),
            ]);
        } catch (Throwable $exception) {
            return response("Dashboard failed:\n".$exception::class."\n".$exception->getMessage(), 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function health(): \Illuminate\Http\Response
    {
        $checks = [];

        foreach ([
            'portfolio' => fn () => $this->portfolios->defaultPortfolio()->id,
            'portfolio refresh' => fn () => $this->portfolios->refresh($this->portfolios->defaultPortfolio())->id,
            'opportunities' => fn () => $this->opportunityQuery()->limit(1)->count(),
            'positions' => fn () => Position::query()->where('status', 'open')->limit(1)->count(),
            'decisions' => fn () => BotDecisionLog::query()->latest('decided_at')->limit(1)->count(),
            'markets' => fn () => Market::query()->where('active', true)->where('closed', false)->limit(1)->count(),
        ] as $label => $callback) {
            try {
                $checks[] = $label.': ok ('.$callback().')';
            } catch (Throwable $exception) {
                $checks[] = $label.': error - '.$exception::class.' - '.$exception->getMessage();
            }
        }

        return response(implode("\n", $checks), 200)->header('Content-Type', 'text/plain');
    }

    public function markets(Request $request): View
    {
        $markets = Market::query()
            ->with(['outcomes.latestSignal'])
            ->where('active', true)
            ->where('closed', false)
            ->when($request->string('q')->toString(), fn ($query, $q) => $query->where('question', 'like', "%{$q}%"))
            ->when($request->string('category')->toString(), fn ($query, $category) => $query->where('category_name', $category))
            ->orderByDesc($request->string('sort')->toString() === 'liquidity' ? 'liquidity' : 'volume')
            ->paginate(24)
            ->withQueryString();

        return view('markets.index', [
            'markets' => $markets,
            'categories' => Market::whereNotNull('category_name')->distinct()->orderBy('category_name')->pluck('category_name'),
            'filters' => $request->only(['q', 'category', 'sort']),
        ]);
    }

    public function market(Market $market): View
    {
        return view('markets.show', [
            'market' => $market->load(['outcomes.latestSignal', 'outcomes.topPositions']),
            'decisions' => BotDecisionLog::with('outcome')->whereHas('outcome', fn ($query) => $query->where('market_id', $market->id))->latest('decided_at')->limit(12)->get(),
        ]);
    }

    public function opportunities(): View
    {
        return view('opportunities.index', [
            'signals' => $this->opportunityQuery()->paginate(30),
        ]);
    }

    public function portfolio(): View
    {
        $portfolio = $this->portfolios->refresh($this->portfolios->defaultPortfolio());

        return view('portfolio.index', [
            'portfolio' => $portfolio,
            'positions' => $portfolio->positions()->with('outcome.market')->latest()->paginate(25),
        ]);
    }

    public function history(): View
    {
        return view('history.index', [
            'trades' => Trade::with('outcome.market')->latest('executed_at')->paginate(35),
        ]);
    }

    public function settings(): View
    {
        return view('settings.index', [
            'portfolio' => $this->portfolios->defaultPortfolio()->load('settings'),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'risk_level' => ['required', 'in:safe,balanced,aggressive'],
            'max_amount_per_trade' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'minimum_liquidity' => ['required', 'numeric', 'min:0'],
            'max_spread' => ['required', 'numeric', 'min:0', 'max:1'],
            'min_edge' => ['required', 'numeric', 'min:0', 'max:1'],
            'min_confidence' => ['required', 'integer', 'min:1', 'max:99'],
            'max_open_positions' => ['required', 'integer', 'min:1', 'max:100'],
            'max_total_exposure' => ['required', 'numeric', 'min:1'],
        ]);

        $portfolio = $this->portfolios->defaultPortfolio();
        $portfolio->settings()->update([
            ...$data,
            'enabled' => $request->boolean('enabled'),
        ]);

        return back()->with('status', 'Bot settings saved.');
    }

    public function resetPortfolio(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'starting_balance' => ['required', 'numeric', 'min:100', 'max:10000000'],
        ]);

        $portfolio = $this->portfolios->defaultPortfolio();
        $portfolio->positions()->delete();
        $portfolio->trades()->delete();
        $portfolio->update([
            'starting_balance' => $data['starting_balance'],
            'cash_balance' => $data['starting_balance'],
            'realized_pnl' => 0,
            'unrealized_pnl' => 0,
            'total_exposure' => 0,
        ]);

        BotDecisionLog::create([
            'portfolio_id' => $portfolio->id,
            'action' => 'reset',
            'status' => 'completed',
            'reason' => 'Paper portfolio reset from settings.',
            'decided_at' => now(),
        ]);

        return back()->with('status', 'Paper portfolio reset.');
    }

    private function opportunityQuery()
    {
        return AiSignal::query()
            ->with('outcome.market')
            ->whereHas('outcome.market', fn ($query) => $query->where('active', true)->where('closed', false))
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')->from('ai_signals')->groupBy('market_outcome_id');
            })
            ->orderByRaw("CASE grade WHEN 'Strong Entry' THEN 1 WHEN 'Good Entry' THEN 2 WHEN 'Watch' THEN 3 WHEN 'Too Late' THEN 4 ELSE 5 END")
            ->orderByDesc('edge');
    }
}
