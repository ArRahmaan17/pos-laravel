<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-bs-theme="dark" data-assets-path="../assets/"
    data-template="vertical-menu-template">
@include('singleton.head')
@vite(['resources/app.js', 'resources/css/app.css'])
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <x-sidebar />
            <div class="layout-page">
                <x-navbar />
                {{-- <x-breadcrumbs /> --}}
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <x-footer />
                    @if (env('APP_SUBS') === 'ON' && getScope() !== 'user_created')
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
                                                                    <h4 class="card-title text-center text-capitalize mb-1">
                                                                        {{ $subscription->name }}
                                                                    </h4>
                                                                    <p class="text-center mb-5">
                                                                        {{ $subscription->description }}</p>
                                                                    <div class="text-center h-px-50">
                                                                        <div class="d-flex justify-content-center">
                                                                            <sup class="h6 text-body pricing-currency mt-2 mb-0 me-1">Rp.</sup>
                                                                            <h1 class="price-toggle price-yearly text-primary mb-0 d-none">
                                                                                {{ numberFormat($subscription->price - ($subscription->price * 5) / 100) }}
                                                                            </h1>
                                                                            <h1 class="price-toggle price-monthly text-primary mb-0 text-sm text-md-md">
                                                                                {{ numberFormat($subscription->price) }}
                                                                            </h1>
                                                                            <sub class="h6 text-body pricing-duration mt-auto mb-1">/month</sub>
                                                                        </div>
                                                                        <small class="price-yearly price-yearly-toggle text-muted d-none">Rp.
                                                                            {{ numberFormat($subscription->price * 12 - ($subscription->price * 5) / 100) }}
                                                                            / year</small>
                                                                    </div>

                                                                    <ul class="list-group my-5 pt-9">
                                                                        @foreach ($subscription->planFeature as $plan)
                                                                            <li class="mb-4 d-flex align-items-center justify-around">
                                                                                <div class="col-10">{{ $plan->text_feature }}</div>
                                                                                @if (in_array($plan->category, ['logic', 'full_access_report', 'custom_report', 'custom_menu']))
                                                                                    @if ($plan->status)
                                                                                        <div class="col-2 badge bg-label-success me-2"><i
                                                                                                class="bx bx-check bx-xs"></i></div>
                                                                                    @else
                                                                                        <div class="col-2 badge bg-label-danger me-2"><i
                                                                                                class="bx bx-x bx-xs"></i></div>
                                                                                    @endif
                                                                                @endif
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
                        <div class="modal fade" id="SubscriptionProcessModal" tabindex="-1" aria-modal="true" role="dialog" data-bs-backdrop="static"
                            data-bs-keyboard="false">
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
                                                            <label class="form-check-label d-flex gap-4 align-items-center px-3"
                                                                for="customPaymentMethod1">
                                                                <input name="payment-method" class="form-check-input" type="radio" value="credit-card"
                                                                    id="customPaymentMethod1">
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
                                                        <label class="form-label" for="billings-email">Email
                                                            Address</label>
                                                        <input type="text" id="billings-email" class="form-control" readonly
                                                            value="{{ session('userLogged')['user']['email'] }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5 card-body p-md-8">
                                                <h4 class="mb-2">Order Summary</h4>
                                                <p class="mb-8">It can help you manage and service orders before,<br>
                                                    during and after fulfilment.</p>
                                                <div class="bg-lighter p-3 rounded">
                                                    <p>
                                                        <span class="fs-5 lg-fs-3 subs-title">Basic</span>-
                                                        <span class="fs-6 lg-fs-5 subs-description text-muted">A simple start for everyone</span>
                                                    </p>
                                                    <div class="d-flex align-items-center mb-4" id="container-subscription">
                                                        <h1 class="text-heading mb-0 subs-price"></h1>
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
                                                        <h6 class="mb-0 subs-sub-total">$85.99</h6>
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
                                                        <button class="btn btn-outline-success">
                                                            <span class="me-2">Proceed with Payment</span>
                                                            <i class='bx bx-cart scaleX-n1-rtl'></i>
                                                        </button>
                                                    </div>

                                                    <p class="mt-8">By continuing, you accept to our Terms of
                                                        Services and Privacy Policy. Please note
                                                        that payments are non-refundable.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="modal" id="modalDisconect" aria-labelledby="modalDisconectLabel" aria-hidden="true">
                    </div>
                    <div class="offcanvas offcanvas-top h-100 lockscreen" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false"
                        id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasTopLabel">Lockscreen</h5>
                        </div>
                        <div class="offcanvas-body">
                            <div class="container">
                                <div class="authentication-wrapper authentication-basic" style="min-height:80vh">
                                    <div class="authentication-inner py-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="app-brand justify-content-center">
                                                    <a class="app-brand-link gap-2">
                                                        <span class="app-brand-logo demo">
                                                            <img draggable="false" rel="preload" height="100px"
                                                                src="{{ asset('assets/img/icons/lock.webp') }}" />
                                                        </span>
                                                    </a>
                                                </div>
                                                <h4 class="mb-2 text-center">Enter Your Access Pin</h4>
                                                <p class="text-center">Pleasae enter your access pin.</p>
                                                <form id="form-lockscreen" class="mb-2">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="access_pin" class="form-label">Access Pin</label>
                                                        <div class="d-flex gap-1 gap-sm-2 gap-md-3">
                                                            <input type="password" class="form-control single_number" id="access_pin"
                                                                name="access_pin[0]" autofocus />
                                                            <input type="password" class="form-control single_number" name="access_pin[1]" />
                                                            <input type="password" class="form-control single_number" name="access_pin[2]" />
                                                            <input type="password" class="form-control single_number" name="access_pin[3]" />
                                                            <input type="password" class="form-control single_number" name="access_pin[4]" />
                                                            <input type="password" class="form-control single_number" name="access_pin[5]" />
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary d-grid w-100 mb-2">Unlock</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    @include('singleton.foot')
</body>

</html>
