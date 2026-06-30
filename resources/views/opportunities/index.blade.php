<x-layouts.app heading="AI Opportunities" eyebrow="Ranked paper bot candidates">
    <section class="panel tabs-panel">
        <a class="tab active" href="{{ route('opportunities.index') }}">All Opportunities</a>
        <a class="tab" href="{{ route('opportunities.easy-wins') }}">Easy Wins</a>
        <a class="tab" href="{{ route('opportunities.easy-wins', ['mode' => 'fast']) }}">Fast Wins</a>
    </section>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Grade</th><th>Market</th><th>Outcome</th><th>Market</th><th>Fair</th><th>Edge</th><th>Confidence</th><th>Why</th></tr></thead>
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
                                <a href="{{ route('markets.show', $market) }}">{{ Str::limit($market->question, 70) }}</a>
                            @else
                                Unknown market
                            @endif
                        </td>
                        <td>{{ $outcome ? $outcome->name : 'Unknown outcome' }}</td>
                        <td>{{ number_format((float) $signal->market_probability * 100, 1) }}%</td>
                        <td>{{ number_format((float) $signal->fair_probability * 100, 1) }}%</td>
                        <td class="positive">+{{ number_format((float) $signal->edge * 100, 1) }}%</td>
                        <td>{{ $signal->confidence }}%</td>
                        <td>{{ Str::limit($signal->explanation, 120) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">
                            <div class="empty-action">
                                <p>No signals yet. Sync markets, order books, and AI scores from the browser.</p>
                                <form method="post" action="{{ route('sync.data') }}">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
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
