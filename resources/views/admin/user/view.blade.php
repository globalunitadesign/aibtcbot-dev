@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">회원 정보</h5>
                    <div>{{ $view->created_at }}</div>
                </div>
                <form method="POST" action="{{ route('admin.user.update') }}" id="ajaxForm" >
                    @csrf
                    <input type="hidden" name="id" value="{{ $view->id }}">
                    <hr>
                    <table class="table table-bordered mt-5 mb-5">
                        <tbody>
                            <tr>
                                <th class="text-center align-middle">유효 계정</th>
                                <td class="align-middle">
                                    <input type="radio" name="is_valid" value="y" id="is_valid"class="form-check-input" @if($view->member->is_valid == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_valid">활성</label>
                                    <input type="radio" name="is_valid" value="n" id="is_not_valid"class="form-check-input" @if($view->member->is_valid == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_valid">비활성</label>
                                </td>
                                <th class="text-center align-middle">계좌 동결</th>
                                <td class="align-middle">
                                    <input type="radio" name="is_frozen" value="y" id="is_frozen"class="form-check-input" @if($view->profile->is_frozen == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_frozen">활성</label>
                                    <input type="radio" name="is_frozen" value="n" id="is_not_frozen"class="form-check-input"@if($view->profile->is_frozen == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_frozen">비활성</label>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">이름</th>
                                <td class="align-middle">
                                    <input type="text" name="name" value="{{ $view->name }}" class="form-control">
                                </td>
                                <th class="text-center align-middle">아이디</th>
                                <td class="align-middle">{{ $view->member->user->account }}</td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">이메일</th>
                                <td class="align-middle">
                                    <input type="text" name="email" value="{{ $view->profile->email }}" class="form-control">
                                </td>
                                <th class="text-center align-middle">비밀번호</th>
                                <td class="align-middle">
                                    <input type="password" name="password" value="" placeholder="변경을 희망하지 않으면 빈칸으로 두세요." class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">전화번호</th>
                                <td colspan="3" class="align-middle">
                                    <input type="text" name="phone" value="{{ $view->profile->phone }}" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">주소</th>
                                <td colspan=3>
                                    <div class="d-flex mb-3 align-middle">
                                        <div class="col-4 me-2">
                                            <input type="text" name="post_code" id="postcode" placeholder="우편번호"  class="form-control" value="{{ $view->profile->post_code }}">
                                        </div>
                                        <button type="button" onclick="daumPostcode()" class="btn btn-outline-primary">우편번호 찾기</button>
                                    </div>
                                    <div class="d-flex">
                                        <input type="text" name="address" id="address" placeholder="주소"  class="form-control me-2" value="{{ $view->profile->address }}">
                                        <input type="text" name="detail_address" id="detailAddress" placeholder="상세주소"  class="form-control" value="{{ $view->profile->detail_address }}">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">USDT(BNB Smart Chain)</th>
                                <td colspan="3">
                                    <div class="d-flex align-items-center justify-content-between me-3">
                                        @if($view->profile->meta_uid)
                                        <div>{{ $view->profile->meta_uid }}</div>
                                        <button type="button" id="usdtResetBtn" class="btn btn-outline-danger">초기화</button>
                                        @else
                                        <div class="me-5">없음</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">OTP 연동</th>
                                <td colspan=3>
                                    <div class="d-flex align-items-center justify-content-between me-3">
                                    @if($view->otp && $view->otp->secret_key)
                                        <div>연동완료</div>
                                        @if(auth()->guard('admin')->user()->admin_level > 3)
                                        <button type="button" id="otpResetBtn" class="btn btn-outline-danger ms-2">초기화</button>
                                        @endif
                                    @else
                                        <div>미연동</div>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">메모</th>
                                <td colspan=3 class="align-middle">
                                    <textarea name="memo" class="form-control" id="memo" rows="12" >{{ $view->profile->memo }}</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.user.list') }}" class="btn btn-secondary">목록</a>
                        @if (auth()->guard('admin')->user()->admin_level > 3 )
                        <button type="submit" class="btn btn-danger">수정</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">자산 정보</h5>
                </div>
                <hr>
                <table class="table table-bordered mt-5 mb-5">
                    <tbody>
                        <tr>
                            <th class="text-center align-middle">자산<br>수량</th>
                            <td class="align-middle">
                                @foreach($view->member->assets as $asset)
                                <div class="row align-items-center mb-3">
                                    <div class="col-3 text-end">
                                        <label class="form-label mb-0">{{ $asset->coin->name }} :</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" value="{{ $asset->balance }}" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                @endforeach
                            </td>
                            <th class="text-center align-middle">수익<br>지갑</th>
                            <td class="align-middle">
                                @foreach($view->member->incomes as $income)
                                <div class="row align-items-center mb-3">
                                    <div class="col-3 text-end">
                                        <label class="form-label mb-0">{{ $income->coin->name }} :</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" value="{{ $income->balance }}" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                @if (auth()->guard('admin')->user()->admin_level >= 3 )
                <div class="d-flex justify-content-end align-items-center">
                    <a href="{{ route('admin.asset.deposit', ['id' => $view->id]) }}" class="btn btn-info">수동입금</a>
                </div>
                @endif
            </div>
        </div>
        @if($view->avatars->isNotEmpty())
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">아바타 정보</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                        <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">번호</th>
                                @foreach($view->member->incomes as $income)
                                <th scope="col" class="text-center">{{ $income->coin->name }}</th>
                                @endforeach
                                <th scope="col" class="text-center" >생성일자</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($view->avatars->where('is_active', 'y') as $avatar)
                            <tr>
                                <td class="text-center">{{ $avatar->name }}</td>
                                @foreach($avatar->member->incomes as $income)
                                <td class="text-center">{{ $income->balance }}</td>
                                @endforeach
                                <td class="text-center">{{ $avatar->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<form method="POST" action="{{ route('admin.user.reset') }}" id="resetForm">
    @csrf
    <input type="hidden" name="user_id" value="{{ $view->id }}">
</form>
@endsection

@push('script')
<script src="{{ asset('js/admin/user/view.js') }}"></script>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="{{ asset('js/postcode.js') }}"></script>
@endpush
