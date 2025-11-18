<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class MiningPolicy extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'coin_id',
        'refund_coin_id',
        'reward_coin_id',
        'instant_rate',
        'split_rate',
        'exchange_rate',
        'node_amount',
        'node_limit',
        'reward_days',
        'reward_limit',
        'split_period',
        'waiting_period',
        'avatar_cost',
        'avatar_count',
        'avatar_target_amount',
        'benefit_rules',
    ];

    protected $casts = [
        'instant_rate' => 'decimal:9',
	    'split_rate' => 'decimal:9',
	    'exchange_rate' => 'decimal:9',
        'benefit_rules' => 'array',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = [
        'mining_locale_name',
        'mining_locale_memo',
    ];

    public function coin()
    {
        return $this->belongsTo(Coin::class, 'coin_id', 'id');
    }

    public function refundCoin()
    {
        return $this->belongsTo(Coin::class, 'refund_coin_id', 'id');
    }

    public function rewardCoin()
    {
        return $this->belongsTo(Coin::class, 'reward_coin_id', 'id');
    }

    public function translations()
    {
        return $this->hasMany(MiningPolicyTranslation::class, 'policy_id', 'id');
    }

    public function getMiningLocaleNameAttribute()
    {
        return optional($this->translationForLocale())->name;
    }

    public function getMiningLocaleMemoAttribute()
    {
        return optional($this->translationForLocale())->memo;
    }

    public function translationForLocale($locale = null)
    {
        $locale = $locale ?? Cookie::get('app_locale', 'en');

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return $this->translations->firstWhere('locale', $locale);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static $columnDescriptions = [
        'coin_id' => '입금 코인',
        'refund_coin_id' => '원금 코인',
        'reward_coin_id' => '수익 코인',
        'instant_rate' => '즉시 지급 비율',
        'split_rate' => '분할 지급 비율',
        'exchange_rate' => '환율',
        'node_amount' => '채굴값',
        'reward_days' => '채굴 가능 요일',
        'reward_limit' => '채굴 제한',
        'node_limit' => '최대 노드 수량',
        'split_period' => '분할기간',
        'waiting_period' => '대기기간',
        'avatar_cost' => '아바타 생성 비용',
        'avatar_count' => '아바타 생성 개수',
        'avatar_target_amount' => '아바타 생성 누적 금액',
        'benefit_rules' => '혜택 규칙',
    ];

    public function getColumnComment($column)
    {
        return static::$columnDescriptions[$column];
    }

    public function setDailyStat()
    {
        MiningDailyStat::updateOrCreate([
            'policy_id' => $this->id,
            'stat_date' => today(),
            'exchange_rate' => $this->exchange_rate,
            'node_amount' => $this->node_amount,
        ]);
    }
}
