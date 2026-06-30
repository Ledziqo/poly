<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'closed' => 'boolean',
            'archived' => 'boolean',
            'end_at' => 'datetime',
            'synced_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(MarketOutcome::class)->orderBy('sort_order');
    }
}
