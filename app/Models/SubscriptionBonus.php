<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'transfer_id',
        'referrer_id',
        'withdrawal_id',
        'bonus',
    ];

    protected $casts = [
        'bonus' => 'decimal:9',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function transfer()
    {
        return $this->belongsTo(IncomeTransfer::class, 'transfer_id', 'id');
    }

    public function withdrawal()
    {
        return $this->belongsTo(IncomeTransfer::class, 'withdrawal_id', 'id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id', 'id');
    }
}
