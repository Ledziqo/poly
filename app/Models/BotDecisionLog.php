<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotDecisionLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'decided_at' => 'datetime',
        ];
    }

    public function outcome()
    {
        return $this->belongsTo(MarketOutcome::class, 'market_outcome_id');
    }
}
