<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketOutcome extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'order_book' => 'array',
            'price_synced_at' => 'datetime',
        ];
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function signals(): HasMany
    {
        return $this->hasMany(AiSignal::class);
    }

    public function latestSignal(): HasOne
    {
        return $this->hasOne(AiSignal::class)->latestOfMany('scored_at');
    }

    public function topPositions(): HasMany
    {
        return $this->hasMany(TopPosition::class)->orderBy('rank');
    }
}
