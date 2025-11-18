@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">마이닝 정책 추가</h5>
                </div>
                <form method="POST" action="{{ route('admin.mining.policy.store') }}" id="ajaxForm">
                    @csrf
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
                                        <option value="{{ $coin->id }}">{{ $coin->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <th class="text-center align-middle">최대 노드 수량</th>
                                <td class="align-middle d-flex" colspan="3">
                                    <input type="text" name="node_limit" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">개</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">원금 코인</th>
                                <td class="align-middle">
                                    <select name="refund_coin_id" class="form-select w-50">
                                        <option value="">코인 선택</option>
                                        @foreach ($coins as $coin)
                                            <option value="{{ $coin->id }}">{{ $coin->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <th class="text-center align-middle">수익 코인</th>
                                <td class="align-middle">
                                    <select name="reward_coin_id" class="form-select w-50">
                                        <option value="">코인 선택</option>0
                                        @foreach ($coins as $coin)
                                        <option value="{{ $coin->id }}">{{ $coin->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">즉시 지급</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="instant_rate" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">%</div>
                                </td>
                                <th class="text-center align-middle">분할 지급</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="split_rate" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">%</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">채굴량</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="node_amount" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">1 노드 당 채굴량(1일)</div>
                                </td>
                                <th class="text-center align-middle">환율</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="exchange_rate" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">%</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">대기 기간</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="waiting_period" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">일</div>
                                </td>
                                <th class="text-center align-middle">분할 기간</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="split_period" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">일</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">채굴 제한</th>
                                <td class="align-middle d-flex" colspan="3">
                                    <input type="text" name="reward_limit" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">회</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">채굴 가능 요일</th>
                                <td colspan="3" class="align-middle">
                                    @foreach($all_days as $key => $label)
                                        <label class="me-2">
                                            <input type="checkbox" name="reward_days[]" value="{{ $label }}" class="form-check-input">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">아바타 생성 금액</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="avatar_cost" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">(선택한 수익 코인)</div>
                                </td>
                                <th class="text-center align-middle">아바타 생성 개수</th>
                                <td class="align-middle d-flex">
                                    <input type="text" name="avatar_count" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">개</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">아바타 생성 누적 금액</th>
                                <td class="align-middle d-flex" colspan="3">
                                    <input type="text" name="avatar_target_amount" class="form-control w-25">
                                    <div class="px-2 d-flex align-items-center">(선택한 수익 코인)</div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">추천 보너스</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[referral_bonus]" id="is_referral_bonus" value="y" class="form-check-input">
                                    <label class="form-check-label me-3" for="is_referral_bonus">활성</label>
                                    <input type="radio" name="benefit_rules[referral_bonus]" id="is_not_referral_bonus" value="n" class="form-check-input">
                                    <label class="form-check-label" for="is_not_referral_bonus">비활성</label>
                                </td>
                                <th class="text-center align-middle">추천 매칭</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[referral_matching]" value="y" id="is_referral_matching" class="form-check-input">
                                    <label class="form-check-label me-3" for="is_referral_matching">활성</label>
                                    <input type="radio" name="benefit_rules[referral_matching]" value="n" id="is_not_referral_matching" class="form-check-input">
                                    <label class="form-check-label" for="is_not_referral_matching">비활성</label>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">레벨 보너스</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[level_bonus]" value="y" id="is_level_bonus" class="form-check-input">
                                    <label class="form-check-label me-3" for="is_level_bonus">활성</label>
                                    <input type="radio" name="benefit_rules[level_bonus]" value="n" id="is_not_level_bonus" class="form-check-input">
                                    <label class="form-check-label" for="is_not_level_bonus">비활성</label>
                                </td>
                                <th class="text-center align-middle">레벨 매칭</th>
                                <td class="align-middle">
                                    <input type="radio" name="benefit_rules[level_matching]" value="y" id="is_level_matching" class="form-check-input">
                                    <label class="form-check-label me-3" for="is_level_matching">활성</label>
                                    <input type="radio" name="benefit_rules[level_matching]" value="n" id="is_not_level_matching" class="form-check-input">
                                    <label class="form-check-label" for="is_not_level_matching">비활성</label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered mt-5 mb-5">
                        <colgroup>
                            <col style="width: 15%;">
                            <col style="width: 85%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th class="text-center align-middle">이름</th>
                                <td class="align-middle">
                                    @foreach($locale as $key => $val)
                                        <div class="d-flex mb-3">
                                            <div class="me-2" style="width: 30px;">
                                                <label class="form-label mb-0">{{ $val['code'] }} :</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="text" name="translation[{{ $val['code'] }}][name]" value="" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                             <tr>
                                <th class="text-center align-middle">메모</th>
                                <td class="align-middle">
                                    @foreach($locale as $key => $val)
                                    <div class="d-flex mb-3">
                                        <div class="me-2" style="width: 30px;">
                                            <label class="form-label mb-0">{{ $val['code'] }} :</label>
                                        </div>
                                        <div class="col-10">
                                            <textarea name="translation[{{ $val['code'] }}][memo]" class="form-control" rows="5" ></textarea>
                                        </div>
                                    </div>
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.mining.policy') }}" class="btn btn-secondary">목록</a>
                        <button type="submit" class="btn btn-danger">추가</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('js/admin/mining/policy.js') }}"></script>
@endpush
