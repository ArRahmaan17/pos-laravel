@extends('template.single')
@section('title', 'Registration')
@section('content')
<div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner" style="max-width: 80%;">
        <div class="card">
            <div class="card-body">
                @include('singleton.icon')
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
                <form id="form-registration" class="mb-3" action="{{ route('auth.registration.process') }}" autocomplete="off" method="POST">
                    <div class="divider divider-primary">
                        <div class="divider-text">
                            Account
                        </div>
                    </div>
                    @csrf
                    @isset($managerId)
                    <input type="hidden" name="managerId" value="{{ $managerId }}">
                    @endisset
                    @isset($role_id)
                    <input type="hidden" name="role_id" value="{{ $role_id }}">
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="user[name]" class="form-label">Name</label>
                            <input type="text" class="form-control @error('user.name') is-invalid @enderror" id="user[name]" name="user[name]"
                                value="{{ old('user.name') }}" autofocus />
                            @error('user.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="user[username]" class="form-label">Username</label>
                            <input type="text" class="form-control @error('user.username') is-invalid @enderror"
                                id="user[username]" name="user[username]" value="{{ old('user.username') }}" />
                            @error('user.username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="user[email]" class="form-label">Email</label>
                            <input type="text" class="form-control  email @error('user.email') is-invalid @enderror" id="user[email]" name="user[email]"
                                value="{{ old('user.email') }}" />
                            @error('user.email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="user[phone_number]" class="form-label">Phone number</label>
                            <input type="text" class="form-control phone_number @error('user.phone_number') is-invalid @enderror"
                                id="user[phone_number]" name="user[phone_number]" value="{{ old('user.phone_number') }}" />
                            @error('user.phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6 mb-3 form-password-toggle">
                            <label class="form-label" for="user[password]">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control @error('user.password') is-invalid @enderror"
                                    name="user[password]" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="user[password]" />
                                <span class="input-group-text cursor-pointer rounded-end"><i class="bx bx-hide"></i></span>
                                @error('user.password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @if (empty($managerId))
                    <div class="divider divider-primary">
                        <div class="divider-text">
                            Company
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="company[business_id]" class="form-label">Type of Business *</label>
                            <select id="company[business_id]" name="company[business_id]"
                                class="form-control select2 @error('company.business_id') is-invalid @enderror">
                                <option value="">Please Business type</option>
                                @foreach ($types as $type)
                                <option value="{{ $type->id }}" {{ old('company.business_id') === $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('company.business_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="company[name]" class="form-label">Business Name *</label>
                            <input type="text" id="company[name]" name="company[name]" value="{{ old('company.name') }}"
                                class="form-control @error('company.name') is-invalid @enderror" placeholder="Business Name">
                            @error('company.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="company[email]" class="form-label">E-mail *</label>
                            <input type="text" id="company[email]" name="company[email]" value="{{ old('company.email') }}"
                                class="form-control email @error('company.phone_number') is-invalid @enderror" placeholder="example@example.com">
                            @error('company.email')
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
                    </div>
                    <div class="divider divider-primary">
                        <div class="divider-text">
                            Company Address
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="address[place]" class="form-label">Building *</label>
                                    <input type="text" id="address[place]" name="address[place]" value="{{ old('address.place') }}"
                                        class="form-control mb-2 @error('address.place') is-invalid @enderror" placeholder="Building">
                                    @error('address.place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="address[address]" class="form-label">Address *</label>
                                    <input type="text" id="address[address]" name="address[address]" value="{{ old('address.address') }}"
                                        class="form-control mb-2 @error('address.address') is-invalid @enderror" placeholder="Street Address">
                                    @error('address.address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="address[place]" class="form-label">city *</label>
                                    <input type="text" id="address[city]" name="address[city]" value="{{ old('address.city') }}"
                                        class="form-control @error('address.city') is-invalid @enderror" placeholder="City">
                                    @error('address.city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="address[place]" class="form-label">province *</label>
                                    <input type="text" id="address[province]" name="address[province]" value="{{ old('address.province') }}"
                                        class="form-control @error('address.province') is-invalid @enderror" placeholder="State / Province">
                                    @error('address.province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="address[place]" class="form-label">zipcode *</label>
                                    <input type="text" id="address[zip_code]" name="address[zip_code]" value="{{ old('address.zip_code') }}"
                                        class="form-control @error('address.zip_code') is-invalid @enderror" placeholder="Postal / Zip Code">
                                    @error('address.zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <button class="btn btn-outline-success w-100 py-2" type="submit">
                            Daftar
                            <span class='tf-icons bx bx-right-arrow-circle bx-sm'></span>
                        </button>
                        <div class="text-center mt-2">
                            Sudah Punya Akun? <a href="{{ route('auth.login') }}">Masuk</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('resources-js')
<script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
@endpush
@push('resource-js')
@endpush