@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">게시글</h5>
                </div>
                <hr>
                <table class="table table-bordered mt-5 mb-5">
                    <tbody>
                        <tr>
                            <th class="text-center align-middle w-15">게시판</th>
                            <td class="align-middle w-20">{{ $board->board_name }}</td>
                            <th class="text-center align-middle w-15">아이디</th>
                            <td class="text-center w-20">
                                @if ($view->admin)
                                    {{ $view->admin->account }} <span class="badge bg-danger">관리자</span>
                                @elseif ($view->user)
                                    {{ $view->user->account }}
                                @else
                                    -
                                @endif
                            </td>
                            <th class="text-center align-middle w-15">이름</th>
                            <td class="align-middle w-20">
                                @if ($view->admin)
                                    {{ $view->admin->name }}
                                @elseif ($view->user)
                                    {{ $view->user->name }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">제목</th>
                            <td colspan=5 class="align-middle">
                                {{ $view->subject }}
                                @if ($view->is_popup == 'y')
                                    <span class="badge bg-success">팝업</span>
                                @endif
                                @if ($view->is_banner == 'y')
                                <span class="badge bg-info">배너</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">내용</th>
                            <td colspan=5 class="align-middle">
                                @if ($board->is_popup == 'y')
                                    {!! $view->content !!}
                                @else
                                    {!! nl2br(e($view->content)) !!}
                                @endif
                            </td>
                        </tr>
                        @if($board->is_popup == 'n' && $download_urls)
                        <tr>
                            <th class="text-center align-middle">이미지</th>
                            <td colspan=5 class="align-middle">
                                <div class="text-center align-middle">
                                    @foreach($download_urls as $val)
                                        <a href="{{ $val }}">
                                            <img src="{{ $val }}" class="img-fluid me-5" style="height:300px">
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <hr>
                @if(!$comments->isEmpty())
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">답글</h5>
                </div>
                <hr>
                <div class="list-group">
                    @foreach($comments as $comment)
                    <div class="list-group-item">
                        <i class="ti ti-corner-down-right"></i>
                        <strong>{{ $comment->user ? $comment->user->name : $comment->admin->name }}</strong>
                        @if($comment->admin)
                            <span class="badge bg-danger">관리자</span>
                        @endif
                        <div class="ms-4">
                            <p>{!! nl2br(e($comment->content)) !!}</p>
                            <small>{{ $comment->created_at->format('Y-m-d h:i:s') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr>
                @endif
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                        <a href="{{ route('admin.post.list', ['code' => $board->board_code ]) }}" class="btn btn-secondary">목록</a>
                        @if($board->is_comment == 'y')
                            <a href="{{ route('admin.post.view', ['code' => $board->board_code, 'mode' => 'comment', 'id' => $view->id ]) }}" class="btn btn-info ms-2">답글 달기</a>
                        @endif
                    </div>
                    <a href="{{ route('admin.post.view', ['code' => $board->board_code, 'mode' => 'modify', 'id' => $view->id ]) }}" class="btn btn-danger">게시글 수정</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
