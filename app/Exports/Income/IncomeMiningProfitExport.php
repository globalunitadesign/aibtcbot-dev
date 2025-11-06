<?php

namespace App\Exports\Income;

use App\Exports\BaseIncomeExport;
use Illuminate\Support\Facades\DB;

class IncomeMiningProfitExport extends BaseIncomeExport
{
    public function collection()
    {
        $query = DB::table('income_transfers')
            ->leftJoin('incomes', 'income_transfers.income_id', '=', 'incomes.id')
            ->leftJoin('coins', 'incomes.coin_id', '=', 'coins.id')
            ->leftJoin('users', 'income_transfers.user_id', '=', 'users.id')
            ->leftjoin('mining_profits', 'income_transfers.id', '=', 'mining_profits.transfer_id')
            ->leftjoin('mining_rewards', 'mining_profits.reward_id', '=', 'mining_rewards.id')
            ->leftjoin('minings', 'mining_rewards.mining_id', '=', 'minings.id')
            ->leftjoin('mining_policies', 'minings.policy_id', '=', 'mining_policies.id')
            ->leftjoin('mining_policy_translations', 'mining_policies.id', '=', 'mining_policy_translations.policy_id')
            ->select([
                'users.id',
                'users.name',
                'coins.name as coin_name',
                'mining_policy_translations.name as mining_name',
                'minings.coin_amount',
                'income_transfers.amount',
                'mining_profits.type',
                'income_transfers.status',
                'income_transfers.created_at'
            ])
            ->where('mining_policy_translations.locale', 'ko')
            ->orderBy('income_transfers.created_at', 'asc');

        $statusMap = $this->getStatusMap();

        $results = $this->applyCommonFilters($query)->get();

        return $this->formatExportRows($results);
    }


    public function headings(): array
    {
        return ['번호', 'UID', '이름', '종류', '상품이름', '참여수량', '수익', '타입', '상태', '일자'];
    }
}


