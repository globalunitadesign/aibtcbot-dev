@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.user.policy', ['mode' => 'grade']) }}" class="nav-link active">
                    등급
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.user.policy', ['mode' => 'rank']) }}" class="nav-link">
                    승급보너스
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">등급 정책</h5>
                    <!--a href="" class="btn btn-primary">Excel</a-->
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped">
                        <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">이름</th>
                                <th scope="col" class="text-center">레벨</th>
                                <th scope="col" class="text-center">추천 인원</th>
                                <th scope="col" class="text-center">개인 매출</th>
                                <th scope="col" class="text-center">그룹 매출</th>
                                <th scope="col" class="text-center" >수정일자</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($policies as $key => $val)
                            <tr class="user_policy">
                                <input type="hidden" name="id" value="{{ $val['id'] }}" >
                                <td class="text-center">{{ $val->grade->name }}</td>
                                <td class="text-center">{{ $val->grade->level }}</td>
                                <td class="text-center"><input type="text" name="referral_count" value="{{ $val->referral_count }}" class="form-control"></td>
                                <td class="text-center"><input type="text" name="self_sales" value="{{ $val->self_sales }}" class="form-control"></td>
                                <td class="text-center"><input type="text" name="group_sales" value="{{ $val->group_sales }}" class="form-control"></td>
                                <td class="text-center">{{ $val->updated_at }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-danger updateBtn">수정</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                </div>
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
                                <th scope="col" class="ps-0 text-center">이름</th>
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
                                <td class="text-center">{{ $val->grade_name }}</td>
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

<form method="POST" id="updateForm" action="{{ route('admin.user.policy.update') }}" >
    @csrf
    <input type="hidden" name="mode" value="grade">
</form>

@endsection

@push('script')
<script src="{{ asset('js/admin/user/policy.js') }}"></script>
@endpush
