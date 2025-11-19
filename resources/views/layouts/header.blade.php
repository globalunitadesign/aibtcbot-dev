
<div class="headerContainer container fixed-top" style="height: 1px; z-index: 1040;">
    <div class="offcanvas offcanvas-start vh-100 position-absolute" id="sidebar" tabindex="-1">
        <div class="offcanvas-header py-3 bg-dark">
            <h1 class="offcanvas-title text-white flex-grow-1 text-center fs-7 my-1"><img src="{{ asset('/images/logos/logo_bit_w.svg') }}" height="26" alt="" class="me-2"></h1>
            <button type="button" class="btn-close flex-grow-0" data-bs-dismiss="offcanvas" style="filter: invert(1);"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="container-fluid px-1 bg-dark">
                <div class="row text-center py-5 mx-1 text-decoration-none text-light">
                    <form method="POST" id="logoutForm" class="col px-1 cursor-pointer" action="{{ route('logout') }}" >
                        @csrf
                        <div onclick="logout();">
                            <img src="{{ asset('/images/icon/icon_nav_menu_logout.svg') }}" class="mb-2">
                            <div class="text-white fs-4">{{ __('auth.logout') }}</div>
                        </div>
                    </form>
                    <div class="col px-1">
                        <a class="nav-link text-inverse" href="{{ route('board.list', ['code' =>'notice'])}}">
                            <img src="{{ asset('/images/icon/icon_nav_menu_notice.svg') }}" class="mb-2">
                            <div class="text-white fs-4">{{ __('layout.notice') }}</div>
                        </a>
                    </div>
                    <div class="col px-1">
                        <a class="nav-link text-inverse" href="{{ route('board.list', ['code' =>'qna'])}}">
                            <img src="{{ asset('/images/icon/icon_nav_menu_cs.svg') }}" class="mb-2">
                            <div class="text-white fs-4">{{ __('layout.qna') }}</div>
                        </a>
                    </div>
                    <div class="col px-1">
                        <a class="nav-link text-inverse" href="{{ route('profile') }}">
                            <img src="{{ asset('/images/icon/icon_nav_menu_user_info.svg') }}" class="mb-2">
                            <div class="text-white fs-4">{{ __('user.user_info') }}</div>
                        </a>
                    </div>
                </div>
            </div>
            <ul class="navbar-nav px-3" style="margin-bottom: 40px;">
                <li class="border-bottom">
                    <div class="nav-item d-flex justify-content-between align-items-center fs-5 py-3">
                        <div class="ms-3 nav-link text-inverse">{{ __('layout.dark_mode') }}</div>
                        <div class="form-check form-switch">
                            <button class="btn btn-dark" id="themeBtn">On</button>
                        </div>
                    </div>
                </li>
                <li class="border-bottom">
                    <a href="#" class="nav-link text-inverse fs-5 pb-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#language-collapse" aria-expanded="false">
                        <div class="nav-item d-flex justify-content-between align-items-center py-3">
                            <div class="ms-3">{{ __('Language') }}</div>
                            <i class="ti ti-chevron-right collapse-i"></i>
                        </div>
                    </a>
                    <div class="collapse" id="language-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-4 ms-4">
                            @foreach ($locales as $locale)
                                <li><li><a href="{{ route('change.language' ,['locale' => $locale['code']]) }}" class="link-gray d-inline-flex py-2 text-decoration-none fs-4">{{ $locale['name'] }}</a></li></li>
                            @endforeach
                        </ul>
                    </div>
                </li>
                {{--
                <a class="nav-link text-inverse fs-5 pb-0" href="{{ route('chart.ref') }}">
                   <li class="nav-item d-flex justify-content-between align-items-center border-bottom py-3">
                       <div class="ms-3">{{ __('layout.direct_referral_tree') }}</div>
                       <i class="ti ti-chevron-right"></i>
                   </li>
               </a>
               <a class="nav-link text-inverse fs-5 pb-0" href="{{ route('chart.aff') }}">
                   <li class="nav-item d-flex justify-content-between align-items-center border-bottom py-3">
                       <div class="ms-3">{{ __('layout.downline_tree') }}</div>
                       <i class="ti ti-chevron-right"></i>
                   </li>
               </a>
               <a class="nav-link text-inverse fs-5 pb-0" href="{{ route('profile.dashboard') }}">
                   <li class="nav-item d-flex justify-content-between align-items-center border-bottom py-3">
                       <div class="ms-3">{{ __('user.dashboard') }}</div>
                       <i class="ti ti-chevron-right"></i>
                   </li>
               </a>
               <a class="nav-link text-inverse fs-5 pb-0" href="{{ route('about') }}">
                   <li class="nav-item d-flex justify-content-between align-items-center border-bottom py-3">
                       <div class="ms-3">{{ __('layout.company_about') }}</div>
                       <i class="ti ti-chevron-right"></i>
                   </li>
               </a>
               --}}
                <a class="nav-link text-inverse fs-5 pb-0" href="{{ route('board.list', ['code' =>'product'])}}">
                    <li class="nav-item d-flex justify-content-between align-items-center border-bottom py-3">
                        <div class="ms-3">{{ __('layout.product_intro') }}</div>
                        <i class="ti ti-chevron-right"></i>
                    </li>
                </a>
                <a class="nav-link text-inverse fs-5 pb-0" href="{{ route('board.list', ['code' =>'guide'])}}">
                    <li class="nav-item d-flex justify-content-between align-items-center border-bottom py-3">
                        <div class="ms-3">{{ __('layout.guidebook') }}</div>
                        <i class="ti ti-chevron-right"></i>
                    </li>
                </a>
                <a class="nav-link text-inverse fs-5 pb-0" href="{{ route('board.list', ['code' =>'terms'])}}">
                    <li class="nav-item d-flex justify-content-between align-items-center border-bottom py-3">
                        <div class="ms-3">{{ __('layout.terms') }}</div>
                        <i class="ti ti-chevron-right"></i>
                    </li>
                </a>
            </ul>
        </div>
    </div>
</div>
<nav class="headerContainer container px-0 navbar bg-dark fixed-top py-3 border-start border-end border-start-sm-0 border-end-sm-0" style="height: 72px;">
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center w-100">
            @if( !Request::is('home') )
            <a href="{{ url()->previous() }}" class="navbar-brand fs-6 nav-link text-inverse m-0 d-flex justify-content-center align-items-center" style="width: 54px;">
                <i class="ti ti-chevron-left fs-7" style="filter: brightness(0) invert(1);"></i>
            </a>
            @else
            <div style="width: 54px;"></div>
            @endif
            <div class="flex-grow-1 text-center">
                <a class="navbar-brand fs-6 text-black fw-semibold m-0" href="{{ route('home') }}">
                    <h1 class="fs-7 m-0 p-0">
                        <img src="{{ asset('/images/logos/logo_bit_w.svg') }}" height="26" alt="">
                    </h1>
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" style="width: 54px;">
                <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
            </button>
        </div>
    </div>
</nav>
