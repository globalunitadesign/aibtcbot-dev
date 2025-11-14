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
                <form method="POST" action="{{ route('admin.mining.policy.update') }}" id="ajaxForm" data-confirm-message="정책을 변경하시겠습니까?">
                    @csrf
                    <input type="hidden" name="mode" value="">
                    <input type="hidden" name="id" value="{{ $view->id }}">
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">마이닝 정책</h5>
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
                                <th class="text-center align-middle">참여 코인</th>
                                <td class="align-middle">
                                    <select name="coin_id" class="form-select w-50">
                                        <option value="">코인 선택</option>
                                        @foreach ($coins as $coin)
                                        <option value="{{ $coin->id }}" @selected($view->coin_id == $coin->id)>{{ $coin->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <th class="text-center align-middle">최대 노드 수량</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="node_limit" value="{{ $view->node_limit }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">개</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">원금 코인</th>
                                <td class="align-middle">
                                    <select name="refund_coin_id" class="form-select w-50">
                                        <option value="">코인 선택</option>
                                        @foreach ($coins as $coin)
                                        <option value="{{ $coin->id }}" @selected($view->refund_coin_id == $coin->id)>{{ $coin->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <th class="text-center align-middle">수익 코인</th>
                                <td class="align-middle">
                                    <select name="reward_coin_id" class="form-select w-50">
                                        <option value="">코인 선택</option>
                                        @foreach ($coins as $coin)
                                        <option value="{{ $coin->id }}" @selected($view->reward_coin_id == $coin->id)>{{ $coin->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">즉시 지급</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="instant_rate" value="{{ $view->instant_rate }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">%</div>
                                </td>
                                <th class="text-center align-middle">분할 지급</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="split_rate" value="{{ $view->split_rate }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">%</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">대기 기간</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="waiting_period" value="{{ $view->waiting_period }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">일</div>
                                </td>
                                <th class="text-center align-middle">분할 기간</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="split_period" value="{{ $view->split_period }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">일</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">채굴 제한</th>
                                <td class="align-middle d-flex" colspan="3">
                                    <input type="text" name="reward_limit" value="{{ $view->reward_limit }}" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">회</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">채굴 가능 요일</th>
                                <td colspan="3" class="align-middle">
                                    @foreach($all_days as $key => $label)
                                        <label class="me-2">
                                            <input type="checkbox" name="reward_days[]" value="{{ $label }}" class="form-check-input"
                                                {{ in_array($label, $selected_days) ? 'checked' : '' }}>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">추천 보너스</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[referral_bonus]" id="is_referral_bonus" value="y" class="form-check-input" @if($view->benefit_rules['referral_bonus'] == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_referral_bonus">활성</label>
                                    <input type="radio" name="benefit_rules[referral_bonus]" id="is_not_referral_bonus" value="n" class="form-check-input" @if($view->benefit_rules['referral_bonus'] == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_referral_bonus">비활성</label>
                                </td>
                                <th class="text-center align-middle">추천 매칭</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[referral_matching]" value="y" id="is_referral_matching" class="form-check-input" @if($view->benefit_rules['referral_matching'] == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_referral_matching">활성</label>
                                    <input type="radio" name="benefit_rules[referral_matching]" value="n" id="is_not_referral_matching" class="form-check-input" @if($view->benefit_rules['referral_matching'] == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_referral_matching">비활성</label>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">레벨 보너스</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[level_bonus]" value="y" id="is_level_bonus" class="form-check-input" @if($view->benefit_rules['level_bonus'] == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_level_bonus">활성</label>
                                    <input type="radio" name="benefit_rules[level_bonus]" value="n" id="is_not_level_bonus" class="form-check-input" @if($view->benefit_rules['level_bonus'] == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_level_bonus">비활성</label>
                                </td>
                                <th class="text-center align-middle">레벨 매칭</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[level_matching]" value="y" id="is_level_matching" class="form-check-input" @if($view->benefit_rules['level_matching'] == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_level_matching">활성</label>
                                    <input type="radio" name="benefit_rules[level_matching]" value="n" id="is_not_level_matching" class="form-check-input" @if($view->benefit_rules['level_matching'] == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_level_matching">비활성</label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('admin.mining.profit', ['id' => $view->id]) }}">
                                            <i class="ti ti-settings"></i>
                                            수익 상세설정
                                        </a>
                                    </div>
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
