<?php

namespace App\Http\Controllers\Chart;

use App\Models\AthUser;
use App\Models\Chart;
use App\Http\Controllers\Controller;
use App\Services\ChartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AffChartController extends Controller
{

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $chart = new ChartService();
        $chart->mode = 'aff';

        if($request->admin){
            $chart->is_admin = 1;
            $member_id = 1;
        } else {
            $user = auth()->user();
            $member_id = $user->member->id;
        }

        $member_id = $request->search ? $request->search : $member_id;

        $chart->getChartData($member_id);

        return view('chart.chart', ['chartData' => $chart->data]);

    }

}
