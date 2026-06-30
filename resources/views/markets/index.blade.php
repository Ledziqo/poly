<x-layouts.app heading="Markets" eyebrow="Real Polymarket market browser">
    <section class="panel">
        <form class="filters" method="get">
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search markets">
            <select name="category">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="sort">
                <option value="volume" @selected(($filters['sort'] ?? '') === 'volume')>Highest volume</option>
                <option value="liquidity" @selected(($filters['sort'] ?? '') === 'liquidity')>Highest liquidity</option>
            </select>
            <button>Filter</button>
        </form>
    </section>

    <section class="cards market-grid">
        @forelse ($markets as $market)
            @php $best = $market->outcomes->pluck('latestSignal')->filter()->sortByDesc('edge')->first(); @endphp
            <a class="market-card" href="{{ route('markets.show', $market) }}">
                <span>{{ $market->category_name ?? 'Uncategorized' }}</span>
                <strong>{{ $market->question }}</strong>
                <small>Vol ${{ number_format((float) $market->volume, 0) }} · Liq ${{ number_format((float) $market->liquidity, 0) }}</small>
                @if ($best)
                    <div class="card-footer">
                        <x-grade :grade="$best->grade" />
                        <b class="positive">+{{ number_format((float) $best->edge * 100, 1) }}%</b>
                    </div>
                @endif
            </a>
        @empty
            <div class="panel empty">No markets yet. Run <code>php artisan poly:sync-markets</code>.</div>
        @endforelse
    </section>

    <div class="pagination">{{ $markets->links() }}</div>
</x-layouts.app>
