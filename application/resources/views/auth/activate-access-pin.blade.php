@extends('template.single')
@section('title', 'Activate Access Pin')
@section('content')
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="card">
                <div class="card-body">
                    <div class="app-brand justify-content-center">
                        <a href="{{ route('dashboard.index') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img draggable="false" rel="preload" height="100px" src="{{ asset('assets/img/icons/lock.webp') }}" />
                            </span>
                        </a>
                    </div>
                    <h4 class="mb-2 text-center">Activate Your Access Pin</h4>
                    <p class="text-center">Pleasae setup your access pin.</p>
                    <form id="formAuthentication" class="mb-2" action="{{ route('privacy.access-pin') }}" method="POST">
                        @csrf
                        @if (!empty(session('userLogged')['user']['pin']))
                            <div class="mb-3">
                                <label for="access_pin" class="form-label">Current Access Pin</label>
                                <div class="d-flex gap-3">
                                    <input type="password" class="form-control single_number" id="access_pin" name="access_pin[]" autofocus />
                                    <input type="password" class="form-control single_number" name="access_pin[]" />
                                    <input type="password" class="form-control single_number" name="access_pin[]" />
                                    <input type="password" class="form-control single_number" name="access_pin[]" />
                                    <input type="password" class="form-control single_number" name="access_pin[]" />
                                    <input type="password" class="form-control single_number" name="access_pin[]" />
                                </div>
                            </div>
                        @endIf
                        <div class="mb-3">
                            <label for="access_pin" class="form-label">Access Pin</label>
                            <div class="d-flex gap-3">
                                <input type="password" class="form-control single_number" id="access_pin" name="access_pin[0]" autofocus />
                                <input type="password" class="form-control single_number" name="access_pin[1]" />
                                <input type="password" class="form-control single_number" name="access_pin[2]" />
                                <input type="password" class="form-control single_number" name="access_pin[3]" />
                                <input type="password" class="form-control single_number" name="access_pin[4]" />
                                <input type="password" class="form-control single_number" name="access_pin[5]" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_access_pin" class="form-label">Confirm Access Pin</label>
                            <div class="d-flex gap-3">
                                <input type="password" class="form-control single_number" id="confirm_access_pin" name="confirm_access_pin[0]" />
                                <input type="password" class="form-control single_number" name="confirm_access_pin[1]" />
                                <input type="password" class="form-control single_number" name="confirm_access_pin[2]" />
                                <input type="password" class="form-control single_number" name="confirm_access_pin[3]" />
                                <input type="password" class="form-control single_number" name="confirm_access_pin[4]" />
                                <input type="password" class="form-control single_number" name="confirm_access_pin[5]" />
                            </div>
                        </div>
                        @error('*')
                            <div class="alert alert-danger">{{ preg_replace('/[_]|(\.\d)/i', ' ', $message) }}
                                {{-- // <div class="alert alert-danger">{{ $message }} --}}
                            </div>
                        @enderror
                        <button class="btn btn-primary d-grid w-100 mb-2">Activate</button>
                        @if (!empty(session('userLogged')['user']['pin']))
                            <div class="text-center">
                                <a href="{{ route('dashboard.index') }}" class="d-flex align-items-center justify-content-center">
                                    <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                                    Back to home
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            formattedInput();
            $('input').keydown(function(e) {
                if (e.which === 9) {
                    e.preventDefault();
                }
            });
            $('.single_number').keyup(function(e) {
                if (e.currentTarget.value.split('').length === 1 && /\d{1}/y.exec(e.currentTarget.value) != null) {
                    if (e.currentTarget.nextElementSibling) {
                        $(e.currentTarget.nextElementSibling).focus();
                    } else {
                        $($(e.currentTarget).parents('.mb-3')[0].nextElementSibling).find('.single_number:first').focus()
                    }
                }
            });
        });
    </script>
@endpush
