<?php

namespace App\Exports\Income;

use App\Exports\BaseIncomeExport;
use Illuminate\Support\Facades\DB;

class IncomeLevelBonusExport extends BaseIncomeExport
{
    public function collection()
    {
        $query = DB::table('income_transfers')
            ->leftJoin('incomes', 'income_transfers.income_id', '=', 'incomes.id')
            ->leftJoin('coins', 'incomes.coin_id', '=', 'coins.id')
            ->leftJoin('users', 'income_transfers.user_id', '=', 'users.id')
            ->leftJoin('user_profiles', 'income_transfers.user_id', '=', 'user_profiles.user_id')
            ->leftJoin('member_grades', 'user_profiles.grade_id', '=', 'member_grades.id')
            ->leftJoin('level_bonuses', 'income_transfers.id', '=', 'level_bonuses.transfer_id')
            ->leftJoin('mining_rewards', 'level_bonuses.reward_id', '=', 'mining_rewards.id')
            ->select(
                'users.id',
                'users.name',
                'member_grades.name as grade_name',
                'coins.name as coin_name',
                'income_transfers.amount as bonus',
                'income_transfers.status',
                'level_bonuses.referrer_id',
                'mining_rewards.reward',
                'income_transfers.created_at',
            )
            ->orderBy('income_transfers.created_at', 'asc');

        $statusMap = $this->getStatusMap();

        $results = $this->applyCommonFilters($query)->get();

        return $this->formatExportRows($results);
    }

    public function headings(): array
    {
        return ['번호', 'UID', '이름', '등급', '종류', '보너스', '상태','산하ID', '데일리', '일자'];
    }
}
