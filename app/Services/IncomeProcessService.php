<?php

namespace App\Services;

use App\Models\Income;
use App\Models\IncomeAccumulation;
use App\Models\MiningPolicy;
use App\Services\MemberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeProcessService
{
    /**
     *
     * @param Income $income
     * @param MiningPolicy $policy
     * @param float $amount
     */
    public function addProfitAndProcess(Income $income, MiningPolicy $policy, float $amount)
    {
        DB::transaction(function () use ($income, $policy, $amount) {

            $income->balance += $amount;
            $income->save();

            $progress = IncomeAccumulation::firstOrCreate([
                'income_id' => $income->id,
                'mining_policy_id' => $policy->id,
            ]);

            $progress->accumulated_amount += $amount;
            $progress->save();

            $this->processPolicyCondition($progress, $policy, $income);

        });
    }

    /**
     */
    protected function processPolicyCondition(IncomeAccumulation $progress, MiningPolicy $policy, Income $income)
    {
        $service = new MemberService();

        while ($progress->accumulated_amount >= $policy->avatar_target_amount) {

            Log::channel('avatar')->info('Start to add avatar', ['user_id' => $progress->income->member->user_id, 'accumulated_amount' => $progress->accumulated_amount, 'avatar_count' => $policy->avatar_count]);

            $progress->accumulated_amount -= $policy->avatar_cost;
            $progress->save();

            $income->balance -= $policy->avatar_cost;
            $income->save();

            for ($i = 0; $i < $policy->avatar_count; $i++) {
                $user = $income->member->user;
                $service->addAvatar($user);
                Log::channel('avatar')->info('Success to add avatar', ['user_id' => $user->id, 'count' => $i+1]);
            }
        }
    }
}
