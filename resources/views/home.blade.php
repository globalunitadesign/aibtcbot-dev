@extends('layouts.master')

@section('content')
<main class="homeContainer container py-5 position-relative">
    <div class="pb-3 mb-4">
        <div class="d-flex justify-content-start align-items-center">
            <p class="mb-2 pe-3 fs-4">
                {{ __('user.level') }}<span class="fw-semibold d-inline-block ps-2">{{ Auth::user()->member->grade->name }}</span>
            </p>
            <p class="mb-2 divider ps-3 position-relative fs-4">
                UID<span class="fw-semibold d-inline-block ps-2">U{{ Auth::user()->id }}</span>
            </p>
            <a href="#">
                <p type="button" class="mb-2 fs-4 ps-2 copyBtn" data-copy="U{{ Auth::user()->id }}">{{ __('system.copy') }}</p>
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <!-- <h4 class="m-0 fs-6 lh-md pe-1">{{ Auth::user()->name }} <span class="fw-normal lh-base">{{ __('messages.member.member_welcome') }}</span></h4> -->
                <h4 class="m-0 fs-6 pe-1"><span class="fw-normal lh-base">Hello, </span>{{ Auth::user()->name }}</h4>
                <p class="m-0 pe-1 text-body opacity-50">Make crypto work smarter for you</p>
            </div>
            <div>
                <a href="{{ route('profile') }}" class="btn btn-dark w-100 text-decoration-none d-flex p-0">
                    <p class="small mb-0 px-3 py-2">MY</p>
                </a>
            </div>
        </div>
    </div>
    <div class="mb-2">
        @isset($notice)
        <a href="{{ route('board.view', ['code' => $notice->board->board_code, 'mode' => 'view', 'id' => $notice->id]) }}" >
            <div class="alert alert-light d-flex" role="alert">
                <img src="{{ asset('images/icon/icon_notice.svg') }}">
                <p class="fs-3 ms-2 mb-0 flex-grow-1">{{ $notice->subject }}</p>
            </div>
        </a>
        @endif
    </div>
    <div id="carouselExampleCaptions" data-bs-ride="carousel" data-bs-interval="2400" class="carousel slide mb-5">
        <div class="carousel-indicators opacity-25" style="filter: invert(1);">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/bit_ban1.jpg') }}" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <!-- <h5>First slide label</h5>
                    <p>Some representative placeholder content for the first slide.</p> -->
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/bit_ban2.jpg') }}" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <!-- <h5>Second slide label</h5>
                    <p>Some representative placeholder content for the second slide.</p> -->
                </div>
            </div>
            <div class="carousel-item">
                <!--a href="{{ route('board.list', ['code' =>'product'])}}"-->
                <a href="#" onclick="alertModal('{{ __('system.coming_soon_notice') }}')">
                    <img src="{{ asset('images/bit_ban3.jpg') }}" class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <!-- <h5>Third slide label</h5>
                        <p>Some representative placeholder content for the third slide.</p> -->
                    </div>
                </a>
            </div>
        </div>
        <!-- <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon opacity-25" style="filter: invert(1);" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon opacity-25" style="filter: invert(1);" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button> -->
    </div>
    <div class="card maincard mb-5 text-center">
        <div class="card-body py-4 px-0">
            <nav>
                <div class="nav justify-content-center" id="nav-tab" role="tablist">
                    <button class="nav-link nav-asset active ps-3 pe-3" id="nav-asset-tab" data-bs-toggle="tab" data-bs-target="#nav-asset" type="button" role="tab" aria-controls="nav-asset" aria-selected="true">
                        <h5 class="link-card-tab fs-5 my-3">{{ __('asset.assets_held') }}</h5>
                    </button>
                    <button class="nav-link nav-wallet ps-3 pe-3 position-relative divider-w" id="nav-wallet-tab" data-bs-toggle="tab" data-bs-target="#nav-wallet" type="button" role="tab" aria-controls="nav-wallet" aria-selected="false">
                        <h5 class="link-card-tab fs-5 my-3">{{ __('asset.income_wallet') }}</h5>
                    </button>
                    @if($avatar_data->isNotEmpty())
                    <button class="nav-link nav-wallet ps-3 pe-3 position-relative divider-w" id="nav-avatar-tab" data-bs-toggle="tab" data-bs-target="#nav-avatar" type="button" role="tab" aria-controls="nav-avatar" aria-selected="false">
                        <h5 class="link-card-tab fs-5 my-3">{{ __('user.avatar') }}</h5>
                    </button>
                    @endif
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-asset" role="tabpanel" aria-labelledby="nav-asset-tab" tabindex="0">
                    <div>
                    @php
                        $firstTwo = $assets->take(2);
                        $remaining = $assets->slice(2);
                    @endphp
                        @foreach($firstTwo as $asset)
                        <div class="d-flex justify-content-between align-items-center pt-4 px-4">
                            <h6 class="text-white fs-4 fw-normal lh-md m-0">{{ $asset->coin->name }}</h6>
                            <h4 class="fw-bold text-white fs-5 fs-md-6 text-end flex-grow-1 m-0 px-1">{{ number_format(floor( $asset->balance * 10000) / 10000, 4) }}</h4>
                            <a href="{{ route('asset', ['id' => $asset->encrypted_id]) }}">
                                <span class="btn btn-outline-light btn-sm py-1 px-3 ms-2 break-keep-all">{{ __('system.detail') }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @if($remaining->isNotEmpty())
                    <div class="collapse-box">
                        <div class="collapse" id="collapseBox">
                            @foreach($remaining as $asset)
                            <div class="d-flex justify-content-between align-items-center pt-4 px-4">
                                <h6 class="text-white fs-4 fw-normal lh-md m-0">{{ $asset->coin->name }}</h6>
                                <a href="{{ route('asset', ['id' => $asset->encrypted_id]) }}">
                                    <h4 class="fw-bold text-white fs-6 fs-md-6 m-0">{{ number_format(floor( $asset->balance * 10000) / 10000, 4) }}<span class="btn btn-outline-light btn-sm py-1 px-3 mb-1 ms-2">{{ __('system.detail') }}</span></h4>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <a class="d-block mt-4 mb-2 opacity-75 text-white" style="height: 24px;" data-bs-toggle="collapse" href="#collapseBox" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <span class="fs-4 mb-4"></span>
                        </a>
                    </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="nav-wallet" role="tabpanel" aria-labelledby="nav-wallet-tab" tabindex="0">
                    <div>
                    @php
                        $firstTwo = $incomes->take(2);
                        $remaining = $incomes->slice(2);
                    @endphp
                        @foreach($firstTwo as $income)
                        <div class="d-flex justify-content-between align-items-center pt-4 px-4">
                            <h6 class="text-white fs-4 fw-normal lh-md m-0">{{ $income->coin->name }}</h6>
                            <h4 class="fw-bold text-white fs-6 fs-md-6 text-end flex-grow-1 m-0 px-1">{{ number_format(floor( $income->balance * 10000) / 10000, 4) }}</h4>
                            <a href="{{ route('income', ['id' => $income->encrypted_id]) }}">
                                <span class="btn btn-outline-light btn-sm py-1 px-3 ms-2">{{ __('system.detail') }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @if($remaining->isNotEmpty())
                    <div class="collapse-box">
                        <div class="collapse" id="collapseBox">
                            @foreach($remaining as $income)
                            <div class="d-flex justify-content-between align-items-center pt-4 px-4">
                                <h6 class="text-white fs-4 fw-normal lh-md m-0">{{ $income->coin->name }}</h6>
                                <a href="{{ route('income', ['id' => $income->encrypted_id]) }}">
                                    <h4 class="fw-bold text-white fs-6 fs-md-6 m-0">{{ number_format(floor( $income->balance * 10000) / 10000, 4) }}<span class="btn btn-outline-light btn-sm py-1 px-3 mb-1 ms-2">{{ __('system.detail') }}</span></h4>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <a class="d-block mt-4 mb-2 opacity-75 text-white" style="height: 24px;" data-bs-toggle="collapse" href="#collapseBox" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <span class="fs-4 mb-4"></span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @if($avatar_data->isNotEmpty())
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade" id="nav-avatar" role="tabpanel" aria-labelledby="nav-avatar-tab" tabindex="0">
                    <div>
                        @php
                            $firstTwo = $avatar_data->take(2);
                            $remaining = $avatar_data->slice(2);
                        @endphp
                        @foreach($firstTwo as $income)
                            <div class="d-flex justify-content-between align-items-center pt-4 px-4">
                                <h6 class="text-white fs-4 fw-normal lh-md m-0">{{ $income['code'] }}</h6>
                                <h4 class="fw-bold text-white fs-6 fs-md-6 text-end flex-grow-1 m-0 px-1">{{ number_format(floor( $income['balance'] * 10000) / 10000, 4) }}</h4>
                                <a href="{{ route('income.avatar', ['id' => $income['id'] ]) }}">
                                    <span class="btn btn-outline-light btn-sm py-1 px-3 ms-2">{{ __('system.detail') }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if($remaining->isNotEmpty())
                        <div class="collapse-box">
                            <div class="collapse" id="collapseBox">
                                @foreach($remaining as $income)
                                    <div class="d-flex justify-content-between align-items-center pt-4 px-4">
                                        <h6 class="text-white fs-4 fw-normal lh-md m-0">{{ $income['code'] }}</h6>
                                        <a href="{{ route('income', ['id' => $income['id'], 'type' => 'avatar' ]) }}">
                                            <h4 class="fw-bold text-white fs-6 fs-md-6 m-0">{{ number_format(floor( $income['balance'] * 10000) / 10000, 4) }}<span class="btn btn-outline-light btn-sm py-1 px-3 mb-1 ms-2">{{ __('system.detail') }}</span></h4>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <a class="d-block mt-4 mb-2 opacity-75 text-white" style="height: 24px;" data-bs-toggle="collapse" href="#collapseBox" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <span class="fs-4 mb-4"></span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="mb-5">
        <h5 class="mb-3"></h5>
        <div class="d-flex justify-content-start align-items-start mb-3">
            <a href="{{ route('asset.deposit') }}" class="link-body-emphasis w-100" style="max-width: 25%">
                <div class="d-flex align-items-center flex-column">
                    <img src="{{ asset('/images/icon/icon_main_deposit.png') }}" width="44" class="mb-1">
                    <p class="m-0 fw-medium fs-3 text-center">{{ __('asset.deposit') }}</p>
                </div>
            </a>
            {{--
            <a href="{{ route('asset.withdrawal') }}" class="link-body-emphasis w-100" style="max-width: 25%">
                <div class="d-flex align-items-center flex-column">
                    <img src="{{ asset('/images/icon/icon_main_withdrawal.png') }}" width="44" class="mb-1">
                    <p class="m-0 fw-medium fs-3 text-center">{{ __('asset.withdrawal') }}</p>
                </div>
            </a>
            <a href="{{ route('trading') }}" class="link-body-emphasis w-100" style="max-width: 25%">
                <div class="d-flex align-items-center flex-column">
                <img src="{{ asset('/images/icon/icon_main_trading.png') }}" width="44" class="mb-1">
                    <p class="m-0 fw-medium fs-3 text-center">{{ __('asset.trading') }}</p>
                </div>
            </a>
            --}}
            <a href="{{ route('mining') }}" class="link-body-emphasis w-100" style="max-width: 25%">
                <div class="d-flex align-items-center flex-column">
                <img src="{{ asset('/images/icon/icon_main_investment.png') }}" width="44" class="mb-1">
                    <p class="m-0 fw-medium fs-3 text-center">{{ __('AI BTC BOT') }}</p>
                </div>
            </a>
        </div>
    </div>
    {{--
    <div class="pb-5">
        <h5 class="mb-3">{{ __('etc.crypto_price') }}</h5>
        <div class="row g-3">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="30%" scope="col" class="text-gray vertical-top">{{ __('etc.trade_pair') }}</th>
                        <th width="40%" scope="col" class="text-gray vertical-top">{{ __('etc.current_price') }}</th>
                        <th width="30%" scope="col" class="text-gray vertical-top">{{ __('etc.change_rate') }}</th>
                    </tr>
                </thead>
                <tbody id="crypto-prices-tbody">
                    <tr>
                        <th scope="row">BTC</th>
                        <td>121,960,000</td>
                        <td>+0.56%</td>
                    </tr>
                </tbody>
                </table>
        </div>
    </div>
    --}}
    <!-- <div class="d-flex card text-center rounded-0" style="background: #070707; margin-bottom: 100px;">
        <div class="card-body p-4 position-relative" style="background: url('images/mockup.png') center bottom no-repeat; background-size: 620px; height: 640px;">
            <h5 class="text-white pt-4 opacity-50">Building Decentralized</h5>
            <h3 class="text-white pb-4">Innovation<br>Together</</h3>
            {{--
            <a href="{{ route('staking') }}" class="position-absolute w-100 px-3" style="bottom: 0; left: 0;">
                <button class="btn btn-primary w-100 py-3 my-5 fs-4">Stake Now <span class="opacity-50">&</span> Earn Rewards!</button>
            </a>
            --}}
        </div>
    </div> -->
    <div class="position-relative width100_div" style="height: auto; margin-bottom: 100px;">
        <div class="position-absolute w-100 text-center video_text">
            <!-- <h5 class="text-white pt-5 opacity-50">Building Decentralized</h5> -->
            <h5 class="text-white pb-4">Bitcoin is an innovative payment <br> network and  a new kind of money.</h5>
        </div>
        <video class="bg-video__content" autoplay="" muted="" loop="" playsinline="">
            <source src="{{ asset('images/bit_video_bottom.mp4') }}" type="video/mp4" />
        </video>
    </div>
</main>
@isset($popup)
@include('components.popup-form')
@endif
@endsection

@push('script')
<script src="{{ asset('js/crypto.js') }}"></script>
@endpush

