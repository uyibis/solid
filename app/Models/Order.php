<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'type',
        'master_id',
        'order_id',
        'instrument',
        'order_type',
        'quantity',
        'price',
        'status',
        'time',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];
}
