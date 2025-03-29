<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopifyOrder extends Model
{
    protected $fillable = [
        'order_id',
        'email',
        'total_price',
        'currency',
        'created_at',
        'customer_name',
    ];

}
