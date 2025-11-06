<?php

namespace App\Http\Controllers\Admin\Income;

use App\Exports\Income\IncomeDepositExport;
use App\Exports\Income\IncomeWithdrawalExport;
use App\Exports\Income\IncomeMiningProfitExport;
use App\Exports\Income\IncomeReferralBonusExport;
use App\Exports\Income\IncomeReferralMatchingExport;
use App\Exports\Income\IncomeLevelBonusExport;
use App\Exports\Income\IncomeLevelMatchingExport;
use App\Exports\Income\IncomeRankBonusExport;
use App\Models\IncomeTransfer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class IncomeController extends Controller
{

    public function __construct()
    {

    }

    public function list(Request $request)
    {
        $list = IncomeTransfer::where('income_transfers.type', $request->input('type', 'deposit'))
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('income_transfers.status', $request->status);
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

            $query->whereBetween('income_transfers.created_at', [$start, $end]);
        })
        ->latest()
        ->orderBy('id', 'desc')
        ->paginate(10);

        return match ($request->type) {
            'withdrawal' => view('admin.income.withdrawal-list', compact('list')),
            'mining_profit' => view('admin.income.mining-profit-list', compact('list')),
            'referral_bonus' => view('admin.income.referral-list', compact('list')),
            'referral_matching' => view('admin.income.referral-matching-list', compact('list')),
            'level_bonus' => view('admin.income.level-list', compact('list')),
            'level_matching' => view('admin.income.level-matching-list', compact('list')),
            'rank_bonus' => view('admin.income.rank-list', compact('list')),
            default => view('admin.income.deposit-list', compact('list')),
        };
    }

    public function view($id)
    {
        $view = IncomeTransfer::find($id);

        return view('admin.income.view', compact('view'));
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        try {

            $transfer = IncomeTransfer::find($request->id);

            $transfer->update(['memo' => $request->memo]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '거래 정보 수정이 완료되었습니다.',
                'url' => route('admin.income.view', ['id' => $transfer->id]),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Failed to update incomeTranfer info', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }

    }

    public function export(Request $request)
    {
        $current = now()->toDateString();

        $exports = [
            'deposit'           => IncomeDepositExport::class,
            'withdrawal'        => IncomeWithdrawalExport::class,
            'mining_profit'     => IncomeMiningProfitExport::class,
            'referral_bonus'    => IncomeReferralBonusExport::class,
            'referral_matching' => IncomeReferralMatchingExport::class,
            'level_bonus'       => IncomeLevelBonusExport::class,
            'level_matching'    => IncomeLevelMatchingExport::class,
            'rank_bonus'        => IncomeRankBonusExport::class,
        ];

        $file_names = [
            'deposit'           => '수익 내부이체 내역',
            'withdrawal'        => '수익 외부출금 내역',
            'mining_profit'     => '수익 마이닝 수익 내역',
            'referral_bonus'    => '수익 추천 보너스 내역',
            'referral_matching' => '수익 추천 매칭 내역',
            'level_bonus'       => '수익 레벨 보너스 내역',
            'level_matching'    => '수익 레벨 매칭 내역',
            'rank_bonus'        => '수익 승급 보너스 내역',
        ];

        if (!isset($exports[$request->type])) {
            abort(400, '유효하지 않은 타입입니다.');
        }

        $export_class = $exports[$request->type];
        $file_name = $file_names[$request->type] . ' ' . $current . '.xlsx';

        return Excel::download(new $export_class($request->all()), $file_name);
    }
}
