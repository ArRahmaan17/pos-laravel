@extends('template.parent')
@section('title', 'User Profile')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('man.customer-user.profile') }}"><i class="bx bx-user me-1"></i>
                        Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('man.customer-company.index') }}"><i class="bx bx-building me-1"></i>
                        Company</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="pages-account-settings-connections.html"><i class="bx bx-link-alt me-1"></i>
                        Connections</a>
                </li> --}}
            </ul>

            <div class="card mb-4">
                <h5 class="card-header">@yield('title')</h5>
                <!-- Account -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{ !empty(session('userLogged')['user']['profile_picture']) ? session('userLogged')['user']['profile_picture'] : asset('/assets/img/avatars/1.png') }}"
                            alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>
                            <p class="text-muted mb-0">Allowed JPG or PNG. Max size of 800K</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <form id="form-update-profile" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ session('userLogged')['user']['name'] }}"
                                    autofocus />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phone_number">Phone Number</label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control phone_number"
                                    value="{{ session('userLogged')['user']['phone_number'] }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input class="form-control" type="text" name="username" id="username"
                                    value="{{ session('userLogged')['user']['username'] }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control email" type="text" id="email" name="email"
                                    value="{{ session('userLogged')['user']['email'] }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" id="role" name="role" class="form-control" value="{{ session('userLogged')['role']['name'] }}"
                                    disabled />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="affiliate_code" class="form-label">affiliate code</label>
                                <div class="input-group">
                                    <input type="text" id="affiliate_code" name="affiliate_code" class="form-control"
                                        value="{{ session('userLogged')['user']['affiliate_code'] }}"
                                        @if (empty(session('userLogged')['user']['affiliate_code'])) autofocus @else readonly @endif />
                                    <button type="button"
                                        class="btn btn-icon btn-success @if (empty(session('userLogged')['user']['affiliate_code'])) btn-success generate @else btn-info copy @endif">
                                        @if (empty(session('userLogged')['user']['affiliate_code']))
                                            <i class='bx bx-link'></i>
                                        @else
                                            <i class='bx bx-copy'></i>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button id="save-profile" type="button" class="btn btn-primary me-2"><i class='bx bx-save mb-1'></i><span
                                    class="d-none d-sm-inline-block">Save changes</span></button>
                        </div>
                    </form>
                </div>
                <!-- /Account -->
            </div>
            @if (in_array(session('userLogged')['role']['name'], ['Developer', 'Manager']))
                <div class="row m-0 p-0 gap-2">
                    <div class="card col">
                        <h5 class="card-header">Change Password</h5>
                        <div class="card-body">
                            <div class="mb-3 col-12 mb-0">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading fw-bold mb-1">Are you sure you want to change your password?
                                    </h6>
                                </div>
                            </div>
                            <a href="{{ route('auth.request-change-password') }}" class="btn btn-warning"><i class='bx bxs-right-arrow-alt mb-1'></i><span
                                    class="d-none d-sm-inline-block">Change
                                    Password</span></a>
                        </div>
                    </div>
                    <div class="card col">
                        <h5 class="card-header">Delete Account</h5>
                        <div class="card-body">
                            <div class="mb-3 col-12 mb-0">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?
                                    </h6>
                                    <p class="mb-0">Once you delete your account, there is no going back. Please be
                                        certain.
                                    </p>
                                </div>
                            </div>
                            <form id="formAccountDeactivation" onsubmit="return false">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" />
                                    <label class="form-check-label" for="accountActivation">I confirm my account
                                        deactivation</label>
                                </div>
                                <button type="submit" class="btn btn-danger deactivate-account"><i class='bx bxs-lock-alt mb-1'></i><span
                                        class="d-none d-sm-inline-block">Deactivate
                                        Account</span></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script>
        $(function() {
            formattedInput();
            $('.generate').click(function() {
                $.ajax({
                    type: "PATCH",
                    url: `{{ route('man.customer-user.generate-affiliate-code') }}`,
                    data: {
                        '_token': `{{ csrf_token() }}`
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#affiliate_code').val(response.data.affiliate_code);
                        $('#affiliate_code').attr('readonly', 'true');
                        $('.generate').off('click');
                        $('.generate').html("<i class='bx bx-copy'></i>")
                            .removeClass('btn-success generate')
                            .addClass('btn-info copy');
                        $('.copy').click(function() {
                            copyToClipboard('affiliate_code');
                        });
                    },
                    error: function(error) {

                    }
                });
            });
            $('.copy').click(function() {
                copyToClipboard('affiliate_code');
            });
            $('#save-profile').click(function() {
                let data = serializeObject($('#form-update-profile'))
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-user.update-profile') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        iziToast.success({
                            id: 'alert-update-profile-action',
                            title: 'Success',
                            message: `${response.message}`,
                            position: 'topRight',
                            layout: 1,
                            displayMode: 'replace'
                        });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-update-profile-action',
                            title: 'Error',
                            message: `${response.responseJSON.message}`,
                            position: 'topRight',
                            layout: 1,
                            displayMode: 'replace'
                        });
                    }
                });
            });
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
        })
    </script>
@endpush
