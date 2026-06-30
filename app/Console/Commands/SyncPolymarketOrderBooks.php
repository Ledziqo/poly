<?php

namespace App\Console\Commands;

use App\Services\Polymarket\OrderBookSyncService;
use Illuminate\Console\Command;

class SyncPolymarketOrderBooks extends Command
{
    protected $signature = 'poly:sync-orderbooks {--limit=150}';

    protected $description = 'Sync Polymarket CLOB order books for tracked outcomes.';

    public function handle(OrderBookSyncService $sync): int
    {
        $count = $sync->sync((int) $this->option('limit'));
        $this->info("Synced {$count} outcome order books.");

        return self::SUCCESS;
    }
}
