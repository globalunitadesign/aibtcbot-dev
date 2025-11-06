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
        'parent_id',
        'level',
        'grade_id',
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

    public function parent()
    {
        return $this->belongsTo(UserProfile::class, 'parent_id', 'user_id');
    }

    public function children()
    {
        return $this->hasMany(UserProfile::class, 'parent_id', 'user_id');
    }

    public function grade()
    {
        return $this->belongsTo(UserGrade::class, 'grade_id', 'id');
    }

    public function getReferralCountAttribute()
    {
        return $this->children()->where('is_valid', 'y')->count();
    }

    public function getIsReferralAttribute()
    {
        $is_valid = 'n';
        $min_valid = AssetPolicy::first()->min_valid;

        $max_amount_in_usdt = AssetTransfer::where('user_id', $this->user_id)
            ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
            ->whereIn('status', ['waiting', 'completed'])
            ->get()
            ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

        if ($max_amount_in_usdt >= $min_valid) {
            $is_valid = 'y';
        }

        return $is_valid;
    }

    public function getParentTree($max_level = 20)
    {
        $levels = [];
        $current = $this;

        for ($i = 1; $i <= $max_level; $i++) {
            $parent = $current->parent;

            if (!$parent) {
                break;
            }

            $levels[$i] = $parent;
            $current = $parent;
        }

        return $levels;
    }


    public function getChildrenTree($max_level = 20)
    {
        $levels = [];
        $current_level_users = collect([$this]);

        for ($i = 1; $i <= $max_level; $i++) {
            $next_level = $current_level_users
                ->flatMap(function ($user) {
                    return $user->children;
                });

            if ($next_level->isEmpty()) {
                break;
            }

            $levels[$i] = $next_level;
            $current_level_users = $next_level;
        }

        return $levels;
    }

    public function getSelfSales()
    {
        $self_sales = AssetTransfer::where('user_id', $this->user_id)
            ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
            ->where('status', 'completed')
            ->get()
            ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

        return $self_sales;
    }

    public function getGroupSales()
    {
        $childrens = $this->getChildrenTree(20);
        $group_sales = 0;

        foreach ($childrens as $level => $profiles) {
            foreach ($profiles as $profile) {
                $user = $profile->user;
                if(!$user) continue;

                $group_sales += AssetTransfer::where('user_id', $user->id)
                    ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
                    ->where('status', 'completed')
                    ->get()
                    ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());
            }
        }

        return $group_sales;
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

    public function referralBonus($mining)
    {

        if ($mining->getBenefitRule('referral_bonus') === 'n'){
            Log::channel('bonus')->warning('This marketing does not allow a referral bonus.', ['mining_id' => $mining->id, 'marketing_id' => $mining->policy->marketing_id]);
            return;
        }

        try {

            DB::beginTransaction();

            $parents = $this->getParentTree(20);

            foreach ($parents as $level => $parent_profile) {

                if ($parent_profile->is_valid === 'n') continue;

                if (!$parent_profile->getHasMining($mining->policy_id)) continue;

                $policy = ReferralPolicy::where('marketing_id', $mining->policy->marketing_id)
                    ->where('grade_id', $parent_profile->grade->id)
                    ->first();

                if (!$policy) continue;

                $rate_key = "level_{$level}_rate";

                $bonus = $mining->coin_amount * $policy->$rate_key / 100;

                if ($bonus <= 0) continue;

                $income = Income::where('user_id', $parent_profile->user_id)->where('coin_id', 1)->first();

                $transfer = IncomeTransfer::create([
                    'user_id'   => $parent_profile->user_id,
                    'income_id'  => $income->id,
                    'type' => 'referral_bonus',
                    'status' => 'completed',
                    'amount'    => $bonus,
                    'actual_amount' => $bonus,
                    'before_balance' => $income->balance,
                    'after_balance' => $income->balance + $bonus,
                ]);

                $referral_bonus = ReferralBonus::create([
                    'user_id'   => $parent_profile->user_id,
                    'referrer_id' => $this->user_id,
                    'mining_id'   => $mining->id,
                    'transfer_id'  => $transfer->id,
                    'bonus' => $bonus,
                ]);

                $income->increment('balance', $bonus);

                Log::channel('bonus')->info('Success referral bonus', [
                    'user_id' => $parent_profile->user_id,
                    'referrer_id' => $this->user_id,
                    'level' => $level,
                    'mining_id' => $mining->id,
                    'bonus_id' => $referral_bonus->id,
                    'transfer_id' => $transfer->id,
                    'bonus' => $bonus,
                    'before_balance' => $transfer->before_balance,
                    'after_balance' => $transfer->after_balance,
                ]);

                $this->referralMatching($referral_bonus);
            }

            DB::commit();

        }  catch (\Exception $e) {

            DB::rollBack();

            Log::channel('bonus')->error('Referral bonus transaction failed', [
                'mining_id' => $mining->id,
                'user_id' => $this->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function referralMatching($bonus)
    {
        if ($bonus->mining->getBenefitRule('referral_matching') === 'n'){
            Log::channel('bonus')->warning('This marketing does not allow a referral matching.', ['bonus_id' => $bonus->id, 'marketing_id' => $bonus->mining->policy->marketing_id]);
            return;
        }

        $user = $bonus->user->profile;
        $parents = $user->getParentTree(20);

        foreach ($parents as $level => $parent_profile) {

            if ($parent_profile->is_valid === 'n') continue;

            if (!$parent_profile->getHasMining($bonus->mining->policy_id)) continue;

            $policy = ReferralMatchingPolicy::where('marketing_id', $bonus->mining->policy->marketing_id)
                ->where('grade_id', $parent_profile->grade->id)
                ->first();

            if (!$policy) continue;

            $rate_key = "level_{$level}_rate";

            $matching = $bonus->transfer->amount * $policy->$rate_key / 100;

            if ($matching <= 0) continue;

            $income = Income::where('user_id', $parent_profile->user_id)
                ->where('coin_id', 1)
                ->first();

            $transfer = IncomeTransfer::create([
                'user_id'   => $parent_profile->user_id,
                'income_id'  => $income->id,
                'type' => 'referral_matching',
                'status' => 'completed',
                'amount'    => $matching,
                'actual_amount' => $matching,
                'before_balance' => $income->balance,
                'after_balance' => $income->balance + $matching,
            ]);

            $referral_matching = ReferralMatching::create([
                'user_id'   => $parent_profile->user_id,
                'referrer_id' => $user->user_id,
                'bonus_id'   => $bonus->id,
                'transfer_id'  => $transfer->id,
                'matching' => $matching,
            ]);

            $income->increment('balance', $matching);

            Log::channel('bonus')->info('Success referral matching', [
                'user_id' => $parent_profile->user_id,
                'referrer_id' => $user->user_id,
                'level' => $level,
                'bonus_id' => $bonus->id,
                'matching_id' => $referral_matching->id,
                'transfer_id' => $transfer->id,
                'matching' => $matching,
                'before_balance' => $transfer->before_balance,
                'after_balance' => $transfer->after_balance,
            ]);
        }
    }

    public function rankBonus()
    {
        $policies = RankPolicy::join('user_grades', 'rank_policies.grade_id', '=', 'user_grades.id')
            ->where('user_grades.level', '<=', $this->grade->level)
            ->select('rank_policies.*')
            ->get();

        foreach ($policies as $policy) {
            if (!$policy) {
                continue;
            }

            $bonus_given = RankBonus::where('user_id', $this->user_id)
                ->where('policy_id', $policy->id)
                ->exists();

            if ($bonus_given) {
                continue;
            }

            $direct_children = $this->getChildrenTree(1);
            $direct_count = isset($direct_children[1]) ? $direct_children[1]->count() : 0;

            $direct_min_level = (int) $policy->conditions['direct']['min_level'];
            $direct_required_count = (int) $policy->conditions['direct']['referral_count'];

            $direct_met_count = $direct_children[1]->filter(function ($child) use ($direct_min_level) {
                $level = $child->grade->level;
                return $level >= $direct_min_level;
            })->count();

            if ($direct_met_count < $direct_required_count) {
                Log::channel('bonus')->info("Rank bonus not paid - User ID: {$this->user_id}, Reason: Insufficient qualified directs for required levels.");
                continue;
            }

            $all_children = collect($this->getChildrenTree(20))->flatten(1);
            $all_count = $all_children->count();

            $all_min_level = (int) $policy->conditions['all']['min_level'];
            $all_required_count = (int) $policy->conditions['all']['referral_count'];

            $all_met_count = $all_children->filter(function ($child) use ($all_min_level) {
                $level = $child->grade->level;
                return $level >= $all_min_level;
            })->count();

            if ($all_met_count < $all_required_count) {
                Log::channel('bonus')->info("Rank bonus not paid - User ID: {$this->user_id}, Reason: Insufficient qualified downline members.");
                continue;
            }

            DB::beginTransaction();

            try {
                $bonus = $policy->bonus;

                $self_sales = $this->getSelfSales();
                $group_sales = $this->getGroupSales();

                $income = Income::where('user_id', $this->user_id)->where('coin_id', 1)->first();

                $transfer = IncomeTransfer::create([
                    'user_id'        => $this->user_id,
                    'income_id'      => $income->id,
                    'type'           => 'rank_bonus',
                    'status'         => 'completed',
                    'amount'         => $bonus,
                    'actual_amount'  => $bonus,
                    'before_balance' => $income->balance,
                    'after_balance'  => $income->balance + $bonus,
                ]);

                $rank_bonus = RankBonus::create([
                    'user_id'        => $this->user_id,
                    'policy_id'      => $policy->id,
                    'transfer_id'    => $transfer->id,
                    'self_sales'     => $self_sales,
                    'group_sales'    => $group_sales,
                    'direct_count' => $direct_count,
                    'referral_count' => $all_count,
                    'bonus'          => $bonus,
                ]);

                $income->increment('balance', $bonus);

                DB::commit();

                Log::channel('bonus')->info('Success rank bonus', [
                    'user_id'     => $this->user_id,
                    'bonus'       => $bonus,
                    'transfer_id' => $transfer->id,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                Log::channel('bonus')->error('Failed rank bonus', [
                    'user_id' => $this->user_id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }

    public function levelBonus($profit)
    {

        $mining = $profit->reward->mining;

        if (!$mining) {
            Log::channel('bonus')->warning('Missing mining for profit', ['profit_id' => $profit->id]);
            return;
        }

        if ( $mining->getBenefitRule('level_bonus') === 'n' ){
            Log::channel('bonus')->warning('This marketing does not allow a level bonus.', ['profit_id' => $profit->id, 'marketing_id' => $mining->policy->marketing_id]);
            return;
        }

        try {

            DB::beginTransaction();

            $user = $profit->user->profile;
            $parents = $user->getParentTree(20);

            $marketing_id = $mining->policy->marketing_id;

            foreach ($parents as $level => $parent_profile) {

                if ($parent_profile->is_valid === 'n') continue;

                if (!$parent_profile->getHasMining($mining->policy_id)) continue;

                $condition = $parent_profile->checkLevelCondition($marketing_id);

                if (!$condition) {
                    Log::channel('bonus')->warning('No Level Condition matched for level bonus', [
                        'profit_id' => $profit->id,
                        'user_id'   => $parent_profile->user_id,
                        'level'     => $level,
                    ]);
                    continue;
                }

                $max_depth = $condition->max_depth;

                if ($max_depth < $level) {
                    Log::channel('bonus')->warning('Not Condition for level bonus', [
                        'profit_id' => $profit->id,
                        'parent_id' => $parent_profile->id,
                        'referrer_id' => $profit->user->id,
                        'parent_level' => $level,
                        'max_depth' => $max_depth,
                    ]);

                    continue;
                }

                $policy = LevelPolicy::where('marketing_id', $marketing_id)
                    ->where('depth', $level)
                    ->first();

                $amount = $profit->reward->reward;

                $base_bonus = $amount * $policy->bonus / 100;

                if ($base_bonus <= 0) continue;

                $payout_rate = $profit->reward_rate;
                $split_days = $profit->type === 'daily' ? $mining->split_period : 1;

                $bonus = $base_bonus * $payout_rate / 100 / $split_days;

                $income = $mining->income;

                $transfer = IncomeTransfer::create([
                    'user_id' => $parent_profile->user_id,
                    'income_id' => $income->id,
                    'type' => 'level_bonus',
                    'status' => 'completed',
                    'amount' => $bonus,
                    'actual_amount' => $bonus,
                    'before_balance' => $income->balance,
                    'after_balance' => $income->balance + $bonus,
                ]);

                $level_bonus = LevelBonus::create([
                    'user_id' => $parent_profile->user_id,
                    'referrer_id' => $this->user_id,
                    'transfer_id' => $transfer->id,
                    'profit_id' => $profit->id,
                    'bonus' => $bonus,
                ]);

                $income->increment('balance', $bonus);

                Log::channel('bonus')->info('Success level bonus', [
                    'user_id' => $parent_profile->user_id,
                    'referrer_id' => $this->user_id,
                    'level' => $level,
                    'max_depth' => $max_depth,
                    'profit_id' => $profit->id,
                    'bonus_id' => $level_bonus->id,
                    'transfer_id' => $transfer->id,
                    'bonus' => $bonus,
                    'before_balance' => $transfer->before_balance,
                    'after_balance' => $transfer->after_balance,
                ]);

                $this->levelMatching($level_bonus);
            }

            DB::commit();

        }  catch (\Exception $e) {

            DB::rollBack();

            Log::channel('bonus')->error('Level bonus transaction failed', [
                'profit_id' => $profit->id,
                'user_id' => $this->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function levelMatching($bonus)
    {
        $mining = $bonus->profit->reward->mining;

        if ( $mining->getBenefitRule('level_matching') === 'n' ){
            Log::channel('bonus')->warning('This marketing does not allow a level matching.', ['bonus_id' => $bonus->id, 'marketing_id' => $mining->policy->marketing_id]);
            return;
        }

        if (!$mining) {
            Log::channel('bonus')->warning('Missing mining for bonus', ['bonus_id' => $bonus->id]);
            return;
        }

        $user = $bonus->user->profile;
        $parents = $user->getParentTree(20);

        $marketing_id = $mining->policy->marketing_id;

        foreach ($parents as $level => $parent_profile) {

            if ($parent_profile->is_valid === 'n') continue;

            if (!$parent_profile->getHasMining($mining->policy_id)) continue;

            $condition = $parent_profile->checkLevelCondition($marketing_id);

            if (!$condition) {
                Log::channel('bonus')->warning('No Level Condition matched for level matching', [
                    'bonus_id' => $bonus->id,
                    'user_id'   => $parent_profile->user_id,
                    'level'     => $level,
                ]);
                continue;
            }

            $max_depth = $condition->max_depth;

            if ($max_depth < $level) {
                Log::channel('bonus')->warning('Not Condition for level matching', [
                    'bonus_id' => $bonus->id,
                    'parent_id' => $parent_profile->id,
                    'parent_level' => $level,
                    'max_depth' => $max_depth,
                ]);

                continue;
            }

            $policy = LevelPolicy::where('marketing_id', $marketing_id)
                ->where('depth', $level)
                ->first();

            $matching = $bonus->bonus * $policy->matching / 100;

            if ($matching <= 0) continue;

            $income = $mining->income;

            $transfer = IncomeTransfer::create([
                'user_id'   => $parent_profile->user_id,
                'income_id'  => $income->id,
                'type' => 'level_matching',
                'status' => 'completed',
                'amount'    => $matching,
                'actual_amount' => $matching,
                'before_balance' => $income->balance,
                'after_balance' => $income->balance + $matching,
            ]);

            $level_matching = LevelMatching::create([
                'user_id'   => $parent_profile->user_id,
                'referrer_id' => $user->user_id,
                'bonus_id'   => $bonus->id,
                'transfer_id'  => $transfer->id,
                'matching' => $matching,
            ]);

            $income->increment('balance', $matching);

            Log::channel('bonus')->info('Success level matching', [
                'user_id' => $parent_profile->user_id,
                'referrer_id' => $user->user_id,
                'level' => $level,
                'max_depth' => $max_depth,
                'bonus_id' => $bonus->id,
                'matching_id' => $level_matching->id,
                'transfer_id' => $transfer->id,
                'matching' => $matching,
                'before_balance' => $transfer->before_balance,
                'after_balance' => $transfer->after_balance,
            ]);
        }
    }


    public function checkUserValidity()
    {
        if ($this->is_valid === 'y') return;

        $asset_policy = AssetPolicy::first();

        $max_amount_in_usdt = AssetTransfer::where('user_id', $this->user_id)
            ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
            ->where('status', 'completed')
            ->get()
            ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

        if ($asset_policy && $asset_policy->min_valid <= $max_amount_in_usdt) {
            $this->update(['is_valid' => 'y']);
            Log::channel('user')->info('Success to change is_valid', ['user_id' => $this->user_id]);
        } else {
            Log::channel('user')->info('Failed to change is_valid', ['user_id' => $this->user_id, 'max_amount' => $max_amount_in_usdt]);
        }
    }

    public function checkUserGrade()
    {
        $this->evaluateUserGrade();

        $parent_tree = $this->getParentTree(20);

        foreach ($parent_tree as $parent_profile) {
            if ($parent_profile) {
                $parent_profile->evaluateUserGrade();
            }
        }
    }

    public function evaluateUserGrade()
    {

        $self_sales = AssetTransfer::where('user_id', $this->user_id)
            ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
            ->where('status', 'completed')
            ->get()
            ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

        $children_tree = $this->getChildrenTree(20);
        $group_sales = 0;
        foreach ($children_tree as $profiles) {
            foreach ($profiles as $child_profile) {
                if ($child_profile->user) {

                    $group_sales += AssetTransfer::where('user_id', $child_profile->user_id)
                        ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
                        ->where('status', 'completed')
                        ->get()
                        ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());
                }
            }
        }

        $this->checkLevelUp($this->grade->level, $this->referral_count, $self_sales, $group_sales);
    }


    public function checkLevelCondition($marketing_id)
    {
        $level_conditions = LevelConditionPolicy::where('marketing_id', $marketing_id)
            ->orderBy('node_amount', 'desc')
            ->get();

        $user_referral_count = $this->referral_count;
        $total_node_amount = Mining::where('user_id', $this->user_id)->sum('node_amount');

        foreach ($level_conditions as $level_condition) {
            $node_check = $total_node_amount >= $level_condition->node_amount;
            $referral_check = $level_condition->condition === 'and'
                ? $user_referral_count >= $level_condition->referral_count && $node_check
                : $user_referral_count >= $level_condition->referral_count || $node_check;

            if ($referral_check) {
                return $level_condition;
            }
        }
        return null;
    }

    private function checkLevelUp($current_level, $referral_count, $self_sales, $group_sales)
    {

        $next_level = $current_level + 1;
        $next_grade = UserGrade::where('level', $next_level)->first();
        $next_policy = GradePolicy::where('grade_id', $next_grade->id)->first();

        if (!$next_policy) {
            return;
        }

        if (
            $referral_count >= $next_policy->referral_count &&
            $self_sales >= $next_policy->self_sales &&
            $group_sales >= $next_policy->group_sales
        ) {
            $result = UserProfile::where('id', $this->id)->update([
                'grade_id' => $next_grade->id
            ]);

            if (!$result) {
                throw new \Exception("Failed to update grade_id for user_id {$this->user_id}");
            }

            Log::channel('user')->info("User ID {$this->user_id} level up: {$current_level} → {$next_level}, self_sales : {$self_sales}, group_sales : {$group_sales}");

            $this->checkLevelUp($next_level, $referral_count, $self_sales, $group_sales);
        }

        return;
    }
}
