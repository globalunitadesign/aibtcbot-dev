<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use App\Models\Crypto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AssetTransfer extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'member_id',
        'asset_id',
        'type',
        'status',
        'amount',
        'tax',
        'fee',
        'actual_amount',
        'before_balance',
        'after_balance',
        'txid',
        'image_urls',
        'memo'
    ];

    protected $casts = [
        'amount' => 'decimal:9',
        'tax' => 'decimal:9',
        'fee' => 'decimal:9',
        'actual_amount' => 'decimal:9',
        'before_balance' => 'decimal:9',
        'after_balance' => 'decimal:9',
        'image_urls' => 'array',
    ];

    protected $appends =
    [
        'status_text',
        'type_text',
        'waiting_period',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }

    public function getAmountInUsdt()
    {

        $price = Crypto::getCurrentPrice($this->asset->coin?->code);
        $amount_in_usdt = $this->amount * (float) $price;

        return $amount_in_usdt;

    }

    public function getTypeTextAttribute()
    {
        switch ($this->type) {
            case 'deposit' :
                return __('asset.deposit');
            break;
            case 'withdrawal' :
                return __('asset.withdrawal');
            break;
            case 'internal' :
                return __('asset.internal_transfer');
            break;
            case 'manual_deposit' :
                return __('asset.manual_deposit');
            break;
            case 'staking_refund' :
                return __('asset.staking_refund');
            break;
        }
    }

    public function getStatusTextAttribute()
    {

        $deposit_status_map = [
            'pending' => __('system.apply'),
            'waiting' => __('system.waiting'),
            'completed' => __('system.completed'),
            'canceled' => __('system.cancel'),
            'refunded' => __('system.refund'),
        ];

        $withdrawal_status_map = [
            'pending' => __('system.apply'),
            'completed' => __('system.completed'),
            'canceled' => __('system.cancel'),
        ];

        if ($this->type === 'deposit') {
            return $deposit_status_map[$this->status];
        } else if ($this->type === 'withdrawal') {
            return $withdrawal_status_map[$this->status];
        }
    }

    public function getWaitingPeriodAttribute()
    {
        $created_at = $this->created_at;
        $deposit_period = AssetPolicy::first()->deposit_period;

        $start_date = Carbon::parse($created_at)->startOfDay();
        $end_date = $start_date->copy()->addDays($deposit_period);

        $now = Carbon::now()->startOfDay();

        if ($now->gte($end_date) || $this->status == 'canceled' || $this->status == 'refunded') {
            return 0;
        }

        $waiting_period = $now->diffInDays($end_date);

        return $waiting_period;

    }

    public static function reflectDeposit()
    {

        $deposit_period = AssetPolicy::first()->deposit_period;

        $cutoff = now()->subDays($deposit_period)->endOfDay();

        $transfers = self::where('status', 'waiting')
            ->where('type', 'deposit')
            ->where('created_at', '<=', $cutoff)
            ->get();

        Log::channel('asset')->info('start to reflected to user asset balance', ['cutoff' => $cutoff]);
        Log::channel('asset')->info('transfer count', ['transfer_count' => count($transfers)]);

        foreach ($transfers as $deposit) {
            $deposit->processDeposit();
        }

        Log::channel('asset')->info('end to reflected to user asset balance');
    }

    public function processDeposit()
    {
        DB::beginTransaction();

        try {
            $asset = $this->asset;

            $before_balance = $asset->balance;
            $amount = $this->amount;
            $after_balance = $asset->balance + $amount;

            $asset->update(['balance' => $after_balance]);
            $this->update([
                'status' => 'completed',
                'before_balance' => $before_balance,
                'after_balance' => $after_balance,
            ]);

            Log::channel('asset')->info('Deposited amount reflected to user asset balance', [
                'mebmer_id' => $asset->member_id,
                'transfer_id' => $this->id,
                'balance' => $amount,
                'before_balance' => $before_balance,
                'after_balance' => $after_balance,
                'timestamp' => now(),
            ]);

            DB::commit();

            $member = Member::where('id', $asset->member_id)->first();

            $member->checkMemberValidity();
            $member->checkMemberGrade();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::channel('asset')->error('Failed to reflected to user asset balance', [
                'transfer_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

        }
    }
}
