<footer class="footerContainer container px-0 fixed-bottom bg-white border-start border-end border-start-sm-0 border-end-sm-0 border-top">
    <div class="container">
        <div class="row text-center py-3">
           @if(auth()->user()->member->is_referral == 'y')
            <div class="col px-1 ">
                <a href="{{ route('register',['mid' => Auth::user()->id]) }}" class="text-decoration-none text-dark">
                    <img src="{{ asset('/images/icon/icon_menu_register.svg') }}" class="pb-1">
                    <div class="fs-3">{{ __('auth.join') }}</div>
                </a>
            </div>
            <div class="col px-1">
                <a href="#" class="text-decoration-none text-dark copyBtn" data-copy="{{ route('register', ['mid' => Auth::user()->id]) }}">
                    <img src="{{ asset('/images/icon/icon_menu_link.svg') }}" class="pb-1">
                    <div class="fs-3">{{ __('layout.referral_link') }}</div>
                </a>
            </div>
            @else
            <div class="col px-1 ">
                <span style="cursor:not-allowed">
                    <img src="{{ asset('/images/icon/icon_menu_register.svg') }}" class="pb-1" style="filter: brightness(200%) saturate(40%) opacity(60%);">
                    <div class="fs-3 text-decoration-none text-dark text-opacity-30">{{ __('auth.join') }}</div>
                </span>
            </div>
            <div class="col px-1">
                <span style="cursor:not-allowed">
                    <img src="{{ asset('/images/icon/icon_menu_link.svg') }}" class="pb-1" style="filter: brightness(200%) saturate(40%) opacity(60%);">
                    <div class="fs-3 text-decoration-none text-dark text-opacity-30">{{ __('layout.referral_link') }}</div>
                </span>
            </div>
            @endif
            <div class="col px-1">
                <a href="{{ route('home') }}" class="text-decoration-none text-dark">
                    <img src="{{ asset('/images/icon/icon_menu_home.svg') }}" class="pb-1">
                    <div class="fs-3">{{ __('layout.home') }}</div>
                </a>
            </div>
            <div class="col px-1">
                <a href="{{ route('profile.dashboard') }}" class="text-decoration-none text-dark">
                    <img src="{{ asset('/images/icon/icon_menu_team.svg') }}" class="pb-1">
                    <div class="fs-3">{{ __('user.dashboard') }}</div>
                </a>
            </div>
            <div class="col px-1">
                <!--a href="{{ route('board.list', ['code' => 'terms']) }}" class="text-decoration-none text-dark"-->
                <a href="#" class="text-decoration-none text-dark" onclick="alertModal('{{ __('system.coming_soon_notice') }}')">
                    <img src="{{ asset('/images/icon/icon_menu_terms.svg') }}" class="pb-1">
                    <div class="fs-3">{{ __('layout.terms') }}</div>
                </a>
            </div>
        </div>
    </div>
</footer>
