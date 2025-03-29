<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Trader;

class TraderActivityLog extends Model
{
    protected $fillable = [
        'trader_id',
        'ip_address',
    ];

    /**
     * Relationship to the Trader model.
     */
    public function trader()
    {
        return $this->belongsTo(Trader::class);
    }
}
