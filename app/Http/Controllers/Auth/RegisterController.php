<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationCode;
use App\Services\MemberService;
use App\Models\Avatar;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserOtp;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RegisterController extends Controller
{


    /**
    * Display the form for creating a new user.
    *
    * @method GET
    * @return \Illuminate\View\View
    */
    public function index($mid=null)
    {
        return view('auth.register', compact('mid'));
    }

    public function terms()
    {
        return view('auth.terms');
    }

    /**
    * Register.
    *
    * @method POST
    * @return \Illuminate\Http\JsonResponse
    */
    public function register(Request $request)
    {
        $validated = $this->validator($request->all())->validate();

        if (session('verification_code') != $request->code) {
            return response()->json([
                'status' => 'error',
                'message' =>  __('auth.email_verification_failed_notice'),
            ]);
        }

        session(['email_verified' => true]);

        try {

            $user = $this->create($validated);

            Auth::login($user);

            return response()->json([
                'status' => 'success',
                'message' => __('auth.register_success_notice'),
                'url' => route('home'),
            ]);

        } catch (\Exception $e) {

            Log::channel('user')->error('Failed to join', ['message' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => __('auth.register_failed_notice'),
            ]);
        }
    }


    public function accountCheck(Request $request)
    {
        $account = trim($request->account);

        if ($account === '') {
            return response()->json([
                'status' => 'error',
                'message' => __('auth.id_enter_notice'),
            ]);
        }

        $exists = $account !== '' && User::where('account', $account)->exists();

        return response()->json([
            'status' => $exists ? 'error' : 'success',
            'message' => $exists ? __('auth.id_already_taken_notice') : __('auth.id_available_notice'),
        ]);
    }

    public function emailCheck(Request $request)
    {

        $exists = UserProfile::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => __('auth.email_already_taken_notice'),
            ]);
        }

        $code = rand(100000, 999999);

        session([
            'verification_code' => $code,
            'verified_email' => $request->email,
            'email_verified' => false,
        ]);

        try {
            Mail::to($request->email)->send(new EmailVerificationCode($code));
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            \Log::error('메일 전송 실패: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('auth.email_invalid_notice'),
            ]);
        }

        return response()->json(['message' =>  __('auth.verify_code_sent_notice')]);

    }

    public function referrerCheck(Request $request)
    {
        $service = new MemberService();
        $referrer_info  = $service->memberParseCode($request->referrerId);

        if ($referrer_info['type'] === 'avatar') {
            $exists = Avatar::where('id', $referrer_info['id'])->exists();
        } else {
            $exists = User::where('id', $referrer_info['id'])->exists();
        }

        return response()->json([
            'status' => $exists ? 'success' : 'error',
            'message' => $exists ? __('auth.recommender_available_notice') : __('auth.recommender_not_found_notice'),
        ]);
    }

     /**
    * Form validation.
    *
    *
    * @return Illuminate\Support\Facades\Validator
    */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[가-힣a-zA-Z\s]+$/u'],
            'account' => ['required', 'string', 'min:4', 'max:20', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:16', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/', 'confirmed'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'code' => ['required', 'string'],
            'phone' => ['required', 'string', 'min:9', 'max:12', 'regex:/^[\d+]+$/'],
            'referrerId' => ['required', 'string'],
            'metaUid' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9]+$/'],
        ]);
    }


    /**
    * Creating a new user.
    *
    *
    * @return App\Models\User
    */
    private function create(array $data)
    {
        DB::beginTransaction();

        try {
            $service = new MemberService();
            $referrer_info = $service->memberParseCode($data['referrerId']);

            if ($referrer_info['type'] === 'avatar') {
                $referrer = Member::where('avatar_id', $referrer_info['id'])->first();
            } else {
                $referrer = Member::where('user_id', $referrer_info['id'])->first();
            }

            if (!$referrer) {
                throw new Exception('존재하지 않는 추천인 UID입니다.');
            }

            $user = User::create([
                'name' => $data['name'],
                'account' => $data['account'],
                'password' => Hash::make($data['password'])
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'meta_uid' => $data['metaUid'],
            ]);

            UserOtp::create([
                'user_id' => $user->id,
            ]);

            $service->addMember($user->id,'user', $referrer->id);

            DB::commit();

            Log::channel('user')->info('Success to join', ['user_id' => $user->id]);

            return $user;

        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception('회원가입에 실패하였습니다. 다시 시도해주세요.' . $e->getMessage());
        }
    }
}
