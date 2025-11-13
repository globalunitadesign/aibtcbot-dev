<?php

namespace App\Http\Controllers\Admin\User;

use App\Models\MemberGrade;
use App\Models\GradePolicy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class GradeController extends Controller
{

    public function index(Request $request)
    {

        $list = MemberGrade::paginate(10);

        return view('admin.user.grade', compact('list'));

    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            $grade = MemberGrade::create($validated);

            GradePolicy::create([
                'grade_id' => $grade->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '등급이 추가되었습니다.',
                'url' => route('admin.user.grade'),
            ]);


        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Failed to insert coin', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }

    }


    public function delete(Request $request)
    {

        DB::beginTransaction();

        try {

            $grade = MemberGrade::findOrFail($request->id);

            $policy = GradePolicy::where('grade_id', $grade->id)->first();

            if ($policy) {
                $policy->delete();
            }

            $grade->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '등급이 삭제되었습니다.',
                'url' => route('admin.user.grade'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to delete user grade', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }
    }
}
