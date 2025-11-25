<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center text-capitalize">
                <i class='bx bxs-user-account'></i> &nbsp;
                <span class="d-none d-sm-block col-1 col-sm-10 col-md-12 text-truncate fw-bold">{!! session('userLogged')['user']['name'] !!}</span>
            </div>
        </div>

        <ul class="navbar-nav flex-row justify-content-between align-items-center ms-auto">
            <li class="serverTime text-xs my-auto px-2 fw-bold">
                <div class="spinner-border spinner-border-sm"></div>
            </li>
            @if (env('APP_SUBS') == 'ON' && in_array(getRole(), ['Developer', 'Manager']))
                <li class="nav-item me-4">
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#AppSubscriptionModal"
                            class="{{ isset(session('userLogged')['subscription']['name']) ? 'btn btn-success' : 'btn btn-warning' }} buy-now">
                            {!! session('userLogged')['subscription']['name'] ??
                                '<i class="bx bxs-layer-plus mb-1" ></i><span class="d-none d-md-inline-block">Choose Subscription</span>' !!}
                        </button>
                    </div>
                </li>
            @endif
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ !empty(session('userLogged')['user']['profile_picture']) && session('userLogged')['user']['profile_picture'] !== null ? asset('/customer-profile-picture/' . session('userLogged')['user']['profile_picture']) : asset('resources/default/user/profesional/1.webp') }}"
                            alt class="w-px-40 h-100 rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="avatar avatar-online">
                                    <img src="{{ !empty(session('userLogged')['user']['profile_picture']) && session('userLogged')['user']['profile_picture'] !== null ? asset('/customer-profile-picture/' . session('userLogged')['user']['profile_picture']) : asset('resources/default/user/profesional/1.webp') }}"
                                        alt class="w-px-40 h-100 rounded-circle" />
                                </div>
                                <div>{{ buatSingkatan(session('userLogged')['company']['name']) }}</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    {!! buildMenu($profileAppMenu, 1) !!}
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    @if (in_array(getRole(), ['Developer', 'Manager']))
                        <li>
                            <a class="dropdown-item" href="{{ route('auth.change-company') }}">
                                <i class='bx bxs-door-open me-2'></i>
                                <span class="align-middle">Change Company</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item trigger-lockscreen">
                            <i class='bx bx-lock-alt me-2'></i>
                            <span class="align-middle">Lock Screen</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('auth.logout') }}">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out System</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
