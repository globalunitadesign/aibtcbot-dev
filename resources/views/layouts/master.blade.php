<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="" id="htmlPage" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @stack('meta')
    <link rel="icon" type="image/png" href="{{ asset('images/logos/bit_symbol.png') }}" size="32x32">
    <script src="{{ asset('js/theme_set.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body class="p-0">

    <div class="layoutContainer container min-vh-100 overflow-hidden px-0 bg-body border border-sm-0 layout-padding">
        @if(Auth::check() && !Request::is('register*'))
            @include('layouts.header')
        @endif
        
        <div class="contentContainer">
            @yield('content')
        </div>
        
        @if(Auth::check() && !Request::is('register*'))
            @include('layouts.footer')
        @endif
    </div>

    @include('components.alert-form')
    @include('components.confirm-form')

    @if(!empty($popups))
    @foreach($popups as $popup)
        @php
            $popup_data = json_decode($popup->content);
            $cookie_name = 'popup_hidden_' . $popup->id;
        @endphp

        @if(!request()->cookie($cookie_name))
            @include('components.popup-form', [
                'popup' => $popup,
                'popup_data' => $popup_data,
                'cookie_name' => $cookie_name,
            ])
        @endif
    @endforeach
@endif

<div id="msg_error" data-label="{{ __('system.error_notice') }}"></div>
<div id="msg_session_expried" data-label="{{ __('auth.session_expired_notice') }}"></div>
<div id="msg_logout" data-label="{{ __('user.logout_confirm') }}"></div>
<div id="msg_required" data-label="{{ __('system.required_fields_notice') }}"></div>
<div id="msg_copy" data-label="{{ __('system.copy_notice') }}"></div>
<div id="msg_comming_soon" data-label="{{ __('system.coming_soon_notice') }}"></div>

@stack('message')

<script src="{{ asset('libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/theme.js') }}"></script>
@stack('script')

</body>
</html>
