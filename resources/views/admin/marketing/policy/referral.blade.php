@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist" style="margin-left: -300px; margin-right: -300px; width: calc(100% + 600px);">
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'referral_bonus']) }}" class="nav-link active">
                    추천보너스
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'referral_matching']) }}" class="nav-link">
                    추천매칭
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'level_bonus']) }}" class="nav-link">
                    레벨보너스
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.marketing.policy', ['id' => $marketing->id, 'mode' => 'level_condition']) }}" class="nav-link">
                    레벨조건
                </a>
            </li>
        </ul>
        <div class="card full-card" style="margin-left: -300px; margin-right: -300px; width: calc(100% + 600px);">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">추천보너스 정책</h5>
                    <!--a href="" class="btn btn-primary">Excel</a-->
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped">
                        <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">이름</th>
                                @for($i =1; $i <= 21; $i++)
                                <th scope="col" class="text-center" >{{ $i }}</th>
                                @endfor
                                <th scope="col" class="text-center" >수정일자</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($policies as $key => $val)
                            <tr class="marketing_policy">
                                <input type="hidden" name="id" value="{{ $val->id }}" >
                                <td class="text-center">{{ $val->grade->name }}</td>
                                @for($i =1; $i <= 21; $i++)
                                    <td class="text-center" ><input type="text" name="level_{{ $i }}_rate" value="{{ $val->{'level_'.$i.'_rate'} }}" class="form-control form-control-sm" style="min-width: 50px;"></td>
                                @endfor
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

<form method="POST" id="updateForm" action="{{ route('admin.marketing.policy.update') }}" >
    @csrf
    <input type="hidden" name="marketing_id" value="{{ $marketing->id }}">
    <input type="hidden" name="mode" value="referral_bonus">
</form>

@endsection

@push('script')
<script src="{{ asset('js/admin/marketing/policy.js') }}"></script>
@endpush
