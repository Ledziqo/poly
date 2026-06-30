<?php

namespace App\Services\Polymarket;

use App\Models\MarketOutcome;
use App\Models\PriceSnapshot;

class OrderBookSyncService
{
    public function __construct(private readonly PolymarketClient $client)
    {
    }

    public function sync(int $limit = 150): int
    {
        $count = 0;

        MarketOutcome::query()
            ->whereNotNull('token_id')
            ->with('market')
            ->whereHas('market', fn ($query) => $query->where('active', true)->where('closed', false))
            ->limit($limit)
            ->get()
            ->each(function (MarketOutcome $outcome) use (&$count) {
                $book = $this->client->orderBook($outcome->token_id);
                $bids = $this->levels($book['bids'] ?? [], descending: true);
                $asks = $this->levels($book['asks'] ?? []);
                $bestBid = $bids[0]['price'] ?? null;
                $bestAsk = $asks[0]['price'] ?? null;
                $spread = $bestBid !== null && $bestAsk !== null ? max(0, $bestAsk - $bestBid) : null;
                $price = $this->displayPrice($bestBid, $bestAsk, $outcome->price);
                $liquidity = collect($bids)->merge($asks)->take(20)->sum(fn ($level) => $level['price'] * $level['size']);

                $payload = ['bids' => $bids, 'asks' => $asks];

                $outcome->update([
                    'price' => $price,
                    'best_bid' => $bestBid,
                    'best_ask' => $bestAsk,
                    'spread' => $spread,
                    'liquidity' => $liquidity,
                    'order_book' => $payload,
                    'price_synced_at' => now(),
                ]);

                PriceSnapshot::create([
                    'market_outcome_id' => $outcome->id,
                    'price' => $price,
                    'best_bid' => $bestBid,
                    'best_ask' => $bestAsk,
                    'spread' => $spread,
                    'liquidity' => $liquidity,
                    'order_book' => $payload,
                    'captured_at' => now(),
                ]);

                $count++;
            });

        return $count;
    }

    private function levels(array $levels, bool $descending = false): array
    {
        $collection = collect($levels)
            ->map(fn ($level) => [
                'price' => (float) ($level['price'] ?? 0),
                'size' => (float) ($level['size'] ?? 0),
            ])
            ->filter(fn ($level) => $level['price'] > 0 && $level['size'] > 0);

        return ($descending ? $collection->sortByDesc('price') : $collection->sortBy('price'))
            ->values()
            ->all();
    }

    private function displayPrice(?float $bestBid, ?float $bestAsk, ?float $fallback): ?float
    {
        if ($bestBid !== null && $bestAsk !== null && ($bestAsk - $bestBid) <= 0.10) {
            return round(($bestBid + $bestAsk) / 2, 4);
        }

        return $fallback !== null ? round($fallback, 4) : null;
    }
}
