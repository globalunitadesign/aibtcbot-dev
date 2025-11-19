@extends('admin.layouts.master')

@section('content')
    <div class="body-wrapper">
        <div class="container-fluid">
            <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist" >
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.mining.policy.view', ['mode' => 'mining', 'id' => $policy->id]) }}" class="nav-link @if(request('mode') == 'mining') active @endif">
                        환율 & 채굴값
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.mining.policy.view', ['mode' => 'policy', 'id' => $policy->id]) }}" class="nav-link @if(request('mode') == 'policy') active @endif">
                        마이닝 정책
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.mining.policy.view', ['mode' => 'avatar', 'id' => $policy->id]) }}" class="nav-link @if(request('mode') == 'avatar') active @endif">
                        아바타 설정
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.mining.policy.view', ['mode' => 'translation', 'id' => $policy->id]) }}" class="nav-link @if(request('mode') == 'translation') active @endif">
                        다국어 설정
                    </a>
                </li>
            </ul>
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between">
                        <h5 class="card-title">마이닝 다국어</h5>
                    </div>
                    <form method="POST" action="{{ route('admin.mining.policy.update') }}" id="ajaxForm" data-confirm-message="다국어 설명을 변경하시겠습니까?">
                        @csrf
                        <input type="hidden" name="id" value={{ $policy->id }}>
                        <input type="hidden" name="mode" value="translation">
                        <hr>
                        @foreach($view as $key => $val)
                            <table class="table table-bordered mt-5 mb-5">
                                <colgroup>
                                    <col style="width: 5%;">
                                    <col style="width: 10%;">
                                    <col style="width: 85%;">
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <th class="text-center align-middle" rowspan="2">{{ $val->locale }}</th>
                                        <th class="text-center align-middle">이름</th>
                                        <td class="align-middle" colspan="3">
                                            <input type="text" name="translation[{{ $val->locale }}][name]" value="{{ $val->name }}" class="form-control form-control-sm">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-center align-middle">메모</th>
                                        <td class="align-middle" colspan=3>
                                            <textarea name="translation[{{ $val->locale }}][memo]" class="form-control" rows="5" >{{ $val->memo }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.mining.policy', ['id' => '1']) }}" class="btn btn-secondary">목록</a>
                            <button type="submit" class="btn btn-danger">수정</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
