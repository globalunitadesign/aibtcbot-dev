<?php

namespace App\Exports\Income;

use App\Exports\BaseIncomeExport;
use Illuminate\Support\Facades\DB;

class IncomeRankBonusExport extends BaseIncomeExport
{
    public function collection()
    {
        $query = DB::table('income_transfers')
            ->leftJoin('incomes', 'income_transfers.income_id', '=', 'incomes.id')
            ->leftJoin('coins', 'incomes.coin_id', '=', 'coins.id')
            ->leftJoin('users', 'income_transfers.user_id', '=', 'users.id')
            ->leftJoin('user_profiles', 'income_transfers.user_id', '=', 'user_profiles.user_id')
            ->leftJoin('user_grades', 'user_profiles.grade_id', '=', 'user_grades.id')
            ->leftJoin('rank_bonuses', 'income_transfers.id', '=', 'rank_bonuses.transfer_id')
            ->leftJoin('rank_policies', 'rank_bonuses.policy_id', '=', 'rank_policies.id')
            ->leftJoin('user_grades as rank_grade', 'rank_policies.grade_id', '=', 'rank_grade.id')
            ->select(
                'users.id',
                'users.name',
                'user_grades.name as grade_name',
                'coins.name as coin_name',
                'income_transfers.amount as bonus',
                'rank_bonuses.self_sales',
                'rank_bonuses.group_sales',
                'rank_bonuses.referral_count',
                'rank_grade.name as rank_grade_name',
                'income_transfers.created_at',
            )
            ->orderBy('income_transfers.created_at', 'asc');

        $statusMap = $this->getStatusMap();

        $results = $this->applyCommonFilters($query)->get();

        return $this->formatExportRows($results);
    }

    public function headings(): array
    {
        return ['번호', 'UID', '이름', '등급', '종류', '보너스', '개인매출', '그룹매출', '직추천 수', '보너스 등급', '지급일자'];
    }
}
