<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class LevelPolicy extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'marketing_id',
        'depth',
        'bonus',
        'matching',
    ];

    protected $casts = [
	    'bonus' => 'decimal:9',
	    'matching' => 'decimal:9',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static $columnDescriptions = [
        'depth' => '뎁스',
        'bonus' => '보너스',
        'matching' => '매칭',
    ];

    public function getColumnComment($column)
    {
        return static::$columnDescriptions[$column];
    }
}
