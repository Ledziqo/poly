<?php

namespace App\Console\Commands;

use App\Services\Trading\PortfolioService;
use Illuminate\Console\Command;

class RefreshPortfolio extends Command
{
    protected $signature = 'poly:refresh-portfolio';

    protected $description = 'Refresh paper portfolio PnL and exposure.';

    public function handle(PortfolioService $portfolios): int
    {
        $portfolio = $portfolios->refresh($portfolios->defaultPortfolio());
        $this->info("Portfolio refreshed. Cash: {$portfolio->cash_balance}, unrealized PnL: {$portfolio->unrealized_pnl}.");

        return self::SUCCESS;
    }
}
