<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceSnapshot extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'order_book' => 'array',
            'captured_at' => 'datetime',
        ];
    }
}
