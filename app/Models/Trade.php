<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trade extends Model
{
    protected $fillable = [
        'instrument',
        'execution_time',
        'execution_price',
        'quantity',
        'market_position',
        'order_action',
        'order_type',
        'account_name',
        'trade_status',
    ];
}
