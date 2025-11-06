@extends('layouts.master')

@section('content')
<main class="container-fluid py-5 mb-5">
    @if($mode == 'write')
    <form method="POST" action="{{ route('board.write') }}" id="boardForm">
    @else
    <form method="POST" action="{{ route('board.modify') }}" id="boardForm">
    @endif
        @csrf
        <input type="hidden" name="board_id" value="{{ $board->id }}">
        @if($mode == 'modify')
        <input type="hidden" name="post_id" value="{{ $view->id }}">
        @endif
        <div class="mb-4">
            <h5 class="card-title">
                @if($mode == 'write')
                {{ $board->locale_name }}
                @else
                {{ $board->locale_name }}
                @endif
            </h5>
        </div>
        <div class="mb-4">
            <label for="subject" class="form-label fw-bold">{{ __('system.title') }}</label>
            <input type="text" class="form-control" id="subject" name="subject" value="{{ $view->subject ?? '' }}" >
        </div>
        <div class="mb-4">
            <label for="content" class="form-label fw-bold">{{ __('system.contents') }}</label>
            <textarea name="content" id="content" class="form-control" rows="12"></textarea>
        </div>
        <div class="mb-4">
            <label for="content" class="form-label fw-bold">{{ __('etc.image_upload') }}</label>
            <div class="d-flex">
                <div class="preview-box" style="width: 60px; height: 60px; position: relative;">
                    <label style="display: block; width: 100%; height: 100%; position: relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="#adb5bd"
                             class="cursor-pointer svg-icon p-2"
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; cursor: pointer; border-radius: 4px;"
                             viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>
                        <img src="" class="img-preview d-none cursor-pointer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border: 3px solid #adb5bd; border-radius: 4px;">
                        <input type="file" name="image_urls[0]" class="file-input d-none" accept="image/jpeg, image/png">
                        <input type="hidden" name="file_key[]">
                    </label>
                </div>
                <div class="preview-box" style="width: 60px; height: 60px; position: relative;">
                    <label style="display: block; width: 100%; height: 100%; position: relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="#adb5bd"
                             class="cursor-pointer svg-icon p-2"
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; cursor: pointer; border-radius: 4px;"
                             viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>
                        <img src="" class="img-preview d-none cursor-pointer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border: 3px solid #adb5bd; border-radius: 4px;" />
                        <input type="file" name="image_urls[1]" class="file-input d-none" accept="image/jpeg, image/png">
                        <input type="hidden" name="file_key[]">
                    </label>
                </div>
                <div class="preview-box" style="width: 60px; height: 60px; position: relative;">
                    <label style="display: block; width: 100%; height: 100%; position: relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="#adb5bd"
                             class="cursor-pointer svg-icon p-2"
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; cursor: pointer; border-radius: 4px;"
                             viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>
                        <img src="" class="img-preview d-none cursor-pointer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border: 3px solid #adb5bd; border-radius: 4px;" />
                        <input type="file" name="image_urls[2]" class="file-input d-none" accept="image/jpeg, image/png">
                        <input type="hidden" name="file_key[]">
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <div class="d-flex justify-content-end align-items-center">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <a href="{{ route('board.list', ['code' => $board->board_code ])}}" class="btn btn-secondary">{{ __('system.list') }}</a>
                    @if($mode == 'write')
                    <button type="submit" class="btn btn-inverse">{{ __('layout.submit_request') }}</button>
                    @else
                    <button type="submit" class="btn btn-info">{{ __('system.modify') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</main>
@endsection

@push('message')
<div id="msg_input_title" data-label="{{ __('layout.input_title_notice') }}"></div>
<div id="msg_input_contents" data-label="{{ __('layout.input_contents_notice') }}"></div>
@endpush

@push('script')
    <script src="{{ asset('js/board/post.js') }}"></script>
@endpush
