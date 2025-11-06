@extends('layouts.master')

@section('content')
<main class="container-fluid py-5 mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <h3>{{ __('user.user_info') }}</h3>    
    </div>
    <form method="POST" action="{{ route('profile.update') }}" id="ajaxForm" class="mb-5">
        @csrf
        <input type="hidden" name="id" value="{{ $view->user_id }}">
        <hr>
        <div class="table-responsive overflow-x-auto">
            <table class="table table-bordered my-5">
                <tbody>
                    <tr>
                        <th width="30%" class="text-center text-body align-middle">{{ __('user.name') }}</th>
                        <td width="70%" class="align-middle text-body">{{ $view->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-center text-body align-middle">{{ __('UID') }}</th>
                        <td class="align-middle text-body">{{ $view->user_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-center text-body align-middle">{{ __('auth.password') }}</th>
                        <td class="align-middle text-body"><a href="{{ route('profile.password') }}" class="btn btn-info btn-sm">{{ __('auth.reset_password') }}</a></td>
                    </tr>
                    <tr>
                        <th class="text-center text-body align-middle">{{ __('user.email') }}</th>
                        <td class="align-middle text-body">{{ $view->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-center text-body align-middle">{{ __('user.phone') }}</th>
                        <td class="align-middle text-body">
                            <input type="text" name="phone" value="{{ $view->phone }}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center text-body align-middle">{{ __('user.kyc_verification') }}</th>
                        <td class="align-middle text-body">
                            @if (!$view->user->kyc)
                            <a class="btn btn-info btn-sm px-4" href="{{ route('kyc') }}">{{ __('auth.verify') }}</a>
                            @elseif ($view->user->kyc->status === 'pending')
                            {{ __('auth.verified_pending') }}
                            @elseif ($view->user->kyc->status === 'rejected')
                            {{ __('auth.verified_failed') }} <a class="btn btn-info btn-sm px-4 m-0 ms-2" href="{{ route('kyc') }}">{{ __('auth.verify') }}</a>
                            <p class="m-0 mt-2 text-danger fw-semibold">{{ $view->user->kyc->memo }}</p>
                            @else 
                            {{ __('auth.verified_success') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center text-body align-middle">{{ __('user.otp_connect') }}</th>
                        <td class="align-middle text-body">
                            @if (!$view->user->otp || !$view->user->otp->secret_key)
                            {{ __('user.connect_unlinked') }}
                            @else 
                            {{ __('user.connect_linked') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center text-body align-middle">{{ __('user.meta_id') }}</th>
                        <td class="align-middle text-body">
                            <input type="text" name="meta_uid" value="{{ $view->meta_uid }}" class="form-control"  {{ $view->meta_uid ? 'readonly' : '' }}>
                            <div class="alert alert-danger mt-4 mb-2" role="alert">
                                <h6 class="text-danger text-center fw-bold fs-4 m-0 lh-base">{{ __('user.meta_id_guide_1') }}</h6>
                            </div>
                            <p class="mb-4">
                                {{ __('user.meta_id_guide_2') }}
                            </p>
                        </td>
                    </tr>
                    <!--tr>
                        <th class="text-center text-body align-middle">{{ __('messages.member.address') }}</th>           
                        <td>
                            <div class="d-flex mb-3 align-middle text-body">
                                <div class="col-6 me-2">
                                    <input type="text" name="post_code" id="postcode" placeholder="{{ __('messages.member.postcode') }}"  class="form-control" value="{{ $view->post_code }}">
                                </div>
                                <button type="button" onclick="daumPostcode()" class="btn btn-outline-primary btn-sm me-2">{{ __('messages.member.find_postcode') }}</button>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="address" id="address" placeholder="{{ __('messages.member.address') }}"  class="form-control me-2" value="{{ $view->address }}">
                            </div>
                            <div>
                                <input type="text" name="detail_address" id="detailAddress" placeholder="{{ __('messages.member.detail_address') }}"  class="form-control" value="{{ $view->detail_address }}">
                            </div>
                        </td>
                    </tr-->
                </tbody>
            </table>
        </div>
        <hr>
        <div class="d-flex justify-content-end mb-5">
            <button type="submit" class="btn btn-info">{{ __('system.save') }}</button>
        </div>
    </form>
</main>
<form method="POST" id="confirmForm" >
    @csrf
</form>
@endsection

@push('script')
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="{{ asset('js/postcode.js') }}"></script>
@endpush