<aside class="left-sidebar">
    <div>

        <div class="mt-5 mb-5">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <a href="{{ route('admin') }}" class="text-nowrap logo-img">
                    <img src="{{ asset('images/logos/symbol.png') }}">
                </a>
            </div>
            <p class="mb-3 text-center">어서오세요, <a href="{{ route('admin.manager.view', ['id' => auth()->guard('admin')->user()->id])}}">{{ Auth::guard('admin')->user()->name }}</a>님</p>
            <div class="d-flex justify-content-center align-items-center">
                <a href="{{ route('home') }}" class="btn btn-outline-primary mx-2">Home</a>
                <form method="POST" action=" {{ route('admin.logout') }}" >
                    @csrf
                    <button type="submit" class="btn btn-outline-danger mx-2">Logout</button>
                </form>
            </div>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">회원 관리</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.user.list') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="hide-menu">회원</span>
                    </a>
                </li>
                @if (auth()->guard('admin')->user()->admin_level >= 3 )
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.user.grade') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="hide-menu">등급</span>
                    </a>
                </li>
                @endif
                {{--
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('chart.ref', ['admin' => 1]) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-sitemap"></i>
                        </span>
                        <span class="hide-menu">추천 조직도</span>
                    </a>
                </li>
                --}}
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('chart.aff', ['admin' => 1]) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-sitemap-filled"></i>
                        </span>
                        <span class="hide-menu">산하 조직도</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.user.kyc.list') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-lock"></i>
                        </span>
                        <span class="hide-menu">KYC 인증</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-6" class="fs-6"></iconify-icon>
                    <span class="hide-menu">자금 관리</span>
                </li>
                @if (auth()->guard('admin')->user()->admin_level >= 3 )
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.coin') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-coin"></i>
                        </span>
                        <span class="hide-menu">코인</span>
                    </a>
                </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.asset.list', ['type' => 'deposit']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-cash-banknote"></i>
                        </span>
                        <span class="hide-menu">자산</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.income.list', ['type' => 'deposit']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-gift"></i>
                        </span>
                        <span class="hide-menu">수익</span>
                    </a>
                </li>
                @if (auth()->guard('admin')->user()->admin_level >= 2 )
                {{--
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.trading.list') }}" aria-expanded="false">
                        <span>
                        <i class="ti ti-exchange"></i>
                        </span>
                        <span class="hide-menu">트레이딩</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.staking.list', ['status' => 'pending']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-chart-bar"></i>
                        </span>
                        <span class="hide-menu">스테이킹</span>
                    </a>
                </li>
                --}}
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.mining.list', ['status' => 'pending']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-diamond"></i>
                        </span>
                        <span class="hide-menu">마이닝</span>
                    </a>
                </li>
                @endif
                @if (auth()->guard('admin')->user()->admin_level >= 3 )
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4" class="fs-6"></iconify-icon>
                    <span class="hide-menu">정책 관리</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.user.policy', ['mode' => 'grade']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-user-off"></i>
                        </span>
                        <span class="hide-menu">회원 정책</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.asset.policy') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-cash-banknote-off"></i>
                        </span>
                        <span class="hide-menu">자산 정책</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.marketing.list') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-shopping-cart-off"></i>
                    </span>
                        <span class="hide-menu">마케팅 정책</span>
                    </a>
                </li>
                {{--
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.trading.policy') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-exchange-off"></i>
                        </span>
                        <span class="hide-menu">트레이딩 정책</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.staking.policy', ['id' => '1']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-chart-bar-off"></i>
                        </span>
                        <span class="hide-menu">스테이킹 정책</span>
                    </a>
                </li>
                --}}
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.mining.policy', ['id' => '1']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-diamond-off"></i>
                        </span>
                        <span class="hide-menu">마이닝 정책</span>
                    </a>
                </li>
                @endif
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4" class="fs-6"></iconify-icon>
                    <span class="hide-menu">게시판 관리</span>
                </li>
                @if (auth()->guard('admin')->user()->admin_level >= 3 )
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.board.list') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-clipboard-list"></i>
                        </span>
                            <span class="hide-menu">게시판</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.post.list', ['code' => 'notice']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-clipboard-text"></i>
                        </span>
                        <span class="hide-menu">게시글</span>
                    </a>
                </li>
                 <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4" class="fs-6"></iconify-icon>
                    <span class="hide-menu">언어 관리</span>
                </li>
                @if (auth()->guard('admin')->user()->admin_level >= 3 )
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.language', ['mode' => 'default']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-world"></i>
                        </span>
                        <span class="hide-menu">기본 설정</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.language', ['mode' => 'message']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-message"></i>
                        </span>
                        <span class="hide-menu">메시지 설정</span>
                    </a>
                </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.language', ['mode' => 'language']) }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-language"></i>
                        </span>
                        <span class="hide-menu">언어 설정</span>
                    </a>
                </li>
                @if (auth()->guard('admin')->user()->admin_level >= 4 )
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4" class="fs-6"></iconify-icon>
                    <span class="hide-menu">관리자 기능</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.manager.list') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="hide-menu">관리자 관리</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
