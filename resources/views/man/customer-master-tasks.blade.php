@extends('template.parent')
@section('title', 'Master Tasks')
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
                        <button class="btn btn-success" id="add-customer-master-tasks" data-bs-toggle="modal" data-bs-target="#modal-customer-master-tasks">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-master-tasks">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Priority</th>
                                    <th scope="col">Repeateable</th>
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
    <div class="modal fade" id="modal-customer-master-tasks" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add New @yield('title')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-master-tasks">
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
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" placeholder="Enter Description" class="form-control" style="resize:none" id="description" cols="10" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="roleId" class="form-label">Role</label>
                                <select class="form-control select2" name="roleId" id="roleId">
                                    <option value="" disabled>Select Role</option>
                                    @foreach ($customer_roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->as_role }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-control select2" name="priority" id="priority">
                                    <option value="" disabled>Select Priority</option>
                                    <option value="P1">P1 - Must Have (Urgent & Important)</option>
                                    <option value="P2">P2 - Should Have (Important, Not Urgent)</option>
                                    <option value="P3">P3 - Nice to Have (Not Important, Not Urgent)</option>
                                    <option value="P4">P4 - Optional/Low Impact</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="repeateable" class="form-label">Repeateable</label>
                                <select class="form-control select2" name="repeateable" id="repeateable">
                                    <option value="" disabled>Select Repeateable</option>
                                    <option value="1">Repeateable</option>
                                    <option value="0">Not Repeateable</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-master-tasks" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-master-tasks" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.dataTableCustomerMasterTasks = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idCustomerUser = $(this).data("customer-master-tasks");
                $("#edit-customer-master-tasks").data("customer-master-tasks", idCustomerUser);
                if (window.dataTableCustomerMasterTasks.rows('.selected').data().length == 0) {
                    $('#table-customer-master-tasks tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerMasterTasks.rows('.selected').data()[0];

                $('#modal-customer-master-tasks').modal('show');
                $('#modal-customer-master-tasks').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-master-tasks').addClass('d-none');
                $('#edit-customer-master-tasks').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-master-tasks.show') }}/" + idCustomerUser,
                    dataType: "json",
                    success: function(response) {
                        let formElement = $('#modal-customer-master-tasks form');
                        $.each(response.data, function(indexInArray, valueOfElement) {
                            formElement.find(`[name=${indexInArray}]`).val(valueOfElement).trigger('change');
                        });
                        iziToast.success({
                            id: 'alert-customer-master-tasks-action',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-master-tasks-action',
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
                if (window.dataTableCustomerMasterTasks.rows('.selected').data().length == 0) {
                    $('#table-customer-master-tasks tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idCustomerUser = $(this).data("customer-master-tasks");
                var data = window.dataTableCustomerMasterTasks.rows('.selected').data()[0];
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
                                url: "{{ route('man.customer-master-tasks.delete') }}/" +
                                    idCustomerUser,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-master-tasks-action',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.dataTableCustomerMasterTasks.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-customer-master-tasks-action',
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
            window.dataTableCustomerMasterTasks = $("#table-customer-master-tasks").DataTable({
                ajax: "{{ route('man.customer-master-tasks.data-table') }}",
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
                    name: 'description',
                    data: 'description',
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
                    name: 'priority',
                    data: 'priority',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 5,
                    name: 'repeateable',
                    data: 'repeateable',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 6,
                    name: 'action',
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }]
            });
            window.dataTableCustomerMasterTasks.on('draw.dt', function() {
                actionData();
            });
            $('#save-customer-master-tasks').click(function() {
                let data = serializeObject($('#form-customer-master-tasks'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-master-tasks.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-master-tasks').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-master-tasks-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerMasterTasks.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-customer-master-tasks .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-master-tasks').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-master-tasks-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-master-tasks').click(function() {
                let data = serializeObject($('#form-customer-master-tasks'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('man.customer-master-tasks.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-master-tasks').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-master-tasks-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerMasterTasks.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-master-tasks .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-master-tasks').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-master-tasks-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-customer-master-tasks').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-master-tasks').removeClass('d-none');
                $('#edit-customer-master-tasks').addClass('d-none');
                $('#modal-customer-master-tasks .is-invalid').removeClass('is-invalid')
                $('#table-customer-master-tasks tbody').find('tr').removeClass('selected');
            });
            $('#modal-customer-master-tasks').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-master-tasks'),

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
