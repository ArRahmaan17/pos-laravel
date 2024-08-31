<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ env('APP_NAME') }} | @yield('title')</title>
    {{-- <title>{{ (session('app') == 'aset' ? env('APP_NAME') : session('app') == 'ssh') ? env('APP_NAME_2') : '' }}</title> --}}
    <style>
        *::-webkit-scrollbar {
            width: 1px;
            /* Adjust the width as needed */
        }

        /* Style for the track (the area where the scrollbar moves) */
        *::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            /* Change the color as desired */
        }

        /* Style for the thumb (the draggable part of the scrollbar) */
        *::-webkit-scrollbar-thumb {
            background-color: #888;
            /* Change the color as desired */
            border-radius: 6px;
            /* Adjust the border radius for rounded corners */
        }

        /* Style for the thumb when hovered */
        *::-webkit-scrollbar-thumb:hover {
            background-color: #555;
            /* Change the color as desired */
        }
    </style>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.2/fc-5.0.0/fh-4.0.1/datatables.min.css" rel="stylesheet">
    @stack('css')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('home') }}" class="app-brand-link">
                        @if (session('app') == 'aset')
                            <span
                                class="app-brand-text demo menu-text fw-bolder ms-2 text-capitalize text-wrap col-12">@yield('title')</span>
                        @elseif (session('app') == 'ssh')
                            <span
                                class="app-brand-text demo menu-text fw-bolder ms-2 text-capitalize text-wrap col-12">{{ env('APP_NAME_2') }}</span>
                        @endif
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>
                {!! buildMenu($sidebarAppMenu, session('app')) !!}
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

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
                                    session('userLogged')->user->name .
                                    '</i> &nbsp;as&nbsp;<i>' .
                                    session('userLogged')->role->name .
                                    '</i>' ??
                                    'Login as :' !!}
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
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
                                                        <img src="{{ !empty(session('userLogged')->user->foto) && session('userLogged')->user->foto !== null ? asset(session('userLogged')->user->foto) : '../assets/img/avatars/1.png' }}"
                                                            alt class="w-px-40 h-100 rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span
                                                        class="fw-semibold d-block">{{ session('userLogged')->user->displayname }}</span>
                                                    {{-- <small class="text-muted">{{ getRole() }} --}}
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    {{-- {!! buildMenu($menu_profile, session('app'), 'profile') !!} --}}
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        {{-- <a class="dropdown-item" href="{{ route('logout.system') }}">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out System</span>
                                        </a> --}}
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div
                            class="container-fluid d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                , made with ❤️ by
                                <a href="https://devwandering.com" target="_blank"
                                    class="footer-link fw-bolder">Developer BACODE</a>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.2/fc-5.0.0/fh-4.0.1/datatables.min.js"></script>
    @if (env('APP_ENV') === 'production')
        <script>
            document.addEventListener('contextmenu', (e) => {
                e.preventDefault();
            })
        </script>
    @endif
    <script>
        function serializeObject(node) {
            var o = {};
            var a = node.serializeArray();
            $.each(a, function() {
                if (this.value !== "") {
                    if (o[this.name]) {
                        if (!o[this.name].push) {
                            o[this.name] = [o[this.name]];
                        }
                        o[this.name].push(this.value || '');
                    } else {
                        o[this.name] = this.value || '';
                    }
                }
            });
            return o;
        }

        function removeDuplicates(array) {
            return array.filter((value, index) => array.indexOf(value) === index);
        }

        function buildTree(elements, parentId = 0) {
            var branch = [];
            elements.forEach(element => {
                if (element['parent'] == parentId) {
                    var children = buildTree(elements, element['id']);
                    if (children.length > 0) {
                        element['children'] = children;
                    }
                    branch.push(element);
                }
            });
            return branch
        }

        function formattedInput() {
            $('.phone_number').inputmask('(+62) 999-999-9999[9]')
            $('.email').inputmask({
                mask: "*{1,15}[.*{1,15}][.*{1,15}][.*{1,15}]@*{1,15}[.*{2,6}][.*{1,2}]",
                greedy: false,
                onBeforePaste: function(pastedValue, opts) {
                    pastedValue = pastedValue.toLowerCase();
                    return pastedValue.replace("mailto:", "");
                },
                definitions: {
                    '*': {
                        validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
                        casing: "lower"
                    }
                }
            });
        }

        function serializeFiles(node) {
            let form = $(node),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                if ($(tag)[0].files.length > 0) {
                    $.each($(tag)[0].files, function(i, file) {
                        formData.append(tag.name, file);
                    });
                }
            });

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });
            return formData;
        };
        $(function() {
            $(".menu-sub").find('.menu-item.active').parents('.menu-item').addClass('active open')
            $.extend($.fn.dataTable.defaults, {
                "pageLength": 5,
                "aLengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                "responsive": true
            });
        });
    </script>

    @stack('js')
</body>

</html>
