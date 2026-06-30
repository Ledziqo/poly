<?php

namespace App\Services\Trading;

use App\Models\AiSignal;
use App\Models\BotDecisionLog;
use App\Models\MarketOutcome;
use App\Models\Portfolio;
use App\Models\Position;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;

class PaperTradingBot
{
    public function __construct(
        private readonly PortfolioService $portfolioService,
        private readonly OrderBookFillSimulator $fills,
    ) {
    }

    public function run(): array
    {
        $portfolio = $this->portfolioService->defaultPortfolio();
        $settings = $portfolio->settings;

        if (! $settings->enabled) {
            $this->log($portfolio, null, 'paused', 'skipped', 'Bot is paused in settings.');
            return ['entered' => 0, 'exited' => 0, 'skipped' => 1];
        }

        $exited = $this->manageExits($portfolio);
        $entered = $this->findEntries($portfolio);
        $this->portfolioService->refresh($portfolio);

        return ['entered' => $entered, 'exited' => $exited, 'skipped' => 0];
    }

    private function findEntries(Portfolio $portfolio): int
    {
        $settings = $portfolio->settings;
        $entered = 0;
        $openCount = $portfolio->positions()->where('status', 'open')->count();

        AiSignal::query()
            ->with(['outcome.market'])
            ->whereIn('grade', ['Strong Entry', 'Good Entry'])
            ->where('scored_at', '>=', now()->subMinutes(30))
            ->orderByDesc('edge')
            ->limit(20)
            ->get()
            ->each(function (AiSignal $signal) use ($portfolio, $settings, &$entered, &$openCount) {
                $outcome = $signal->outcome;

                if (! $outcome || $portfolio->positions()->where('status', 'open')->where('market_outcome_id', $outcome->id)->exists()) {
                    return;
                }

                $skip = $this->entrySkipReason($portfolio, $signal, $openCount);

                if ($skip) {
                    $this->log($portfolio, $outcome, 'entry', 'skipped', $skip, ['signal_id' => $signal->id]);
                    return;
                }

                $budget = min((float) $settings->max_amount_per_trade, $this->riskBudget($settings->risk_level, (float) $settings->max_amount_per_trade));
                $fill = $this->fills->buy($outcome, $budget, (float) $settings->max_slippage);

                if ($fill['status'] === 'rejected') {
                    $this->log($portfolio, $outcome, 'entry', 'skipped', 'Order book could not fill inside slippage/liquidity rules.', $fill);
                    return;
                }

                DB::transaction(function () use ($portfolio, $outcome, $signal, $fill) {
                    $position = Position::create([
                        'portfolio_id' => $portfolio->id,
                        'market_outcome_id' => $outcome->id,
                        'status' => 'open',
                        'shares' => $fill['shares'],
                        'avg_entry_price' => $fill['avg_price'],
                        'current_price' => $outcome->price,
                        'cost_basis' => $fill['notional'],
                        'market_value' => $fill['notional'],
                        'take_profit_price' => min(0.99, $fill['avg_price'] + max(0.05, ((float) $signal->edge * 0.8))),
                        'stop_loss_price' => max(0.01, $fill['avg_price'] - 0.08),
                        'opened_at' => now(),
                    ]);

                    Trade::create([
                        'portfolio_id' => $portfolio->id,
                        'position_id' => $position->id,
                        'market_outcome_id' => $outcome->id,
                        'side' => 'buy',
                        'source' => 'bot',
                        'shares' => $fill['shares'],
                        'avg_price' => $fill['avg_price'],
                        'notional' => $fill['notional'],
                        'slippage' => $fill['slippage'],
                        'fill_status' => $fill['status'],
                        'fill_details' => $fill,
                        'explanation' => $signal->explanation,
                        'executed_at' => now(),
                    ]);

                    $portfolio->decrement('cash_balance', $fill['notional']);
                    $this->log($portfolio, $outcome, 'entry', 'entered', 'Entered because signal passed edge, confidence, liquidity, spread, exposure, and order-book fill rules.', ['signal_id' => $signal->id, 'fill' => $fill]);
                });

                $entered++;
                $openCount++;
            });

        return $entered;
    }

    private function manageExits(Portfolio $portfolio): int
    {
        $exited = 0;

        $portfolio->positions()->where('status', 'open')->with(['outcome.latestSignal'])->get()->each(function (Position $position) use ($portfolio, &$exited) {
            $outcome = $position->outcome;
            $price = (float) ($outcome->price ?? $position->current_price ?? $position->avg_entry_price);
            $reason = null;

            if ($position->take_profit_price && $price >= (float) $position->take_profit_price) {
                $reason = 'Take profit reached.';
            } elseif ($position->stop_loss_price && $price <= (float) $position->stop_loss_price) {
                $reason = 'Stop loss reached.';
            } elseif ($outcome->latestSignal && (float) $outcome->latestSignal->edge < 0.01) {
                $reason = 'AI edge decayed below hold threshold.';
            } elseif ($outcome->market->end_at && $outcome->market->end_at->isPast()) {
                $reason = 'Market reached expiry handling window.';
            }

            if (! $reason) {
                return;
            }

            $fill = $this->fills->sell($outcome, (float) $position->shares, (float) $portfolio->settings->max_slippage);

            if ($fill['status'] === 'rejected') {
                $this->log($portfolio, $outcome, 'exit', 'skipped', 'Exit signal fired but order book could not fill inside slippage rules.', $fill);
                return;
            }

            DB::transaction(function () use ($portfolio, $position, $outcome, $fill, $reason) {
                $pnl = (float) $fill['notional'] - (float) $position->cost_basis;

                $position->update([
                    'status' => 'closed',
                    'current_price' => $fill['avg_price'],
                    'market_value' => $fill['notional'],
                    'realized_pnl' => $pnl,
                    'unrealized_pnl' => 0,
                    'closed_at' => now(),
                ]);

                Trade::create([
                    'portfolio_id' => $portfolio->id,
                    'position_id' => $position->id,
                    'market_outcome_id' => $outcome->id,
                    'side' => 'sell',
                    'source' => 'bot',
                    'shares' => $fill['shares'],
                    'avg_price' => $fill['avg_price'],
                    'notional' => $fill['notional'],
                    'slippage' => $fill['slippage'],
                    'fill_status' => $fill['status'],
                    'fill_details' => $fill,
                    'explanation' => $reason,
                    'executed_at' => now(),
                ]);

                $portfolio->increment('cash_balance', $fill['notional']);
                $this->log($portfolio, $outcome, 'exit', 'exited', $reason, ['fill' => $fill, 'pnl' => $pnl]);
            });

            $exited++;
        });

        return $exited;
    }

    private function entrySkipReason(Portfolio $portfolio, AiSignal $signal, int $openCount): ?string
    {
        $settings = $portfolio->settings;
        $outcome = $signal->outcome;

        if ($openCount >= (int) $settings->max_open_positions) {
            return 'Max open positions reached.';
        }

        if ((float) $portfolio->total_exposure >= (float) $settings->max_total_exposure) {
            return 'Max total exposure reached.';
        }

        if ((float) $outcome->liquidity < (float) $settings->minimum_liquidity) {
            return 'Liquidity is below the minimum threshold.';
        }

        if ((float) $outcome->spread > (float) $settings->max_spread) {
            return 'Spread is wider than allowed.';
        }

        if ((float) $signal->edge < (float) $settings->min_edge) {
            return 'AI edge is below the entry threshold.';
        }

        if ((int) $signal->confidence < (int) $settings->min_confidence) {
            return 'AI confidence is below the entry threshold.';
        }

        if ((float) $portfolio->cash_balance <= 0) {
            return 'Paper cash balance is empty.';
        }

        return null;
    }

    private function riskBudget(string $riskLevel, float $max): float
    {
        return match ($riskLevel) {
            'safe' => $max * 0.45,
            'aggressive' => $max,
            default => $max * 0.70,
        };
    }

    private function log(Portfolio $portfolio, ?MarketOutcome $outcome, string $action, string $status, string $reason, array $context = []): void
    {
        BotDecisionLog::create([
            'portfolio_id' => $portfolio->id,
            'market_outcome_id' => $outcome?->id,
            'action' => $action,
            'status' => $status,
            'reason' => $reason,
            'context' => $context,
            'decided_at' => now(),
        ]);
    }
}
