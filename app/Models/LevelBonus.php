<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelBonus extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'member_id',
        'referrer_id',
        'transfer_id',
        'profit_id',
        'bonus',
    ];

    protected $casts = [
        'bonus' => 'decimal:9',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function profit()
    {
        return $this->belongsTo(MiningProfit::class, 'profit_id', 'id');
    }

    public function transfer()
    {
        return $this->belongsTo(IncomeTransfer::class, 'transfer_id', 'id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id', 'id');
    }

    public function matchings()
    {
        return $this->hasMany(LevelMatching::class, 'bonus_id', 'id');
    }

}
