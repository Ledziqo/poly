<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fill_details' => 'array',
            'executed_at' => 'datetime',
        ];
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(MarketOutcome::class, 'market_outcome_id');
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
