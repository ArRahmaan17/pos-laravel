@extends('template.parent')
@section('title', 'User Profile')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link @if (count(explode('user', url()->full())) > 1) active @endif" href="{{ route('man.customer-user.profile') }}"><i
                            class="bx bx-user me-1"></i>
                        Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (count(explode('company', url()->full())) > 1) active @endif" href="{{ route('man.customer-company.profile') }}"><i
                            class="bx bx-building me-1"></i>
                        Company</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="pages-account-settings-connections.html"><i class="bx bx-link-alt me-1"></i>
                        Connections</a>
                </li> --}}
            </ul>

            <div class="card mb-4">
                <h5 class="card-header">@yield('title')</h5>
                <div class="card-body">
                    <form action="#" id="form-customer-company" method="POST" class="mt-2" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="hidden" name="userId" value="{{ session('userLogged')['company']['userId'] }}">
                        </div>
                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-3">
                            <img src="@if (session('userLogged')['company']['picture'] === 'default-company.png') {{ asset('/cp/default-company.png') }} @else {{ asset('cp/' . session('userLogged')['company']['picture']) }} @endif"
                                alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" name="picture" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                                <p class="text-muted mb-0">Allowed JPG, PNG. Max size of 800K</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="businessId" class="form-label">Type of Business *</label>
                            <select id="businessId" name="businessId" class="form-control select2">
                                <option value="" disabled selected>Please Select</option>
                                @foreach ($types as $type)
                                    <option @if (session('userLogged')['company']['businessId']) selected @endif value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Business Name *</label>
                            <input type="text" id="name" name="name" value="{{ session('userLogged')['company']['name'] }}" class="form-control"
                                placeholder="Business Name">
                            <div class="invalid-feedback"></div>

                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Contact Number *</label>
                            <input type="text" id="phone_number" name="phone_number" value="{{ session('userLogged')['company']['phone_number'] }}"
                                class="form-control phone_number" placeholder="(+62) 895-222-2222">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="text" id="email" name="email" class="form-control email"
                                value="{{ session('userLogged')['company']['email'] }}" placeholder="example@example.com">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="address[place]" class="form-label">Building *</label>
                                    <input type="text" id="address[place]" name="address[place]"
                                        value="{{ session('userLogged')['company']['address']['place'] }}" class="form-control mb-2" placeholder="Building">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-6">
                                    <label for="address[address]" class="form-label">Address *</label>
                                    <input type="text" id="address[address]" name="address[address]"
                                        value="{{ session('userLogged')['company']['address']['address'] }}" class="form-control mb-2"
                                        placeholder="Street Address">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" id="address[city]" name="address[city]"
                                        value="{{ session('userLogged')['company']['address']['city'] }}" class="form-control" placeholder="City">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" id="address[province]" name="address[province]"
                                        value="{{ session('userLogged')['company']['address']['province'] }}" class="form-control"
                                        placeholder="State / Province">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" id="address[zipCode]" name="address[zipCode]"
                                        value="{{ session('userLogged')['company']['address']['zipCode'] }}" class="form-control"
                                        placeholder="Postal / Zip Code">
                                    <div class="invalid-feedback"></div>
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
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
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
                let data = serializeFiles('#form-customer-company');
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-company.update') }}/{{session('userLogged')['company']['id']}}`,
                    data: data,
                    processData: false,
                    contentType: false,
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
                            message: `${error.responseJSON.message}`,
                            position: 'topRight',
                            layout: 1,
                            displayMode: 'replace'
                        });
                    }
                });
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
        })
    </script>
@endpush
