<?php

namespace App\Services\Trading;

use App\Models\BotSetting;
use App\Models\Portfolio;
use App\Models\Position;

class PortfolioService
{
    public function defaultPortfolio(): Portfolio
    {
        $portfolio = Portfolio::firstOrCreate(['id' => 1], [
            'name' => 'Paper Portfolio',
            'starting_balance' => 10000,
            'cash_balance' => 10000,
        ]);

        BotSetting::firstOrCreate(['portfolio_id' => $portfolio->id]);

        return $portfolio->fresh(['settings']);
    }

    public function refresh(Portfolio $portfolio): Portfolio
    {
        $realized = 0.0;
        $unrealized = 0.0;
        $exposure = 0.0;

        $portfolio->positions()->where('status', 'open')->with('outcome')->get()->each(function (Position $position) use (&$unrealized, &$exposure) {
            $price = (float) ($position->outcome->price ?? $position->avg_entry_price);
            $marketValue = (float) $position->shares * $price;
            $pnl = $marketValue - (float) $position->cost_basis;

            $position->update([
                'current_price' => $price,
                'market_value' => $marketValue,
                'unrealized_pnl' => $pnl,
            ]);

            $unrealized += $pnl;
            $exposure += $marketValue;
        });

        $realized = (float) $portfolio->positions()->where('status', 'closed')->sum('realized_pnl');

        $portfolio->update([
            'realized_pnl' => $realized,
            'unrealized_pnl' => $unrealized,
            'total_exposure' => $exposure,
        ]);

        return $portfolio->fresh(['settings']);
    }
}
