@extends('template.single')
@section('title', 'Login')
@section('content')
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <div class="app-brand justify-content-center">
                        <a class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img rel='preload' style="width: 100px;height: 100px;" src="{{ asset('assets/img/icons/icon.png') }}" />
                            </span>
                        </a>
                    </div>
                    <div class="col-12">
                        @if (session('error'))
                            <div class="alert alert-danger">{!! session('error') !!}</div>
                        @endif
                    </div>
                    <form id="formAuthentication" class="mb-3" action="{{ route('auth.login.process') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Email/Username/Phone number</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                id="username" name="username" value="{{ old('username') }}"
                                placeholder="Masukan Email/Username/Phone Number Anda" autofocus />
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer rounded-end"><i class="bx bx-hide"></i></span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember_me" />
                                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                                </div>
                                <a href="auth-forgot-password-basic.html">
                                    <small>Forgot Password?</small>
                                </a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary w-100 py-2" type="submit">
                                Masuk
                                <span class='tf-icons bx bx-right-arrow-circle bx-tada bx-sm'></span>
                            </button>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('auth.registration') }}" class="btn btn-success w-100 py-2">
                                Registrasi
                                <span class='tf-icons bx bx-right-arrow-circle bx-tada bx-sm'></span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
