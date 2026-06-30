<?php

namespace App\Services\Polymarket;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class PolymarketClient
{
    private function http(): PendingRequest
    {
        return Http::acceptJson()
            ->timeout(20)
            ->retry(2, 500)
            ->withUserAgent('PolyPaperBot/0.1');
    }

    public function markets(int $limit = 100): array
    {
        return $this->http()
            ->get(config('polymarket.gamma_url').'/markets', [
                'active' => 'true',
                'closed' => 'false',
                'limit' => $limit,
                'order' => 'volume',
                'ascending' => 'false',
            ])
            ->throw()
            ->json();
    }

    public function orderBook(string $tokenId): array
    {
        return $this->http()
            ->get(config('polymarket.clob_url').'/book', ['token_id' => $tokenId])
            ->throw()
            ->json();
    }

    public function marketPositions(string $marketId): array
    {
        return $this->http()
            ->get(config('polymarket.data_url').'/positions', ['market' => $marketId])
            ->throw()
            ->json();
    }
}
