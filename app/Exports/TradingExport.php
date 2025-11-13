<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class TradingExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {

        $query = DB::table('tradings')
            ->leftJoin('trading_profits', 'tradings.id', '=', 'trading_profits.trading_id')
            ->leftJoin('users', 'tradings.user_id', '=', 'users.id')
            ->leftJoin('user_profiles', 'tradings.user_id', '=', 'user_profiles.user_id')
            ->leftJoin('member_grades', 'user_profiles.grade_id', '=', 'member_grades.id')
            ->leftJoin('coins', 'tradings.coin_id', '=', 'coins.id')
            ->select(
                'member_grades.name as grade_name',
                'users.id',
                'users.name',
                'coins.name as coin_name',
                'tradings.current_count',
                'tradings.balance',
                DB::raw('sum(trading_profits.profit) as sum_proft'),
                'tradings.profit_rate',
                'tradings.created_at',
            )
            ->groupBy(
                'member_grades.name',
                'users.id',
                'users.name',
                'coins.name',
                'tradings.current_count',
                'tradings.balance',
                'tradings.profit_rate',
                'tradings.created_at'
            );


        if (!empty($this->filters['keyword']) && $this->filters['category'] == 'mid') {
            $query->where('users.id', $this->filters['keyword']);
        }

        if (!empty($this->filters['keyword']) && $this->filters['category'] == 'account') {
            $query->where('users.account', $this->filters['keyword']);
        }

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $start = Carbon::parse($this->filters['start_date'])->startOfDay();
            $end = Carbon::parse($this->filters['end_date'])->endOfDay();
            $query->whereBetween('users.created_at', [$start, $end]);
        }

        return $query->get();
    }


    public function headings(): array
    {
        return ['등급', 'MID', '회원명', '자산종류', '퀀트횟수', '보유자산', '수익', '수익률', '가입일자'];
    }
}
