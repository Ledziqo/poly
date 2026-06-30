<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSignal extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'scored_at' => 'datetime',
        ];
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(MarketOutcome::class, 'market_outcome_id');
    }
}
