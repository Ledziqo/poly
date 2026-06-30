<?php

namespace App\Services\Signals;

use App\Models\AiSignal;
use App\Models\MarketOutcome;

class AiSignalService
{
    public function scoreMarkets(int $limit = 300): int
    {
        $count = 0;

        MarketOutcome::query()
            ->with('market')
            ->whereNotNull('price')
            ->whereHas('market', fn ($query) => $query->where('active', true)->where('closed', false))
            ->orderByDesc('liquidity')
            ->limit($limit)
            ->get()
            ->each(function (MarketOutcome $outcome) use (&$count) {
                $this->score($outcome);
                $count++;
            });

        return $count;
    }

    public function score(MarketOutcome $outcome): AiSignal
    {
        $market = $outcome->market;
        $marketProbability = (float) $outcome->price;
        $liquidityBoost = min(0.05, log10(max(10, (float) $outcome->liquidity)) / 100);
        $volumeBoost = min(0.04, log10(max(10, (float) $market->volume)) / 120);
        $spreadPenalty = min(0.08, (float) $outcome->spread);
        $expiryPenalty = $market->end_at && $market->end_at->diffInHours(now(), false) > -24 ? 0.03 : 0;
        $fairProbability = max(0.02, min(0.98, $marketProbability + $liquidityBoost + $volumeBoost - $spreadPenalty - $expiryPenalty));
        $edge = $fairProbability - $marketProbability;
        $confidence = (int) max(1, min(99, 50 + ($edge * 500) + ($liquidityBoost * 200) - ($spreadPenalty * 200)));
        $grade = $this->grade($edge, $confidence, (float) $outcome->liquidity, (float) $outcome->spread);

        return AiSignal::create([
            'market_outcome_id' => $outcome->id,
            'market_probability' => $marketProbability,
            'fair_probability' => round($fairProbability, 4),
            'edge' => round($edge, 4),
            'confidence' => $confidence,
            'grade' => $grade,
            'features' => [
                'liquidity_boost' => round($liquidityBoost, 4),
                'volume_boost' => round($volumeBoost, 4),
                'spread_penalty' => round($spreadPenalty, 4),
                'expiry_penalty' => round($expiryPenalty, 4),
            ],
            'explanation' => $this->explanation($grade, $edge, $confidence, $outcome),
            'scored_at' => now(),
        ]);
    }

    private function grade(float $edge, int $confidence, float $liquidity, float $spread): string
    {
        if ($liquidity < 500 || $spread > 0.12 || $edge < 0.02) {
            return 'Skip';
        }

        if ($edge >= 0.08 && $confidence >= 80) {
            return 'Strong Entry';
        }

        if ($edge >= 0.05 && $confidence >= 65) {
            return 'Good Entry';
        }

        if ($edge >= 0.03) {
            return 'Watch';
        }

        return 'Too Late';
    }

    private function explanation(string $grade, float $edge, int $confidence, MarketOutcome $outcome): string
    {
        return sprintf(
            '%s: fair probability is %.1f%% versus market %.1f%%, edge %.1f%%, confidence %d%%, spread %.1f%%, liquidity $%s.',
            $grade,
            (($outcome->price ?? 0) + $edge) * 100,
            ((float) $outcome->price) * 100,
            $edge * 100,
            $confidence,
            ((float) $outcome->spread) * 100,
            number_format((float) $outcome->liquidity, 0)
        );
    }
}
