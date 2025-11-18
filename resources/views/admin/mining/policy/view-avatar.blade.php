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
                <a href="{{ route('admin.mining.policy.view', ['mode' => 'avatar', 'id' => $view->id]) }}" class="nav-link @if(request('mode') == 'avatar') active @endif">
                    아바타 설정
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
                <form method="POST" action="{{ route('admin.mining.policy.update') }}" id="ajaxForm" data-confirm-message="정책을 변경하시겠습니까?">
                    @csrf
                    <input type="hidden" name="mode" value="avatar">
                    <input type="hidden" name="id" value="{{ $view->id }}">
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">아바타 설정</h5>
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
                                <th class="text-center align-middle">아바타 생성 비용</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="avatar_cost" value="{{ $view->avatar_cost }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">{{  $view->rewardCoin->code }}</div>
                                </td>
                                <th class="text-center align-middle">아바타 생성 개수</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="avatar_count" value="{{ $view->avatar_count }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">개</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">아바타 생성 누적 금액</th>
                                <td class="align-middle d-flex" colspan="3">
                                    <input type="text" name="avatar_target_amount" value="{{ $view->avatar_target_amount }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">{{  $view->rewardCoin->code }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.mining.policy') }}" class="btn btn-secondary">목록</a>
                        <button type="submit" class="btn btn-danger">수정</button>
                    </div>
                </form>
            </div>
        </div>
        @if($modify_logs->isNotEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">정책 변경 로그</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table text-nowrap align-middle mb-0 table-striped">
                            <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">변경 내용</th>
                                <th scope="col" class="ps-0 text-center">변경 전</th>
                                <th scope="col" class="ps-0 text-center">변경 후</th>
                                <th scope="col" class="ps-0 text-center">관리자</th>
                                <th scope="col" class="ps-0 text-center">수정일자</th>
                            </tr>
                            </thead>
                            <tbody class="table-group-divider">
                            @foreach($modify_logs as $key => $val)
                                <tr>
                                    <td class="text-center">{{ $val->column_description }}</td>
                                    <td class="text-center">{{ $val->old_value }}</td>
                                    <td class="text-center">{{ $val->new_value }}</td>
                                    <td class="text-center">{{ $val->name }}</td>
                                    <td class="text-center">{{ $val->created_at }}</td>
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
@endsection

@push('script')
    <script src="{{ asset('js/admin/mining/policy.js') }}"></script>
@endpush
