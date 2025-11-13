@extends('layouts.master')

@section('content')

<header class="px-4 py-5 w-100 border-top-title" style="background: url('../images/tit_bg_01.png') center right no-repeat, #1e1e1f;" >
    <h2 class="text-white mb-1 px-1">Dashboard</h2>
    <h6 class="text-white px-1">{{ __('user.dashboard') }}</h6>
</header>
<main class="container-fluid py-5 mb-5">
    <div class="px-3 mb-5">
        <ul class="nav nav-underline mb-3 fs-6" id="dashboard-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="dashboard-mypage-tab" data-bs-toggle="pill" data-bs-target="#dashboard-mypage" type="button" role="tab" aria-controls="dashboard-mypage" aria-selected="true">{{ __('asset.my_info') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dashboard-myteam-tab" data-bs-toggle="pill" data-bs-target="#dashboard-myteam" type="button" role="tab" aria-controls="dashboard-myteam" aria-selected="false">{{ __('asset.team_info') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dashboard-avatar-tab" data-bs-toggle="pill" data-bs-target="#dashboard-avatar" type="button" role="tab" aria-controls="dashboard-avatar" aria-selected="false">{{ __('user.avatar') }}</button>
            </li>
        </ul>
        <div class="tab-content" id="dashboard-tabContent">
            <div class="tab-pane fade show active" id="dashboard-mypage" role="tabpanel" aria-labelledby="dashboard-mypage-tab" tabindex="0">
                <p class="py-3 fs-4">
                    {{ __('user.level') }}<span class="text-body fw-semibold ps-2 d-inline-block">{{ $data['grade'] }}</span>
                </p>
                <div class="p-4 rounded bg-light text-body mb-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <p class="text-body fs-4 m-0">{{ __('mining.total_node') }}</p>
                            <h3 class="text-primary fs-6 mb-1">{{ $data['total_node_amount'] }}</h3>
                        </div>
                    </div>
                    @foreach ($data['total_staking'] as $coin => $staking)
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <p class="text-body fs-4 m-0">{{ __('mining.total_staking').' ('.$coin.')' }}</p>
                                <h3 class="text-primary fs-6 mb-1">{{ $staking }}</h3>
                            </div>
                        </div>
                    @endforeach
                    @foreach ($data['total_reward'] as $coin => $reward)
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <p class="text-body fs-4 m-0">{{ __('mining.total_mining').' ('.$coin.')' }}</p>
                                <h3 class="text-primary fs-6 mb-1">{{ $reward }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="tab-pane fade show" id="dashboard-myteam" role="tabpanel" aria-labelledby="dashboard-myteam-tab" tabindex="0">
                <div class="p-4 rounded bg-light text-body mb-4 mt-5">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <p class="text-body fs-4 m-0">{{ __('asset.referral_count') }}</p>
                            <h3 class="text-primary fs-6 mb-1">{{ $data['direct_count'] }}</h3>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <p class="text-body fs-4 m-0">{{ __('asset.child_count') }}</p>
                            <h3 class="text-primary fs-6 mb-1">{{ $data['all_count'] }}</h3>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <p class="text-body fs-4 m-0">{{ __('asset.total_group_sales') }}</p>
                            <h3 class="text-primary fs-6 mb-1">{{ $data['group_sales'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="dashboard-avatar" role="tabpanel" aria-labelledby="dashboard-avatar-tab" tabindex="0">
                <p class="py-3 fs-4">
                    {{ __('user.avatar') }} {{ __('system.amount') }}<span class="text-body fw-semibold ps-2 d-inline-block">{{ $data['avatars']->count() }}</span>
                </p>
                @foreach ($data['avatars'] as $avatar)
                <div class="p-4 rounded bg-light text-body mb-4">
                    <div class="d-flex justify-content-between mb-3">
                        <p class="text-body fs-4 m-0">{{ __('user.avatar') }} {{ $loop->iteration }}</p>
                        @if ($avatar->is_active === 'n')
                        <p class="text-danger mb-1">비활성</p>
                        @else
                        <p class="text-primary mb-1">활성</p>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-body fs-4 m-0">{{ $avatar->name }}</p>
                        <a href="{{ route('avatar.view', ['id' => $avatar->id]) }}">
                            <p class="text-body fs-4 mb-1">계정확인</p>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</main>
@endsection
