<x-layouts.app heading="Trade History" eyebrow="Every simulated fill">
    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Time</th><th>Side</th><th>Market</th><th>Outcome</th><th>Shares</th><th>Avg Price</th><th>Notional</th><th>Fill</th></tr></thead>
                <tbody>
                @forelse ($trades as $trade)
                    <tr>
                        <td>{{ $trade->executed_at?->format('M j H:i') }}</td>
                        <td>{{ strtoupper($trade->side) }}</td>
                        <td><a href="{{ route('markets.show', $trade->outcome->market) }}">{{ Str::limit($trade->outcome->market->question, 68) }}</a></td>
                        <td>{{ $trade->outcome->name }}</td>
                        <td>{{ number_format((float) $trade->shares, 2) }}</td>
                        <td>{{ number_format((float) $trade->avg_price * 100, 1) }}%</td>
                        <td>${{ number_format((float) $trade->notional, 2) }}</td>
                        <td>{{ $trade->fill_status }}</td>
                    </tr>
                    <tr class="explain"><td colspan="8">{{ $trade->explanation }}</td></tr>
                @empty
                    <tr><td colspan="8" class="empty">No trades yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="pagination">{{ $trades->links() }}</div>
</x-layouts.app>
