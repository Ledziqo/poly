<?php

namespace App\Services\Polymarket;

use App\Models\Category;
use App\Models\Market;
use App\Models\MarketOutcome;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MarketSyncService
{
    public function __construct(private readonly PolymarketClient $client)
    {
    }

    public function syncMarkets(?int $limit = null): int
    {
        $items = $this->client->markets($limit ?? config('polymarket.market_sync_limit'));
        $count = 0;

        foreach ($items as $item) {
            $categoryName = $this->categoryName($item);
            $category = null;

            if ($categoryName) {
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName]
                );
            }

            $market = Market::updateOrCreate(
                ['polymarket_id' => (string) Arr::get($item, 'id')],
                [
                    'slug' => Arr::get($item, 'slug'),
                    'question' => Arr::get($item, 'question', Arr::get($item, 'title', 'Untitled market')),
                    'description' => Arr::get($item, 'description'),
                    'category_id' => $category?->id,
                    'category_name' => $categoryName,
                    'active' => filter_var(Arr::get($item, 'active', true), FILTER_VALIDATE_BOOLEAN),
                    'closed' => filter_var(Arr::get($item, 'closed', false), FILTER_VALIDATE_BOOLEAN),
                    'archived' => filter_var(Arr::get($item, 'archived', false), FILTER_VALIDATE_BOOLEAN),
                    'volume' => $this->decimal($item, ['volumeNum', 'volume', 'volume24hr']),
                    'liquidity' => $this->decimal($item, ['liquidityNum', 'liquidity']),
                    'last_price' => $this->decimalOrNull($item, ['lastTradePrice', 'lastPrice']),
                    'end_at' => $this->dateOrNull(Arr::get($item, 'endDate')),
                    'synced_at' => now(),
                    'raw_payload' => $item,
                ]
            );

            foreach ($this->outcomes($item) as $index => $outcome) {
                MarketOutcome::updateOrCreate(
                    ['market_id' => $market->id, 'name' => $outcome['name']],
                    [
                        'token_id' => $outcome['token_id'],
                        'sort_order' => $index,
                        'price' => $outcome['price'],
                    ]
                );
            }

            $count++;
        }

        return $count;
    }

    private function outcomes(array $item): array
    {
        $names = $this->decodeList(Arr::get($item, 'outcomes')) ?: ['Yes', 'No'];
        $tokenIds = $this->decodeList(Arr::get($item, 'clobTokenIds'));
        $prices = $this->decodeList(Arr::get($item, 'outcomePrices'));

        return collect($names)->map(fn ($name, $index) => [
            'name' => (string) $name,
            'token_id' => isset($tokenIds[$index]) ? (string) $tokenIds[$index] : null,
            'price' => isset($prices[$index]) && is_numeric($prices[$index]) ? (float) $prices[$index] : null,
        ])->values()->all();
    }

    private function decodeList(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function categoryName(array $item): ?string
    {
        return Arr::get($item, 'category')
            ?: Arr::get($item, 'events.0.category')
            ?: Arr::get($item, 'tags.0.label');
    }

    private function decimal(array $item, array $keys): float
    {
        return (float) ($this->decimalOrNull($item, $keys) ?? 0);
    }

    private function decimalOrNull(array $item, array $keys): ?float
    {
        foreach ($keys as $key) {
            $value = Arr::get($item, $key);

            if (is_numeric($value)) {
                return (float) $value;
            }
        }

        return null;
    }

    private function dateOrNull(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }
}
