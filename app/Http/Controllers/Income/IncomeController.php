<?php

namespace App\Http\Controllers\Income;

use App\Models\UserProfile;
use App\Models\Income;
use App\Models\IncomeTransfer;
use App\Models\TradingProfit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

class IncomeController extends Controller
{
    public function __construct()
    {

    }


    public function index(Request $request)
    {
        $user = auth()->user();

        $income_id = Hashids::decode($request->id);
        $income = Income::findOrFail($income_id[0]);

        if ($income->member_id != $user->member->id) {
             return redirect()->route('home');
        }

        $data = $income->getIncomeInfo();

        $limit = 5;
        $list = IncomeTransfer::where('member_id', $user->member->id)
            ->where('income_id', $income->id)
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->where('status', 'completed')
            ->latest()
            ->take($limit)
            ->get();

        $total_count = IncomeTransfer::where('member_id', $user->member->id)
            ->where('income_id', $income->id)
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->where('status', 'completed')
            ->count();

        $has_more = $total_count > $limit;

        return view('income.income', compact('data', 'list', 'has_more', 'limit'));
    }

    public function avatar(Request $request)
    {
        $user = auth()->user();

        $avatars = $user->avatars->where('is_active', 'y');

        $incomes = collect();
        $income_ids = [];

        $data = [
            'total_bonus' => 0,
            'total_balance' => 0,
        ];

        foreach ($avatars as $avatar) {
            $income = $avatar->member->incomes->where('coin_id', $request->id)->first();
            if ($income) {
                $income_ids[] = $income->id;
                $data['total_balance'] += $income->balance;

                foreach ($income->transfers->whereIn('type', ['rank_bonus', 'referral_bonus', 'referral_matching', 'level_bonus', 'level_matching']) as $transfer) {
                    $data['total_bonus'] += $transfer->amount;
                }
            }
        }

        $limit = 5;
        $list = IncomeTransfer::whereIn('income_id', $income_ids)
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->where('status', 'completed')
            ->latest('id')
            ->take($limit + 1)
            ->get();

        $has_more = $list->count() > $limit;
        $list = $list->take($limit);

        return view('income.avatar', compact('data', 'list', 'has_more', 'limit'));
    }

    public function list(Request $request)
    {
        $user = auth()->user();

        $income_id = Hashids::decode($request->id);
        $income = Income::findOrFail($income_id[0]);

        if ($income->member_id != $user->member->id) {
             return redirect()->route('home');
        }

        $limit = 10;

        $list = IncomeTransfer::where('member_id', $user->member->id)
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->where('income_id', $income->id)
            ->where('status', 'completed')
            ->latest()
            ->take($limit)
            ->get();

        $total_count = IncomeTransfer::where('member_id', $user->member->id)
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->where('income_id', $income->id)
            ->where('status', 'completed')
            ->count();

        $has_more = $total_count > $limit;

        return view('income.list', compact('list', 'has_more', 'limit'));
    }

    public function loadMore(Request $request)
    {
        $user = auth()->user();

        $income_id = Hashids::decode($request->id);

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);

        $query = IncomeTransfer::where('member_id', $user->member->id)
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->where('income_id', $income_id)
            ->where('status', 'completed')
            ->latest();

        $items = $query->skip($offset)->take($limit + 1)->get();

        $hasMore = $items->count() > $limit;

        $items = $items->take($limit)->map(function ($item) {

            return [
                'created_at' => $item->created_at->format('Y-m-d'),
                'amount' => $item->amount,
                'referrer_id' => match ($item->type) {
                    'referral_bonus' => optional($item->referralBonus)->referrer_id,
                    'referral_matching' => optional($item->referralMatching)->referrer_id,
                    'level_bonus' => optional($item->levelBonus)->referrer_id,
                    'level_matching' => optional($item->levelMatching)->referrer_id,
                    default => null,
                },
                'type_text' => match ($item->type) {
                    'referral_bonus' => $item->type_text . (
                        !empty(optional(optional(optional($item->referralBonus)->mining)->policy)->mining_locale_name)
                            ? '<br>(' . $item->referralBonus->mining->policy->mining_locale_name . ')'
                            : ''
                        ),
                    'referral_matching' => $item->type_text . (
                        !empty(optional(optional(optional($item->referralMatching)->bonus)->mining)->policy->mining_locale_name)
                            ? '<br>(' . $item->referralMatching->bonus->mining->policy->mining_locale_name . ')'
                            : ''
                        ),
                    'level_bonus' => $item->type_text . (
                        !empty(optional(optional(optional($item->levelBonus)->mining)->policy)->mining_locale_name)
                            ? '<br>(' . $item->levelBonus->mining->policy->mining_locale_name . ')'
                            : ''
                        ),
                    'level_matching' => $item->type_text . (
                        !empty(optional(optional(optional($item->levelMatching)->bonus)->mining)->policy->mining_locale_name)
                            ? '<br>(' . $item->levelMatching->bonus->mining->policy->mining_locale_name . ')'
                            : ''
                        ),
                    default => $item->type_text,
                },
            ];
        });

        return response()->json([
            'items' => $items,
            'hasMore' => $hasMore,
        ]);
    }
}
