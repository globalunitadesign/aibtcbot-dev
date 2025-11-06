<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiningRefund extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'user_id',
        'mining_id',
        'transfer_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:9',
    ];

    public function mining()
    {
        return $this->belongsTo(Mining::class, 'mining_id', 'id');
    }

    public function transfer()
    {
        return $this->belongsTo(AssetTransfer::class, 'transfer_id', 'id');
    }
}
