<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Models\Marketing;
use App\Models\ReferralPolicy;
use App\Models\ReferralMatchingPolicy;
use App\Models\LevelPolicy;
use App\Models\LevelConditionPolicy;
use App\Models\PolicyModifyLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PolicyController extends Controller
{

    public function index(Request $request)
    {
        $marketing = Marketing::find($request->id);

        switch ($request->mode) {

            case 'referral_matching' :

                $policies = ReferralMatchingPolicy::where('marketing_id', $request->id)->get();

                $modify_logs = PolicyModifyLog::join('referral_matching_policies', 'referral_matching_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('user_grades', 'user_grades.id', '=', 'referral_matching_policies.grade_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('user_grades.name as grade_name', 'admins.name', 'policy_modify_logs.*')
                    ->where('referral_matching_policies.marketing_id', $marketing->id)
                    ->where('policy_modify_logs.policy_type', 'referral_matching_policies')
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.marketing.policy.referral-matching', compact('marketing', 'policies', 'modify_logs'));

            case 'level_bonus' :

                $policies = LevelPolicy::where('marketing_id', $request->id)->get();

                $modify_logs = PolicyModifyLog::join('level_policies', 'level_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('level_policies.depth', 'admins.name', 'policy_modify_logs.*')
                    ->where('level_policies.marketing_id', $marketing->id)
                    ->where('policy_modify_logs.policy_type', 'level_policies')
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.marketing.policy.level', compact('marketing', 'policies', 'modify_logs'));

            case 'level_condition' :

                $policies = LevelConditionPolicy::where('marketing_id', $request->id)->get();

                $modify_logs = PolicyModifyLog::join('level_condition_policies', 'level_condition_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('level_condition_policies.node_amount', 'admins.name', 'policy_modify_logs.*')
                    ->where('level_condition_policies.marketing_id', $marketing->id)
                    ->where('policy_modify_logs.policy_type', 'level_condition_policies')
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.marketing.policy.level-condition', compact('marketing', 'policies', 'modify_logs'));

            default :

                $policies = ReferralPolicy::where('marketing_id', $request->id)->get();

                $modify_logs = PolicyModifyLog::join('referral_policies', 'referral_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('user_grades', 'user_grades.id', '=', 'referral_policies.grade_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('user_grades.name as grade_name', 'admins.name', 'policy_modify_logs.*')
                    ->where('referral_policies.marketing_id', $marketing->id)
                    ->where('policy_modify_logs.policy_type', 'referral_policies')
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.marketing.policy.referral', compact('marketing', 'policies', 'modify_logs'));

        }
    }

    public function store(Request $request)
    {
        try {
            switch ($request->mode) {

                case 'level_bonus' :
                    if (LevelPolicy::where('marketing_id', $request->marketing_id)->where('depth', $request->depth)->exists()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => '이미 해당 뎁스에 대한 정책이 존재합니다.',
                        ]);
                    }

                    DB::transaction(function () use ($request) {
                        LevelPolicy::create([
                            'marketing_id' => $request->marketing_id,
                            'depth' => $request->depth,
                            'bonus' => $request->bonus ?? 0,
                            'matching' => $request->matching ?? 0,
                        ]);
                    });

                    return response()->json([
                        'status' => 'success',
                        'message' => '정책이 추가되었습니다.',
                        'url' => route('admin.marketing.policy', ['id' => $request->marketing_id, 'mode' => 'level_bonus']),
                    ]);

                case 'level_condition' :

                    DB::transaction(function () use ($request) {
                        LevelConditionPolicy::create([
                            'marketing_id' => $request->marketing_id,
                            'node_amount' => $request->node_amount,
                            'max_depth' => $request->max_depth,
                            'referral_count' => $request->referral_count,
                            'condition' => $request->condition,
                        ]);
                    });

                    return response()->json([
                        'status' => 'success',
                        'message' => '정책이 추가되었습니다.',
                        'url' => route('admin.marketing.policy', ['id' => $request->marketing_id, 'mode' => 'level_condition']),
                    ]);

                default :
                    return response()->json([
                        'status' => 'error',
                        'message' => '잘못된 요청입니다.',
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create income policy', [
                'mode'  => $request->mode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        try {
            switch ($request->mode) {

                case 'referral_bonus' :


                    $referral_policy = ReferralPolicy::findOrFail($request->id);
                    $referral_policy->update($request->all());

                break;

                case 'referral_matching' :

                    $referral_matching_policy = ReferralMatchingPolicy::findOrFail($request->id);
                    $referral_matching_policy->update($request->all());

                    break;

                case 'level_bonus' :

                    $Level_policy = LevelPolicy::findOrFail($request->id);
                    $Level_policy->update($request->all());

                    break;

                case 'level_condition' :

                    $Level_condition_policy = LevelConditionPolicy::findOrFail($request->id);
                    $Level_condition_policy->update($request->all());

                    break;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '정책이 수정되었습니다.',
                'url' => route('admin.marketing.policy', ['id' => $request->marketing_id, 'mode' => $request->mode]),
            ]);


        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update '.$request->mode.' policy', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }
    }
}
