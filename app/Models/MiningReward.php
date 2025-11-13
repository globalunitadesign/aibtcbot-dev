<?php

namespace App\Models;

use App\Services\BonusService;
use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MiningReward extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'user_id',
        'mining_id',
        'reward',
        'reward_date',
        'start_date',
        'end_date',
        'profit_count',
    ];

    protected $casts = [
        'reward' => 'decimal:9',
    ];

    public function getStatusTextAttribute()
    {
        return '지급 완료';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function mining()
    {
        return $this->belongsTo(Mining::class, 'mining_id', 'id');
    }

    public function profits()
    {
        return $this->hasMany(MiningProfit::class, 'reward_id', 'id');
    }

    public function hasInstantProfit()
    {
        return $this->profits()
            ->where('type', 'instant')
            ->exists();
    }

    public function getInstantProfit()
    {
        $base_reward   = $this->reward / 2;
        $instant_rate  = $this->mining->policy->instant_rate / 100;
        $instant_profit = $base_reward * $instant_rate;

        return $instant_profit;
    }

    public function getSplitProfit()
    {
        $base_reward  = $this->reward / 2;
        $split_rate   = $this->mining->policy->split_rate / 100;

        $period = $this->mining->policy->split_period;

        $split_amount = $base_reward * $split_rate;
        $split_profit = $split_amount / $period;

        return $split_profit;
    }

    public function getMiningProfit()
    {
        if ($this->hasInstantProfit()) {
            $type = 'daily';
            $rate = $this->mining->policy->split_rate;
            $profit = $this->getSplitProfit();
        } else {
            $type = 'instant';
            $rate = $this->mining->policy->instant_rate;
            $profit = $this->getInstantProfit();
        }

        return [
            'type'   => $type,
            'rate'   => $rate,
            'profit' => $profit,
        ];
    }

    public static function distributeProfit()
    {
        Log::channel('mining')->info('distribute Daily Mining Profit');

        $today = now();
        $rewards = self::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        foreach ($rewards as $reward) {

            DB::beginTransaction();

            try {

                $mining_profit = $reward->getMiningProfit();

                $type = $mining_profit['type'];
                $rate = $mining_profit['rate'];
                $profit = $mining_profit['profit'];

                if ($profit <= 0) continue;

                $income = $reward->mining->income;

                $transfer = IncomeTransfer::create([
                    'user_id' => $reward->user_id,
                    'income_id' => $income->id,
                    'type' => 'mining_profit',
                    'status' => 'completed',
                    'amount' => $profit,
                    'actual_amount' => $profit,
                    'before_balance' => $income->balance,
                    'after_balance' => $income->balance + $profit,
                ]);

                $income->increment('balance', $profit);

                $mining_profit = MiningProfit::create([
                    'user_id' => $reward->user_id,
                    'reward_id' => $reward->id,
                    'transfer_id' => $transfer->id,
                    'type' => $type,
                    'profit' => $profit,
                    'node_amount' => $reward->mining->policy->node_amount,
                    'reward_rate' => $rate,
                ]);

                if ($type === 'daily') $reward->increment('profit_count');

                Log::channel('mining')->info('daily mining distributed', [
                    'user_id' => $reward->user_id,
                    'reward_id' => $reward->id,
                    'transfer_id' => $transfer->id,
                    'type' => $type,
                    'profit' => $profit,
                    'timestamp' => now(),
                ]);

                $service = new BonusService();
                $service->levelBonus($mining_profit);

                DB::commit();

            } catch (\Throwable $e) {

                DB::rollBack();

                Log::channel('mining')->error('Failed to distribute daily mining', [
                    'user_id' => $reward->user_id,
                    'reward_id' => $reward->id,
                    'error' => $e->getMessage(),
                ]);

            }
        }
    }
}
