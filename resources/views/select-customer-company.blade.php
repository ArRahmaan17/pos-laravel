<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ env('APP_NAME') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

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
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="col-12">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center align-items-middle fs-1 fw-bold">
                            SELECT YOUR COMPANY
                        </div>
                        <div class="d-flex row company-container justify-content-center gap-2 gap-md-5 mt-5">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script>
        $(function() {
            $.ajax({
                type: "GET",
                url: `{{ route('list-company') }}`,
                dataType: "json",
                success: function(response) {
                    const cardContainer = document.querySelector('.company-container')
                    response.data.forEach(item => {
                        const card = `
                               <div class="card mb-4 col-12 col-sm-5 col-md-4 col-lg-3">
                                    <img
                                        src="../cp/${item.picture}"
                                        class="card-img-top w-full"
                                        style="max-height:150px; object-fit: cover;"
                                        alt="${item.name}"
                                    />
                                    <div class="card-body">
                                        <h5 class="card-title fs-3">${item.name}</h5>
                                        <p class="card-text text-truncate" title="${item.type.name}"><strong>Business Type:</strong> ${item.type.name}</p>
                                        <p class="card-text">
                                            <strong>Location:</strong> ${item.address.city}, ${item.address.province}
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <form action="{{ route('select-customer-company') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="${item.id}">
                                            <button class="btn btn-success d-block d-sm-inline-block">Apply <i class='mb-1 bx bx-log-in' ></i></button>
                                        </form>
                                    </div>
                                </div>`;
                        cardContainer.innerHTML += card;
                    });
                }
            });
        });
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
