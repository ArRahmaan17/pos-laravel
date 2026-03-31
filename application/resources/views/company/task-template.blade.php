@extends('template.parent')
@section('title', 'Tasks Template')
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
                        <button class="btn btn-outline-success" id="add-task-template" data-bs-toggle="modal" data-bs-target="#modal-task-template">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-task-template">
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
    <div class="modal fade" id="modal-task-template" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add New @yield('title')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-task-template">
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
                            <div class="col-12 mb-3">
                                <label for="role_id" class="form-label">Role</label>
                                <select class="form-control select2" name="role_id" id="role_id">
                                    <option value="">Select Role</option>
                                    @foreach ($customer_roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (getScope() === 'global')
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-2 float-end p-0 m-0">
                                        <div class="form-check form-check-inline p-0 m-0">
                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                            <label class="form-check-label" for="inlineCheckbox1">Make Global</label>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                    <button type="button" id="save-task-template" class="btn btn-outline-success">Save
                        changes</button>
                    <button type="button" id="edit-task-template" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('resource-js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.dataTableCustomerMasterTasks = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idCustomerUser = $(this).data("task-template");
                $("#edit-task-template").data("task-template", idCustomerUser);
                if (window.dataTableCustomerMasterTasks.rows('.selected').data().length === 0) {
                    $('#table-task-template tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerMasterTasks.rows('.selected').data()[0];

                $('#modal-task-template').modal('show');
                $('#modal-task-template').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-task-template').addClass('d-none');
                $('#edit-task-template').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('company.task-template.show') }}/" + idCustomerUser,
                    dataType: "json",
                    success: function(response) {
                        let formElement = $('#modal-task-template form');
                        $.each(response.data, function(indexInArray, valueOfElement) {
                            formElement.find(`[name=${indexInArray}]`).val(valueOfElement).trigger('change');
                        });
                        iziToast.success({
                            id: 'alert-task-template-action',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-task-template-action',
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
                if (window.dataTableCustomerMasterTasks.rows('.selected').data().length === 0) {
                    $('#table-task-template tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idCustomerUser = $(this).data("task-template");
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
                                url: "{{ route('company.task-template.delete') }}/" +
                                    idCustomerUser,
                                data: {

                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-task-template-action',
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
                                        id: 'alert-task-template-action',
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
            window.dataTableCustomerMasterTasks = $("#table-task-template").DataTable({
                ajax: "{{ route('company.task-template.data-table') }}",
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
            $('#save-task-template').click(function() {
                let data = serializeObject($('#form-task-template'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('company.task-template.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-task-template').modal('hide')
                        iziToast.success({
                            id: 'alert-task-template-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerMasterTasks.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-task-template .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-task-template').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-task-template-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-task-template').click(function() {
                let data = serializeObject($('#form-task-template'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('company.task-template.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-task-template').modal('hide')
                        iziToast.success({
                            id: 'alert-task-template-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerMasterTasks.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-task-template .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-task-template').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-task-template-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-task-template').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-task-template').removeClass('d-none');
                $('#edit-task-template').addClass('d-none');
                $('#modal-task-template .is-invalid').removeClass('is-invalid')
                $('#table-task-template tbody').find('tr').removeClass('selected');
            });
            $('#modal-task-template').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-task-template'),

                    });
                }, 140);
            });
            formattedInput();
        });
    </script>
@endpush
