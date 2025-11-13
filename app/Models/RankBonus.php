<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankBonus extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'member_id',
        'policy_id',
        'transfer_id',
        'self_sales',
        'group_sales',
        'bonus',
        'direct_count',
        'referral_count',
    ];

    protected $casts = [
        'self_sales' => 'decimal:9',
        'group_sales' => 'decimal:9',
        'bonus' => 'decimal:9',
    ];

    public function policy()
    {
        return $this->belongsTo(RankPolicy::class, 'policy_id', 'id');
    }

    public function referrals()
    {
        return $this->hasMany(RankBonusReferral::class, 'bonus_id', 'id');
    }
}

