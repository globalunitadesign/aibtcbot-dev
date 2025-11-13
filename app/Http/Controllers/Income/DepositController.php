<?php

namespace App\Http\Controllers\Income;

use App\Models\Income;
use App\Models\IncomeTransfer;
use App\Models\AssetPolicy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

class DepositController extends Controller
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

        $internal_period = AssetPolicy::first()->internal_period;

        return view('income.deposit', compact('incomes', 'internal_period'));
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'income' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {

            $user = auth()->user();

            $income_id = Hashids::decode($validated['income']);
            $income = Income::findOrFail($income_id[0]);

            if($income->balance < $validated['amount']) {
                  return response()->json([
                    'status' => 'error',
                    'message' => __('asset.lack_balance_notice'),
                ]);
            }

            IncomeTransfer::create([
                'member_id' => $user->member->id,
                'income_id' => $income->id,
                'type' => 'deposit',
                'status' => 'waiting',
                'amount' => $validated['amount'],
                'actual_amount' => $validated['amount'],
                'before_balance' => $income->balance,
                'after_balance' => $income->balance - $validated['amount'],
            ]);

            $income->decrement('balance', $validated['amount']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('asset.deposit_apply_notice'),
                'url' => route('home'),
            ]);


        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => __('system.error_notice') . $e->getMessage(),
            ]);
        }
    }

    public function list()
    {
        $user = auth()->user();
        $limit = 10;

        $list = IncomeTransfer::where('member_id', $user->member->id)
            ->where('type', 'deposit')
            ->latest()
            ->take($limit)
            ->get();

        $total_count = IncomeTransfer::where('member_id', $user->member->id)
            ->where('type', 'deposit')
            ->count();

        $has_more = $total_count > $limit;

        return view('income.deposit-list', compact('list', 'has_more', 'limit'));
    }

    public function loadMore(Request $request)
    {
        $user = auth()->user();

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);

        $query = IncomeTransfer::with('income.coin')
            ->where('member_id',  $user->member->id)
            ->where('type', 'deposit')
            ->orderByDesc('id');

        $items = $query->skip($offset)->take($limit + 1)->get();

        $hasMore = $items->count() > $limit;

        $items = $items->take($limit)->map(function ($item) {
            return [
                'created_at' => $item->created_at->format('Y-m-d'),
                'waiting_period' => $item->waiting_period,
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
