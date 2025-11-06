@extends('admin.layouts.master')

@section('content')
    <div class="body-wrapper">
        <div class="container-fluid">
            <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'referral_bonus']) }}" class="nav-link">
                        추천보너스
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'referral_matching']) }}" class="nav-link">
                        추천매칭
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'level_bonus']) }}" class="nav-link active">
                        레벨보너스
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'level_condition']) }}" class="nav-link">
                        레벨조건
                    </a>
                </li>
            </ul>
            <div class="card full-card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">레벨보너스 정책</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table text-nowrap align-middle mb-0 table-striped">
                            <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">뎁스</th>
                                <th scope="col" class="text-center">레벨 보너스</th>
                                <th scope="col" class="text-center">레벨 매칭</th>
                                <th scope="col" class="text-center">수정일자</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody class="table-group-divider">
                            @if($policies->isNotEmpty())
                            @foreach($policies as $key => $val)
                                <tr class="marketing_policy">
                                    <input type="hidden" name="id" value="{{ $val->id }}">
                                    <td class="text-center">{{ $val->depth }}</td>
                                    <td class="text-center">
                                        <input type="text" name="bonus" value="{{ $val->bonus }}" class="form-control w-50">
                                    </td>
                                    <td class="text-center">
                                        <input type="text" name="matching" value="{{ $val->matching }}" class="form-control w-50">
                                    </td>
                                    <td class="text-center">{{ $val->updated_at }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger updateBtn">수정</button>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="5">No Data.</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                        <hr>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <h5 class="card-title">정책 추가</h5>
                        </div>
                        <form method="POST" action="{{ route('admin.marketing.policy.store') }}" id="ajaxForm" data-confirm-message="정책을 추가하시겠습니까?" >
                            @csrf
                            <hr>
                            <input type="hidden" name="mode" value="level_bonus">
                            <input type="hidden" name="marketing_id" value="{{ $marketing->id }}">
                            <table class="table table-bordered mt-5 mb-2">
                                <tbody>
                                <tr>
                                    <th class="text-center align-middle">뎁스</th>
                                    <td class="align-middle" colspan="3">
                                        <select name="depth" class="form-select w-25">
                                            @for($i=1; $i<31; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle">보너스</th>
                                    <td class="align-middle">
                                        <input type="text" name="bonus" value="" class="form-control w-50">
                                    </td>
                                    <th class="text-center align-middle">매칭</th>
                                    <td class="align-middle">
                                        <input type="text" name="matching" value="" class="form-control w-50">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end align-items-center">
                                <button type="submit" class="btn btn-danger">추가</button>
                            </div>
                        </form>
                    </div>
            </div>
            @if($modify_logs->isNotEmpty())
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <h5 class="card-title">정책 변경 로그</h5>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table text-nowrap align-middle mb-0 table-striped">
                                <thead>
                                <tr class="border-2 border-bottom border-primary border-0">
                                    <th scope="col" class="ps-0 text-center">뎁스</th>
                                    <th scope="col" class="ps-0 text-center">변경 내용</th>
                                    <th scope="col" class="ps-0 text-center">변경 전</th>
                                    <th scope="col" class="ps-0 text-center">변경 후</th>
                                    <th scope="col" class="ps-0 text-center">관리자</th>
                                    <th scope="col" class="ps-0 text-center">수정일자</th>
                                </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                @foreach($modify_logs as $key => $val)
                                    <tr>
                                        <td class="text-center">{{ $val->depth }}</td>
                                        <td class="text-center">{{ $val->column_description }}</td>
                                        <td class="text-center">{{ $val->old_value }}</td>
                                        <td class="text-center">{{ $val->new_value }}</td>
                                        <td class="text-center">{{ $val->name }}</td>
                                        <td class="text-center">{{ $val->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <hr>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <form method="POST" id="updateForm" action="{{ route('admin.marketing.policy.update') }}" >
        @csrf
        <input type="hidden" name="marketing_id" value="{{ $marketing->id }}">
        <input type="hidden" name="mode" value="level_bonus">
    </form>
@endsection
@push('script')
    <script src="{{ asset('js/admin/marketing/policy.js') }}"></script>
@endpush
