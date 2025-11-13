<?php

namespace App\Http\Controllers\Income;

use App\Models\UserProfile;
use App\Models\Coin;
use App\Models\Income;
use App\Models\IncomeTransfer;
use App\Models\AssetPolicy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

class WithdrawalController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $user = auth()->user();

        $incomes = Income::where('member_id', $user->member->id)
        ->whereHas('coin', function ($query) {
            $query->where('is_active', 'y');
            $query->where('is_income', 'y');
        })
        ->get();

        return view('income.withdrawal', compact('incomes'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'tax' => 'nullable|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $asset_policy = AssetPolicy::first();

            if (!$asset_policy->isWithdrawalAvailableToday()) {
                throw new \Exception(__('asset.withdrawal_disabled_day_notice'));
            }

            $user = UserProfile::where('user_id', $user->id)->first();

            if($user->is_frozen === 'y') {
                throw new \Exception(__('asset.withdrawal_frozen_account_notice'));
            }

            if($validated['amount'] < $asset_policy->min_withdrawal) {
                throw new \Exception(__('asset.withdrawal_min_notice'));
            }

            $income_id = Hashids::decode($request->income);

            if (empty($income_id)) {
                throw new \Exception(__('asset.asset_not_found_notice'));
            }

            $income = Income::findOrFail($income_id[0]);

            if($income->balance < $validated['amount']) {
                throw new \Exception(__('asset.lack_balance_notice'));
            }

            $amount = $validated['amount'];
            $tax = $validated['tax'] ?? 0;
            $fee = $validated['fee'] ?? 0;
            $actual_amount = $amount - $tax - $fee;

            $incomeTransfer = IncomeTransfer::create([
                'member_id' => $user->member->id,
                'income_id' => $income->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'tax' => $tax,
                'fee' => $fee,
                'actual_amount' => $actual_amount,
                'before_balance' => $income->balance,
                'after_balance' => $income->balance - $amount,
            ]);

            $income->decrement('balance', $amount);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' =>  __('asset.withdrawal_apply_notice'),
                'url' => route('income.withdrawal.complete', ['id' => $incomeTransfer->id]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function complete($id)
    {
        $incomeTransfer = IncomeTransfer::find($id);

        $amount = $incomeTransfer->amount;

        return view('income.withdrawal-complete', compact('amount'));
    }

    public function list()
    {
        $user = auth()->user();
        $limit = 10;

        $list = IncomeTransfer::where('member_id', $user->member->id)
            ->where('type', 'withdrawal')
            ->latest()
            ->take($limit)
            ->get();

        $total_count = IncomeTransfer::where('member_id', $user->member->id)
            ->where('type', 'withdrawal')
            ->count();

        $has_more = $total_count > $limit;


        return view('income.withdrawal-list', compact('list', 'has_more', 'limit'));
    }

    public function loadMore(Request $request)
    {
        $user = auth()->user();

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);

        $query = IncomeTransfer::with('income.coin')
            ->where('member_id', $user->member->id)
            ->where('type', 'withdrawal')
            ->orderByDesc('id');

        $items = $query->skip($offset)->take($limit + 1)->get();

        $hasMore = $items->count() > $limit;

        $items = $items->take($limit)->map(function ($item) {
            return [
                'created_at' => $item->created_at->format('Y-m-d'),
                'coin_code' => $item->asset->coin->code,
                'status_text' => $item->status_text,
                'amount' => $item->amount,
            ];
        });

        return response()->json([
            'items' => $items,
            'hasMore' => $hasMore,
        ]);
    }
}
