<?php

namespace App\Http\Controllers\Asset;

use App\Models\UserProfile;
use App\Models\Coin;
use App\Models\Asset;
use App\Models\AssetPolicy;
use App\Models\AssetTransfer;
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

        $assets = Asset::where('member_id', $user->member->id)
        ->whereHas('coin', function ($query) {
            $query->where('is_active', 'y');
            $query->where('is_asset', 'y');
        })
        ->get();

        return view('asset.withdrawal', compact('assets'));
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

            if ($user->profile->is_frozen === 'y') {
                throw new \Exception(__('asset.withdrawal_frozen_account_notice'));
            }

            if ($validated['amount'] < $asset_policy->min_withdrawal) {
                throw new \Exception(__('asset.withdrawal_min_notice'));
            }

            $asset_id = Hashids::decode($request->asset);

            if (empty($asset_id)) {
                throw new \Exception(__('asset.asset_not_found_notice'));
            }

            $asset = Asset::findOrFail($asset_id[0]);

            if ($asset->balance < $validated['amount']) {
                throw new \Exception(__('asset.lack_balance_notice'));
            }

            $amount = $validated['amount'];

            $tax = $validated['tax'] ?? 0;
            $fee = 0;
            //$fee = $validated['fee'] ?? 0;
            //$actual_amount = $amount - $tax - $fee;
            $actual_amount = $amount - $tax;

            $assetTransfer = AssetTransfer::create([
                'member_id' => $user->member->id,
                'asset_id' => $asset->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'tax' => $tax,
                'fee' => $fee,
                'actual_amount' => $actual_amount,
                'before_balance' => $asset->balance,
                'after_balance' => $asset->balance - $amount,
            ]);

            $asset->decrement('balance', $amount);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' =>  __('asset.withdrawal_apply_notice'),
                'url' => route('asset.withdrawal.complete', ['id' => $assetTransfer->id]),
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
        $assetTransfer = AssetTransfer::find($id);

        $amount = $assetTransfer->amount;

        return view('asset.withdrawal-complete', compact('amount'));
    }

    public function list()
    {
        $user = auth()->user();
        $limit = 10;

        $list = AssetTransfer::where('member_id', $user->member->id)
            ->where('type', 'withdrawal')
            ->latest()
            ->take($limit)
            ->get();

        $total_count = AssetTransfer::where('member_id', $user->member->id)
            ->where('type', 'withdrawal')
            ->count();

        $has_more = $total_count > $limit;

        return view('asset.withdrawal-list', compact('list', 'has_more', 'limit'));
    }

    public function loadMore(Request $request)
    {
        $user = auth()->user();

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);

        $query = AssetTransfer::with('asset.coin')
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
                'actual_amount' => $item->actual_amount,
            ];
        });

        return response()->json([
            'items' => $items,
            'hasMore' => $hasMore,
        ]);
    }
}
