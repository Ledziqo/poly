@php
    $equity = (float) $portfolio->cash_balance + (float) $portfolio->total_exposure;
    $pnl = (float) $portfolio->realized_pnl + (float) $portfolio->unrealized_pnl;
    $roi = (float) $portfolio->starting_balance > 0 ? ($pnl / (float) $portfolio->starting_balance) * 100 : 0;
@endphp

<x-layouts.app heading="Portfolio" eyebrow="Fake balance, real market marks">
    <section class="grid stats-grid">
        <x-stat label="Equity" :value="'$'.number_format($equity, 2)" />
        <x-stat label="Cash" :value="'$'.number_format((float) $portfolio->cash_balance, 2)" />
        <x-stat label="Realized PnL" :value="'$'.number_format((float) $portfolio->realized_pnl, 2)" :tone="(float) $portfolio->realized_pnl >= 0 ? 'positive' : 'negative'" />
        <x-stat label="ROI" :value="number_format($roi, 2).'%'" :tone="$roi >= 0 ? 'positive' : 'negative'" />
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Positions</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Status</th><th>Market</th><th>Outcome</th><th>Shares</th><th>Entry</th><th>Current</th><th>Value</th><th>PnL</th></tr></thead>
                <tbody>
                @forelse ($positions as $position)
                    <tr>
                        <td>{{ ucfirst($position->status) }}</td>
                        <td><a href="{{ route('markets.show', $position->outcome->market) }}">{{ Str::limit($position->outcome->market->question, 70) }}</a></td>
                        <td>{{ $position->outcome->name }}</td>
                        <td>{{ number_format((float) $position->shares, 2) }}</td>
                        <td>{{ number_format((float) $position->avg_entry_price * 100, 1) }}%</td>
                        <td>{{ number_format((float) $position->current_price * 100, 1) }}%</td>
                        <td>${{ number_format((float) $position->market_value, 2) }}</td>
                        <td @class([(float) ($position->status === 'open' ? $position->unrealized_pnl : $position->realized_pnl) >= 0 ? 'positive' : 'negative'])>${{ number_format((float) ($position->status === 'open' ? $position->unrealized_pnl : $position->realized_pnl), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">No positions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="pagination">{{ $positions->links() }}</div>
</x-layouts.app>
