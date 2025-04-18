<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTrader extends Model
{
    use HasFactory;

    protected $table = 'user_traders';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'trader_id',
        'status',
    ];

    /**
     * Get the trader associated with this user.
     */
    public function trader()
    {
        return $this->hasMany(Trader::class, 'user_trader_id');
    }
    public function slaves()
    {
        return $this->hasMany(ShopifyOrderItem::class, 'product_id');
    }
}
