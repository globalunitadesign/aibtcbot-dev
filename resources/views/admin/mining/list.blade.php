@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between">
                    <ul class="nav nav-tabs mt-3" id="tableTabs" role="tablist" >
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.mining.list', array_merge(request()->query(), ['status' => 'pending'])) }}" class="nav-link {{ Request('status') == 'pending' ? 'active' : '' }}">진행중</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.mining.list', array_merge(request()->query(), ['status' => 'completed'])) }}" class="nav-link {{ Request('status') == 'completed' ? 'active' : '' }}">만료</a>
                        </li>
                    </ul>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.asset.list') }}" method="GET">
                            @foreach(request()->query() as $key => $value)
                                @if($key !== 'start_date' && $key !== 'end_date')
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <div class="row align-items-center">
                                <div class="col-12 col-md-2 mb-2">
                                    <label for="search" class="sr-only">Category</label>
                                    <select name="category" id="category" class="form-control" >
                                        <option value="">카테고리 선택</option>
                                        <option value="mid" @if(request()->category == 'mid') selected @endif>MID 조회</option>
                                        <option value="account" @if(request()->category == 'account') selected @endif>아이디 조회</option>
                                        <option value="name" @if(request()->category == 'name') selected @endif>이름 조회</option>
                                        <option value="phone" @if(request()->category == 'phone') selected @endif>연락처 조회</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2 mb-2">
                                    <label for="search" class="sr-only">Keyword</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                                </div>
                                <div class="col-12 col-md-2 mb-2">
                                    <label for="start_date" class="sr-only">Start Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request()->get('start_date') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-2 mb-2">
                                    <label for="end_date" class="sr-only">End Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request()->get('end_date') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-2 text-center mt-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-end">
                        <!--a href="{{ route('admin.asset.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-primary">Excel</a-->
                        </div>
                        <div class="table-responsive">
                            <table class="table text-nowrap align-middle mb-0 table-striped table-hover">
                                <thead>
                                    <tr class="border-2 border-bottom border-primary border-0">
                                        <th scope="col" class="text-center">번호</th>
                                        <th scope="col" class="text-center">UID</th>
                                        <th scope="col" class="text-center">이름</th>
                                        <th scope="col" class="text-center">종류</th>
                                        <th scope="col" class="text-center">노드수량</th>
                                        <th scope="col" class="text-center">담보수량</th>
                                        <th scope="col" class="text-center">상품이름</th>
                                        <th scope="col" class="text-center">분할기간</th>
                                        <th scope="col" class="text-center">상태</th>
                                        <th scope="col" class="text-center">일자</th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                    @if($list->isNotEmpty())
                                    @foreach ($list as $key => $value)
                                    <tr style="cursor:pointer;" onclick="window.location='{{ route('admin.mining.view', ['id' => $value->id]) }}';">
                                        <td scope="col" class="text-center">{{ $list->firstItem() + $key }}</td>
                                        <td scope="col" class="text-center">{{ $value->user_id }}</td>
                                        <td scope="col" class="text-center">{{ $value->user->name }}</td>
                                        <td scope="col" class="text-center">{{ $value->asset->coin->name }}</td>
                                        <td scope="col" class="text-center">{{ $value->node_amount }}</td>
                                        <td scope="col" class="text-center">{{ number_format($value->refund_coin_amount) }}</td>
                                        <td scope="col" class="text-center">{{ $value->policy->mining_locale_name }}</td>
                                        <td scope="col" class="text-center">{{ $value->split_period }}</td>
                                        <td scope="col" class="text-center">
                                            @switch($value->status)
                                                @case('pending')
                                                    {{ __('진행중') }}
                                                    @break
                                                @case('completed')
                                                    {{ __('만료') }}
                                            @endswitch
                                        </td>
                                        <td scope="col" class="text-center">{{ $value->created_at }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-5">
                            {{ $list->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
