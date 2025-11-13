<?php

namespace App\Http\Controllers\Asset;


use App\Models\UserProfile;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\TradingProfit;
use App\Models\Bonus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

class AssetController extends Controller
{
    public function __construct()
    {

    }


    public function index(Request $request)
    {
        $user = auth()->user();

        $asset_id = Hashids::decode($request->id);
        $asset = Asset::findOrFail($asset_id[0]);

        if ($asset->member_id != $user->member->id) {
             return redirect()->route('home');
        }

        $data = $asset->getAssetInfo();

        $limit = 5;
        $list = AssetTransfer::where('member_id', $user->member->id)
            ->where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->latest()
            ->take($limit)
            ->get();

        $total_count = AssetTransfer::where('member_id', $user->member->id)
            ->where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->count();

        $has_more = $total_count > $limit;

        return view('asset.asset', compact('data', 'list', 'has_more', 'limit'));

    }

     public function list(Request $request)
    {
        $user = auth()->user();

        $decrypted_id = Hashids::decode($request->id);
        $asset = Asset::findOrFail($decrypted_id[0]);
        $encrypted_id = $asset->encrypted_id;

        if ($asset->member_id != $user->member->id) {
             return redirect()->route('home');
        }

        $limit = 10;
        $list = AssetTransfer::where('member_id', $user->member->id)
            ->where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->latest()
            ->take($limit)
            ->get();

        $total_count = AssetTransfer::where('member_id', $user->member->id)
            ->where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->count();

        $has_more = $total_count > $limit;

        return view('asset.list', compact('encrypted_id', 'list', 'has_more', 'limit'));

    }

    public function loadMore(Request $request)
    {
        $user = auth()->user();

        $decrypted_id = Hashids::decode($request->encrypted_id);
        $asset = Asset::findOrFail($decrypted_id[0]);

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);

        $query = AssetTransfer::where('member_id', $user->member->id)
            ->where('asset_id', $asset->id)
            ->where('status', 'completed')
            ->latest();

        $items = $query->skip($offset)->take($limit + 1)->get();

        $hasMore = $items->count() > $limit;

        $items = $items->take($limit)->map(function ($item) {
            return [
                'created_at' => $item->created_at->format('Y-m-d'),
                'amount' => $item->amount,
                'type_text' => $item->type_text,
            ];
        });

        return response()->json([
            'items' => $items,
            'hasMore' => $hasMore,
        ]);
    }
}
