<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class LevelConditionPolicy extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'marketing_id',
        'node_amount',
        'max_depth',
        'referral_count',
        'condition',
    ];

    protected $casts = [
	    'node_amount' => 'decimal:9',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static $columnDescriptions = [
        'node_amount' => '노드 참여 수량',
        'max_depth' => '최대 적용 뎁스',
        'referral_count' => '추천인원 수',
        'condition' => '조건 조합 방식',
    ];

    public function getColumnComment($column)
    {
        return static::$columnDescriptions[$column];
    }
}
