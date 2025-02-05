<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ env('APP_NAME') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

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
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner" style="max-width: 80%;">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <svg width="25" viewBox="0 0 25 42" version="1.1"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <defs>
                                            <path
                                                d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                                                id="path-1"></path>
                                            <path
                                                d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                                                id="path-3"></path>
                                            <path
                                                d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                                                id="path-4"></path>
                                            <path
                                                d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                                                id="path-5"></path>
                                        </defs>
                                        <g id="g-app-brand" stroke="none" stroke-width="1" fill="none"
                                            fill-rule="evenodd">
                                            <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                                <g id="Icon" transform="translate(27.000000, 15.000000)">
                                                    <g id="Mask" transform="translate(0.000000, 8.000000)">
                                                        <mask id="mask-2" fill="white">
                                                            <use xlink:href="#path-1"></use>
                                                        </mask>
                                                        <use fill="#696cff" xlink:href="#path-1"></use>
                                                        <g id="Path-3" mask="url(#mask-2)">
                                                            <use fill="#696cff" xlink:href="#path-3"></use>
                                                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3">
                                                            </use>
                                                        </g>
                                                        <g id="Path-4" mask="url(#mask-2)">
                                                            <use fill="#696cff" xlink:href="#path-4"></use>
                                                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4">
                                                            </use>
                                                        </g>
                                                    </g>
                                                    <g id="Triangle"
                                                        transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                                        <use fill="#696cff" xlink:href="#path-5"></use>
                                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5">
                                                        </use>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </span>
                            </a>
                            <h3 class="mb-0">Registration</h3>
                        </div>
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <div class="row justify-content-start align-items-center">
                                    <div class="col-2">
                                        <i class='bx bxs-error-alt bx-lg'></i>
                                        <span class="fw-bold">Error</span>
                                    </div>
                                </div>
                                <div class="px-2">
                                    {!! session('error') !!}
                                </div>
                            </div>
                        @endif
                        <!-- /Logo -->
                        <form id="formAuthentication" enctype="multipart/form-data" class="mb-3"
                            action="{{ route('auth.registration.process') }}" method="POST">
                            <div class="divider">
                                <div class="divider-text">
                                    Account
                                </div>
                            </div>
                            @csrf
                            @isset($managerId)
                                <input type="hidden" name="managerId" value="{{ $managerId }}">
                            @endisset
                            @isset($roleId)
                                <input type="hidden" name="roleId" value="{{ $roleId }}">
                            @endisset
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="user[name]" class="form-label">Name</label>
                                    <input type="text"
                                        class="form-control @error('user.name') is-invalid @enderror" id="user[name]"
                                        name="user[name]" value="{{ old('user.name') }}" autofocus />
                                    @error('user.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="user[username]" class="form-label">Username</label>
                                    <input type="text"
                                        class="form-control @error('user.username') is-invalid @enderror"
                                        id="user[username]"name="user[username]" value="{{ old('user.username') }}"
                                        autofocus />
                                    @error('user.username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="user[email]" class="form-label">Email</label>
                                    <input type="text"
                                        class="form-control  email @error('user.email') is-invalid @enderror"
                                        id="user[email]" name="user[email]" value="{{ old('user.email') }}" />
                                    @error('user.email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="user[phone_number]" class="form-label">Phone number</label>
                                    <input type="text"
                                        class="form-control phone_number @error('user.phone_number') is-invalid @enderror"
                                        id="user[phone_number]" name="user[phone_number]"
                                        value="{{ old('user.phone_number') }}" />
                                    @error('user.phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-3 form-password-toggle">
                                    <label class="form-label" for="user[password]">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password"
                                            class="form-control @error('user.password') is-invalid @enderror"
                                            name="user[password]"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="user[password]" />
                                        <span class="input-group-text cursor-pointer"><i
                                                class="bx bx-hide"></i></span>
                                        @error('user.password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @if (empty($managerId))
                                <div class="divider">
                                    <div class="divider-text">
                                        Company
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="company[businessId]" class="form-label">Type of Business *</label>
                                        <select id="company[businessId]" name="company[businessId]"
                                            class="form-control select2 @error('company.businessId') is-invalid @enderror">
                                            <option value="">Please Business type</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('company.businessId') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('company.businessId')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="company[name]" class="form-label">Business Name *</label>
                                        <input type="text" id="company[name]" name="company[name]"
                                            value="{{ old('company.name') }}"
                                            class="form-control @error('company.name') is-invalid @enderror"
                                            placeholder="Business Name">
                                        @error('company.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="company[phone_number]" class="form-label">Contact Number *</label>
                                        <input type="text" id="company[phone_number]" name="company[phone_number]"
                                            value="{{ old('company.phone_number') }}"
                                            class="form-control phone_number @error('company.phone_number') is-invalid @enderror"
                                            placeholder="(+62) 895-222-2222">
                                        @error('company.phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="company[email]" class="form-label">E-mail *</label>
                                        <input type="text" id="company[email]" name="company[email]"
                                            value="{{ old('company.email') }}"
                                            class="form-control email @error('company.phone_number') is-invalid @enderror"
                                            placeholder="example@example.com">
                                        @error('company.email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="address[place]" class="form-label">Building *</label>
                                                <input type="text" id="address[place]" name="address[place]"
                                                    value="{{ old('address.place') }}"
                                                    class="form-control mb-2 @error('address.place') is-invalid @enderror"
                                                    placeholder="Building">
                                                @error('address.place')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <label for="address[address]" class="form-label">Address *</label>
                                                <input type="text" id="address[address]" name="address[address]"
                                                    value="{{ old('address.address') }}"
                                                    class="form-control mb-2 @error('address.address') is-invalid @enderror"
                                                    placeholder="Street Address">
                                                @error('address.address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" id="address[city]" name="address[city]"
                                                    value="{{ old('address.city') }}"
                                                    class="form-control @error('address.city') is-invalid @enderror"
                                                    placeholder="City">
                                                @error('address.city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" id="address[province]" name="address[province]"
                                                    value="{{ old('address.province') }}"
                                                    class="form-control @error('address.province') is-invalid @enderror"
                                                    placeholder="State / Province">
                                                @error('address.province')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" id="address[zipCode]" name="address[zipCode]"
                                                    value="{{ old('address.zipCode') }}"
                                                    class="form-control @error('address.zipCode') is-invalid @enderror"
                                                    placeholder="Postal / Zip Code">
                                                @error('address.zipCode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="mb-3">
                                <button class="btn btn-success w-100 py-2" type="submit">
                                    Register
                                    <span class='tf-icons bx bx-right-arrow-circle bx-tada bx-sm'></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- / Content -->
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script>
        $(function() {
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
            $('.select2').select2();
            let accountUserImage = document.getElementById('uploadedAvatar');
            const fileInput = document.querySelector('.account-file-input'),
                resetFileInput = document.querySelector('.account-image-reset');

            if (accountUserImage) {
                const resetImage = accountUserImage.src;
                fileInput.onchange = () => {
                    if (fileInput.files[0]) {
                        accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
                    }
                };
                resetFileInput.onclick = () => {
                    fileInput.value = '';
                    accountUserImage.src = resetImage;
                };
            }
        });
    </script>
</body>

</html>
