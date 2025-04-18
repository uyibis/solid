<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaveTrader extends Model
{
    protected $fillable=['name','code','status','order_id','connection_status'];

    public function order()
    {
        return $this->belongsTo(ShopifyOrder::class);
    }

}
