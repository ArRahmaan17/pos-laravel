<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
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
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-pricing.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
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
                        <span
                            class="app-brand-text demo menu-text fw-bolder ms-2 text-capitalize text-wrap col-12">{{ !session('userLogged')['company']['name'] ? env('APP_NAME') : session('userLogged')['company']['name'] }}</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>
                <div class="menu-inner">
                    {!! buildMenu($sidebarAppMenu) !!}
                </div>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class='bx bxs-user-account'></i>
                                {!! ' Logged in&nbsp;<i>' . session('userLogged')['user']['name'] . '</i> &nbsp;as&nbsp;<i>' . session('userLogged')['role']['name'] . '</i>' ??
                                    'Login as :' !!}
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row justify-content-between align-items-center ms-auto">
                            <!-- User -->
                            @if (env('APP_SUBS') == 'ON')
                                <li class="nav-item me-4">
                                    <div class="">
                                        <button data-bs-toggle="modal" data-bs-target="#AppSubscriptionModal"
                                            class="{{ isset(session('userLogged')['subscription']['name']) ? 'btn btn-success' : 'btn btn-warning' }} buy-now">
                                            {{ session('userLogged')['subscription']['name'] ?? 'Choose Subscription' }}
                                        </button>
                                    </div>
                                </li>
                            @endif
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
                                        {{-- <a class="dropdown-item" href="{{ route('logout.system') }}">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out System</span>
                                        </a> --}}
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('content')
                        <div class="modal fade" id="AppSubscriptionModal" tabindex="-1" aria-modal="true" role="dialog" data-bs-backdrop="static"
                            data-bs-keyboard="false">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header m-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="pb-4 rounded-top">
                                            <div class="container-fluid py-6 px-xl-4 px-auto">
                                                <h3 class="text-center mb-2 mt-4">Pricing Plans</h3>
                                                <p class="text-center mb-0">
                                                    Choose the best plan to fit your needs.
                                                </p>
                                                <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 pt-6 pb-6">
                                                    <label class="form-check form-switch ms-sm-6 ps-sm-6 me-0">
                                                        <input type="checkbox" class="form-check-input price-duration-toggler" />
                                                        <span class="form-check-label fs-6 text-body">Annually</span>
                                                    </label>
                                                </div>

                                                <div class="row px-lg-12">
                                                    @foreach ($subscriptions as $subscription)
                                                        <!-- Basic -->
                                                        <div class="col-12 col-lg-4 mb-md-0">
                                                            <div class="card border-primary border shadow-none">
                                                                <div class="card-body position-relative pt-4">
                                                                    @if (isset(session('userLogged')['subscription']['name']))
                                                                        <div class="position-absolute end-0 me-5 top-0 mt-4">
                                                                            <span class="badge bg-label-primary rounded-1">Popular</span>
                                                                        </div>
                                                                    @endif
                                                                    <div class="my-5 pt-6 text-center">
                                                                        <img src="../../assets/img/icons/unicons/wallet-round.png" alt="Pro Image"
                                                                            width="120" />
                                                                    </div>
                                                                    <h4 class="card-title text-center text-capitalize mb-1">
                                                                        {{ $subscription->name }}
                                                                    </h4>
                                                                    <p class="text-center mb-5">{{ $subscription->description }}</p>
                                                                    <div class="text-center h-px-50">
                                                                        <div class="d-flex justify-content-center">
                                                                            <sup class="h6 text-body pricing-currency mt-2 mb-0 me-1">Rp.</sup>
                                                                            <h1 class="price-toggle price-yearly text-primary mb-0 d-none">
                                                                                {{ numberFormat($subscription->price - ($subscription->price * 5) / 100) }}
                                                                            </h1>
                                                                            <h1 class="price-toggle price-monthly text-primary mb-0">
                                                                                {{ numberFormat($subscription->price) }}</h1>
                                                                            <sub class="h6 text-body pricing-duration mt-auto mb-1">/month</sub>
                                                                        </div>
                                                                        <small class="price-yearly price-yearly-toggle text-muted d-none">Rp.
                                                                            {{ numberFormat($subscription->price * 12 - ($subscription->price * 5) / 100) }}
                                                                            / year</small>
                                                                    </div>

                                                                    <ul class="list-group my-5 pt-9">
                                                                        @foreach ($subscription->planFeature as $plan)
                                                                            <li class="mb-4 d-flex align-items-center">
                                                                                <span class="badge w-px-30 h-px-30 rounded-pill bg-label-primary me-2"><i
                                                                                        class="bx bx-check bx-xs"></i></span>
                                                                                <span>{{ $plan->planFeature }}</span>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>

                                                                    <button type="button" data-subscription="{{ $subscription->id }}"
                                                                        class="btn btn-primary d-grid w-100 buy-now process-subscription">{{ isset(session('userLogged')['subscription']['name']) ? 'Current Plan' : 'Upgrade Plan' }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="SubscriptionProcessModal" tabindex="-1" aria-modal="true" role="dialog"
                            data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header m-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-7 card-body border-end p-md-8">
                                                <h4 class="mb-2">Checkout</h4>
                                                <div class="row g-5 py-3">
                                                    <div class="col-md col-lg-12 col-xl-6">
                                                        <div class="form-check border border-3 border-secondary rounded">
                                                            <label
                                                                class="form-check-label d-flex gap-4 align-items-center px-3"
                                                                for="customPaymentMethod1">
                                                                <input name="payment-method" class="form-check-input" type="radio"
                                                                    value="credit-card" id="customPaymentMethod1">
                                                                <span class="col-11 d-flex justify-content-start align-items-center">
                                                                    <i class='bx bx-qr-scan h1 pt-3'></i>
                                                                    <span class="ms-4 fw-bold text-heading">Qris</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4 class="mb-6">Billing Details</h4>
                                                <div class="row g-6">
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="billings-email">Email Address</label>
                                                        <input type="text" id="billings-email" class="form-control"
                                                            placeholder="john.doe@gmail.com">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="billings-password">Password</label>
                                                        <input type="password" id="billings-password" class="form-control" placeholder="Password">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="billings-country">Country</label>
                                                        <select id="billings-country" class="form-select" data-allow-clear="true">
                                                            <option value="">Select</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Brazil">Brazil</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="China">China</option>
                                                            <option value="France">France</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="India">India</option>
                                                            <option value="Turkey">Turkey</option>
                                                            <option value="Ukraine">Ukraine</option>
                                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="United States">United States</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="billings-zip">Billing Zip / Postal Code</label>
                                                        <input type="text" id="billings-zip" class="form-control billings-zip-code"
                                                            placeholder="Zip / Postal Code">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5 card-body p-md-8">
                                                <h4 class="mb-2">Order Summary</h4>
                                                <p class="mb-8">It can help you manage and service orders before,<br> during and after fulfilment.</p>
                                                <div class="bg-lighter p-3 rounded">
                                                    <p>A simple start for everyone</p>
                                                    <div class="d-flex align-items-center mb-4" id="container-subscription">
                                                        <h1 class="text-heading mb-0">Rp. 59.99</h1>
                                                        <sub class="h6 text-body mb-n3">/month</sub>
                                                    </div>
                                                    <div class="d-grid">
                                                        <button type="button" data-bs-target="#AppSubscriptionModal" data-bs-toggle="modal"
                                                            class="btn btn-primary">Change Plan</button>
                                                    </div>
                                                </div>
                                                <div class="mt-5">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <p class="mb-0">Subtotal</p>
                                                        <h6 class="mb-0">$85.99</h6>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <p class="mb-0">Tax</p>
                                                        <h6 class="mb-0">$4.99</h6>
                                                    </div>
                                                    <hr>
                                                    <div class="d-flex justify-content-between align-items-center mt-4 pb-1">
                                                        <p class="mb-0">Total</p>
                                                        <h6 class="mb-0">$90.98</h6>
                                                    </div>
                                                    <div class="d-grid mt-5">
                                                        <button class="btn btn-success">
                                                            <span class="me-2">Proceed with Payment</span>
                                                            <i class="bx bx-right-arrow-alt scaleX-n1-rtl"></i>
                                                        </button>
                                                    </div>

                                                    <p class="mt-8">By continuing, you accept to our Terms of Services and Privacy Policy. Please note
                                                        that payments are non-refundable.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-fluid d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                , made with ❤️ by
                                <a href="https://devwandering.com" target="_blank" class="footer-link fw-bolder">Doglex's</a>
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
    <script src="{{ asset('assets/js/pages-pricing.js') }}"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    @if (env('APP_ENV') === 'production')
        <script>
            document.addEventListener('contextmenu', (e) => {
                e.preventDefault();
            })
        </script>
    @endif
    <script>
        window.process_subscription = null;

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

        function numberFormat(nilai, prefix = 'Rp. ') {
            return $.fn.dataTable.render.number('.', ',', 2, prefix).display(nilai);
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

        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                if (timeoutId) {
                    clearTimeout(timeoutId); // Hapus timeout sebelumnya
                }
                timeoutId = setTimeout(() => {
                    func.apply(this, args); // Panggil fungsi dengan argumen
                }, delay);
            };
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
            $(".menu-sub").find('.menu-link.bg-primary').parents('.menu-item:not(:first)').map((index, element) => {
                $(element).addClass('open');
                $(element).children('.menu-link.menu-toggle').addClass('bg-primary text-white')
            });
            $.extend($.fn.dataTable.defaults, {
                "pageLength": 5,
                "aLengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                "responsive": true
            });
            $('.process-subscription').click(function() {
                window.process_subscription = {id: $(this).data('subscription'), year: $('.price-duration-toggler').prop('checked')};
                $('#AppSubscriptionModal').modal('hide');
                $('#SubscriptionProcessModal').modal('show');
            });
            $('#SubscriptionProcessModal').on('shown.bs.modal', function() {
                if (window.process_subscription == null) {
                    $(this).modal('hide')
                }else{
                $.ajax({
                    type: "get",
                    url: `{{route('dev.app-subscription.show')}}/${window.process_subscription.id}`,
                    dataType: "json",
                    success: function (response) {
                        
                    }
                });
}
            });
            $('#SubscriptionProcessModal').on('hidden.bs.modal', function() {
                window.process_subscription == null;
            });
        });
    </script>

    @stack('js')
</body>

</html>
