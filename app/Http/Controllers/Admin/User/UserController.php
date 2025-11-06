<?php

namespace App\Http\Controllers\Admin\User;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct()
    {

    }


    public function list(Request $request)
    {
        $list = DB::table('users')
        ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
        ->join('user_grades', 'user_profiles.grade_id', '=', 'user_grades.id')
        ->select('user_profiles.*', 'users.name', 'users.account', 'user_grades.name as grade_name')
        ->when(request('keyword') != '', function ($query) {
            if(request('category') == 'mid'){
                $query->where('users.id', request('keyword'));
            } else if (request('category') == 'account') {
                $query->where('users.account', request('keyword'));
            } else if (request('category') == 'name') {
                $query->where('users.name', request('keyword'));
            } else {
                $query->where('user_profiles.phone', request('keyword'));
            }
        })
        ->when(request('start_date'), function ($query) {
            $start_date = Carbon::parse(request('start_date'))->startOfDay();
            $query->where('users.created_at', '>=', $start_date);
        })
        ->when(request('end_date'), function ($query) {
            $end_date = Carbon::parse(request('end_date'))->endOfDay();
            $query->where('users.created_at', '<=', $end_date);
        })

        ->orderBy('users.created_at', 'desc')
        ->paginate(10);


        return view('admin.user.list', compact('list'));
    }

    public function view($id)
    {

        $view = User::find($id);

        if (!$view) {
            abort(404, '404 not found');
        }

        return view('admin.user.view', compact('view'));
    }

    public function update(Request $request)
    {

        $user = User::find($request->id);
        $user_profile = UserProfile::where('user_id', $request->id)->first();

        if ($user) {

            DB::beginTransaction();

            try {
                $request_data = $request->except('name', 'password');

                $user->update([
                    'name' => $request->name,
                    'password' => $request->password ? Hash::make($request->password) : $user->password,
                ]);

                $user_profile->update($request_data);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => '수정되었습니다.',
                    'url' => route('admin.user.view', ['id' => $user->id]),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error('Failed to update user by admin', ['error' => $e->getMessage()]);

                return response()->json([
                    'status' => 'error',
                    'message' => '예기치 못한 오류가 발생했습니다.',
                ]);
            }
        }
    }

    public function reset(Request $request)
    {

        $user = User::find($request->user_id);

        DB::beginTransaction();

        try {

            switch ($request->mode) {
                case 'usdt' :
                    $user->profile()->update(['meta_uid' => null]);
                break;

                case 'otp' :
                    $user->otp()->update(['secret_key' => null, 'last_verified_at' => null]);
                break;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '초기화되었습니다.',
                'url' => route('admin.user.view', ['id' => $user->id]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to reset usdt address by admin', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }

    }

    public function export(Request $request)
    {
        $current = now()->toDateString();

        return Excel::download(new UserExport($request->all()), '회원 목록 '.$current.'.xlsx');
    }

}
