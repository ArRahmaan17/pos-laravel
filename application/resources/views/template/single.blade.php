<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-bs-theme="dark" data-assets-path="assets/"
    data-template="vertical-menu-template">

<head>
    <!-- Favicon -->
    @include('singleton.head')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/basic/boxicons.min.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/core_v1.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pages/page-auth.css') }}" />
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/theme-switcher.js') }}"></script>
</head>

<body>
    <div class="container-xxl">
        @yield('content')
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>

    @stack('resources-js')
    <script>
        function formattedInput() {
            $('.phone_number').inputmask('+628-999-999-999[9]')
            $('.price').inputmask('currency', {
                radixPoint: ',',
                groupSeparator: ".",
                rightAlign: false,
                allowMinus: false
            });
            $('.number').inputmask('integer', {
                groupSeparator: ".",
                rightAlign: false,
                allowMinus: false
            });
            $('.single_number').inputmask({
                mask: "9{1}",
                placeholder: "",
            });
            $('.email').inputmask({
                mask: "*{1,15}[.*{1,15}][.*{1,15}][.*{1,15}]@*{1,15}[.*{2,6}][.*{1,2}]",
                greedy: false,
                definitions: {
                    '*': {
                        casing: "lower",
                        validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
                    },
                },
                onBeforePaste: function(pastedValue, opts) {
                    pastedValue = pastedValue.toLowerCase();
                    return pastedValue.replace("mailto:", "");
                },
            });
        }
        $(document).ready(function() {
            formattedInput();
        });
    </script>
</body>

</html>