<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Member extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'user_id',
        'avatar_id',
        'parent_id',
        'position',
        'grade_id',
        'referrer_id',
        'level',
        'is_valid',
    ];

    protected $appends = [
        'referral_count',
        'is_referral',
    ];

    public function parent()
    {
        return $this->belongsTo(Member::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Member::class, 'parent_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function avatar()
    {
        return $this->belongsTo(Avatar::class, 'avatar_id', 'id');
    }

    public function grade()
    {
        return $this->belongsTo(MemberGrade::class, 'grade_id', 'id');
    }

    public function referrer()
    {
        return $this->belongsTo(Member::class, 'referrer_id', 'id');
    }

    public function referrals()
    {
        return $this->hasMany(Member::class, 'referrer_id', 'id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'member_id', 'id');
    }

    public function assetTransfers()
    {
        return $this->hasMany(AssetTransfer::class, 'member_id', 'id');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class, 'member_id', 'id');
    }

    public function incomeTransfers()
    {
        return $this->hasMany(IncomeTransfer::class, 'member_id', 'id');
    }

    public function getReferralCountAttribute()
    {
        return $this->referrals()->where('is_valid', 'y')->count();
    }

    public function getIsReferralAttribute()
    {
        $is_valid = 'n';
        $min_valid = AssetPolicy::first()->min_valid;

        $max_amount_in_usdt = AssetTransfer::where('member_id', $this->id)
            ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
            ->whereIn('status', ['waiting', 'completed'])
            ->get()
            ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

        if ($max_amount_in_usdt >= $min_valid) {
            $is_valid = 'y';
        }

        return $is_valid;
    }

    public function getHasMining()
    {
        return Mining::where('user_id', $this->user_id)
            ->exists();
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
        $current_level_members = collect([$this]);

        for ($i = 1; $i <= $max_level; $i++) {
            $next_level = $current_level_members
                ->flatMap(function ($member) {
                    return $member->children;
                });

            if ($next_level->isEmpty()) {
                break;
            }

            $levels[$i] = $next_level;
            $current_level_members = $next_level;
        }

        return $levels;
    }

    public function getSelfSales()
    {
        $self_sales = AssetTransfer::where('member_id', $this->id)
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

        foreach ($childrens as $level => $members) {
            foreach ($members as $member) {
                if(!$member) continue;

                $group_sales += AssetTransfer::where('member_id', $member->id)
                    ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
                    ->where('status', 'completed')
                    ->get()
                    ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());
            }
        }

        return $group_sales;
    }



    public function checkMemberValidity()
    {
        if ($this->is_valid === 'y') return;

        $asset_policy = AssetPolicy::first();

        $max_amount_in_usdt = AssetTransfer::where('member_id', $this->id)
            ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
            ->where('status', 'completed')
            ->get()
            ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

        if ($asset_policy && $asset_policy->min_valid <= $max_amount_in_usdt) {
            $this->update(['is_valid' => 'y']);
            Log::channel('user')->info('Success to change is_valid', ['member_id' => $this->id]);
        } else {
            Log::channel('user')->info('Failed to change is_valid', ['member_id' => $this->id, 'max_amount' => $max_amount_in_usdt]);
        }
    }

    public function checkMemberGrade()
    {
        $this->evaluateMemberGrade();

        $parent_tree = $this->getParentTree(20);

        foreach ($parent_tree as $parent) {
            if ($parent) {
                $parent->evaluateMemberGrade();
            }
        }
    }

    public function evaluateMemberGrade()
    {

        $self_sales = AssetTransfer::where('member_id', $this->id)
            ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
            ->where('status', 'completed')
            ->get()
            ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

        $children_tree = $this->getChildrenTree(20);
        $group_sales = 0;
        foreach ($children_tree as $children) {
            foreach ($children as $child) {
                $group_sales += AssetTransfer::where('member_id', $child->user_id)
                    ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
                    ->where('status', 'completed')
                    ->get()
                    ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());
            }
        }

        $this->checkLevelUp($this->grade->level, $this->referral_count, $self_sales, $group_sales);
    }

    public function checkLevelCondition($mining_policy_id)
    {
        $level_conditions = LevelConditionPolicy::where('mining_policy_id', $mining_policy_id)
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
        $next_grade = MemberGrade::where('level', $next_level)->first();

        if (!$next_grade) {
            return;
        }

        $next_policy = GradePolicy::where('grade_id', $next_grade->id)->first();

        if (!$next_policy) {
            return;
        }

        if (
            $referral_count >= $next_policy->referral_count &&
            $self_sales >= $next_policy->self_sales &&
            $group_sales >= $next_policy->group_sales
        ) {
            $result = Member::where('id', $this->id)->update([
                'grade_id' => $next_grade->id
            ]);

            if (!$result) {
                throw new \Exception("Failed to update grade_id for user_id {$this->user_id}");
            }

            Log::channel('user')->info("User ID {$this->user_id} level up: {$current_level} → {$next_level}, self_sales : {$self_sales}, group_sales : {$group_sales}");

            $this->checkLevelUp($next_level, $referral_count, $self_sales, $group_sales);
        }
    }
}
