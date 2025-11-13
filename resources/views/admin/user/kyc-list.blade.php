@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('components.search-form', ['route' => route('admin.user.list')])
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <h5 class="card-title">KYC 인증 목록</h5>
                            <a href="{{ route('admin.user.kyc.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-primary">Excel</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                                <thead>
                                    <tr class="border-2 border-bottom border-primary border-0">
                                        <th scope="col" class="ps-0 text-center">번호</th>
                                        <th scope="col" class="text-center">아이디</th>
                                        <th scope="col" class="text-center">UID</th>
                                        <th scope="col" class="text-center">회원명</th>
                                        <th scope="col" class="text-center">등급</th>
                                        <th scope="col" class="text-center">연락처</th>
                                        <th scope="col" class="text-center">이메일</th>
                                        <th scope="col" class="text-center">상태</th>
                                        <th scope="col" class="text-center" >일자</th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                    @if($list->isNotEmpty())
                                    @foreach ($list as $key => $value)
                                    <tr style="cursor:pointer;" onclick="window.location='{{ route('admin.user.kyc.view', ['id' => $value->id]) }}';">
                                        <th scope="row" class="ps-0 fw-medium text-center">{{ $list->firstItem() + $key }}</th>
                                        <td class="text-center">{{ $value->user->account }}</td>
                                        <td class="text-center">{{ $value->user_id }}</td>
                                        <td class="text-center">{{ $value->user->name }}</td>
                                        <td class="text-center">{{ $value->user->member->grade->name }}</td>
                                        <td class="text-center">{{ $value->user->profile->phone }}</td>
                                        <td class="text-center">{{ $value->user->profile->email }}</td>
                                        <td class="text-center">{{ $value->status_text }}</td>
                                        <td class="text-center">{{ $value->created_at }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center" colspan="7">No Data.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-5">
                            {{ $list->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
