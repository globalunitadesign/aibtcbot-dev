<?php

namespace App\Http\Controllers;


use App\Models\Coin;
use App\Models\Asset;
use App\Models\Income;
use App\Models\Marketing;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {

    }


    public function index()
    {
        $user = auth()->user();
        $notice = Post::where('board_id', 1)->latest()->first();

        $assets = Asset::where('member_id', $user->member->id)
            ->whereHas('coin', function ($query) {
                $query->where('is_active', 'y');
            })
            ->get();

        $incomes = Income::where('member_id', $user->member->id)
            ->whereHas('coin', function ($query) {
                $query->where('is_active', 'y');
            })
            ->get();

        $avatar_data = collect();

        if ($user->avatars->where('is_active', 'y')->isNotEmpty()) {

            $coins = Coin::all();

            foreach ($coins as $coin) {
                $avatar_data->put($coin->id, collect([
                    'id' => $coin->id,
                    'code' => $coin->code,
                    'balance' => 0,
                ]));
            }

            foreach ($user->avatars as $avatar) {
                if ($avatar->is_active == 'y') {
                    foreach ($avatar->member->incomes as $income) {
                        $coin_id = $income->coin->id;
                        $current_balance = $avatar_data[$coin_id]['balance'] ?? 0;
                        $avatar_data[$coin_id]['balance'] = $current_balance + $income->balance;
                    }
                }
            }
        }

        $popups = Post::where('is_popup', 'y')->get();

        return view('home', compact('notice', 'assets', 'incomes', 'avatar_data', 'popups'));
    }

}
