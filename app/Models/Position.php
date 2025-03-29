<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'type',
        'master_id',
        'instrument',
        'market_position',
        'quantity',
        'average_price',
        'unrealized_pnl',
        'stop_loss',
        'take_profit',
        'status', // Added field
        'time',
    ];

    protected $casts = [
        'time' => 'datetime',
        'stop_loss' => 'decimal:2',
        'take_profit' => 'decimal:2',
        'average_price' => 'decimal:2',
        'unrealized_pnl' => 'decimal:2',
    ];
}
