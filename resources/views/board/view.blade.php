@extends('layouts.master')

@section('content')
<main class="container-fluid py-5 mb-5">
    <div class="d-flex justify-content-between align-items-start flex-column">
        @if($board->board_code == 'notice')
        <span class="badge bg-info mb-3">{{ __('layout.notice') }}</span>
        @endif
        <h4 class="mb-2">{{ $view->subject }}</h4>
    </div>
    <div class="post-info mb-2">
        <span class="me-3"><i class="bi bi-clock"></i> {{ __('system.created_at') }}:{{ $view->created_at->format('Y-m-d') }}</span>
    </div>

    <div class="post-content py-4 px-2 mb-3 fs-4">
    @if ($board->is_popup == 'y')
        {!! $view->content !!}
    @else
        {!! nl2br(e($view->content)) !!}
    @endif
    </div>
    @if($download_urls)
    <div class="text-center align-middle">
        @foreach($download_urls as $val)
            <div class="mb-5">
                <a href="{{ $val }}">
                    <img src="{{ $val }}" class="img-fluid">
                </a>
            </div>
        @endforeach
    </div>
    @endif
    @if($board->is_comment == 'y')
    @if(!$comments->isEmpty())
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="card-title">{{ __('layout.comment_list') }}</h5>
    </div>
    <hr>
    <div class="list-group">
        @foreach($comments as $comment)
            <div class="list-group-item">
                <i class="ti ti-corner-down-right"></i>
                <strong>{{ $comment->user ? $comment->user->name : $comment->admin->name }}</strong>
                @if($comment->admin)
                    <span class="badge bg-danger">{{ __('system.admin') }}</span>
                @endif
                <div class="ms-4">
                    <p>{!! nl2br(e($comment->content)) !!}</p>
                    <small>{{ $comment->created_at->format('Y-m-d h:i:s') }}</small>
                </div>
            </div>
        @endforeach
    </div>
    @endif
    <form method="POST" action="{{ route('board.comment') }}" id="ajaxForm">
        @csrf
        <input type="hidden" name="board_id" value="{{ $board->id }}"/>
        <input type="hidden" name="post_id" value="{{ $view->id }}"/>
        <div class="d-flex align-items-center gap-2 mt-5">
            <textarea name="content" class="form-control flex-grow-1 w-75" id="content" rows="3" placeholder="{{ __('layout.comment_guide') }}"></textarea>
            <button type="submit" class="btn btn-info h-100">{{ __('system.write') }}</button>
        </div>
    </form>
    @endif
    <hr>
    <div class="d-flex justify-content-start align-items-center mb-5">
        <div>
            <a href="{{ route('board.list', ['code' => $board->board_code ])}}" class="btn btn-primary">{{ __('system.list') }}</a>
        </div>
    </div>
</main>
@endsection

@push('script')
@endpush
