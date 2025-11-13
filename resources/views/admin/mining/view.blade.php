@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                            {{ __('마이닝 정보') }}
                    </h5>
                    <div>{{ $view->created_at }}</div>
                </div>
                <hr>
                <table class="table table-bordered mt-5 mb-5">
                    <colgroup>
                        <col style="width: 15%;">
                        <col style="width: 35%;">
                        <col style="width: 15%;">
                        <col style="width: 35%;">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th class="text-center align-middle">아이디</th>
                            <td class="align-middle"><a href="{{ route('admin.user.view', ['id' => $view->user->id])  }}">{{ $view->user->account }}</a></td>
                            <th class="text-center align-middle">이름</th>
                            <td class="align-middle">{{ $view->user->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">상품이름</th>
                            <td class="align-middle" colspan="3"><a href="{{ route('admin.mining.policy.view', ['id' => $view->policy->id, 'mode' => 'mining'])  }}">{{ $view->policy->mining_locale_name }}</a></td>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">종류</th>
                            <td class="align-middle">{{ $view->income->coin->name }}</td>
                            <th class="text-center align-middle">참여수량</th>
                            <td class="align-middle">{{ $view->node_amount }}</td>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">상태</th>
                            <td class="align-middle">
                                @if($view->status == 'pending')
                                    {{ __('진행중') }}
                                @elseif($view->status == 'completed')
                                    {{ __('완료') }}
                                @else
                                    {{ __('오류') }}
                                @endif
                            </td>
                            <th class="text-center align-middle">채굴 횟수</th>
                            <td class="align-middle">{{ $view->reward_count }}</td>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">시작일</th>
                            <td class="align-middle">{{ date_format($view->started_at, 'Y-m-d') }}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('admin.mining.list', ['status' => 'pending']) }}" class="btn btn-secondary">목록</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.mining.view', ['id' => $view->id]) }}" method="GET">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-3 mb-2">
                            <label for="start_date" class="sr-only">Start Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request()->get('start_date') ?? today()->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-2">
                            <label for="end_date" class="sr-only">End Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request()->get('end_date') ?? today()->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-2 text-center mt-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">마이닝 수익 목록</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                        <thead>
                        <tr class="border-2 border-bottom border-primary border-0">
                            <th scope="col" class="text-center">채굴날짜</th>
                            <th scope="col" class="text-center">채굴량/수익</th>
                            <th scope="col" class="text-center">타입</th>
                            <th scope="col" class="text-center">일자</th>
                        </tr>
                        </thead>
                        <tbody class="table-group-divider">
                        @if($rewards->isNotEmpty())
                            @foreach($rewards as $reward)
                                <tr>
                                    <td class="text-center">{{ $reward->reward_date }}</td>
                                    <td class="text-center">{{ $reward->reward }}</td>
                                    <td class="text-center"></td>
                                    <td class="text-center">{{ $reward->created_at }}</td>
                                </tr>
                                @foreach($reward->profits as $profit)
                                    <tr>
                                        <td class="text-center"><i class="bi bi-arrow-return-right"></i></td>
                                        <td class="text-center">{{ $profit->profit }}</td>
                                        <td class="text-center">
                                            @if($profit->type == 'instant')
                                                {{ __('즉시') }}
                                            @elseif($profit->type == 'daily')
                                                {{ __('분할') }}
                                            @else
                                                {{ __('오류') }}
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $profit->created_at }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="4">No Data.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">레벨 보너스 목록</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                        <thead>
                        <tr class="border-2 border-bottom border-primary border-0">
                            <th scope="col" class="text-center">번호</th>
                            <th scope="col" class="text-center">UID</th>
                            <th scope="col" class="text-center">이름</th>
                            <th scope="col" class="text-center">등급</th>
                            <th scope="col" class="text-center">종류</th>
                            <th scope="col" class="text-center">보너스 / 매칭</th>
                            <th scope="col" class="text-center">상태</th>
                            <th scope="col" class="text-center">산하ID</th>
                            <th scope="col" class="text-center">데일리 / 보너스</th>
                            <th scope="col" class="text-center">일자</th>
                        </tr>
                        </thead>
                        <tbody class="table-group-divider">
                        @if($level_bonuses->isNotEmpty())
                            @foreach($level_bonuses as $bonus)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $bonus->user_id }}</td>
                                    <td class="text-center">{{ $bonus->user->name }}</td>
                                    <td class="text-center">{{ $bonus->user->member->grade->name }}</td>
                                    <td class="text-center">{{ $bonus->transfer->income->coin->name }}</td>
                                    <td class="text-center">{{ $bonus->bonus }}</td>
                                    <td scope="col" class="text-center">
                                        @switch($bonus->transfer->status)
                                            @case('pending')
                                                {{ __('신청') }}
                                                @break
                                            @case('waiting')
                                                {{ __('대기') }}
                                                @break
                                            @case('completed')
                                                {{ __('완료') }}
                                                @break
                                            @case('canceled')
                                                {{ __('취소') }}
                                                @break
                                            @default
                                                {{ __('환불') }}
                                        @endswitch
                                    </td>
                                    <td class="text-center">{{ $bonus->referrer_id }}</td>
                                    <td class="text-center">{{ $bonus->profit->profit }}</td>
                                    <td class="text-center">{{ $bonus->transfer->created_at }}</td>
                                </tr>
                                @foreach($bonus->matchings as $matching)
                                    <tr>
                                        <td class="text-center"><i class="bi bi-arrow-return-right"></i></td>
                                        <td class="text-center">{{ $matching->user_id }}</td>
                                        <td class="text-center">{{ $matching->user->name }}</td>
                                        <td class="text-center">{{ $matching->user->member->grade->name }}</td>
                                        <td class="text-center">{{ $matching->transfer->income->coin->name }}</td>
                                        <td class="text-center">{{ $matching->matching }}</td>
                                        <td scope="col" class="text-center">
                                            @switch($matching->transfer->status)
                                                @case('pending')
                                                    {{ __('신청') }}
                                                    @break
                                                @case('waiting')
                                                    {{ __('대기') }}
                                                    @break
                                                @case('completed')
                                                    {{ __('완료') }}
                                                    @break
                                                @case('canceled')
                                                    {{ __('취소') }}
                                                    @break
                                                @default
                                                    {{ __('환불') }}
                                            @endswitch
                                        </td>
                                        <td class="text-center">{{ $matching->referrer_id }}</td>
                                        <td class="text-center">{{ $matching->bonus->bonus }}</td>
                                        <td class="text-center">{{ $matching->transfer->created_at }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="10">No Data.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">추천 보너스 목록</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                        <thead>
                        <tr class="border-2 border-bottom border-primary border-0">
                            <th scope="col" class="text-center">번호</th>
                            <th scope="col" class="text-center">UID</th>
                            <th scope="col" class="text-center">이름</th>
                            <th scope="col" class="text-center">등급</th>
                            <th scope="col" class="text-center">종류</th>
                            <th scope="col" class="text-center">보너스 / 매칭</th>
                            <th scope="col" class="text-center">상태</th>
                            <th scope="col" class="text-center">산하ID</th>
                            <th scope="col" class="text-center">참여금액 / 보너스</th>
                            <th scope="col" class="text-center">일자</th>
                        </tr>
                        </thead>
                        <tbody class="table-group-divider">
                        @if ($referral_bonuses->isNotEmpty())
                            @foreach ($referral_bonuses as $bonus)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $bonus->user_id }}</td>
                                    <td class="text-center">{{ $bonus->user->name }}</td>
                                    <td class="text-center">{{ $bonus->user->member->grade->name }}</td>
                                    <td class="text-center">{{ $bonus->transfer->income->coin->name }}</td>
                                    <td class="text-center">{{ $bonus->bonus }}</td>
                                    <td scope="col" class="text-center">
                                        @switch($bonus->transfer->status)
                                            @case('pending')
                                                {{ __('신청') }}
                                                @break
                                            @case('waiting')
                                                {{ __('대기') }}
                                                @break
                                            @case('completed')
                                                {{ __('완료') }}
                                                @break
                                            @case('canceled')
                                                {{ __('취소') }}
                                                @break
                                            @default
                                                {{ __('환불') }}
                                        @endswitch
                                    </td>
                                    <td class="text-center">{{ $bonus->referrer_id }}</td>
                                    <td class="text-center">{{ $view->coin_amount }}</td>
                                    <td class="text-center">{{ $bonus->transfer->created_at }}</td>
                                </tr>
                                @foreach($bonus->matchings as $matching)
                                    <tr>
                                        <td class="text-center"><i class="bi bi-arrow-return-right"></i></td>
                                        <td class="text-center">{{ $matching->user_id }}</td>
                                        <td class="text-center">{{ $matching->user->name }}</td>
                                        <td class="text-center">{{ $matching->user->member->grade->name }}</td>
                                        <td class="text-center">{{ $matching->transfer->income->coin->name }}</td>
                                        <td class="text-center">{{ $matching->matching }}</td>
                                        <td scope="col" class="text-center">
                                            @switch($matching->transfer->status)
                                                @case('pending')
                                                    {{ __('신청') }}
                                                    @break
                                                @case('waiting')
                                                    {{ __('대기') }}
                                                    @break
                                                @case('completed')
                                                    {{ __('완료') }}
                                                    @break
                                                @case('canceled')
                                                    {{ __('취소') }}
                                                    @break
                                                @default
                                                    {{ __('환불') }}
                                            @endswitch
                                        </td>
                                        <td class="text-center">{{ $matching->referrer_id }}</td>
                                        <td class="text-center">{{ $matching->bonus->bonus }}</td>
                                        <td class="text-center">{{ $matching->transfer->created_at }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="10">No Data.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
