<x-layouts.app :heading="$mode === 'fast' ? 'Fast Wins' : 'Easy Wins'" eyebrow="High-confidence paper trade candidates">
    <section class="panel tabs-panel">
        <a class="tab" href="{{ route('opportunities.index') }}">All Opportunities</a>
        <a @class(['tab', 'active' => $mode === 'easy']) href="{{ route('opportunities.easy-wins') }}">Easy Wins</a>
        <a @class(['tab', 'active' => $mode === 'fast']) href="{{ route('opportunities.easy-wins', ['mode' => 'fast']) }}">Fast Wins</a>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>{{ $mode === 'fast' ? 'Soonest high-confidence candidates' : 'Most confident candidates' }}</h2>
            <span>{{ $mode === 'fast' ? 'Ending in 2-72 hours' : 'Confidence 75%+, fair 70%+' }}</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Market</th>
                        <th>Outcome</th>
                        <th>Market Price</th>
                        <th>Fair</th>
                        <th>Edge</th>
                        <th>Confidence</th>
                        <th>Liquidity</th>
                        <th>Spread</th>
                        <th>Ends</th>
                        <th>Why</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($signals as $signal)
                    @php
                        $outcome = $signal->outcome;
                        $market = $outcome ? $outcome->market : null;
                    @endphp
                    <tr>
                        <td><x-grade :grade="$signal->grade" /></td>
                        <td>
                            @if ($market)
                                <a href="{{ route('markets.show', $market) }}">{{ Str::limit($market->question, 68) }}</a>
                            @else
                                Unknown market
                            @endif
                        </td>
                        <td>{{ $outcome ? $outcome->name : 'Unknown outcome' }}</td>
                        <td>{{ number_format((float) $signal->market_probability * 100, 1) }}%</td>
                        <td>{{ number_format((float) $signal->fair_probability * 100, 1) }}%</td>
                        <td class="positive">+{{ number_format((float) $signal->edge * 100, 1) }}%</td>
                        <td>{{ $signal->confidence }}%</td>
                        <td>${{ number_format((float) ($outcome->liquidity ?? 0), 0) }}</td>
                        <td>{{ number_format((float) ($outcome->spread ?? 0) * 100, 1) }}%</td>
                        <td>{{ $market && $market->end_at ? $market->end_at->diffForHumans() : 'Unknown' }}</td>
                        <td>{{ Str::limit($signal->explanation, 120) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="empty">
                            <div class="empty-action">
                                <p>No {{ $mode === 'fast' ? 'fast wins' : 'easy wins' }} yet. Sync data first, or loosen settings like minimum liquidity, max spread, or min confidence.</p>
                                <form method="post" action="{{ route('sync.data') }}">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="{{ url()->current() }}{{ $mode === 'fast' ? '?mode=fast' : '' }}">
                                    <button>Sync Polymarket Data</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination">{{ $signals->links() }}</div>
</x-layouts.app>
