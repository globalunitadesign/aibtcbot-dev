<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Models\User;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\AssetPolicy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    public function __construct()
    {

    }

    public function deposit($id)
    {

        $user = user::find($id);

        return view('admin.asset.deposit', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {

            $asset = Asset::find($request->id);

            $before_balance = $asset->balance;
            $amount = (float) $request->amount;
            $after_balance = $asset->balance + $amount;

            $asset->update([
                'balance' => $after_balance
            ]);

            $deposit = AssetTransfer::create([
                'member_id' => $asset->member_id,
                'asset_id' => $asset->id,
                'type' => 'manual_deposit',
                'status' => 'completed',
                'amount' => $amount,
                'actual_amount' => $amount,
                'before_balance' => $before_balance,
                'after_balance' => $after_balance,
            ]);

            DB::commit();

            $user = User::find($deposit->member->user_id);

            $user->member->checkUserValidity();
            $user->member->checkMemberGrade();

            return response()->json([
                'status' => 'success',
                'message' => '수동 입금이 완료되었습니다.',
                'url' => route('admin.user.view', ['id' => $user->id]),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::channel('asset')->error('Failed to deposit by admin', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }
    }

    public function update(Request $request)
    {

        $request->validate([
            'id' => ['required', 'integer'],
            'amount' => ['numeric', 'min:0'],
            'memo' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {

            $deposit = AssetTransfer::find($request->id);

            $deposit->update([
                'status' => $request->status ?? $deposit->status,
                'amount' => $request->amount ?? $deposit->amount,
                'actual_amount' => $request->amount ?? $deposit->actual_amount,
                'memo' => $request->memo
            ]);

            if ($request->status === 'completed') {
                $deposit->processDeposit();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '거래 정보 수정이 완료되었습니다.',
                'url' => route('admin.asset.view', ['id' => $deposit->id]),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::channel('asset')->error('Failed to update deposit info', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }

    }
}
