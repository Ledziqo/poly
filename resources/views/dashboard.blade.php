@php
    $equity = (float) $portfolio->cash_balance + (float) $portfolio->total_exposure;
    $totalPnl = (float) $portfolio->realized_pnl + (float) $portfolio->unrealized_pnl;
    $botEnabled = (bool) optional($portfolio->settings)->enabled;
@endphp

<x-layouts.app heading="Command Dashboard" eyebrow="What should the bot trade right now?">
    <section class="grid stats-grid">
        <x-stat label="Paper Equity" :value="'$'.number_format($equity, 2)" />
        <x-stat label="Cash" :value="'$'.number_format((float) $portfolio->cash_balance, 2)" />
        <x-stat label="Total PnL" :value="'$'.number_format($totalPnl, 2)" :tone="$totalPnl >= 0 ? 'positive' : 'negative'" />
        <x-stat label="Bot Status" :value="$botEnabled ? 'Running' : 'Paused'" :tone="$botEnabled ? 'positive' : 'warning'" />
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Best AI Opportunities</h2>
            <a href="{{ route('opportunities.index') }}">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Market</th><th>Outcome</th><th>Price</th><th>Fair</th><th>Edge</th><th>Grade</th></tr></thead>
                <tbody>
                @forelse ($opportunities as $signal)
                    @php
                        $outcome = $signal->outcome;
                        $market = $outcome ? $outcome->market : null;
                    @endphp
                    <tr>
                        <td>
                            @if ($market)
                                <a href="{{ route('markets.show', $market) }}">{{ Str::limit($market->question, 72) }}</a>
                            @else
                                Unknown market
                            @endif
                        </td>
                        <td>{{ $outcome ? $outcome->name : 'Unknown outcome' }}</td>
                        <td>{{ number_format((float) $signal->market_probability * 100, 1) }}%</td>
                        <td>{{ number_format((float) $signal->fair_probability * 100, 1) }}%</td>
                        <td class="positive">+{{ number_format((float) $signal->edge * 100, 1) }}%</td>
                        <td><x-grade :grade="$signal->grade" /></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Run market/order book sync and signal scoring to populate opportunities.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="two-col">
        <div class="panel">
            <div class="panel-head"><h2>Open Paper Positions</h2><a href="{{ route('portfolio.index') }}">Portfolio</a></div>
            @forelse ($positions as $position)
                @php
                    $positionOutcome = $position->outcome;
                    $positionMarket = $positionOutcome ? $positionOutcome->market : null;
                    $positionPnlClass = (float) $position->unrealized_pnl >= 0 ? 'positive' : 'negative';
                @endphp
                <article class="row-card">
                    <div>
                        <strong>{{ $positionOutcome ? $positionOutcome->name : 'Unknown outcome' }}</strong>
                        <span>{{ Str::limit($positionMarket ? $positionMarket->question : 'Unknown market', 82) }}</span>
                    </div>
                    <b class="{{ $positionPnlClass }}">${{ number_format((float) $position->unrealized_pnl, 2) }}</b>
                </article>
            @empty
                <p class="empty">No open positions yet. The bot will enter only when signals pass risk checks.</p>
            @endforelse
        </div>
        <div class="panel">
            <div class="panel-head"><h2>Recent Bot Decisions</h2></div>
            @forelse ($decisions as $decision)
                <article class="decision">
                    <span>{{ strtoupper($decision->status) }} / {{ $decision->action }}</span>
                    <p>{{ $decision->reason }}</p>
                    <small>{{ $decision->decided_at ? $decision->decided_at->diffForHumans() : '' }}</small>
                </article>
            @empty
                <p class="empty">No bot logs yet.</p>
            @endforelse
        </div>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Highest Volume Markets</h2><a href="{{ route('markets.index') }}">Markets</a></div>
        <div class="cards">
            @foreach ($markets as $market)
                <a class="market-card" href="{{ route('markets.show', $market) }}">
                    <span>{{ $market->category_name ?? 'Market' }}</span>
                    <strong>{{ Str::limit($market->question, 96) }}</strong>
                    <small>Vol ${{ number_format((float) $market->volume, 0) }} · Liq ${{ number_format((float) $market->liquidity, 0) }}</small>
                </a>
            @endforeach
        </div>
    </section>
</x-layouts.app>
