<?php

namespace App\Http\Controllers\Profile;

use App\Models\Asset;
use App\Models\Coin;
use App\Models\Income;
use App\Models\Mining;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = $this->getDashboardData();

        return view('profile.dashboard', compact('data'));

    }

    private function getDashboardData()
    {

        $user = auth()->user();
        $grade = $user->member->grade->name;

        $childrens = $user->member->getChildrenTree(20);
        $avatars = $user->avatars;

        $all_count = collect($childrens)->flatten(1)->count();
        $direct_count = isset($childrens[1]) ? $childrens[1]->count() : 0;
        $group_sales = $user->member->getGroupSales();

        $minings = Mining::where('user_id', auth()->id())->get();
        $coins = Coin::all();

        $total_node_amount = $minings->sum('node_amount');
        $total_staking = [];
        $total_reward = [];

        foreach ($coins as $coin) {
            $total_staking[$coin->code] = 0;
            $total_reward[$coin->code] = 0;
        }

        foreach ($minings as $mining) {
            $refund = Asset::find($mining->refund_id);
            $income = Income::find($mining->reward_id);
            foreach ($coins as $coin) {
                if ($refund->coin_id === $coin->id) {
                    $total_staking[$coin->code] += $mining->refund_coin_amount;
                }
                if ($income->coin_id === $coin->id) {
                    foreach ($mining->rewards as $reward) {
                        $total_reward[$coin->code] += $reward->profits->sum('profit');
                    }
                }
            }
        }

        $total_staking = array_filter($total_staking, fn($v) => $v != 0);
        $total_reward = array_filter($total_reward, fn($v) => $v != 0);

        return [
            'grade' => $grade,
            'all_count' => $all_count,
            'direct_count' => $direct_count,
            'group_sales' => $group_sales,
            'total_node_amount' => $total_node_amount,
            'total_staking' => $total_staking,
            'total_reward' => $total_reward,
            'avatars' => $avatars,
        ];
    }
}
