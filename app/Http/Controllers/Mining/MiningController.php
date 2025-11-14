<?php

namespace App\Http\Controllers\Mining;


use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Income;
use App\Models\Marketing;
use App\Models\Mining;
use App\Models\MiningPolicy;
use App\Http\Controllers\Controller;
use App\Services\BonusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MiningController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $assets = Asset::where('member_id', $user->member->id)
            ->whereHas('coin', function ($query) {
                $query->where('is_mining', 'y');
            })
            ->get();

        return view('mining.mining', compact( 'assets'));
    }

    public function data(Request $request)
    {
        $Mining = MiningPolicy::where('coin_id', $request->coin)
            ->get();

        return response()->json($Mining->toArray());
    }

    public function list()
    {
        $minings = Mining::where('user_id', auth()->id())->get();

        return view('mining.list', compact('minings'));
    }

    public function confirm($id)
    {
        $user = auth()->user();

        $mining = MiningPolicy::find($id);

        $asset = Asset::where('member_id', $user->member->id)
            ->where('coin_id', $mining->coin_id)
            ->first();

        $balance = $asset->balance;

        $date = $this->getMiningDate($mining);

        return view('mining.confirm', compact( 'mining', 'date', 'balance'));
    }

    public function store(Request $request)
    {

        $user = auth()->user();
        $policy = MiningPolicy::find($request->policy);

        $asset = Asset::where('member_id', $user->member->id)->where('coin_id', $policy->coin_id)->first();
        $refund = Asset::where('member_id', $user->member->id)->where('coin_id', $policy->refund_coin_id)->first();
        $reward = Income::where('member_id', $user->member->id)->where('coin_id', $policy->reward_coin_id)->first();

        if ($asset->balance < $request->coin_amount) {
            return response()->json([
                'status' => 'error',
                'message' =>  __('asset.lack_balance_notice'),
            ]);
        }

        /*
        if ($policy->marketing->is_required === 'n') {

            $coin_amount = $user->profile->getMarketingAmount();

            if ($coin_amount['required'] <= 0 ) {
                return response()->json([
                    'status' => 'error',
                    'message' =>  __('mining.required_mining_notice'),
                ]);
            }

            if ($request->coin_amount + $coin_amount['other'] > $coin_amount['required'] * 10) {
                return response()->json([
                    'status' => 'error',
                    'message' =>  __('mining.max_mining_amount_notice'),
                ]);
            }
        }
       */

        DB::beginTransaction();

        try {

            $date = $this->getMiningDate($policy);

            $mining = Mining::create([
                'user_id' => $user->id,
                'asset_id' => $asset->id,
                'refund_id' => $refund->id,
                'reward_id' => $reward->id,
                'policy_id' => $policy->id,
                'coin_amount' => $request->coin_amount,
                'refund_coin_amount' => $request->refund_coin_amount,
                'node_amount' => $request->node_amount,
                'exchange_rate' => $request->exchange_rate,
                'split_period' => $policy->split_period,
                'reward_count' => 0,
                'reward_limit' => $policy->reward_limit,
                'started_at' => $date['start'],
            ]);

            AssetTransfer::create([
                'member_id' => $user->member->id,
                'asset_id' => $asset->id,
                'type' => 'mining',
                'status' => 'completed',
                'amount' => $request->coin_amount,
                'actual_amount' => $request->coin_amount,
                'before_balance' => $asset->balance,
                'after_balance' => $asset->balance - $request->coin_amount,
            ]);

            $asset->update([
                'balance' => $asset->balance - $request->coin_amount
            ]);

            $service = new BonusService();
            $service->referralBonus($mining);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('mining.mining_success_notice'),
                'url' => route('home'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' =>  $e->getMessage(),
            ]);

        }

    }

    private function getMiningDate($policy)
    {
        $start = Carbon::today()->addDays($policy->waiting_period+1);
        return [
            'start' => $start,
        ];
    }

}
