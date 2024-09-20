@extends('template.parent')
@section('title', 'Company')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-middle">
                    <div class="col-6">
                        <h3>@yield('title')</h3>
                    </div>
                    <div class="col-6 text-end">
                        <button class="btn btn-success" id="add-customer-company" data-bs-toggle="modal"
                            data-bs-target="#modal-customer-company">Add <i class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-company">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Business type</th>
                                    <th scope="col">
                                        Address
                                    </th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-customer-company" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Register Your @yield('title')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-company" method="POST" class="container mt-2"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id">
                        <p class="mb-4">Please provide all required details to register your business with us.</p>
                        <div class="mb-3">
                            @if (getRole() === 'Developer')
                                <label for="userId" class="form-label">Customer *</label>
                                <select id="userId" name="userId" class="form-select select2">
                                    <option value="" disabled selected>Please Select</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">test</div>
                            @else
                                <input type="hidden" name="userId" value="{{ session('userLogged')->user->id }}">
                            @endif
                        </div>
                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-3">
                            <img src="{{ asset('/cp/default-picture.png') }}" alt="user-avatar" class="d-block rounded"
                                height="100" width="100" id="uploadedAvatar" />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" name="picture" id="upload" class="account-file-input" hidden
                                        accept="image/png, image/jpeg" />
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
                            <select id="businessId" name="businessId" class="form-select select2">
                                <option value="" disabled selected>Please Select</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">test</div>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Business Name *</label>
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Business Name">
                            <div class="invalid-feedback">test</div>

                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Contact Number *</label>
                            <input type="text" id="phone_number" name="phone_number"
                                class="form-control phone_number" placeholder="(+62) 895-222-2222">
                            <div class="invalid-feedback">test</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="text" id="email" name="email" class="form-control email"
                                placeholder="example@example.com">
                            <div class="invalid-feedback">test</div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="address[place]" class="form-label">Building *</label>
                                    <input type="text" id="address[place]" name="address[place]"
                                        class="form-control mb-2" placeholder="Building">
                                    <div class="invalid-feedback">test</div>
                                </div>
                                <div class="col-6">
                                    <label for="address[address]" class="form-label">Address *</label>
                                    <input type="text" id="address[address]" name="address[address]"
                                        class="form-control mb-2" placeholder="Street Address">
                                    <div class="invalid-feedback">test</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" id="address[city]" name="address[city]" class="form-control"
                                        placeholder="City">
                                    <div class="invalid-feedback">test</div>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" id="address[province]" name="address[province]"
                                        class="form-control" placeholder="State / Province">
                                    <div class="invalid-feedback">test</div>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" id="address[zipCode]" name="address[zipCode]"
                                        class="form-control" placeholder="Postal / Zip Code">
                                    <div class="invalid-feedback">test</div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-company" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-company" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.datatableCustomerCompany = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idCustomerCompany = $(this).data("customer-company");
                $("#edit-customer-company").data("customer-company", idCustomerCompany);
                if (window.datatableCustomerCompany.rows('.selected').data().length == 0) {
                    $('#table-customer-company tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableCustomerCompany.rows('.selected').data()[0];

                $('#modal-customer-company').modal('show');
                $('#modal-customer-company').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-company').addClass('d-none');
                $('#edit-customer-company').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-company.show') }}/" + idCustomerCompany,
                    dataType: "json",
                    success: function(response) {
                        let formElement = $('#modal-customer-company').find("form");
                        $('#modal-customer-company').find("form")
                            .find('select, input').map(function(index, element) {
                                let name = (element.name.split('[').length > 1) ? '.' + element.name
                                    .split('[').join('.').split(']').join('') : element.name;
                                if (response.data[name] !== undefined && $("[name='" + element
                                        .name + "']").length != 0) {
                                    if (name == 'picture') {
                                        $("#uploadedAvatar").prop('src',
                                            `{{ url('/') }}/cp/` + response.data[name])
                                    } else {
                                        $("[name='" + element.name + "']").val(response.data[name])
                                    }
                                }
                            });
                        formElement.find("[name='address[place]']")
                            .val(response.data.address.place)
                            .trigger('change');
                        formElement.find("[name='address[address]']")
                            .val(response.data.address.address)
                            .trigger('change');
                        formElement.find("[name='address[city]']")
                            .val(response.data.address.city)
                            .trigger('change');
                        formElement.find("[name='address[province]']")
                            .val(response.data.address.province)
                            .trigger('change');
                        formElement.find("[name='address[zipCode]']")
                            .val(response.data.address.zipCode)
                            .trigger('change');
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-company-action',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        setTimeout(() => {
                            $('#modal-customer-company').modal('hide');
                        }, 400);
                    }
                });
            })

            $('.delete').click(function() {
                if (window.datatableCustomerCompany.rows('.selected').data().length == 0) {
                    $('#table-customer-company tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idCustomerCompany = $(this).data("customer-company");
                var data = window.datatableCustomerCompany.rows('.selected').data()[0];
                iziToast.question({
                    timeout: 5000,
                    layout: 2,
                    close: false,
                    overlay: true,
                    color: 'red',
                    displayMode: 'once',
                    id: 'question',
                    zindex: 9999,
                    title: 'Confirmation',
                    message: "Are you sure you want to delete this company data?",
                    position: 'center',
                    icon: 'bx bx-question-mark',
                    buttons: [
                        ['<button><b>OK</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('man.customer-company.delete') }}/" +
                                    idCustomerCompany,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-company-action',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.datatableCustomerCompany.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-customer-company-action',
                                        title: 'Error',
                                        message: error.responseJSON.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                }
                            });
                        }, true],
                        ['<button>CANCEL</button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                        }],
                    ],
                });
            });
        }

        $(function() {
            window.datatableCustomerCompany = $("#table-customer-company").DataTable({
                ajax: "{{ route('man.customer-company.data-table') }}",
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                columns: [{
                    target: 0,
                    name: 'order_number',
                    data: 'order_number',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 1,
                    name: 'name',
                    data: 'name',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 2,
                    name: 'phone_number',
                    data: 'phone_number',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 3,
                    name: 'business',
                    data: 'business',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'address',
                    data: 'address',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 5,
                    name: 'action',
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }]
            });
            window.datatableCustomerCompany.on('draw.dt', function() {
                actionData();
            });
            $('#save-customer-company').click(function() {
                let data = serializeFiles($('#form-customer-company'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-company.store') }}`,
                    data: data,
                    dataType: "json",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modal-customer-company').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-company-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableCustomerCompany.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-customer-company .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            let name = (indexInArray
                                    .split('.').length > 1) ?
                                `${indexInArray.split('.').join('[')}]` :
                                indexInArray
                            $('#modal-customer-company').find("[name='" + name +
                                "']").addClass('is-invalid');
                            $('#modal-customer-company').find("[name='" + name +
                                "']").siblings('.invalid-feedback').html(
                                valueOfElement[0])
                        });
                        iziToast.error({
                            id: 'alert-customer-company-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-company').click(function() {
                let data = serializeFiles($('#form-customer-company'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-company.update') }}/${$('#form-customer-company').find('[name=id]').val()}`,
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-company').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-company-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableCustomerCompany.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-company .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            let name = (indexInArray
                                    .split('.').length > 1) ?
                                `${indexInArray.split('.').join('[')}]` :
                                indexInArray
                            $('#modal-customer-company').find("[name='" + name +
                                "']").addClass('is-invalid');
                            $('#modal-customer-company').find("[name='" + name +
                                "']").siblings('.invalid-feedback').html(
                                valueOfElement[0])
                        });
                        iziToast.error({
                            id: 'alert-customer-company-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-customer-company').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-company').removeClass('d-none');
                $('#edit-customer-company').addClass('d-none');
                $('#modal-customer-company .is-invalid').removeClass('is-invalid')
                $('#table-customer-company tbody').find('tr').removeClass('selected');
                $('#uploadedAvatar').prop('src', `{{ asset('cp/default-picture.png') }}`);
            });
            $('#modal-customer-company').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-company'),
                    });
                    $('.select2-container').addClass('form-control')
                }, 140);
            });
            formattedInput();
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
        });
    </script>
@endpush
