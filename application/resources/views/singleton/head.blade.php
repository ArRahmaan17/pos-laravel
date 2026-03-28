<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <link rel="icon" type="image/png" href="{{ asset('assets/favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="{{ $app_name ?? 'Todos' }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    <meta property="og:title" content="@yield('title') - {{ env('OG_TITLE', env('APP_NAME')) }}">
    <meta property="og:description" content="{{ env('OG_DESCRIPTION') }}">
    <meta property="og:image" content="{{ asset(env('OG_IMAGE')) }}">
    <meta property="og:url" content="{{ env('OG_URL', url()->current()) }}">
    <meta property="og:type" content="{{ env('OG_TYPE', 'website') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title') - {{ env('OG_TITLE', env('APP_NAME')) }}">
    <meta name="twitter:description" content="{{ env('OG_DESCRIPTION') }}">
    <meta name="twitter:image" content="{{ asset(env('OG_IMAGE')) }}">

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

    <meta content="{{ env('OG_DESCRIPTION') }}" name="description" />
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