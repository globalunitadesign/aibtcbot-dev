<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class UserExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {

        $query = DB::table('users')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->leftJoin('member_grades', 'member_grades.grade_id', '=', 'member_grades.id')
            ->leftJoin('user_profiles as parent_profiles', 'user_profiles.parent_id', '=', 'parent_profiles.user_id')
            ->leftJoin('users as parents', 'parent_profiles.user_id', '=', 'parents.id')
            ->select(
                'users.account',
                'users.id',
                'users.name',
                'user_profiles.level',
                'member_grades.name as grade_name',
                'user_profiles.phone',
                'user_profiles.email',
                'user_profiles.meta_uid',
                'users.created_at',
                'parent_profiles.user_id',
                'parents.name as parent_name',
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

        $results = $query->get();

        return $results->map(function ($item, $index) {
            return collect([
                '번호' => $index + 1,
            ])->merge((array) $item);
        });
    }


    public function headings(): array
    {
        return ['번호', '아이디', 'MID', '회원명', '노드 레벨', '등급', '연락처', '이메일', 'USDT 주소', '가입일자', '추천인UID', '추천인 이름'];
    }
}
