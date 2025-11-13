<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

class Asset extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'member_id',
        'coin_id',
        'balance',
    ];

    protected $casts = [
    'balance' => 'decimal:9',
    ];

    protected $appends = [
        'encrypted_id',
        'fee_rate',
        'tax_rate',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class, 'coin_id', 'id');
    }

    public function transfers()
    {
        return $this->hasMany(AssetTransfer::class, 'asset_id', 'id');
    }

    public function profits()
    {
        return $this->hasMany(TradingProfit::class, 'asset_id', 'id');
    }

    public function getEncryptedIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    public function getFeeRateAttribute()
    {
        $policy = AssetPolicy::first();

        if (!$policy) {
            return 0;
        }

        $first_deposit = $this->transfers()
            ->whereIn('type', ['deposit', 'manual_deposit'])
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$first_deposit) {
            return 0;
        }

        $days = now()->diffInDays($first_deposit->created_at);

        return ($days >= $policy->period) ? $policy->fee_rate : 0;
    }

    public function getTaxRateAttribute()
    {

        $policy = AssetPolicy::first();

        if (!$policy) {
            return 0;
        }

        return $policy->tax_rate;
    }


    public function getAssetInfo()
    {
        $yesterday = Carbon::yesterday();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $member = Member::find($this->member->id);

        $direct_count = 0;
        $children_tree = $member->getChildrenTree(21);

        if ($children_tree) {
            $direct_count = count($children_tree[1]);
        }

        $referral_count = 0;
        $group_sales = 0;
        $group_sales_expected = 0;

        foreach ($children_tree as $level => $children) {
            foreach ($children as $child) {
                $referral_count++;

                $group_sales += AssetTransfer::where('member_id', $child->id)
                    ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
                    ->where('status', 'completed')
                    ->get()
                    ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

                $group_sales_expected += AssetTransfer::where('member_id', $child->id)
                    ->whereIn('type', ['deposit', 'internal'])
                    ->where('status', 'waiting')
                    ->get()
                    ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());
            }
        }

        $income = Income::where('member_id', $this->member->id)
                ->where('coin_id', $this->coin_id)
                ->first();

        $deposits = IncomeTransfer::where('income_id', $income->id)
            ->where('type', 'deposit')
            ->get();

        $withdrawal = IncomeTransfer::where('income_id', $income->id)
            ->where('type', 'withdrawal')
            ->get();

        $profits =  IncomeTransfer::where('income_id', $income->id)
            ->where('type', 'trading_profit')
            ->get();

        $profit_today = $profits->where('created_at', '>=', $today)->sum('amount');
        $profit_yesterday = $profits->where('created_at', '>=', $yesterday)->where('created_at', '<', $today)->sum('amount');
        $profit_total = $profits->sum('amount');


        $bonuses =  IncomeTransfer::where('income_id', $income->id)
            ->where('type', 'subscription_bonus')
            ->get();

        $bonus_today = $bonuses->where('created_at', '>=', $today)->sum('amount');
        $bonus_yesterday = $bonuses->where('created_at', '>=', $yesterday)->where('created_at', '<', $today)->sum('amount');
        $bonus_total = $bonuses->sum('amount');

        return [
            'encrypted_id' => $this->encrypted_id,
            'coin_name' => $this->coin->name,
            'balance' => $this->balance,
            'grade' => $member->grade->name,
            'profit' => [
                'today' => $profit_today,
                'yesterday' => $profit_yesterday,
                'total' => $profit_total,
            ],
            'bonus' => [
                'today' => $bonus_today,
                'yesterday' => $bonus_yesterday,
                'total' => $bonus_total,
            ],
            'direct_count' => $direct_count,
            'referral_count' => $referral_count,
            'group_sales' => $group_sales,
            'group_sales_expected'  => $group_sales_expected,
        ];
    }
}
