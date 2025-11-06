<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MiningProfit extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'user_id',
        'reward_id',
        'transfer_id',
        'type',
        'profit',
        'node_amount',
        'reward_rate',
    ];

    protected $casts = [
        'profit' => 'decimal:9',
        'node_amount' => 'decimal:9',
        'reward_rate' => 'decimal:9',
    ];

    public function getStatusTextAttribute()
    {
        return '지급 완료';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reward()
    {
        return $this->belongsTo(MiningReward::class, 'reward_id', 'id');
    }

    public function transfer()
    {
        return $this->belongsTo(IncomeTransfer::class, 'transfer_id', 'id');
    }

    public function levelBonus()
    {
        return $this->hasOne(LevelBonus::class, 'profit_id', 'id');
    }
}
