<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class Marketing extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'image_urls',
        'benefit_rules',
        'is_required',
    ];

    protected $casts = [
        'image_urls' => 'array',
        'benefit_rules' => 'array',
    ];

    protected $appends = [
        'benefit_rules_text',
        'marketing_locale_name',
        'marketing_locale_memo',
    ];

    public function translations()
    {
        return $this->hasMany(MarketingTranslation::class, 'marketing_id', 'id');
    }

    public function policy()
    {
        return $this->hasMany(MiningPolicy::class, 'marketing_id', 'id');
    }

    public function getBenefitRulesTextAttribute()
    {
        $rules = $this->benefit_rules ?? [];
        $labels = [
            'referral_bonus'   => '추천보너스',
            'referral_matching'=> '추천매칭',
            'level_bonus'      => '레벨보너스',
            'level_matching'   => '레벨매칭',
        ];

        $result = [];

        foreach ($labels as $key => $label) {
            if (isset($rules[$key])) {
                $result[] = $label . ' ' . ($rules[$key] === 'y' ? '허용' : '허용안함');
            }
        }

        return implode(' / ', $result);
    }

    public function getMarketingLocaleNameAttribute()
    {
        return optional($this->translationForLocale())->name;
    }

    public function getMarketingLocaleMemoAttribute()
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
}
