@extends('template.parent')
@section('title', 'User Management')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <style>
        .code-container {
            position: relative;
            display: block;
            background-color: #292D3E;
            /* padding: 10px; */
            border-radius: 5px;
            overflow: hidden;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff5d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }

        code {
            position: relative;
            margin: 0;
            color: #ffffff;
        }

        .copy-btn:hover {
            background-color: #0056b3;
        }
    </style>
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
                        <button class="btn btn-success" id="add-customer-user" data-bs-toggle="modal" data-bs-target="#modal-customer-user">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-registration-link">Generate Registration Link <i
                                class='bx bx-link-alt pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-user">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Role</th>
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
    <div class="modal fade" id="modal-customer-user" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add New @yield('title')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">The user account created here will use the default password: <b>{{ defaultPassword() }}</b> please note the
                        username and password if you have more than 1 companies in our application</div>
                    <form action="#" id="form-customer-user">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Enter Username" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="email" class="form-label">email</label>
                                <input type="text" id="email" name="email" class="form-control email" placeholder="Enter Email" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="phone_number" class="form-label">phone number</label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control phone_number"
                                    placeholder="Enter Phone Number" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                @if (in_array(getRole(), ['Manager', 'Developer']))
                                    <label for="roleId" class="form-label">Role User</label>
                                    <select class="form-control select2" name="roleId" id="roleId">
                                        <option value="">Select Role</option>
                                        @foreach ($customer_roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="roleId" value="{{ session('userLogged')['company']['userId'] }}">
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-user" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-user" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-create-registration-link" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Registration Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-create-registration-link">
                        @csrf
                        <div class="row">
                            <div class="col mb-3">
                                <label for="managerIdLink" class="form-label">Customer User</label>
                                @if (getRole() === 'Developer')
                                    <select class="form-control select2" name="managerIdLink" id="managerIdLink">
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}
                                                ({{ $user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
<<<<<<< HEAD
                                    <input type="hidden" name="managerIdLink" value="{{ session('userLogged')['user']['id'] }}">
=======
                                    <input type="hidden" name="managerIdLink" value="{{ session('userLogged')['company']['userId'] }}">
>>>>>>> cb3cc10 (feat: product transaction (model), authentication (module), app good unit (module, model), customer company (module), customer company discount (module,model, migration), company good (module,migration), company warehouse (module), customer role (module, model), warehouse rack (module), user customer (module), check authorization page (middleware), customer role (model))
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                @if (getRole() === 'Developer')
                                    <label for="customerRoleIdLink" class="form-label">Customer User Role</label>
                                    <select class="form-control select2" name="customerRoleIdLink" id="customerRoleIdLink">
                                        <option value="">Select Role</option>
                                        @foreach ($customer_roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="managerIdLink" value="{{ session('userLogged')['role']['id'] }}">
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="time_limit" class="form-label">LifeTime Link</label>
<<<<<<< HEAD
                                <input type="text" id="time_limit" name="time_limit" class="form-control" placeholder="(Minutes/Hours/Days)" />
=======
                                <input type="text" id="time_limit" name="time_limit" placeholder="60 minutes / 1 hours / 1 days" class="form-control"
                                    placeholder="(Minutes/Hours/Days)" />
>>>>>>> cb3cc10 (feat: product transaction (model), authentication (module), app good unit (module, model), customer company (module), customer company discount (module,model, migration), company good (module,migration), company warehouse (module), customer role (module, model), warehouse rack (module), user customer (module), check authorization page (middleware), customer role (model))
                            </div>
                        </div>
                        <div id="container-link" class="row px-3 d-none">
                            <p>Your Link</p>
                            <div class="col-12 mb-3 code-container">
                                <code id="registration-link-code">
                                </code>
                                <button class="copy-btn" type="button" onclick="copyToClipboard()">Copy</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="create-registration-link" class="btn btn-primary"><i class='bx bx-key'></i>Generate</button>
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
        window.dataTableAppRole = null;
        window.state = 'add';

        function actionData() {
            $('.login-as').click(function() {
                $.ajax({
                    type: "POST",
                    url: `{{ route('auth.login-as') }}/${$(this).data('user-customer')}`,
                    data: {
                        '_token': `{{ csrf_token() }}`
                    },
                    dataType: "json",
                    success: function(response) {
                        location.reload();
                    }
                });
            });
            $('.edit').click(function() {
                window.state = 'update';
                let idAppRole = $(this).data("customer-user");
                $("#edit-customer-user").data("customer-user", idAppRole);
                if (window.dataTableAppRole.rows('.selected').data().length == 0) {
                    $('#table-customer-user tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableAppRole.rows('.selected').data()[0];

                $('#modal-customer-user').modal('show');
                $('#modal-customer-user').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-user').addClass('d-none');
                $('#edit-customer-user').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-user.show') }}/" + idAppRole,
                    dataType: "json",
                    success: function(response) {
                        let formElement = $('#modal-customer-user').find("form");
                        formElement.find('[name=id]')
                            .val(response.data[0].user.id)
                            .trigger('change');
                        formElement.find('[name=name]')
                            .val(response.data[0].user.name)
                            .trigger('change');
                        formElement.find('[name=username]')
                            .val(response.data[0].user.username)
                            .trigger('change');
                        formElement.find('[name=email]')
                            .val(response.data[0].user.email)
                            .trigger('change');
                        formElement.find('[name=phone_number]')
                            .val(response.data[0].user.phone_number)
                            .trigger('change');
                        formElement.find('[name=roleId]')
                            .val(response.data[0].role.id)
                            .trigger('change')
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-user-action',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            })

            $('.delete').click(function() {
                if (window.dataTableAppRole.rows('.selected').data().length == 0) {
                    $('#table-customer-user tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppRole = $(this).data("customer-user");
                var data = window.dataTableAppRole.rows('.selected').data()[0];
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
                    message: "Are you sure you want to delete this user data?",
                    position: 'center',
                    icon: 'bx bx-question-mark',
                    buttons: [
                        ['<button><b>OK</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('man.customer-user.delete') }}/" +
                                    idAppRole,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-user-action',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.dataTableAppRole.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-customer-user-action',
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
            window.dataTableAppRole = $("#table-customer-user").DataTable({
                ajax: "{{ route('man.customer-user.data-table') }}",
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
                    name: 'role',
                    data: 'role',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'action',
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }]
            });
            window.dataTableAppRole.on('draw.dt', function() {
                actionData();
            });
            $('#save-customer-user').click(function() {
                let data = serializeObject($('#form-customer-user'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-user.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-user').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-user-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableAppRole.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-customer-user .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-user').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-user-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#create-registration-link').click(function() {
                let data = serializeObject($('#form-create-registration-link'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-user.registration-link') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#container-link').removeClass('d-none');
                        iziToast.success({
                            id: 'alert-create-registration-link-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 1,
                            displayMode: 'replace'
                        });
                        $('#container-link').find('code').html(response.link)
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-create-registration-link-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-user').click(function() {
                let data = serializeObject($('#form-customer-user'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('man.customer-user.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-user').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-user-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableAppRole.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-user .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-user').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-user-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-customer-user').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-user').removeClass('d-none');
                $('#edit-customer-user').addClass('d-none');
                $('#modal-customer-user .is-invalid').removeClass('is-invalid')
                $('#table-customer-user tbody').find('tr').removeClass('selected');
            });
            $('#modal-customer-user').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-user'),

                    });
                }, 140);
            });
            $('#modal-create-registration-link').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-create-registration-link'),

                    });
                }, 140);
            });
            $('#managerIdLink').change(function(e) {
                let id = e.currentTarget.value;
                $.ajax({
                    type: "get",
                    url: `{{ route('man.customer-role.role') }}/${(`{{ getRole() }}` === 'Developer' ) ? id : `{{ session('userLogged')['user']['id'] }}`}`,
                    dataType: "json",
                    success: function(response) {
                        $('#customerRoleIdLink').html()
                    }
                });
            });
            $('#time_limit').inputmask('9[9][9] [minutes]|[hours]|[days]');
            formattedInput();
        });
    </script>
@endpush
