<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta property="og:title" content="@yield('title') - {{ env('APP_NAME') }}">
    <meta property="og:description" content="Penjelasan singkat apa isi website ini agar orang tertarik klik.">
    <meta property="og:image" content="{{ asset('assets/banner-preview.jpg') }}">
    <meta property="og:url" content="https://websitekamu.com">
    <meta property="og:type" content="website">
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
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

        .text-xs {
            font-size: 0.75rem;
            line-height: calc(1 / 0.75);
        }

        .h-100 {
            height: 100vh;
        }
    </style>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }}" name="description" />
    <meta content="{{ env('APP_NAME') }}" name="name" />
    <meta content="{{ env('APP_AUTHOR') }}" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="@yield('title')" name="title_page" />

    <!-- Favicon -->
    @include('singleton.favico')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="{{ asset('assets/css/googleapis.css') }}" rel="stylesheet" />
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/basic/boxicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/transformations.min.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/core_v1.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pages/page-auth.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/iziModal.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    @stack('resource-css')

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/js/theme-switcher.js') }}"></script>
    <style>
        .blur {
            filter: blur(16px);
        }
    </style>
</head>
