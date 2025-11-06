@extends('layouts.master')

@section('content')
<header class="p-4 w-100 border-top-title" style="background: url('../images/tit_bg_01.png') center right no-repeat, #1e1e1f;" >
    <h2 class="text-white mb-1 px-1">{{ $marketing->translationForLocale('en')['name'] }}</h2>
    <h6 class="text-white mb-4 px-1">{{ $marketing->marketing_locale_name }}</h6>
    <div class="m-0 px-1">
        <a href="{{ route('mining.list') }}">
            <h5 class="btn btn-header text-white border-0 m-0">{{ __('mining.mining_list') }}</h5>
        </a>
    </div>
</header>
<main class="container-fluid py-5 mb-5">
    <div class="px-3 mb-5">
        <fieldset class="mb-4">
            <legend class="fs-4 text-body mb-3">{{ __('mining.select_mining_asset_guide') }}</legend>
            <div class="d-grid d-grid-col-2 mb-3">
                @foreach($assets as $asset)
                <div>
                    <input type="radio" name="coin_check" value="{{ $asset->coin->id }}" id="{{ $asset->coin->code }}" class="btn-check" autocomplete="off">
                    <label class="btn btn-light w-100 p-4 rounded text-center fs-5 d-flex flex-column align-items-center" for="{{ $asset->coin->code }}">
                        <img src="{{ asset($asset->coin->image_urls[0]) }}" width="40" alt="{{ $asset->coin->code }}" class="img-fluid mb-2">
                        {{ $asset->coin->name }}
                    </label>
                </div>
                @endforeach
            </div>
        </fieldset>

        <fieldset id="miningData" class="d-none">
            {{-- {{ __('staking.select_staking_category_guide') }} --}}
            <div id="miningDataContainer"></div>
        </fieldset>
        {{--
        <div class="mt-4">
            <h6>{{ __('staking.profit_generated') }}</h6>
            <p class="mb-1">- {{ __('staking.profit_generated_guide1') }}</p>
            <p class="mb-1">- {{ __('staking.profit_generated_guide2') }}</p>
        </div>
        --}}
    </div>
    <form method="POST" action="{{ route('mining.data')}}" id="miningDataForm">
    @csrf
    <input type="hidden" name="coin" value="">
    <input type="hidden" name="marketing" value="{{ request()->route('id') }}">
    </form>
</main>
@endsection
@push('script')
<template id="miningDataTemplate">
<div class="mb-4 miningData">
<div class="bg-light w-100 p-4 rounded fs-5">
    <div class="row g-3 text-start">
        <div class="col-12 p-0">
            <span class="fs-5 text-primary fw-semibold mining-name"></span>
        </div>
        <div class="col-6">
            <p class="fs-4 fw-light m-0">{{ __('mining.max_node_amount') }}</p>
            <p class="fs-6 m-0 fw-semibold text-body mining-limit"></p>
        </div>
        <div class="col-6">
            <p class="fs-4 fw-light m-0">{{ __('mining.mining_period') }}</p>
            <p class="fs-6 m-0 fw-semibold text-body mining-period"></p>
        </div>
    </div>
    <button type="button" class="btn btn-primary w-100 py-2 mt-4 fs-4 mining-btn">{{ __('mining.participate') }}</button>
</div>
</div>
</template>
<script src="{{ asset('js/mining/mining.js') }}"></script>
@endpush
