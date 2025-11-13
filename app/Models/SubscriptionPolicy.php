<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPolicy extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'grade_id',
        'level_1_rate',
        'level_2_rate',
        'level_3_rate',
        'level_4_rate',
        'level_5_rate',
        'level_6_rate',
        'level_7_rate',
        'level_8_rate',
        'level_9_rate',
        'level_10_rate',
        'level_11_rate',
        'level_12_rate',
        'level_13_rate',
        'level_14_rate',
        'level_15_rate',
        'level_16_rate',
        'level_17_rate',
        'level_18_rate',
        'level_19_rate',
        'level_20_rate',
        'level_21_rate',
    ];

    protected $casts = [
        'level_1_rate' => 'decimal:9',
        'level_2_rate' => 'decimal:9',
        'level_3_rate' => 'decimal:9',
        'level_4_rate' => 'decimal:9',
        'level_5_rate' => 'decimal:9',
        'level_6_rate' => 'decimal:9',
        'level_7_rate' => 'decimal:9',
        'level_8_rate' => 'decimal:9',
        'level_9_rate' => 'decimal:9',
        'level_10_rate' => 'decimal:9',
        'level_11_rate' => 'decimal:9',
        'level_12_rate' => 'decimal:9',
        'level_13_rate' => 'decimal:9',
        'level_14_rate' => 'decimal:9',
        'level_15_rate' => 'decimal:9',
        'level_16_rate' => 'decimal:9',
        'level_17_rate' => 'decimal:9',
        'level_18_rate' => 'decimal:9',
        'level_19_rate' => 'decimal:9',
        'level_20_rate' => 'decimal:9',
        'level_21_rate' => 'decimal:9',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static $columnDescriptions = [
        'level_1_rate' => '1레벨',
        'level_2_rate' => '2레벨',
        'level_3_rate' => '3레벨',
        'level_4_rate' => '4레벨',
        'level_5_rate' => '5레벨',
        'level_6_rate' => '6레벨',
        'level_7_rate' => '7레벨',
        'level_8_rate' => '8레벨',
        'level_9_rate' => '9레벨',
        'level_10_rate' => '10레벨',
        'level_11_rate' => '11레벨',
        'level_12_rate' => '12레벨',
        'level_13_rate' => '13레벨',
        'level_14_rate' => '14레벨',
        'level_15_rate' => '15레벨',
        'level_16_rate' => '16레벨',
        'level_17_rate' => '17레벨',
        'level_18_rate' => '18레벨',
        'level_19_rate' => '19레벨',
        'level_20_rate' => '20레벨',
        'level_21_rate' => '21레벨',
    ];

    public function grade()
    {
        return $this->belongsTo(MembereGrade::class, 'grade_id', 'id');
    }


    public function getColumnComment($column)
    {
        return static::$columnDescriptions[$column];
    }
}
