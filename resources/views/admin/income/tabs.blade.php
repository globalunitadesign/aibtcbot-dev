{{-- resources/views/admin/income/tabs.blade.php --}}
<div class="d-flex justify-content-between">
    <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist">
        @php
            $tabs = [
                'deposit'          => '내부이체',
                'withdrawal'       => '외부출금',
                'mining_profit'       => '마이닝',
                'referral_bonus'   => '추천보너스',
                'referral_matching'=> '추천매칭',
                'level_bonus'      => '레벨보너스',
                'level_matching'   => '레벨매칭',
                'rank_bonus'       => '승급보너스',
            ];
        @endphp

        @foreach ($tabs as $type => $label)
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.income.list', array_merge(Arr::except(request()->query(), ['page']), ['type' => $type])) }}"
                   class="nav-link {{ request('type') === $type ? 'active' : '' }}">
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
