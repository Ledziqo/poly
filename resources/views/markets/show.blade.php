<x-layouts.app :heading="Str::limit($market->question, 84)" eyebrow="Market detail">
    <section class="grid stats-grid">
        <x-stat label="Volume" :value="'$'.number_format((float) $market->volume, 0)" />
        <x-stat label="Liquidity" :value="'$'.number_format((float) $market->liquidity, 0)" />
        <x-stat label="Ends" :value="$market->end_at?->format('M j, Y') ?? 'Unknown'" />
        <x-stat label="Category" :value="$market->category_name ?? 'Market'" />
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Outcomes</h2><span>Prices from order book snapshots</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Outcome</th><th>Price</th><th>Bid</th><th>Ask</th><th>Spread</th><th>AI Fair</th><th>Edge</th><th>Grade</th></tr></thead>
                <tbody>
                @foreach ($market->outcomes as $outcome)
                    @php $signal = $outcome->latestSignal; @endphp
                    <tr>
                        <td>{{ $outcome->name }}</td>
                        <td>{{ $outcome->price !== null ? number_format((float) $outcome->price * 100, 1).'%' : '-' }}</td>
                        <td>{{ $outcome->best_bid !== null ? number_format((float) $outcome->best_bid * 100, 1).'%' : '-' }}</td>
                        <td>{{ $outcome->best_ask !== null ? number_format((float) $outcome->best_ask * 100, 1).'%' : '-' }}</td>
                        <td>{{ $outcome->spread !== null ? number_format((float) $outcome->spread * 100, 1).'%' : '-' }}</td>
                        <td>{{ $signal ? number_format((float) $signal->fair_probability * 100, 1).'%' : '-' }}</td>
                        <td class="positive">{{ $signal ? '+'.number_format((float) $signal->edge * 100, 1).'%' : '-' }}</td>
                        <td>{{ $signal ? '' : '-' }}@if($signal)<x-grade :grade="$signal->grade" />@endif</td>
                    </tr>
                    @if ($signal)
                        <tr class="explain"><td colspan="8">{{ $signal->explanation }}</td></tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="two-col">
        <div class="panel">
            <div class="panel-head"><h2>Top Positions</h2><span>Exact/API-derived/estimated</span></div>
            @foreach ($market->outcomes as $outcome)
                <h3>{{ $outcome->name }}</h3>
                @forelse ($outcome->topPositions->take(10) as $position)
                    <article class="row-card">
                        <div><strong>#{{ $position->rank }} {{ $position->trader_name ?? Str::limit($position->wallet, 14) }}</strong><span>{{ $position->data_quality }}</span></div>
                        <b>${{ number_format((float) $position->amount, 0) }}</b>
                    </article>
                @empty
                    <p class="empty">No wallet position data synced for {{ $outcome->name }} yet.</p>
                @endforelse
            @endforeach
        </div>
        <div class="panel">
            <div class="panel-head"><h2>Bot History</h2></div>
            @forelse ($decisions as $decision)
                <article class="decision">
                    <span>{{ strtoupper($decision->status) }} / {{ $decision->outcome?->name ?? 'market' }}</span>
                    <p>{{ $decision->reason }}</p>
                    <small>{{ $decision->decided_at?->format('M j H:i') }}</small>
                </article>
            @empty
                <p class="empty">No bot decisions for this market yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
