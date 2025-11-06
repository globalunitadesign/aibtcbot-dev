@extends('layouts.master')

@section('content')

<header class="p-4 w-100 border-top-title" style="background: url('../images/tit_bg_01.png') center right no-repeat, #1e1e1f;" >
    <h2 class="text-white mb-1 px-1">Withdrawal</h2>
    <h6 class="text-white mb-4 px-1">{{ __('asset.withdrawal') }}</h6>
    <div class="m-0 px-1">
        <a href="{{ route('income.withdrawal.list') }}">
            <h5 class="btn btn-header text-white border-0 m-0">{{ __('asset.withdrawal_list') }}</h5>
        </a>
    </div>
</header>
<main class="container-fluid py-5 mb-5">
    <div class="px-3 mb-5">
        <form method="POST" action="{{ route('income.withdrawal.store') }}" id="withdrawalForm">
            @csrf
            <fieldset>
                <legend class="mb-3 fs-4 text-body">{{ __('asset.select_withdrawal_asset_guide') }}</legend>
                <div class="d-grid d-grid-col-2 mb-3">
                @foreach ($incomes as $income)
                    <div class="selectedAsset">
                        <input type="radio" class="btn-check" name="income" value="{{ $income->encrypted_id }}" id="{{ $income->coin->code }}" autocomplete="off" data-balance="{{ $income->balance }}">
                        <label class="btn btn-light w-100 p-4 rounded text-center fs-5 d-flex flex-column align-items-center" for="{{ $income->coin->code }}">
                            <img src="{{ $income->coin->image_urls[0] }}" width="40" alt="{{ $income->coin->code }}" class="img-fluid mb-2">
                            {{ $income->coin->name }}
                        </label>
                        <input type="hidden" class="tax_rate" value="{{ $income->tax_rate }}">
                        <input type="hidden" class="fee_rate" value="{{ $income->fee_rate }}">
                    </div>
                @endforeach
                </div>
            </fieldset>
            <div class="my-4">
                <label class="form-label fs-4 text-body">{{ __('asset.withdrawal_amount_guide') }}</label>
                <input type="text" name="amount" class="form-control mb-3"  placeholder="0">
                <p class="mb-5 opacity-50 fw-light fs-4 d-none" id="stock-label">{{ __('system.stock_amount') }}: <span id="stock" class="fw-bold"></span></p>
                <input type="hidden" name="tax">
                <input type="hidden" name="fee">
                <div>
                    <div class="text-center">
                        <!--p class="mb-1">
                        <span class="pe-1">{{ __('asset.fee') }}: <span id="fee">0.00</span></span>
                        <span class="divider position-relative ps-2">{{ __('asset.tax') }}: <span id="tax">0.00</span></span>
                        </p-->
                        <h4 class="pb-4 text-primary">{{ __('asset.withdrawal_actual_amount') }}: <span id="finalAmount">0.00</span></h4>
                    </div>
                    <div class="text-body mb-4">
                        <h6 class="text-body mt-4">{{ __('asset.withdrawal_notice') }}</h6>
                        <p class="mb-1">- {{ __('asset.withdrawal_min_amount') }}</p>
                        <p class="mb-1">- {{ __('asset.withdrawal_arrival_period_guide') }}</p>
                        {{--<p class="mb-1">- {{ __('asset.withdrawal_fee_guide') }}</p>--}}
                        <p class="mb-1">- {{ __('asset.withdrawal_tax_guide') }}</p>
                    </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 mb-4 fs-4">{{ __('asset.withdrawal') }}</button>
        </form>
    </div>
</main>
@endsection

@push('message')
<div id="msg_withdrawal_asset" data-label="{{ __('asset.select_withdrawal_asset_guide') }}"></div>
<div id="msg_withdrawal_amount" data-label="{{ __('asset.withdrawal_amount_guide') }}"></div>
@endpush

@push('script')
<script src="{{ asset('js/income/withdrawal.js') }}"></script>
@endpush
