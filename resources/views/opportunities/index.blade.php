<x-layouts.app heading="AI Opportunities" eyebrow="Ranked paper bot candidates">
    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Grade</th><th>Market</th><th>Outcome</th><th>Market</th><th>Fair</th><th>Edge</th><th>Confidence</th><th>Why</th></tr></thead>
                <tbody>
                @forelse ($signals as $signal)
                    <tr>
                        <td><x-grade :grade="$signal->grade" /></td>
                        <td><a href="{{ route('markets.show', $signal->outcome->market) }}">{{ Str::limit($signal->outcome->market->question, 70) }}</a></td>
                        <td>{{ $signal->outcome->name }}</td>
                        <td>{{ number_format((float) $signal->market_probability * 100, 1) }}%</td>
                        <td>{{ number_format((float) $signal->fair_probability * 100, 1) }}%</td>
                        <td class="positive">+{{ number_format((float) $signal->edge * 100, 1) }}%</td>
                        <td>{{ $signal->confidence }}%</td>
                        <td>{{ Str::limit($signal->explanation, 120) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">No signals yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="pagination">{{ $signals->links() }}</div>
</x-layouts.app>
