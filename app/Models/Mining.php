<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Mining extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'user_id',
        'asset_id',
        'refund_id',
        'reward_id',
        'policy_id',
        'status',
        'coin_amount',
        'refund_coin_amount',
        'node_amount',
        'exchange_rate',
        'split_period',
        'reward_count',
        'reward_limit',
        'started_at',
        'maturity_at',
    ];

    protected $casts = [
        'coin_amount' => 'decimal:9',
        'refund_coin_amount' => 'decimal:9',
        'node_amount' => 'decimal:9',
        'exchange_rate' => 'decimal:9',
        'started_at' => 'datetime:Y-m-d',
        'maturity_at' => 'datetime:Y-m-d',
    ];

    protected $appends = [
        'status_text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'refund_id', 'id');
    }

    public function income()
    {
        return $this->belongsTo(Income::class, 'reward_id', 'id');
    }

    public function policy()
    {
        return $this->belongsTo(MiningPolicy::class, 'policy_id', 'id');
    }

    public function refunds()
    {
        return $this->hasMany(MiningRefund::class, 'mining_id', 'id');
    }

    public function rewards()
    {
        return $this->hasMany(MiningReward::class, 'mining_id', 'id');
    }

    public function referralBonus()
    {
        return $this->hasMany(ReferralBonus::class, 'mining_id', 'id');
    }

    public function getStatusTextAttribute()
    {
        if ($this->status === 'pending') {
            return '진행중';
        } else if ($this->status === 'completed') {
            return '만료';
        }
        return '오류';
    }

    public function getIsRewardAvaliableToday()
    {
        $today = now()->toDateString();

        if ($this->rewards()->whereDate('reward_date', $today)->exists()) {

            return false;
        }

        $reward_days = $this->policy->reward_days;

        if (empty($reward_days)) {

            return true;
        }

        $days = array_map('trim', explode(',', strtolower($reward_days)));

        $today_day = strtolower(now()->format('D'));

        return in_array($today_day, $days);
    }

    public function getBenefitRule($type)
    {
        $benefit_rules = $this->policy->marketing->benefit_rules;

        return $benefit_rules[$type];
    }

    public static function storeMiningReward()
    {
        Log::channel('mining')->info('store mining rewards');

        $today = now();
        $minings = self::where('started_at', '<=', $today)
            ->whereColumn('reward_count', '<', 'reward_limit')
            ->where('status','pending')
            ->get();

        foreach ($minings as $mining) {

            if (!$mining->getIsRewardAvaliableToday()) continue;

            DB::beginTransaction();

            try {

                $reward = ($mining->policy->node_amount * $mining->node_amount);

                MiningReward::create([
                    'user_id'   => $mining->user_id,
                    'mining_id' => $mining->id,
                    'reward' => $reward,
                    'reward_date' => $today,
                    'start_date' => $today,
                    'end_date' => $today->copy()->addDays($mining->split_period),
                ]);

                $mining->reward_count += 1;

                if ($mining->reward_count >= $mining->reward_limit) {
                    $mining->maturity_at = now()->addDay();
                }

                $mining->save();

                DB::commit();

            } catch (\Exception $e) {

                DB::rollBack();

                Log::channel('mining')->error('Failed to store mining reward', [
                    'user_id' => $mining->user_id,
                    'mining_id' => $mining->id,
                    'reward_date' => $today,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public static function finalizePayout()
    {
        Log::channel('mining')->info('finalize Pay Out mining');

        $today = now()->toDateString();

        $minings = self::whereDate('maturity_at', '<', $today)
            ->whereColumn('reward_limit', '=', 'reward_count')
            ->where('status', 'pending')
            ->get();

        foreach ($minings as $mining) {

            DB::beginTransaction();

            try {

                $asset = $mining->asset;

                $transfer = AssetTransfer::create([
                    'user_id' => $mining->user_id,
                    'asset_id' => $asset->id,
                    'type' => 'mining_refund',
                    'status' => 'completed',
                    'amount' => $mining->refund_coin_amount,
                    'actual_amount' => $mining->refund_coin_amount,
                    'before_balance' => $asset->balance,
                    'after_balance' => $asset->balance + $mining->refund_coin_amount,
                ]);

                $asset->increment('balance', $mining->refund_coin_amount);

                MiningRefund::create([
                    'user_id' => $mining->user_id,
                    'mining_id' => $mining->id,
                    'transfer_id' => $transfer->id,
                    'amount' => $mining->refund_coin_amount,
                ]);

                Log::channel('mining')->info('Mining principal successfully paid out', [
                    'user_id' => $mining->user_id,
                    'mining_id' => $mining->id,
                    'transfer_id' => $transfer->id,
                    'amount' => $mining->refund_coin_amount,
                    'timestamp' => now(),
                ]);

                $mining->update(['status' => 'completed']);

                DB::commit();

            } catch (\Throwable $e) {

                DB::rollBack();

                Log::channel('mining')->error('Failed to pay out mining principal', [
                    'user_id' => $mining->user_id,
                    'mining_id' => $mining->id,
                    'error' => $e->getMessage(),
                ]);

            }
        }
    }
}
