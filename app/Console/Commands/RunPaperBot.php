<?php

namespace App\Console\Commands;

use App\Services\Trading\PaperTradingBot;
use Illuminate\Console\Command;

class RunPaperBot extends Command
{
    protected $signature = 'poly:run-bot';

    protected $description = 'Run automatic paper trading decisions.';

    public function handle(PaperTradingBot $bot): int
    {
        $result = $bot->run();
        $this->info('Paper bot: '.json_encode($result));

        return self::SUCCESS;
    }
}
