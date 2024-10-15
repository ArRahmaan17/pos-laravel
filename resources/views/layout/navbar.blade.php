<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <i class='bx bxs-user-account'></i>
                {!! ' Logged in&nbsp;<i>' .
                    session('userLogged')['user']['name'] .
                    '</i> &nbsp;as&nbsp;<i>' .
                    session('userLogged')['role']['name'] .
                    '</i>' ??
                    'Login as :' !!}
            </div>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ !empty(session('userLogged')->user->foto) && session('userLogged')->user->foto !== null ? asset(session('userLogged')->user->foto) : '../assets/img/avatars/1.png' }}"
                            alt class="w-px-40 h-100 rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ !empty(session('userLogged')['user']['foto']) && session('userLogged')['user']['foto'] !== null ? asset(session('userLogged')['user']['foto']) : '../assets/img/avatars/1.png' }}"
                                            alt class="w-px-40 h-100 rounded-circle" />
                                    </div>
                                </div>
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
                    <li>
                        <a class="dropdown-item button-logout" href="{{ route('auth.logout') }}">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>

{{-- JQuery --}}
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

<script>
    $('.button-logout').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            title: "Are you sure?",
            text: "Are you sure you want to logout?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Logout it!"
        }).then((result) => {
			if (result.isConfirmed) {
				document.location.href = url;
			}
		});
    })
</script>
