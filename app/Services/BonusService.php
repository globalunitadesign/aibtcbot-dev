<?php

namespace App\Services;

use App\Models\Income;
use App\Models\IncomeTransfer;
use App\Models\LevelBonus;
use App\Models\LevelMatching;
use App\Models\LevelPolicy;
use App\Models\Member;
use App\Models\RankBonus;
use App\Models\RankPolicy;
use App\Models\ReferralBonus;
use App\Models\ReferralMatching;
use App\Models\ReferralMatchingPolicy;
use App\Models\ReferralPolicy;
use App\Models\User;
use App\Models\Avatar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BonusService
{
    public function referralBonus($mining)
    {

        if ($mining->getBenefitRule('referral_bonus') === 'n'){
            Log::channel('bonus')->warning('This marketing does not allow a referral bonus.', ['mining_id' => $mining->id, 'marketing_id' => $mining->policy->marketing_id]);
            return;
        }

        $member = $mining->user->member;

        try {

            DB::beginTransaction();

            $parents = $member->getParentTree(20);

            foreach ($parents as $level => $parent) {

                if ($parent->is_valid === 'n') continue;

                if (!$parent->getHasMining($mining->policy_id)) continue;

                $policy = ReferralPolicy::where('marketing_id', $mining->policy->marketing_id)
                    ->where('grade_id', $parent->grade->id)
                    ->first();

                if (!$policy) continue;

                $rate_key = "level_{$level}_rate";

                $bonus = $mining->coin_amount * $policy->$rate_key / 100;

                if ($bonus <= 0) continue;

                $income = Income::where('member_id', $parent->id)->where('coin_id', 1)->first();

                $transfer = IncomeTransfer::create([
                    'member_id'   => $parent->id,
                    'income_id'  => $income->id,
                    'type' => 'referral_bonus',
                    'status' => 'completed',
                    'amount'    => $bonus,
                    'actual_amount' => $bonus,
                    'before_balance' => $income->balance,
                    'after_balance' => $income->balance + $bonus,
                ]);

                $referral_bonus = ReferralBonus::create([
                    'member_id'   => $parent->id,
                    'referrer_id' => $member->id,
                    'mining_id'   => $mining->id,
                    'transfer_id'  => $transfer->id,
                    'bonus' => $bonus,
                ]);

                $income->increment('balance', $bonus);

                Log::channel('bonus')->info('Success referral bonus', [
                    'member_id' => $parent->id,
                    'referrer_id' => $member->id,
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
                'member_id' => $member->id,
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

        $member = $bonus->member;
        $parents = $member->getParentTree(20);

        foreach ($parents as $level => $parent) {

            if (!$parent->getHasMining($bonus->mining->policy_id)) continue;

            $policy = ReferralMatchingPolicy::where('marketing_id', $bonus->mining->policy->marketing_id)
                ->where('grade_id', $parent->grade->id)
                ->first();

            if (!$policy) continue;

            $rate_key = "level_{$level}_rate";

            $matching = $bonus->transfer->amount * $policy->$rate_key / 100;

            if ($matching <= 0) continue;

            $income = Income::where('member_id', $parent->id)
                ->where('coin_id', 1)
                ->first();

            $transfer = IncomeTransfer::create([
                'member_id'   => $parent->id,
                'income_id'  => $income->id,
                'type' => 'referral_matching',
                'status' => 'completed',
                'amount'    => $matching,
                'actual_amount' => $matching,
                'before_balance' => $income->balance,
                'after_balance' => $income->balance + $matching,
            ]);

            $referral_matching = ReferralMatching::create([
                'member_id'   => $parent->id,
                'referrer_id' => $member->id,
                'bonus_id'   => $bonus->id,
                'transfer_id'  => $transfer->id,
                'matching' => $matching,
            ]);

            $income->increment('balance', $matching);

            Log::channel('bonus')->info('Success referral matching', [
                'member_id' => $parent->id,
                'referrer_id' => $member->id,
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

    public function rankBonus($member)
    {
        $policies = RankPolicy::join('member_grades', 'rank_policies.grade_id', '=', 'member_grades.id')
            ->where('member_grades.level', '<=', $member->grade->level)
            ->select('rank_policies.*')
            ->get();

        foreach ($policies as $policy) {
            if (!$policy) {
                continue;
            }

            $bonus_given = RankBonus::where('member_id', $member->id)
                ->where('policy_id', $policy->id)
                ->exists();

            if ($bonus_given) {
                continue;
            }

            $direct_children = $member->getChildrenTree(1);
            $direct_count = isset($direct_children[1]) ? $direct_children[1]->count() : 0;

            $direct_min_level = (int) $policy->conditions['direct']['min_level'];
            $direct_required_count = (int) $policy->conditions['direct']['referral_count'];

            $direct_met_count = $direct_children[1]->filter(function ($child) use ($direct_min_level) {
                $level = $child->grade->level;
                return $level >= $direct_min_level;
            })->count();

            if ($direct_met_count < $direct_required_count) {
                Log::channel('bonus')->info("Rank bonus not paid - Member ID: {$member->id}, Reason: Insufficient qualified directs for required levels.");
                continue;
            }

            $all_children = collect($member->getChildrenTree(20))->flatten(1);
            $all_count = $all_children->count();

            $all_min_level = (int) $policy->conditions['all']['min_level'];
            $all_required_count = (int) $policy->conditions['all']['referral_count'];

            $all_met_count = $all_children->filter(function ($child) use ($all_min_level) {
                $level = $child->grade->level;
                return $level >= $all_min_level;
            })->count();

            if ($all_met_count < $all_required_count) {
                Log::channel('bonus')->info("Rank bonus not paid - Member ID: {$member->id}, Reason: Insufficient qualified downline members.");
                continue;
            }

            DB::beginTransaction();

            try {
                $bonus = $policy->bonus;

                $self_sales = $member->getSelfSales();
                $group_sales = $member->getGroupSales();

                $income = Income::where('member_id', $member->id)->where('coin_id', 1)->first();

                $transfer = IncomeTransfer::create([
                    'member_id'      => $member->id,
                    'income_id'      => $income->id,
                    'type'           => 'rank_bonus',
                    'status'         => 'completed',
                    'amount'         => $bonus,
                    'actual_amount'  => $bonus,
                    'before_balance' => $income->balance,
                    'after_balance'  => $income->balance + $bonus,
                ]);

                $rank_bonus = RankBonus::create([
                    'member_id'      => $member->id,
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
                    'member_id'   => $member->id,
                    'bonus'       => $bonus,
                    'transfer_id' => $transfer->id,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                Log::channel('bonus')->error('Failed rank bonus', [
                    'member_id' => $member->id,
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

        if ($mining->getBenefitRule('level_bonus') === 'n' ){
            Log::channel('bonus')->warning('This marketing does not allow a level bonus.', ['profit_id' => $profit->id, 'marketing_id' => $mining->policy->marketing_id]);
            return;
        }

        try {

            DB::beginTransaction();

            $member = $profit->user->member;
            $parents = $member->getParentTree(20);

            $marketing_id = $mining->policy->marketing_id;

            foreach ($parents as $level => $parent) {

                if ($parent->is_valid === 'n') continue;

                if (!$parent->getHasMining($mining->policy_id)) continue;

                $condition = $parent->checkLevelCondition($marketing_id);

                if (!$condition) {
                    Log::channel('bonus')->warning('No Level Condition matched for level bonus', [
                        'profit_id' => $profit->id,
                        'member_id'   => $parent->id,
                        'level'     => $level,
                    ]);
                    continue;
                }

                $max_depth = $condition->max_depth;

                if ($max_depth < $level) {
                    Log::channel('bonus')->warning('Not Condition for level bonus', [
                        'profit_id' => $profit->id,
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
                    'member_id' => $parent->id,
                    'income_id' => $income->id,
                    'type' => 'level_bonus',
                    'status' => 'completed',
                    'amount' => $bonus,
                    'actual_amount' => $bonus,
                    'before_balance' => $income->balance,
                    'after_balance' => $income->balance + $bonus,
                ]);

                $level_bonus = LevelBonus::create([
                    'member_id' => $parent->id,
                    'referrer_id' => $member->id,
                    'transfer_id' => $transfer->id,
                    'profit_id' => $profit->id,
                    'bonus' => $bonus,
                ]);

                $income->increment('balance', $bonus);

                Log::channel('bonus')->info('Success level bonus', [
                    'member_id' => $parent->id,
                    'referrer_id' => $member->id,
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
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function levelMatching($bonus)
    {
        $mining = $bonus->profit->reward->mining;

        if ($mining->getBenefitRule('level_matching') === 'n' ){
            Log::channel('bonus')->warning('This marketing does not allow a level matching.', ['bonus_id' => $bonus->id, 'marketing_id' => $mining->policy->marketing_id]);
            return;
        }

        if (!$mining) {
            Log::channel('bonus')->warning('Missing mining for bonus', ['bonus_id' => $bonus->id]);
            return;
        }

        $member = $bonus->user->member;
        $parents = $member->getParentTree(20);

        $marketing_id = $mining->policy->marketing_id;

        foreach ($parents as $level => $parent) {

            if ($parent->is_valid === 'n') continue;

            if (!$parent->getHasMining($mining->policy_id)) continue;

            $condition = $parent->checkLevelCondition($marketing_id);

            if (!$condition) {
                Log::channel('bonus')->warning('No Level Condition matched for level matching', [
                    'bonus_id' => $bonus->id,
                    'member_id'   => $parent->id,
                    'level'     => $level,
                ]);
                continue;
            }

            $max_depth = $condition->max_depth;

            if ($max_depth < $level) {
                Log::channel('bonus')->warning('Not Condition for level matching', [
                    'bonus_id' => $bonus->id,
                    'parent_id' => $parent->id,
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
                'member_id'   => $parent->id,
                'income_id'  => $income->id,
                'type' => 'level_matching',
                'status' => 'completed',
                'amount'    => $matching,
                'actual_amount' => $matching,
                'before_balance' => $income->balance,
                'after_balance' => $income->balance + $matching,
            ]);

            $level_matching = LevelMatching::create([
                'member_id'   => $parent->id,
                'referrer_id' => $member->id,
                'bonus_id'   => $bonus->id,
                'transfer_id'  => $transfer->id,
                'matching' => $matching,
            ]);

            $income->increment('balance', $matching);

            Log::channel('bonus')->info('Success level matching', [
                'member_id' => $parent->id,
                'referrer_id' => $member->id,
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
}
