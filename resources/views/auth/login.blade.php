@extends('layouts.master')

@section('content')
<div class="page-wrapper login" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" style="transform: translateY(-71px);">
    <div class="position-absolute overflow-hidden w-100 min-vh-100 d-flex align-items-center justify-content-center" style="background: #F7931A;">
        <div class="d-flex align-items-center justify-content-center w-100 z-1 my-4">
            <div class="row justify-content-center w-100">
                <div class="col-11 px-2">
                    <div class="card mb-0" style="background: rgba(117,142,255,0.2); backdrop-filter: blur(2px);">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h1 class="text-white">
                                    <img src="{{ asset('/images/logos/logo_w.png') }}" alt="" class="login-logo" style="height: 30px; width: auto;">
                                </h1>
                            </div>
                            <form method="POST" id="ajaxForm" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="locale" class="text-white form-label">Language</label>
                                    <select class="form-select text-dark border-0" id="locale" required="" style="background: rgba(255,255,255,0.8);">
                                        @foreach ($locales as $locale)
                                            <option value="{{ route('change.language', ['locale' => $locale['code']]) }}"@if (request()->cookie('app_locale', 'en') === $locale['code']) selected @endif>{{ $locale['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="inputAccount" class="text-white form-label">{{ __('auth.login_id') }}</label>
                                    <input type="text" name="account" class="form-control text-white" id="inputAccount" >
                                </div>
                                <div class="mb-4">
                                    <label for="inputPassword" class="text-white form-label">{{ __('auth.password') }}</label>
                                    <input type="password" name="password" class="form-control text-white" id="inputPassword">
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="remember" class="form-check-input primary" id="flexCheckChecked" checked>
                                        <label class="text-white form-check-label text-dark" for="flexCheckChecked">
                                            {{ __('auth.keep_login') }}
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark w-100 py-8 fs-4 mb-5">{{ __('auth.login') }}                                
                                </button>
                                <div class="d-flex align-items-center justify-content-center mb-4">
                                    <a class="text-white fw-normal pe-3" href="{{ route('register') }}">{{ __('auth.sign_up') }}</a>
                                    <a class="text-white fw-normal position-relative divider px-3" href="{{ route('account.request') }}">{{ __('auth.find_id') }}</a>
                                    <a class="text-white fw-normal position-relative divider ps-3" href="{{ route('password.request') }}">{{ __('auth.find_password') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-video">
            <video class="bg-video__content" autoplay muted loop playsinline>
                <source src="{{ asset('images/login_video.mp4') }}" type="video/mp4" />
            </video>
        </div>
    </div>
</div>
@endsection


@push('script')
<script src="{{ asset('js/auth/login.js') }}"></script>
@endpush