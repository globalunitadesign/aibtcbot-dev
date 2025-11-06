@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        @if($mode == 'write')
                        게시글 작성
                        @else
                        게시글 수정
                        @endif
                    </h5>
                </div>
                @if($mode == 'write')
                <form method="POST" id="boardForm" action="{{ route('admin.post.write') }}" >
                @else
                <form method="POST" id="boardForm" action="{{ route('admin.post.modify') }}" >
                @endif
                    @csrf
                    <input type="hidden" name="board_id" value="{{ $board->id }}">
                    @if($mode == 'modify')
                    <input type="hidden" name="post_id" value="{{ $view->id }}">
                    <input type="hidden" name="image_urls" id="image_urls" value="{{ json_encode($view->image_urls) }}">
                    @endif
                    <hr>
                    <table class="table table-bordered mt-5 mb-5">
                        <tbody>
                            <tr>
                                <th class="text-center align-middle">제목</th>
                                <td colspan=3 class="align-middle">
                                    <input type="text" name="subject" id="subject" value="{{ $view->subject ?? '' }}" class="form-control">
                                </td>
                            </tr>
                            @if($board->is_popup == 'y')
                            <tr>
                                <th class="text-center align-middle">팝업</th>
                                <td colspan=3 class="align-middle">
                                @if(isset($view))
                                    <input type="radio" name="is_popup" value="y" id="is_popup" class="form-check-input" @if($view->is_popup == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_popup">활성</label>
                                    <input type="radio" name="is_popup" value="n" id="is_not_popup" class="form-check-input" @if($view->is_popup == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_popup">비활성</label>
                                @else
                                    <input type="radio" name="is_popup" value="y" id="is_popup" class="form-check-input">
                                    <label class="form-check-label me-3" for="is_popup">활성</label>
                                    <input type="radio" name="is_popup" value="n" id="is_not_popup" class="form-check-input" checked>
                                    <label class="form-check-label" for="is_not_popup">비활성</label>
                                @endif
                                </td>
                            </tr>
                            @endif
                            @if($board->is_banner == 'y')
                            <tr>
                                <th class="text-center align-middle">배너</th>
                                <td colspan=3 class="align-middle">
                                @if(isset($view))
                                    <input type="radio" name="is_banner" value="y" id="is_banner" class="form-check-input" @if($view->is_banner == 'y') checked @endif>
                                    <label class="form-check-label me-3" for="is_banner">활성</label>
                                    <input type="radio" name="is_banner" value="n" id="is_not_banner" class="form-check-input" @if($view->is_banner == 'n') checked @endif>
                                    <label class="form-check-label" for="is_not_banner">비활성</label>
                                @else
                                    <input type="radio" name="is_banner" value="y" id="is_banner" class="form-check-input">
                                    <label class="form-check-label me-3" for="is_banner">활성</label>
                                    <input type="radio" name="is_banner" value="n" id="is_not_banner" class="form-check-input" checked>
                                    <label class="form-check-label" for="is_not_banner">비활성</label>
                                @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th class="text-center align-middle">내용</th>
                                <td colspan=3 class="d-flex align-middle">
                                    <div id="editor" data-content="{{ $view->content ?? '' }}"></div>
                                    <textarea name="content" id="content" class="d-none"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.board.list', ['code' => $board->board_code ]) }}" class="btn btn-secondary">목록</a>
                        @if($mode == 'modify')
                        <button type="submit" class="btn btn-danger">수정</button>
                        @else
                        <button type="submit" class="btn btn-danger">작성</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@push('script')
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
<link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
<script src="{{ asset('js/admin/board/post.js') }}"></script>
@endpush
