<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'post_code',
        'address',
        'detail_address',
        'meta_uid',
        'is_valid',
        'is_frozen',
        'is_kyc_verified',
        'memo'
    ];

    protected $appends = [
        'referral_count',
        'is_referral',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getMarketingAmount()
    {
        $amount = ['required' => 0, 'other' => 0];

        $marketing = Marketing::where('is_required', 'y')->first();
        if (!$marketing) return $amount;

        $policy_ids = $marketing->policy->pluck('id')->toArray();
        if (empty($policy_ids)) return $amount;

        $amount['required'] = Mining::where('user_id', $this->user_id)
            ->whereIn('policy_id', $policy_ids)
            ->sum('coin_amount');

        $amount['other'] = Mining::where('user_id', $this->user_id)
            ->whereNotIn('policy_id', $policy_ids)
            ->sum('coin_amount');

        return $amount;
    }

    public function getHasMining($policy_id)
    {
        return Mining::where('user_id', $this->user_id)
            ->where('policy_id', $policy_id)
            ->where('status', 'pending')
            ->exists();
    }
}
