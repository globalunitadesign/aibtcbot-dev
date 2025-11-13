@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">코인 목록</h5>
                    <!--a href="{{ route('admin.coin.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-primary">Excel</a-->
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                        <thead>
                            <tr class="border-2 border-bottom border-primary border-0">
                                <th scope="col" class="ps-0 text-center">번호</th>
                                <th scope="col" class="text-center">코드</th>
                                <th scope="col" class="text-center">이름</th>
                                <th scope="col" class="text-center">주소</th>
                                <th scope="col" class="text-center">사용 여부</th>
                                <th scope="col" class="text-center">자산 여부</th>
                                <th scope="col" class="text-center">수익 여부</th>
                                <th scope="col" class="text-center">마이닝 여부</th>
                                <th scope="col" class="text-center">로고</th>
                                <th scope="col" class="text-center" >추가일자</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @if($list->isNotEmpty())
                            @foreach ($list as $key => $value)
                            <tr>
                                <th scope="row" class="ps-0 fw-medium text-center">{{ $list->firstItem() + $key }}</th>
                                <td class="text-center">{{ $value->code }}</td>
                                <td class="text-center">{{ $value->name }}</td>
                                <td class="text-center">
                                    <div class="d-flex">
                                        <input type="text" name="address[{{ $value->id }}]" value="{{ $value->address }}" class="form-control w-75 me-2">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex">
                                        <label class="form-check-label me-3">
                                            <input type="radio" name="is_active[{{ $value->id }}]" value="y" class="form-check-input" @if($value->is_active == 'y') checked @endif>
                                            사용
                                        </label>
                                        <label class="form-check-label">
                                            <input type="radio" name="is_active[{{ $value->id }}]" value="n" class="form-check-input" @if($value->is_active == 'n') checked @endif>
                                            사용안함
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex">
                                        <label class="form-check-label me-3">
                                            <input type="radio" name="is_asset[{{ $value->id }}]" value="y" class="form-check-input" @if($value->is_asset == 'y') checked @endif>
                                            사용
                                        </label>
                                        <label class="form-check-label">
                                            <input type="radio" name="is_asset[{{ $value->id }}]" value="n" class="form-check-input" @if($value->is_asset == 'n') checked @endif>
                                            사용안함
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex">
                                        <label class="form-check-label me-3">
                                            <input type="radio" name="is_income[{{ $value->id }}]" value="y" class="form-check-input" @if($value->is_income == 'y') checked @endif>
                                            사용
                                        </label>
                                        <label class="form-check-label">
                                            <input type="radio" name="is_income[{{ $value->id }}]" value="n" class="form-check-input" @if($value->is_income == 'n') checked @endif>
                                            사용안함
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex">
                                        <label class="form-check-label me-3">
                                            <input type="radio" name="is_mining[{{ $value->id }}]" value="y" class="form-check-input" @if($value->is_mining == 'y') checked @endif>
                                            사용
                                        </label>
                                        <label class="form-check-label">
                                            <input type="radio" name="is_mining[{{ $value->id }}]" value="n" class="form-check-input" @if($value->is_mining == 'n') checked @endif>
                                            사용안함
                                        </label>
                                    </div>
                                </td>
                                <td class="align-text">
                                    <div class="text-center align-middle">
                                        @if($value->image_urls)
                                            @foreach($value->image_urls as $val)
                                                <a href="{{ $val }}">
                                                    <img src="{{ $val }}" class="img-fluid" style="width:30px; height:30px;">
                                                </a>
                                            @endforeach
                                        @else
                                            {{ __('no image.') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">{{ $value->created_at }}</td>
                                <td><button type="button" class="btn btn-sm btn-danger updateBtn" data-id="{{ $value->id }}">수정</button></td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="11">No Data.</td>
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
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">코인 추가</h5>
                </div>
                <form method="POST" action="{{ route('admin.coin.store') }}" id="ajaxForm" data-confirm-message="코인을 추가하시겠습니까?" enctype="multipart/form-data" >
                    @csrf
                    <hr>
                    <table class="table table-bordered mt-5 mb-5">
                        <tbody>
                            <tr>
                                <th class="text-center align-middle">코드</th>
                                <td class="align-middle">
                                    <input type="text" name="code" value="" class="form-control w-50">
                                </td>
                                <th class="text-center align-middle">이름</th>
                                <td class="align-middle">
                                    <input type="text" name="name" value="" class="form-control w-50">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">로고</th>
                                <td colspan="3" class="align-middle">
                                    <input type="file" name="file" class="form-control w-50">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">주소</th>
                                <td colspan="3" class="align-middle">
                                    <input type="text" name="address" value="" class="form-control w-50">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-end align-items-center">
                        <button type="submit" class="btn btn-danger">추가</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.coin.update') }}" id="updateForm">
    @csrf
</form>
@endsection

@push('script')
<script src="{{ asset('js/admin/coin/policy.js') }}"></script>
@endpush
