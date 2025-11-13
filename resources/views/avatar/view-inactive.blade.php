@extends('layouts.master')

@section('content')

<header class="px-4 py-5 w-100 border-top-title" style="background: url('../images/tit_bg_01.png') center right no-repeat, #1e1e1f;" >
    <h2 class="text-white mb-1 px-1">Avatar</h2>
    <h6 class="text-white px-1">{{ __('user.avatar') }}</h6>
</header>
<main class="container-fluid py-5 mb-5">
    <div class="px-3 mb-5">
        <form method="POST" action="{{ route('avatar.active') }}" id="ajaxForm">
            @csrf
            <input type="hidden" name="id" value="{{ $view->id }}">
            <input type="hidden" name="ownerId" value="{{ $view->owner->id }}">
            <h4 class="text-body mt-4">{{ __('user.main_account') }}</h4>
            <div class="text-body mb-2">
                <p class="mb-1 fs-4">{{ __('user.uid') }}: U{{ $view->owner->id }}</p>
                <p class="mb-1 fs-4">{{ __('user.name') }}: {{ $view->owner->name }}</p>
            </div>
            <h4 class="text-body mt-4">{{ __('user.avatar_account') }}</h4>
            <div class="my-4">
                <p class="mb-1 fs-4">{{ __('user.uid') }}: A{{ $view->id }}</p>
                <p class="mb-1 fs-4">{{ __('user.name') }}: {{ $view->name }}</p>
                <p class="mb-1 fs-4">{{ __('mining.participation_amount') }}: 50 USDT</p>
                <div class="d-flex">
                    <label class="form-label text-body fs-4">{{ __('user.recommender_uid') }}</label>
                    <input type="text" name="referrerId"  class="form-control-sm ms-2 mb-3 text-body">
                </div>
            </div>
            <div class="text-body mb-4">
                <h6 class="text-body mt-4">{{ __('system.notice_label') }}</h6>
                <p class="mb-1">- {{ __('user.avatar_guide_1') }}</p>
                <p class="mb-1">- {{ __('user.avatar_guide_2') }}</p>
                <p class="mb-1">- {{ __('user.avatar_guide_3') }}</p>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 mb-4 fs-4">{{ __('system.active') }}</button>
        </form>
    </div>
</main>
@endsection
