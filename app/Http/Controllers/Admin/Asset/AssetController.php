<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Exports\Asset\AssetDepositExport;
use App\Exports\Asset\AssetWithdrawalExport;
use App\Exports\Asset\AssetMiningExport;
use App\Exports\Asset\AssetMiningRefundExport;
use App\Exports\Asset\AssetManualDepositExport;
use App\Models\AssetTransfer;
use App\Http\Controllers\Controller;
use App\Services\S3Service;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AssetController extends Controller
{
    protected S3Service $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function list(Request $request)
    {
        $list = AssetTransfer::where('asset_transfers.type', $request->input('type', 'deposit'))
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('asset_transfers.status', $request->status);
        })
        ->when($request->filled('category') && $request->filled('keyword'), function ($query) use ($request) {
            switch ($request->category) {
                case 'mid':
                    $query->whereHas('user', function ($query) use ($request) {
                        $query->where('users.id', $request->keyword);
                    });
                    break;
                case 'account':
                    $query->whereHas('user', function ($query) use ($request) {
                        $query->where('users.account', $request->keyword);
                    });
                    break;
                case 'name':
                    $query->whereHas('user', function ($query) use ($request) {
                        $query->where('users.name', $request->keyword);
                    });
                    break;
                case 'phone':
                    $query->whereHas('userProfile', function ($query) use ($request) {
                        $query->where('user_profiles.phone', $request->keyword);
                    });
                    break;
                case 'amount':
                    $query->where('amount', $request->keyword);
                    break;
                case 'fee':
                    $query->where('fee', $request->keyword);
                    break;
                case 'tax':
                    $query->where('tax', $request->keyword);
                    break;
            }
        })
        ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('asset_transfers.created_at', [$start, $end]);
        })
        ->latest()
        ->orderBy('id', 'desc')
        ->paginate(10);

        return view('admin.asset.list', compact('list'));
    }

    public function view($id)
    {
        $view = AssetTransfer::find($id);

        $download_url = null;

        if (!empty($view->image_urls) && isset($view->image_urls[0])) {
            $download_url = $this->s3Service->generateDownloadUrl($view->image_urls[0], 600);
        }

        return view('admin.asset.view', compact('view', 'download_url'));
    }


    public function export(Request $request)
    {
        $current = now()->toDateString();

        $exports = [
            'deposit'        => AssetDepositExport::class,
            'withdrawal'     => AssetWithdrawalExport::class,
            'mining'         => AssetMiningExport::class,
            'mining_refund'  => AssetMiningRefundExport::class,
            'manual_deposit' => AssetManualDepositExport::class,
        ];

        $file_names = [
            'deposit'        => '자산 입금 내역',
            'withdrawal'     => '자산 출금 내역',
            'mining'         => '자산 마이닝 참여 내역',
            'mining_refund'  => '자산 원금상환 내역',
            'manual_deposit' => '자산 수동입금 내역',
        ];

        if (!isset($exports[$request->type])) {
            abort(400, '유효하지 않은 타입입니다.');
        }

        $export_class = $exports[$request->type];
        $file_name = $file_names[$request->type] . ' ' . $current . '.xlsx';

        return Excel::download(new $export_class($request->all()), $file_name);
    }
}
