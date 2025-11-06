<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class MiningDailyStat extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'policy_id',
        'stat_date',
        'exchange_rate',
        'node_amount',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'exchange_rate' => 'decimal:9',
        'node_amount' => 'decimal:9',
    ];

    public function policy()
    {
        return $this->belongsTo(MiningPolicy::class, 'policy_id', 'id');
    }
}
