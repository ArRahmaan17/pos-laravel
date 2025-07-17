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
                        <button class="btn btn-success" id="add-customer-task-management" data-bs-toggle="modal"
                            data-bs-target="#modal-customer-task-management">Add <i class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-task-management">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">User Name</th>
                                    <th scope="col">Activity</th>
                                    <th scope="col">Percentage</th>
                                    <th scope="col">Dead Line</th>
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
    <div class="modal fade" id="modal-customer-task-management" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add New @yield('title')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-task-management">
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
                                <label for="time_limit" class="form-label">Deadline</label>
                                <input type="text" id="time_limit" name="time_limit" class="datepicker form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="text" readonly value="{{ now()->createFromTimeString($serverTime)->format('Y-m-d') }}" id="date"
                                    name="date" class="form-control" />
                            </div>
                        </div>
                        @if (in_array(session('userLogged')['role']['name'], ['Developer', 'Manager']))
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select id="role" name="role" class="form-select select2">
                                        <option value="">Choose One</option>
                                        @foreach ($customer_roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->as_role }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="userId" class="form-label">User</label>
                                    <select id="userId" name="userId" class="form-select select2">
                                        <option value="" disabled>Choose One</option>
                                        @foreach ($employees as $employee)
                                            <option disabled data-role="{{ $employee->roleId }}" value="{{ $employee->roleId }}">{{ $employee->name }}
                                                ({{ $employee->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 my-1 container-progress-task d-none">
                            <h5>Task Progress</h5>
                            <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-1">
                            <button type="button" id="unfinish" class="btn btn-warning"><i class='bx bx-task-x'></i> Add Unfinish Task</button>
                            <button type="button" id="new" class="btn btn-warning"><i class='bx bx-task'></i> Add New Task</button>
                        </div>
                        <div class="row my-2">
                            <div class="accordion container-detail-task" id="accordionTaskDetail">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-task-management" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-task-management" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-customer-task-evidance" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add Evidance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" name='taskId' id="taskId" />
                        <input type="file" class="filepond" name="filepond" multiple data-allow-reorder="true" data-max-file-size="3MB"
                            data-max-files="3">
                        </from>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-task-management" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-task-management" class="btn btn-warning d-none">Update
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
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/image-preview-filepond.js') }}"></script>
    <script src="{{ asset('assets/js/file-validation-filepond.js') }}"></script>
    <script src="{{ asset('assets/js/filepond.min.js') }}"></script>
    <link rel='stylesheet' type="text/css" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link rel='stylesheet' type="text/css" href="{{ asset('assets/css/image-preview-filepond.css') }}">
    <link rel='stylesheet' type="text/css" href="{{ asset('assets/css/filepond.min.css') }}">
    <script>
        window.dataTableCustomerTaskManagement = null;
        window.state = 'add';

        function actionData() {
            $('.start').click((e) => {
                startTask(e)
            });
            $('.edit').click(function() {
                window.state = 'update';
                let idCustomerUser = $(this).data("customer-task-management");
                $("#edit-customer-task-management").data("customer-task-management", idCustomerUser);
                if (window.dataTableCustomerTaskManagement.rows('.selected').data().length == 0) {
                    $('#table-customer-task-management tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerTaskManagement.rows('.selected').data()[0];

                $('#modal-customer-task-management').modal('show');
                $('#modal-customer-task-management').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-task-management').addClass('d-none');
                $('#edit-customer-task-management').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-task-management.show') }}/" + idCustomerUser,
                    dataType: "json",
                    success: function(response) {
                        let formElement = $('#modal-customer-task-management').find("form");
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
                            id: 'alert-customer-task-management-action',
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
                let idCustomerUser = $(this).data("customer-task-management");
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
                                url: "{{ route('man.customer-task-management.delete') }}/" +
                                    idCustomerUser,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-task-management-action',
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
                                        id: 'alert-customer-task-management-action',
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
            $('.end').click((e) => {
                endTask(e)
            })
        }

        function endTask(e) {
            $('#modal-customer-task-evidance').modal('show')
        }

        function startTask(e) {
            console.log(e)
            $.ajax({
                type: "PUT",
                url: `{{ route('man.customer-task-management.start-task') }}/${$(e.currentTarget).data('customer-task-management')}/${$(e.currentTarget).data('customer-task-status')}`,
                data: {
                    '_token': `{{ csrf_token() }}`
                },
                dataType: "json",
                success: function(response) {
                    window.dataTableCustomerTaskManagement.ajax.reload()
                }
            });
        }

        function generateDetailTask(data, type = 'new') {
            return (`<div class="accordion-item shadow-sm my-1 ${type !== 'new' ? 'border border-warning' : ''}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${detail.id}${kebabCase(data.name)}"
                                aria-expanded="false" aria-controls="${type}${kebabCase(data.name)}">
                                <span class="badge bg-label-primary mx-1">${data.priority}</span> ${type !== 'new' ? 'Unfinish Task' : 'New Task'} ${data.name} ${(type !== 'new') ? moment(data.created_at).format('YYYY-MM-DD') : moment(`{{ $serverTime }}`).format('YYYY-MM-DD')} 
                            </button>
                        </h2>
                        <div id="${type}${kebabCase(data.name)}" class="accordion-collapse collapse" data-bs-parent="#accordionTaskDetail">
                            <div class="accordion-body">
                                <div class="row">
                                    ${type !== 'new' ? `<input type="hidden" name="details[${data.index}][id]" id="details[${data.index}][id]" value="${data.id}">`:`<input type="hidden" name="details[${data.index}][masterId]" id="details[${data.index}][masterId]" value="${data.id}">` }
                                    <input type="hidden" name="details[${data.index}][type]" id="details[${data.index}][type]" value="${type}">
                                    <div class="col">
                                        ${data.description}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`);
        }
        const unfinishTask = () => {
            $.ajax({
                type: "GET",
                url: `{{ route('man.customer-task-management.unfinish-task') }}`,
                dataType: "json",
                success: function(response) {
                    iziToast.success({
                        id: 'alert-create-registration-link-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 1,
                        displayMode: 'replace'
                    });
                    response.data.forEach(task => {
                        $('.container-detail-task').append(generateDetailTask(task), 'unfinish');
                    });
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
            $('#unfinish').addClass('disabled')
            $('#unfinish').off('click');
            $('.container-progress-task').removeClass('d-none')
        }
        const newTask = () => {
            $.ajax({
                type: "GET",
                url: `{{ route('man.customer-task-management.new-task') }}`,
                data: {
                    roleId: $('#roleId').val()
                },
                dataType: "json",
                success: function(response) {
                    iziToast.success({
                        id: 'alert-create-registration-link-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 1,
                        displayMode: 'replace'
                    });
                    response.data.forEach((task, index) => {
                        task.index = index;
                        $('.container-detail-task').append(generateDetailTask(task));
                    });
                    $('#new').addClass('disabled')
                    $('#new').off('click');
                    $('.container-progress-task').removeClass('d-none')
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
        }

        function detailTableCustomerTemporaryProduct(d) {
            let contentTableBody = ``;
            d.details.forEach(detail => {
                contentTableBody +=
                    `<div class="accordion-item shadow-sm my-1 ${detail.end_at !== null ? 'border border-warning' : ''}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${kebabCase(detail.master.name)}${detail.id}"
                                aria-expanded="false" aria-controls="${kebabCase(detail.master.name)}${detail.id}">
                                <span class="badge bg-label-primary mx-1">${detail.master.priority}</span> ${detail.end_at !== null ? 'Unfinish Task' : 'New Task'} ${detail.master.name} ${(detail.end_at !== null) ? moment(detail?.created_at).format('YYYY-MM-DD') : moment(`{{ $serverTime }}`).format('YYYY-MM-DD')} 
                            </button>
                        </h2>
                        <div id="${kebabCase(detail.master.name)}${detail.id}" class="accordion-collapse collapse" data-bs-parent="#accordionDetailTable">
                            <div class="accordion-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-fill align-self-center">
                                        ${detail.master.description}
                                    </div>
                                    <div class="flex-fill align-self-center text-end">
                                        ${detail.start_at !== null ? `<button type="button" data-customer-task-management='${detail.taskId}' class="btn btn-icon btn-warning end"><i class='bx bx-check-double'></i></button>`:`<button type="button" class="btn btn-icon btn-success start" data-customer-task-status='unfinish' data-customer-task-management='${detail.taskId}'><i class='bx bx-play'></i></button>` }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`
            });
            return (`<div class="row my-2">
                        <div class="accordion container-detail-task" id="accordionDetailTable">
                            ${contentTableBody}
                        </div>
                    </div>`)
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
                    class: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
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
                    name: 'users.name',
                    data: 'user_name',
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
                    name: 'percentage',
                    data: 'percentage',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 5,
                    name: 'time_limit',
                    data: 'time_limit',
                    orderable: false,
                    searchable: false,
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
            window.dataTableCustomerTaskManagement.on('draw.dt', function() {
                actionData();
            });
            window.dataTableCustomerTaskManagement.on('click', 'tbody td.dt-control', function() {
                let tr = event.target.closest('tr');
                let row = window.dataTableCustomerTaskManagement.row(tr);

                if (row.child.isShown()) {
                    tr.classList.remove('details');
                    row.child.hide();
                } else {
                    $('.start').off('click');
                    tr.classList.add('details');
                    row.child(detailTableCustomerTemporaryProduct(row.data())).show();
                    $('.start').on('click', (e) => {
                        startTask(e)
                    });
                    $('.end').on('click', (e) => {
                        endTask(e)
                    });
                }
            });
            $('#save-customer-task-management').click(function() {
                let data = serializeObject($('#form-customer-task-management'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-task-management.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-task-management').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-task-management-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerTaskManagement.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-customer-task-management .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-task-management').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-task-management-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#new').on('click', () => newTask());
            $('#unfinish').on('click', () => unfinishTask());
            $('#edit-customer-task-management').click(function() {
                let data = serializeObject($('#form-customer-task-management'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('man.customer-task-management.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-task-management').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-task-management-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerTaskManagement.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-task-management .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-task-management').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-task-management-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#role').change(function() {
                $('#userId').find(`option[data-role=${this.value}]`).removeAttr('disabled');
                $('#userId').find(`option:not([data-role=${this.value}])`).attr('disabled', 'disabled');
            })
            $('#modal-customer-task-management').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-task-management').removeClass('d-none');
                $('#edit-customer-task-management').addClass('d-none');
                $('#modal-customer-task-management .is-invalid').removeClass('is-invalid')
                $('#table-customer-task-management tbody').find('tr').removeClass('selected');
                $('.container-detail-task').html(``);
                if ($('#unfinish').hasClass('disabled')) {
                    $('#unfinish').removeClass('disabled')
                    $('#unfinish').on('click', () => unfinishTask());
                }
                if ($('#new').hasClass('disabled')) {
                    $('#new').removeClass('disabled')
                    $('#new').on('click', () => newTask());
                }
                $('.container-progress-task').addClass('d-none');
            });
            $('#modal-customer-task-management').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-task-management'),

                    });
                }, 140);
            });
            $('#time_limit').daterangepicker({
                singleDatePicker: true,
                showDropdowns: false,
                opens: 'down',
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: moment(),
                parentEl: '#modal-customer-task-management .modal-body'
            });
            formattedInput();
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
            );

            // Select the file input and use 
            // create() to turn it into a pond
            FilePond.create(
                document.querySelector('.filepond')
            );
        });
    </script>
@endpush
