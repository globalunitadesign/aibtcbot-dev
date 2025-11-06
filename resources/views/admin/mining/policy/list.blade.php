.@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">마이닝 정책</h5>
                </div>
                <hr>
                <div>
                    <table class="table text-nowrap align-middle mb-0 table-striped">
                        <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">상품이름</th>
                                <th scope="col" class="text-center">즉시 지급</th>
                                <th scope="col" class="text-center">분할 지급</th>
                                <th scope="col" class="text-center">분할 기간</th>
                                <th scope="col" class="text-center">수정일자</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                        @if($policies->isNotEmpty())
                            @foreach($policies as $key => $val)
                            <tr class="staking_policy" style ="cursor:pointer;" onclick="window.location='{{ route('admin.mining.policy.view', ['mode' => 'mining', 'id' => $val->id]) }}'">
                                <td class="text-center">{{ $val->mining_locale_name }}</td>
                                <td class="text-center">{{ $val->instant_rate }}</td>
                                <td class="text-center">{{ $val->split_rate }}</td>
                                <td class="text-center">{{ $val->split_period }}일</td>
                                <td class="text-center">{{ $val['updated_at'] }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">노드 마이닝 상품이 없습니다.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex mt-5">
                        <a href="{{ route('admin.mining.policy.view', ['mode' => 'create']) }}" class="btn btn-info ms-auto">상품 추가</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('js/admin/staking/policy.js') }}"></script>
@endpush
