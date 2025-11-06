@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist" >
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.mining.policy.view', ['mode' => 'mining', 'id' => $view->id]) }}" class="nav-link @if(request('mode') == 'mining') active @endif">
                    환율 & 채굴값
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.mining.policy.view', ['mode' => 'policy', 'id' => $view->id]) }}" class="nav-link @if(request('mode') == 'policy') active @endif">
                    마이닝 정책
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.mining.policy.view', ['mode' => 'translation', 'id' => $view->id]) }}" class="nav-link @if(request('mode') == 'translation') active @endif">
                    다국어 설정
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.mining.policy.update') }}" id="ajaxForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $view->id }}">
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">코인 환율</h5>
                    </div>
                    <hr>
                    <table class="table table-bordered mt-5 mb-5">
                        <tbody>
                        <tr>
                            <th class="text-center align-middle">환율</th>
                            <td class="align-middle d-flex">
                                <div class="px-2 d-flex align-items-center">{{ $view->refundCoin->name }} 1개 = {{ $view->coin->name }}</div>
                                <input type="text" name="exchange_rate" value="{{ $view->exchange_rate }}" class="form-control w-25">
                                <button type="button" id="exchangeBtn" class="btn btn-info btn-sm ms-3">변경</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">채굴값</h5>
                    </div>
                    <hr>
                    <table class="table table-bordered mt-5 mb-5">
                        <tbody>
                        <tr>
                            <th class="text-center align-middle">채굴값</th>
                            <td class="align-middle d-flex">
                                <div class="px-2 d-flex align-items-center">1 노드 당 채굴값(1일)</div>
                                <input type="text" name="node_amount" value="{{ $view->node_amount }}" class="form-control w-25">
                                <button type="button" id="checkBtn" class="btn btn-success btn-sm ms-3">확인</button>
                                <button type="button" id="nodeBtn" class="btn btn-info btn-sm ms-3">변경</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table text-nowrap align-middle mb-0 table-striped">
                        <thead>
                        <tr class="border-2 border-bottom border-primary border-0">
                            <th scope="col" class="ps-0 text-center">노드 수량 합계</th>
                            <th scope="col" class="ps-0 text-center">참여자 채굴량 합계</th>
                            <th scope="col" class="ps-0 text-center">레벨 보너스 합계</th>
                            <th scope="col" class="ps-0 text-center">레벨 매칭 합계</th>
                        </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <tr>
                                <td class="text-center" id="totalNodeAmount">0</td>
                                <td class="text-center" id="totalMiningAmount">0</td>
                                <td class="text-center" id="totalLevelBonus">0</td>
                                <td class="text-center" id="totalLevelMatching">0</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.mining.policy', ['id' => '1']) }}" class="btn btn-secondary">목록</a>
                    </div>
                </form>
            </div>
        </div>
        @if($list)
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">지급 내역</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table text-nowrap align-middle mb-0 table-striped">
                            <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">지급일</th>
                                <th scope="col" class="ps-0 text-center">환율</th>
                                <th scope="col" class="ps-0 text-center">1 노드 당 채굴값(1일)</th>
                                <th scope="col" class="ps-0 text-center">노드 수량 합계</th>
                                <th scope="col" class="ps-0 text-center">참여자 채굴량 합계</th>
                                <th scope="col" class="ps-0 text-center">레벨 보너스 합계</th>
                                <th scope="col" class="ps-0 text-center">레벨 매칭 합계</th>
                            </tr>
                            </thead>
                            <tbody class="table-group-divider">
                            @foreach($list as $key => $val)
                                <tr>
                                    <td class="text-center">{{ $key }}</td>
                                    <td class="text-center">{{ $val['exchange_rate'] }}</td>
                                    <td class="text-center">{{ $val['node_amount'] }}</td>
                                    <td class="text-center">{{ $val['total_node_amount'] }}</td>
                                    <td class="text-center">{{ $val['total_mining_amount'] }}</td>
                                    <td class="text-center">{{ $val['total_level_bonus'] }}</td>
                                    <td class="text-center">{{ $val['total_level_matching'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<form method="POST" action="{{ route('admin.mining.policy.check') }}" id="miningCheckForm">
    @csrf
    <input type="hidden" name="id" value="{{ $view->id }}">
    <input type="hidden" name="check_node_amount" value="">
</form>
@endsection
@push('script')
<script src="{{ asset('js/admin/mining/policy.js') }}"></script>
@endpush
