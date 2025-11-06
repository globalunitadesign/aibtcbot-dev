@extends('layouts.master')

@section('content')
<main class="container-fluid py-5 mb-5">
    <h2 class="mb-3 text-center">{{ $data['coin_name'] }} {{ __('asset.asset_detail') }}</h2>
    <hr>
    <div class="g-3 my-5">
        <div class="p-4 rounded bg-primary-subtle-75 text-body mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-primary fs-4 m-0">{{ __('asset.total_asset') }}</p>
                    <h3 class="text-primary fs-6 mb-1">{{ $data['balance'] }}</h3>
                </div>
            </div>            
        </div>
        @if($list->isNotEmpty())
    <div class="table-responsive pb-5">
        <table class="table table-striped table-bordered">
            <thead class="mb-2">
                <tr>
                    <th>{{ __('system.date') }}</th>
                    <th>{{ __('system.amount') }}</th>
                    <th>{{ __('system.category') }}</th>
                </tr>
            </thead>
            <tbody id="loadMoreContainer">              
                @foreach($list as $key => $val)
                <tr>
                    <td>{{ date_format($val->created_at, 'Y-m-d') }}</td>
                    <td>{{ $val->amount }}</td>
                    <td>{{ $val->type_text }}</td>
                </tr>
                @endforeach
            </tbody>
        </table> 
        @if($has_more)
        <a href="{{ route('asset.list',['id' => $data['encrypted_id']]) }}" class="btn btn-outline-primary w-100 py-2 my-4 fs-4">{{ __('system.load_more') }}</a>
        @endif
    </div>
    @endif
    </div>
</main>
@endsection
