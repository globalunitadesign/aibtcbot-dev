<?php

namespace App\Http\Controllers\Admin\User;


use App\Models\GradePolicy;
use App\Models\PolicyModifyLog;
use App\Http\Controllers\Controller;
use App\Models\RankPolicy;
use App\Models\MemberGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PolicyController extends Controller
{

    public function index(Request $request)
    {
        switch ($request->mode) {

            case 'rank' :

                $policies = RankPolicy::all();
                $member_grades = MemberGrade::all();

                $modify_logs = PolicyModifyLog::join('rank_policies', 'rank_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('member_grades', 'member_grades.id', '=', 'rank_policies.grade_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('member_grades.name as grade_name', 'admins.name', 'policy_modify_logs.*')
                    ->where('policy_modify_logs.policy_type', 'rank_policies')
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.user.policy.rank', compact('policies', 'member_grades', 'modify_logs'));

            default :

                $policies = GradePolicy::all();

                $modify_logs = PolicyModifyLog::join('grade_policies', 'grade_policies.id', '=', 'policy_modify_logs.policy_id')
                    ->join('member_grades', 'member_grades.id', '=', 'grade_policies.grade_id')
                    ->join('admins', 'admins.id', '=', 'policy_modify_logs.admin_id')
                    ->select('member_grades.name as grade_name', 'admins.name', 'policy_modify_logs.*')
                    ->where('policy_modify_logs.policy_type', 'grade_policies')
                    ->orderBy('policy_modify_logs.created_at', 'desc')
                    ->get();

                return view('admin.user.policy.grade', compact('policies', 'modify_logs'));
        }
    }

    public function store(Request $request)
    {
        try {
            switch ($request->mode) {

                case 'rank' :

                    if (RankPolicy::where('grade_id', $request->grade_id)->exists()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => '이미 해당 등급에 대한 정책이 존재합니다.',
                        ]);
                    }

                    DB::transaction(function () use ($request) {
                        RankPolicy::create([
                            'grade_id' => $request->grade_id,
                            'bonus' => $request->bonus,
                            'conditions' => $request->conditions,
                        ]);
                    });

                    return response()->json([
                        'status' => 'success',
                        'message' => '정책이 추가되었습니다.',
                        'url' => route('admin.user.policy', ['mode' => 'rank']),
                    ]);

                default :
                    return response()->json([
                        'status' => 'error',
                        'message' => '잘못된 요청입니다.',
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create user policy', [
                'mode' => $request->mode,
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
                case 'rank' :

                    Log::error('Failed to update user policy', ['requst' => $request->all()]);
                    $rank_policy = RankPolicy::findOrFail($request->id);

                    $data = $request->all();

                    $rank_policy->update($data);

                    break;

                default :
                    $gradePolicy = GradePolicy::findOrFail($request->id);

                    $gradePolicy->update($request->all());

                    break;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '정책이 수정되었습니다.',
                'url' => route('admin.user.policy', ['mode' => $request->mode]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update user policy', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }
    }
}
