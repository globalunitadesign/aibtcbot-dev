@extends('layouts.master')

@section('content')
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" style="transform: translateY(-71px);">
    <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="px-1 py-3 col-sm-11">
                    <div class="card mb-0">
                        <div class="px-4 py-2 text-end">
                            <a href="{{ url()->previous() }}">
                                <button type="button" class="btn-close"></button>
                            </a>
                        </div>
                        <div class="card-body py-0 px-3">
                            <div class="mb-4">
                                <h3 class="text-center">{{ __('auth.sign_up') }}</h3>
                            </div>
                            <form method="POST" action="{{ route('register') }}" id="ajaxForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="inputAccount" class="form-label required">{{ __('auth.login_id') }}</label>
                                    <div class="input-group">
                                        <input type="text" name="account" id="inputAccount" class="form-control required" required>
                                        <button type="button"  id="accountCheck" class="btn btn-primary rounded-end-3">{{ __('auth.duplicate_check') }}</button>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="inputPassword1" class="form-label required">{{ __('auth.password') }}</label>
                                    <input type="password" name="password"  id="inputPassword1" class="form-control required" required>
                                    <div id="msg_password_guide" class="form-text">{{ __('auth.password_guide') }}</div>
                                </div>
                                <div class="mb-4">
                                    <label for="inputPassword2" class="form-label required">{{ __('auth.confirm_password') }}</label>
                                    <input type="password" name="password_confirmation" id="inputPassword2" class="form-control required" required>
                                    <div class="form-text">{{ __('auth.password_guide') }}</div>
                                </div>
                                <div class="mb-4">
                                    <label for="inputName" class="form-label required">{{ __('user.name') }}</label>
                                    <input type="text" name="name" id="inputName" class="form-control required" required>
                                </div>
                                <div class="mb-4">
                                    <label for="inputPhone" class="form-label required">{{ __('user.phone') }}</label>
                                    <input type="text" name="phone" id="inputPhone" class="form-control required" required>
                                </div>
                                <div class="mb-4">
                                    <label for="inputEmail" class="form-label required">{{ __('user.email') }}</label>
                                    <div class="input-group">
                                        <input type="email" name="email" id="inputEmail" class="form-control required" required>
                                        <button type="button" id="verifyCode" class="btn btn-primary rounded-end-3">{{ __('system.send') }}</button>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="inputName" class="form-label required">{{ __('auth.verify_code') }}</label>
                                    <input type="text" name="code" class="form-control required" required>
                                </div>
                                <div class="mb-4">
                                    <label for="inputReferrerId" class="form-label required">{{ __('user.recommender_uid') }}</label>
                                    <input type="text" name="referrerId" id="inputReferrerId" @if($mid)value="{{ $mid }}"@endif class="form-control required" required>
                                </div>
                                <div class="mb-4">
                                    <label for="inputMetaUid" class="form-label">{{ __('user.meta_id') }}</label>
                                    <input type="text" name="metaUid" id="inputMetaUid" class="form-control">
                                </div>
                                <div class="alert alert-danger mt-4 mb-2" role="alert">
                                    <h6 class="text-danger text-center fw-bold fs-4 m-0 lh-base break-keep-all">{{ __('user.meta_id_guide_1') }}</h6>
                                </div>
                                <p class="mb-4 break-keep-all">
                                    {{ __('user.meta_id_guide_2') }}
                                </p>
                                <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mt-4">{{ __('auth.join') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form method="POST" action="{{ route('register.accountCheck') }}"  id="accountCheckForm" >
    @csrf
    <input type="hidden" name="account" id="inputAccountCheck">
</form>
<form method="POST" action="{{ route('register.emailCheck') }}"  id="emailCheckForm" >
    @csrf
    <input type="hidden" name="email" id="inputEmailCheck">
</form>
<form method="POST" action="{{ route('register.referrerCheck') }}"  id="referrerCheckForm" >
    @csrf
    <input type="hidden" name="referrerId" id="inputReferrerCheck">
</form>
@endsection

@push('message')
<div id="msg_password_missmatch" data-label="{{ __('auth.password_missmatch_notice') }}"></div>
<div id="msg_email_invalid" data-label="{{ __('auth.email_invalid_notice') }}"></div>
@endpush

@push('script')
<script src="{{ asset('js/auth/register.js') }}"></script>
@endpush
