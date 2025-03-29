<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopifyOrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'quantity',
        'price',
    ];

}
