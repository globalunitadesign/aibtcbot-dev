@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                            {{ __('마케팅 정보') }}
                    </h5>
                    <div>{{ $view->created_at }}</div>
                </div>
                <hr>
                <form method="POST" action="{{ route('admin.marketing.update') }}" enctype="multipart/form-data" id="ajaxForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $view->id }}">
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
                                @foreach($translations as $translation)
                                    <input type="hidden" name="translation[{{ $translation['locale'] }}][id]" value="{{ $translation['id'] }}">
                                    <div class="d-flex mb-3">
                                        <div class="me-2" style="width: 30px;">
                                            <label class="form-label mb-0">{{ $translation['locale'] }} :</label>
                                        </div>
                                        <div class="col-10">
                                            <input type="text" name="translation[{{ $translation['locale'] }}][name]" value="{{ $translation['name'] }}" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">설명</th>
                            <td class="align-middle" colspan="3">
                                @foreach($translations as $translation)
                                    <div class="d-flex mb-3">
                                        <div class="me-2" style="width: 30px;">
                                            <label class="form-label mb-0">{{ $translation['locale'] }} :</label>
                                        </div>
                                        <div class="col-10">
                                            <textarea name="translation[{{ $translation['locale'] }}][memo]" class="form-control" rows="5" >{{ $translation['memo'] }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                            <tr>
                                <th class="text-center align-middle">아이콘</th>
                                <td colspan="3">
                                    <div class="d-flex">
                                        <img src="{{ $view->image_urls[0] }}" class="img-fluid me-2 align-middler" style="width:38px; height:38px;">
                                        <input type="file" name="file" value="" class="form-control w-50">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">필수 참여 여부</th>
                                <td class="align-middle">
                                    <input type="radio" name="is_required" id="is_required" value="y" class="form-check-input" @if($view->is_required == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_required">활성</label>
                                    <input type="radio" name="is_required" id="is_not_required" value="n" class="form-check-input" @if($view->is_required == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_required">비활성</label>
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
                                        <a href="{{ route('admin.marketing.policy', ['id' => $view->id, 'mode' => 'referral_bonus'])  }}">
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
                        <div>
                            <a href="{{ route('admin.asset.list') }}" class="btn btn-secondary">목록</a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-danger">수정</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
