<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class IncomeAccumulation extends Authenticatable
{
    protected $fillable = [
        'income_id',
        'mining_policy_id',
        'accumulated_amount',
        'next_target_amount',
    ];

    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    public function miningPolicy()
    {
        return $this->belongsTo(MiningPolicy::class);
    }

}
