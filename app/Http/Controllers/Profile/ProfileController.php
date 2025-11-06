<?php

namespace App\Http\Controllers\Profile;


use App\Models\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct()
    {
   
    }
    
    public function index()
    {
        $view = UserProfile::join('users', 'user_profiles.user_id', '=', 'users.id')
        ->select('user_profiles.*', 'users.name')
        ->where('user_profiles.user_id', '=', Auth::id())
        ->first();


        return view('profile.profile', compact('view'));   
    }

    public function update(Request $request)
    {
        $validated = $this->validator($request->all())->validate();
        $user_profile = UserProfile::where('user_id', $request->id)->first();

        if ($user_profile) {

            DB::beginTransaction();

            try {

                $user_profile->update([
                    'phone' => $request->phone,
                    'meta_uid' =>$request->meta_uid,
                    'post_code' => $request->post_code,
                    'address' => $request->address,
                    'detail_address' => $request->detail_address
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' =>  __('system.modify_notice'),
                    'url' => route('home'),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error('Failed to update user', ['error' => $e->getMessage()]);

                return response()->json([
                    'status' => 'error',
                    'message' => __('system.error_notice'),
                ]);
            }
        }
    }

    public function password()
    {
        $view = UserProfile::join('users', 'user_profiles.user_id', '=', 'users.id')
            ->select('user_profiles.*', 'users.name', 'users.account')
            ->where('user_profiles.user_id', '=', Auth::id())
            ->first();

        return view('profile.password', compact('view'));  
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => __('auth.password_missmatch_notice'),
            ]);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => __('system.modify_notice'),
            'url' => route('profile'),
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'string', 'min:9', 'max:12'],
            'meta_uid' => ['nullable', 'string', 'max:50'],
        ]);
    }
}