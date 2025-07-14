@extends('template.single')
@section('content')
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="card">
                <div class="card-body">
                    <div class="app-brand justify-content-center">
                        <a href="{{ route('home') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img rel='preload' src="{{ asset('assets/img/favicon/favicon.ico') }}" />
                            </span>
                        </a>
                    </div>
                    <h4 class="mb-2 text-center">Forgot Password? 🔒</h4>
                    <p class="mb-4">Enter your phone number and we'll send you instructions to reset your password</p>
                    <form id="formAuthentication" class="mb-3" action="{{ route('home') }}" method="POST">
                        <div class="mb-3">
                            <label for="phone number" class="form-label">Phone number</label>
                            <input type="text" class="form-control phone_number" id="phone number" name="phone number"
                                placeholder="Enter your phone number" autofocus />
                        </div>
                        <button class="btn btn-primary d-grid w-100">Send Reset Link</button>
                    </form>
                    <div class="text-center">
                        <a href="{{ route('auth.login') }}" class="d-flex align-items-center justify-content-center">
                            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        formattedInput();
    </script>
@endpush
