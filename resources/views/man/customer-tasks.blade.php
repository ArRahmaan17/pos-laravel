@extends('template.parent')
@section('title', 'Task Management')
@push('css')
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-task-management">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Activity</th>
                                    <th scope="col">Percentage</th>
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
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add New @yield('title')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-user">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Task Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Task Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="start_at" class="form-label">Start At</label>
                                <input type="time" readonly value="{{ now()->createFromTimeString($serverTime)->format('H:i') }}" id="start_at"
                                    name="start_at" class="form-control" />
                            </div>
                        </div>
                        @if (in_array(session('userLogged')['role']['name'], ['Developer', 'Manager']))
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select select2">
                                        <option value="" disabled>Choose One</option>
                                        @foreach ($customer_roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->as_role }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="d-flex justify-content-end gap-1">
                            <button type="button" id="unfinish" class="btn btn-warning"><i class='bx bx-task-x'></i> Add Unfinish Task</button>
                            <button type="button" id="new" class="btn btn-warning"><i class='bx bx-task'></i> Add New Task</button>
                        </div>
                        <div class="row container-detail-task"></div>
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
    {{-- <div class="modal fade" id="modal-create-registration-link" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
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
                                    <input type="hidden" name="managerIdLink" value="{{ session('userLogged')['company']['userId'] }}">
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
                                <input type="text" id="time_limit" name="time_limit" placeholder="60 minutes / 1 hours / 1 days"
                                    class="form-control" />
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
    </div> --}}
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.dataTableCustomerTaskManagement = null;
        window.state = 'add';

        function actionData() {
            $('.login-as').click(function() {
                $.ajax({
                    type: "POST",
                    url: `{{ route('auth.login-as') }}/${$(this).data('customer-user')}`,
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
                let idCustomerUser = $(this).data("customer-user");
                $("#edit-customer-user").data("customer-user", idCustomerUser);
                if (window.dataTableCustomerTaskManagement.rows('.selected').data().length == 0) {
                    $('#table-customer-task-management tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerTaskManagement.rows('.selected').data()[0];

                $('#modal-customer-user').modal('show');
                $('#modal-customer-user').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-user').addClass('d-none');
                $('#edit-customer-user').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-user.show') }}/" + idCustomerUser,
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
                if (window.dataTableCustomerTaskManagement.rows('.selected').data().length == 0) {
                    $('#table-customer-task-management tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idCustomerUser = $(this).data("customer-user");
                var data = window.dataTableCustomerTaskManagement.rows('.selected').data()[0];
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
                                    idCustomerUser,
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
                                    window.dataTableCustomerTaskManagement.ajax.reload()
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
            window.dataTableCustomerTaskManagement = $("#table-customer-task-management").DataTable({
                ajax: "{{ route('man.customer-task-management.data-table') }}",
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
                    name: 'customer_roles.name',
                    data: 'customer_roles.name',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 2,
                    name: 'users.username',
                    data: 'users.username',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 3,
                    name: 'activity',
                    data: 'activity',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'customer_company_tasks.percentage',
                    data: 'customer_company_tasks.percentage',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
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
            window.dataTableCustomerTaskManagement.on('draw.dt', function() {
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
                        window.dataTableCustomerTaskManagement.ajax.reload();

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
            $('#new').click(function() {
                let data = {
                    roleId: $('#roleId').val()
                };
                $.ajax({
                    type: "GET",
                    url: `{{ route('man.customer-task-management.new-task') }}`,
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
                        window.dataTableCustomerTaskManagement.ajax.reload()
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
                $('#table-customer-task-management tbody').find('tr').removeClass('selected');
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
