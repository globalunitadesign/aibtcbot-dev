<?php

namespace App\Http\Controllers\Admin\Mining;

use App\Exports\StakingPolicyExport;
use App\Models\Coin;
use App\Models\MemberGrade;
use App\Models\Mining;
use App\Models\MiningDailyStat;
use App\Models\MiningPolicy;
use App\Models\MiningPolicyTranslation;
use App\Models\LevelPolicy;
use App\Models\LanguagePolicy;
use App\Models\PolicyModifyLog;
use App\Http\Controllers\Controller;
use App\Models\ReferralMatchingPolicy;
use App\Models\ReferralPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PolicyController extends Controller
{

    public function index(Request $request)
    {
        $coins = Coin::all();
        $policies = MiningPolicy::get();

        return view('admin.mining.policy.list', compact('coins', 'policies'));

    }

    public function view(Request $request)
    {
        $coins = Coin::all();
        $locale = LanguagePolicy::where('type', 'locale')->first()->content;

        $all_days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        switch ($request->mode) {
            case 'create' :

                return view('admin.mining.policy.create', compact('coins', 'locale', 'all_days'));

            case 'translation' :

                $policy = MiningPolicy::find($request->id);
                $view = MiningPolicyTranslation::where('policy_id', $policy->id)->get();

                return view('admin.mining.policy.view-translation', compact('policy', 'view'));

            case 'policy' :

                $view = MiningPolicy::find($request->id);

                $selected_days = explode(',', $view->reward_days ?? '');

                $modify_logs = PolicyModifyLog::join('mining_policies', 'mining_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('admins.name', 'policy_modify_logs.*')
                    ->where('policy_modify_logs.policy_type', 'mining_policies')
                    ->where('policy_modify_logs.policy_id', $request->id)
                    ->whereNotIn('policy_modify_logs.column_name', ['exchange_rate', 'node_amount', 'avatar_cost', 'avatar_count', 'avatar_target_amount'])
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.mining.policy.view-policy', compact('coins', 'view', 'all_days', 'selected_days', 'modify_logs'));

            case 'avatar' :

                $view = MiningPolicy::find($request->id);

                $selected_days = explode(',', $view->reward_days ?? '');

                $modify_logs = PolicyModifyLog::join('mining_policies', 'mining_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('admins.name', 'policy_modify_logs.*')
                    ->where('policy_modify_logs.policy_type', 'mining_policies')
                    ->where('policy_modify_logs.policy_id', $request->id)
                    ->whereIn('policy_modify_logs.column_name', ['avatar_cost', 'avatar_count', 'avatar_target_amount'])
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.mining.policy.view-avatar', compact('view', 'modify_logs'));

            default :

                $view = MiningPolicy::find($request->id);

                $mining_daily_stats = MiningDailyStat::where('policy_id', $request->id)->get();

                /*
                foreach ($mining_daily_stats as $mining_daily_stat) {

                    $minings = Mining::where('policy_id', $request->id)->get();

                    $date = $mining_daily_stat->stat_date->format('Y-m-d');

                    $list[$date] = $this->getMiningData($minings, $mining_daily_stat->node_amount);
                    $list[$date]['exchange_rate'] = $mining_daily_stat->exchange_rate;
                    $list[$date]['node_amount'] = $mining_daily_stat->node_amount;
                }
                */

                $list = [];
                return view('admin.mining.policy.view-mining', compact('view', 'list'));

        }
    }

    public function check(Request $request)
    {
        return response()->json($this->getMiningData($request));
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $days = $request->input('reward_days', []);
            $data = $request->except('translation', 'reward_days');

            $data['reward_days'] = implode(',', $days);
            $data['benefit_rules'] = $request->benefit_rules;

            $mining_policy = MiningPolicy::create($data);


            MiningDailyStat::updateOrCreate([
                'policy_id' => $mining_policy->id,
                'stat_date' => today(),
                'exchange_rate' => $data['exchange_rate'],
                'node_amount' => $data['node_amount'],
            ]);

            $locales = $request->translation;

            foreach ($locales as $code => $locale) {
                MiningPolicyTranslation::create([
                    'policy_id' => $mining_policy->id,
                    'locale' => $code,
                    'name' => $locale['name'],
                    'memo' => $locale['memo'],
                ]);
            }

            $grades = MemberGrade::all();

            foreach ($grades as $grade) {
                ReferralPolicy::create([
                    'mining_policy_id' => $mining_policy->id,
                    'grade_id' => $grade->id,
                ]);

                ReferralMatchingPolicy::create([
                    'mining_policy_id' => $mining_policy->id,
                    'grade_id' => $grade->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '노드 마이닝 상품이 추가되었습니다.',
                'url' => route('admin.mining.policy'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create mining policy', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }

    }

    public function update(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                $mining_policy = MiningPolicy::findOrFail($request->id);

                $mining_daily_stat = MiningDailyStat::where('policy_id', $mining_policy->id)
                    ->where('stat_date',  today())
                    ->first();

                switch ($request->mode) {
                    case 'exchange' :

                        $mining_policy->update(['exchange_rate' => $request->exchange_rate]);
                        $mining_daily_stat->update(['exchange_rate' => $request->exchange_rate]);


                        return response()->json([
                            'status' => 'success',
                            'message' => '환율이 변경되었습니다.',
                            'url' => route('admin.mining.policy.view', ['mode' => 'mining', 'id' => $mining_policy->id]),
                        ]);

                    case 'node' :

                        $mining_policy->update(['node_amount' => $request->node_amount]);
                        $mining_daily_stat->update(['node_amount' => $request->node_amount]);

                        return response()->json([
                            'status' => 'success',
                            'message' => '채굴값이 변경되었습니다.',
                            'url' => route('admin.mining.policy.view', ['mode' => 'mining', 'id' => $mining_policy->id]),
                        ]);

                    case 'avatar' :

                        $mining_policy->update([
                            'avatar_cost' => $request->avatar_cost,
                            'avatar_count' => $request->avatar_count,
                            'avatar_target_amount' => $request->avatar_target_amount,
                        ]);

                        return response()->json([
                            'status' => 'success',
                            'message' => '아바타 설정이 변경되었습니다.',
                            'url' => route('admin.mining.policy.view', ['mode' => 'avatar', 'id' => $mining_policy->id]),
                        ]);


                    case 'translation' :

                        $locales = $request->translation;

                        foreach ($locales as $code => $locale) {

                            $translation = MiningPolicyTranslation::where('policy_id', $request->id)
                                ->where('locale', $code)
                                ->first();
                            $translation->update([
                                'name' => $locale['name'],
                                'memo' => $locale['memo'],
                            ]);
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => '다국어 번역이 수정되었습니다.',
                            'url' => route('admin.mining.policy.view', ['mode' => 'translation', 'id' => $mining_policy->id]),
                        ]);

                    default :

                        $days = $request->input('reward_days', []);
                        $data = $request->except(['exchange_rate', 'node_amount', 'mode', 'reward_days']);

                        $data['reward_days'] = implode(',', $days);

                        $mining_policy->update($data);

                        return response()->json([
                            'status' => 'success',
                            'message' => '정책이 수정되었습니다.',
                            'url' => route('admin.mining.policy.view', ['mode' => 'policy', 'id' => $mining_policy->id]),
                        ]);
                }
            });

        } catch (\Exception $e) {

            Log::error('Failed to update mining policy', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }
    }

    public function export()
    {
        $current = now()->toDateString();

        return Excel::download(new StakingPolicyExport(), '스테이킹 상품 내역 ' . $current . '.xlsx');
    }

    public function getMarketingBenefitRules($id)
    {
        $marketing = Marketing::find($id);

        return $marketing->benefit_rules_text;
    }

    private function getMiningData($data)
    {
        $minings = Mining::where('policy_id', $data->id)->get();

        $total_node_amount = 0;
        $total_mining_amount = 0;
        $total_level_bonus = 0;
        $total_level_matching = 0;

        foreach ($minings as $mining) {

            $node_amount = ($data->check_node_amount * $mining->node_amount);
            $total_node_amount += $node_amount;

            $mining_reward = $node_amount / 2;
            $total_mining_amount += $mining_reward;

            $member = $mining->user->member;

            $parents = $member->getParentTree(20);

            foreach ($parents as $level => $parent) {

                $condition = $parent->checkLevelCondition($mining->policy_id);

                if (!$condition) continue;

                $max_depth = $condition->max_depth;

                if ($max_depth < $level) continue;

                if ($parent->is_valid === 'n') continue;

                $policy = LevelPolicy::where('depth', $level)->first();

                $bonus = $node_amount * $policy->bonus / 100;

                $total_level_bonus += $bonus;
                $total_level_matching += $bonus * $policy->matching / 100;
            }
        }

        return [
            'total_node_amount' => $total_node_amount,
            'total_mining_amount' => $total_mining_amount,
            'total_level_bonus' => $total_level_bonus,
            'total_level_matching' => $total_level_matching,
        ];
    }
}
