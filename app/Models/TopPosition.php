<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopPosition extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'observed_at' => 'datetime',
        ];
    }
}
