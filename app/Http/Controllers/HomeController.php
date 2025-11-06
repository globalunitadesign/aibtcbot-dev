<?php

namespace App\Http\Controllers;


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
        $notice = Post::where('board_id', 1)->latest()->first();
        $assets = Asset::where('user_id', Auth::user()->id)
        ->whereHas('coin', function ($query) {
            $query->where('is_active', 'y');
        })
        ->get();
        $incomes = Income::where('user_id', Auth::user()->id)
        ->whereHas('coin', function ($query) {
            $query->where('is_active', 'y');
        })
        ->get();

        $marketings = Marketing::all();

        $popups = Post::where('is_popup', 'y')->get();

        return view('home', compact('notice', 'assets', 'incomes', 'marketings', 'popups'));
    }

}
