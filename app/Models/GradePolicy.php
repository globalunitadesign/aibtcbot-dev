<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradePolicy extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'grade_id',
        'referral_count',
        'self_sales',
        'group_sales',
    ];

    protected $casts = [
        'self_sales' => 'decimal:9',
        'group_sales' => 'decimal:9',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static $columnDescriptions = [
        'referral_count' => '추천 인원',
        'self_sales' => '개인 매출',
        'group_sales' => '그룹 매출',
    ];

    public function grade()
    {
        return $this->belongsTo(MemberGrade::class, 'grade_id', 'id');
    }


    public function getColumnComment($column)
    {
        return static::$columnDescriptions[$column];
    }
}
