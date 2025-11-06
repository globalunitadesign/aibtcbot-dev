@extends('layouts.master')

@section('content')
<div class="container py-5">
    <h2 class="mb-3 text-center">{{ __('mining.mining_list') }}</h2>
    <hr>
    @foreach($minings as $mining)
    <div class="table-responsive overflow-x-auto pt-3">
        <table class="table table-striped table-bordered break-keep-all">
            <thead class="mb-2">
                <tr>
                    <th class="text-center" colspan="3">{{ $mining->policy->mining_locale_name }}</th>
                </tr>
                <tr>
                    <th>{{ __('system.date') }}</th>
                    <th>{{ __('mining.node_amount') }}</th>
                    <th>{{ __('mining.collateral_amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ date_format($mining->created_at, 'Y-m-d h:i:s') }}</td>
                    <td>{{ $mining->node_amount }}</td>
                    <td>{{ number_format($mining->refund_coin_amount) }}</td>
                </tr>
            </tbody>
        </table>
        {{--
        <div class="d-flex justify-content-center align-items-center w-100 mb-3">
            <a href="{{ route('staking.profit', ['id' => $staking->id]) }}">
                <h5 class="btn btn-outline-primary m-0">스테이킹 수익</h5>
            </a>
        </div>
        --}}
    </div>
    @endforeach
</div>
@endsection
