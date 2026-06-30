<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('poly:sync-markets --limit=100')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('poly:sync-orderbooks --limit=150')->everyTwoMinutes()->withoutOverlapping();
Schedule::command('poly:score-signals --limit=300')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('poly:run-bot')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('poly:refresh-portfolio')->everyTwoMinutes()->withoutOverlapping();
