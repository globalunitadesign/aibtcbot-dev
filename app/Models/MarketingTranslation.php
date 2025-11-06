<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingTranslation extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'marketing_id',
        'locale',
        'name',
        'memo',
    ];

    public function policy()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'id');
    }
}
