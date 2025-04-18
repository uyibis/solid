<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trader extends Model
{
    protected $fillable = [
        'user_trader_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = Str::uuid()->toString();
            }
        });
    }

    public function userTrader()
    {
        return $this->belongsTo(UserTrader::class, 'user_trader_id');
    }
}
