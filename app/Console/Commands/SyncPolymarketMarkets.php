<?php

namespace App\Console\Commands;

use App\Services\Polymarket\MarketSyncService;
use Illuminate\Console\Command;

class SyncPolymarketMarkets extends Command
{
    protected $signature = 'poly:sync-markets {--limit=}';

    protected $description = 'Sync active Polymarket markets and outcomes.';

    public function handle(MarketSyncService $sync): int
    {
        $count = $sync->syncMarkets($this->option('limit') ? (int) $this->option('limit') : null);
        $this->info("Synced {$count} markets.");

        return self::SUCCESS;
    }
}
