<?php

namespace App\Console\Commands;

use App\Services\Signals\AiSignalService;
use Illuminate\Console\Command;

class ScoreAiSignals extends Command
{
    protected $signature = 'poly:score-signals {--limit=300}';

    protected $description = 'Score AI-style Polymarket opportunities.';

    public function handle(AiSignalService $signals): int
    {
        $count = $signals->scoreMarkets((int) $this->option('limit'));
        $this->info("Scored {$count} outcomes.");

        return self::SUCCESS;
    }
}
