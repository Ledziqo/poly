<?php

namespace App\Services\Trading;

use App\Models\MarketOutcome;

class OrderBookFillSimulator
{
    public function buy(MarketOutcome $outcome, float $budget, float $maxSlippage): array
    {
        return $this->fill($outcome, $budget, 'asks', $maxSlippage);
    }

    public function sell(MarketOutcome $outcome, float $shares, float $maxSlippage): array
    {
        $book = $outcome->order_book ?? [];
        $levels = collect($book['bids'] ?? [])->sortByDesc('price')->values();
        $remaining = $shares;
        $filledShares = 0.0;
        $notional = 0.0;
        $fills = [];

        foreach ($levels as $level) {
            if ($remaining <= 0) {
                break;
            }

            $levelShares = min($remaining, (float) $level['size']);
            $filledShares += $levelShares;
            $notional += $levelShares * (float) $level['price'];
            $remaining -= $levelShares;
            $fills[] = ['price' => (float) $level['price'], 'shares' => $levelShares];
        }

        return $this->result($outcome, $filledShares, $notional, $shares, $fills, $maxSlippage, false);
    }

    private function fill(MarketOutcome $outcome, float $budget, string $side, float $maxSlippage): array
    {
        $book = $outcome->order_book ?? [];
        $levels = collect($book[$side] ?? [])->sortBy('price')->values();
        $remainingBudget = $budget;
        $filledShares = 0.0;
        $notional = 0.0;
        $fills = [];

        foreach ($levels as $level) {
            if ($remainingBudget <= 0) {
                break;
            }

            $price = (float) $level['price'];
            $availableShares = (float) $level['size'];
            $maxSharesAtLevel = $price > 0 ? $remainingBudget / $price : 0;
            $levelShares = min($availableShares, $maxSharesAtLevel);

            if ($levelShares <= 0) {
                continue;
            }

            $filledShares += $levelShares;
            $notional += $levelShares * $price;
            $remainingBudget -= $levelShares * $price;
            $fills[] = ['price' => $price, 'shares' => $levelShares];
        }

        return $this->result($outcome, $filledShares, $notional, $budget, $fills, $maxSlippage, true);
    }

    private function result(MarketOutcome $outcome, float $shares, float $notional, float $requested, array $fills, float $maxSlippage, bool $buy): array
    {
        $avgPrice = $shares > 0 ? $notional / $shares : 0;
        $reference = (float) ($outcome->price ?? $avgPrice);
        $slippage = $reference > 0 ? abs($avgPrice - $reference) : 0;
        $status = $shares <= 0 ? 'rejected' : 'filled';

        if ($shares > 0 && $slippage > $maxSlippage) {
            $status = 'rejected';
        }

        if ($shares > 0 && $buy && $notional < ($requested * 0.95)) {
            $status = 'partial';
        }

        if ($shares > 0 && ! $buy && $shares < ($requested * 0.95)) {
            $status = 'partial';
        }

        return [
            'status' => $status,
            'shares' => round($shares, 4),
            'avg_price' => round($avgPrice, 4),
            'notional' => round($notional, 2),
            'slippage' => round($slippage, 4),
            'fills' => $fills,
        ];
    }
}
