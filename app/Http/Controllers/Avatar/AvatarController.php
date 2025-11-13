<?php

namespace App\Http\Controllers\Avatar;


use App\Models\Asset;
use App\Models\Coin;
use App\Models\Income;
use App\Services\MemberService;
use App\Models\User;
use App\Models\Avatar;
use App\Models\Member;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AvatarController extends Controller
{
    public function __construct()
    {

    }


    public function view(Request $request)
    {
        $view = Avatar::find($request->id);

        if ($view->is_active === 'n') {
            return view('avatar.view-inactive', compact('view'));
        } else {
            return view('avatar.view', compact('view'));
        }
    }

    public function active(Request $request)
    {

        $avatar = Avatar::find($request->id);

        $service = new MemberService();
        $referrer_info = $service->memberParseCode($request->referrerId);

        if ($referrer_info['type'] === 'avatar') {
            $referrer = Avatar::find($referrer_info['id']);
        } else {
            $referrer = User::find($referrer_info['id']);
        }
        Log::info($avatar->owner);

        if (!$service->hasMemberInTree($avatar->owner->member->id, $referrer->member->id)) {
            return response()->json([
                'status' => 'error',
                'message' => __('user.user_not_found'),
            ]);
        } else {
            $avatar->update(['is_active' => 'y']);
            $member = $service->addMember($avatar->id, 'avatar', $referrer->member->id);



            return response()->json([
                'status' => 'success',
                'message' => __('user.avatar_active_notice'),
                'route' => route('avatar.view', ['id' => $avatar->id]),
            ]);
        }
    }
}
