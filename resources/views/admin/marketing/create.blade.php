@extends('admin.layouts.master')

@section('content')
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            {{ __('마케팅 추가') }}
                        </h5>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('admin.marketing.store') }}"  enctype="multipart/form-data" id="ajaxForm">
                        @csrf
                        <table class="table table-bordered mt-5 mb-5">
                            <colgroup>
                                <col style="width: 15%;">
                                <col style="width: 35%;">
                                <col style="width: 15%;">
                                <col style="width: 35%;">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th class="text-center align-middle">제목</th>
                                <td class="align-middle" colspan="3">
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
                                <th class="text-center align-middle">설명</th>
                                <td class="align-middle" colspan="3">
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
                            <tr>
                                <th class="text-center align-middle">아이콘</th>
                                <td class="align-middle"  colspan="3">
                                    <input type="file" name="file" value="" class="form-control w-50">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">필수 참여 여부</th>
                                <td class="align-middle">
                                    <input type="radio" name="is_required" id="is_required" value="y" class="form-check-input">
                                    <label class="form-check-label me-3" for="is_required">활성</label>
                                    <input type="radio" name="is_required" id="is_not_required" value="n" class="form-check-input">
                                    <label class="form-check-label" for="is_not_required">비활성</label>
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
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('admin.asset.list') }}" class="btn btn-secondary">목록</a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-danger">추가</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
