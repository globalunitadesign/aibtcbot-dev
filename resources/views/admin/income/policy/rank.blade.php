@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid" >
        <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist" >
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.income.policy', ['mode' => 'rank']) }}" class="nav-link active">
                    직급보너스
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.income.policy', ['mode' => 'referral']) }}" class="nav-link">
                    추천보너스
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.income.policy', ['mode' => 'referral_matching']) }}" class="nav-link">
                    추천매칭
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.income.policy', ['mode' => 'level']) }}" class="nav-link">
                    레벨보너스
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.income.policy', ['mode' => 'level_condition']) }}" class="nav-link">
                    레벨조건
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">직급보너스 정책</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                        <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="text-center">레벨</th>
                                <th scope="col" class="text-center">보너스</th>
                                <th scope="col" class="text-center">조건</th>
                                <th scope="col" class="text-center">수정일자</th>
                                <th scope="col" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @if($policies)
                            @foreach($policies as $key => $val)
                            <tr class="income_policy">
                                <input type="hidden" name="id" value="{{ $val->id }}" >
                                <td class="text-center">
                                    {{ $val->grade->name }}
                                </td>
                                <td class="text-center">
                                    <input type="text" name="bonus" value="{{ rtrim(rtrim(number_format($val->bonus, 9, '.', ''), '0'), '.') }}" class="form-control">
                                </td>
                                <td id="input_condition_{{ $key+1 }}">
                                    @if(!is_null($val->conditions))
                                    <div class="row gx-3 align-items-center mb-2 add_condition_{{ $key+1 }}">
                                        <div class="col-2">
                                            <label class="form-label mb-0">직추천</label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">최소 레벨:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[direct][min_level]" value="{{ $val->conditions['direct']['min_level'] }}" class="form-control form-control-sm"/>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">인원 수:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[direct][referral_count]" value="{{ $val->conditions['direct']['referral_count'] }}" class="form-control form-control-sm"/>
                                        </div>
                                    </div>
                                    <div class="row gx-3 align-items-center mb-2 add_condition_{{ $key+1 }}">
                                        <div class="col-2">
                                            <label class="form-label mb-0">추천 산하</label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">최소 레벨:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[all][min_level]" value="{{ $val->conditions['all']['min_level'] }}" class="form-control form-control-sm"/>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">인원 수:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[all][referral_count]" value="{{ $val->conditions['all']['referral_count'] }}" class="form-control form-control-sm"/>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td class="text-center">{{ $val->updated_at }}</td>
                                 <td class="text-center">
                                    <button class="btn btn-sm btn-danger updateBtn">수정</button>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="6">No Data.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-5">

                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">정책 추가</h5>
                </div>
                <form method="POST" action="{{ route('admin.income.policy.store') }}" id="ajaxForm" data-confirm-message="정책을 추가하시겠습니까?" >
                    @csrf
                    <input type="hidden" name="mode" value="rank">
                    <hr>
                    <table class="table table-bordered mt-5 mb-2">
                        <tbody>
                            <tr>
                                <th class="text-center align-middle">레벨</th>
                                <td class="align-middle">
                                    <select name="grade_id" class="form-select w-75">
                                        @foreach($member_grades as $grade)
                                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <th class="text-center align-middle">보너스</th>
                                <td class="align-middle">
                                    <input type="text" name="bonus" value="" class="form-control w-50">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">조건</th>
                                <td colspan="5" class="align-middle" id="input_condition_0">
                                   <div class="row gx-3 align-items-center mb-2 add_condition_0">
                                        <div class="col-1">
                                            <label class="form-label mb-0">직추천</label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">최소 레벨:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[direct][min_level]" value="0" class="form-control form-control-sm"/>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">인원 수:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[direct][referral_count]" value="0" class="form-control form-control-sm"/>
                                        </div>
                                    </div>
                                    <div class="row gx-3 align-items-center mb-2 add_condition_0">
                                        <div class="col-1">
                                            <label class="form-label mb-0">추천 산하</label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">최소 레벨:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[all][min_level]" value="0" class="form-control form-control-sm"/>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0">인원 수:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="conditions[all][referral_count]" value="0" class="form-control form-control-sm"/>
                                        </div>
                                    </div>
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
                                <th scope="col" class="ps-0 text-center">등급</th>
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
                                <td class="text-center">
                                    @if($val->column_name === 'conditions')
                                        @php $oldConditions = json_decode($val->old_value, true); @endphp
                                        @if(is_array($oldConditions))
                                            <ul class="list-unstyled mb-0">
                                                @foreach($oldConditions as $item)
                                                    <li>최소 레벨: {{ $item['min_level'] ?? '?' }}, 최대 레벨: {{ $item['max_level'] ?? '?' }}, 인원 수: {{ $item['referral_count'] ?? '?' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            {{ $val->old_value }}
                                        @endif
                                    @else
                                        {{ $val->old_value }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($val->column_name === 'conditions')
                                        @php $newConditions = json_decode($val->new_value, true); @endphp
                                        @if(is_array($newConditions))
                                            <ul class="list-unstyled mb-0">
                                                @foreach($newConditions as $item)
                                                    <li>최소 레벨: {{ $item['min_level'] ?? '?' }}, 최대 레벨: {{ $item['max_level'] ?? '?' }}, 인원 수: {{ $item['referral_count'] ?? '?' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            {{ $val->new_value }}
                                        @endif
                                    @else
                                        {{ $val->new_value }}
                                    @endif
                                </td>
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

<form method="POST" id="updateForm" action="{{ route('admin.income.policy.update') }}" >
    @csrf
    <input type="hidden" name="mode" value="rank">
</form>

@endsection

@push('script')
<script src="{{ asset('js/admin/income/policy.js') }}"></script>
@endpush
