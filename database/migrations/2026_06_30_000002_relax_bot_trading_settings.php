<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bot_settings')->update([
            'max_open_positions' => 20,
            'max_total_exposure' => 3000,
            'minimum_liquidity' => 500,
            'max_spread' => 0.1000,
            'min_edge' => 0.0300,
            'min_confidence' => 60,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('bot_settings')->update([
            'max_open_positions' => 8,
            'max_total_exposure' => 1500,
            'minimum_liquidity' => 1000,
            'max_spread' => 0.0800,
            'min_edge' => 0.0500,
            'min_confidence' => 65,
            'updated_at' => now(),
        ]);
    }
};
