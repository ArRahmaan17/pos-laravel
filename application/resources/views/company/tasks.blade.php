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
                    <button class="btn btn-outline-success" id="add-task-management" data-bs-toggle="modal"
                        data-bs-target="#modal-task-management">Add <i class='bx bxs-file-plus pb-1'></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="table-task-management">
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
<div class="modal fade" id="modal-task-management" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" id="form-task-management">
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
                    @if (in_array(session('userLogged')['role']['scope']['code'], ['Developer', 'Manager']))
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
                            <label for="user_id" class="form-label">User</label>
                            <select id="user_id" name="user_id" class="form-select select2">
                                <option value="" disabled>Choose One</option>
                                @foreach ($employees as $employee)
                                <option disabled data-role="{{ $employee->role_id }}" value="{{ $employee->role_id }}">{{ $employee->name }}
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
                <button type="button" id="save-task-management" class="btn btn-outline-success">Save
                    changes</button>
                <button type="button" id="edit-task-management" class="btn btn-warning d-none">Update
                    changes</button>
            </div>
        </div>
    </div>
</div>
<div id="modal-customer-task-evidence" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add Evidence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" name='task_id' id="task_id" />
                    <input type="file" class="filepond" name="filepond" multiple data-allow-reorder="true" data-max-file-size="3MB"
                        data-max-files="3">
                    </from>
            </div>
        </div>
    </div>
</div>
@endsection
@push('resource-js')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/image-preview-filepond.js') }}"></script>
<script src="{{ asset('assets/js/file-validation-filepond.js') }}"></script>
<script src="{{ asset('assets/js/filepond.min.js') }}"></script>
<script src="{{ asset('assets/js/md5.js') }}"></script>
<script src="{{ asset('assets/js/fancybox.js') }}"></script>
<link rel='stylesheet' type="text/css" href="{{ asset('assets/css/daterangepicker.css') }}">
<link rel='stylesheet' type="text/css" href="{{ asset('assets/css/image-preview-filepond.css') }}">
<link rel='stylesheet' type="text/css" href="{{ asset('assets/css/filepond.min.css') }}">
<link rel='stylesheet' type="text/css" href="{{ asset('assets/css/fancybox.css') }}">
<script>
    window.dataTableCustomerTaskManagement = null;
    window.state = 'add';

    function actionData() {
        $('.start').click((e) => {
            startTask(e)
        });
        $('.edit').click(function() {
            window.state = 'update';
            let idCustomerTask = $(this).data("task-management");
            $("#edit-task-management").data("task-management", idCustomerTask);
            if (window.dataTableCustomerTaskManagement.rows('.selected').data().length === 0) {
                $('#table-task-management tbody').find('tr').removeClass('selected');
                $(this).parents('tr').addClass('selected')
            }

            var data = window.dataTableCustomerTaskManagement.rows('.selected').data()[0];

            $('#modal-task-management').modal('show');
            $('#modal-task-management').find('.modal-title').html(`Edit @yield('title')`);
            $('#save-task-management').addClass('d-none');
            $('#edit-task-management').removeClass('d-none');

            $.ajax({
                type: "GET",
                url: "{{ route('company.task-management.show') }}/" + idCustomerTask,
                dataType: "json",
                success: function(response) {
                    $('#user_id').find('option').removeAttr('disabled');
                    $('#role').attr('disabled', 'disabled');
                    $('#role').parents('.mb-3').addClass('d-none');
                    $('.container-progress-task').removeClass('d-none');
                    $('.container-progress-task .progress-bar').css({
                        "width": `${response.data.percentage}%`
                    });
                    let formElement = $('#modal-task-management').find("form");
                    $.each(response.data, function(indexInArray, valueOfElement) {
                        if (indexInArray === 'time_limit') {
                            formElement.find(`[name=${indexInArray}]`).data('daterangepicker').setStartDate(valueOfElement);
                            formElement.find(`[name=${indexInArray}]`).data('daterangepicker').setEndDate(valueOfElement);
                        } else {
                            formElement.find(`[name=${indexInArray}]`).val(valueOfElement).trigger('change');
                        }
                    });
                    response.data.details.forEach((task, index) => {
                        task.index = $('.container-detail-task .accordion-item.shadow-sm').length;
                        $('.container-detail-task').append(generateDetailTask(task, 'finish'));
                    });
                },
                error: function(error) {
                    iziToast.error({
                        id: 'alert-task-management-action',
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
            if (window.dataTableCustomerTaskManagement.rows('.selected').data().length === 0) {
                $('#table-task-management tbody').find('tr').removeClass('selected');
                $(this).parents('tr').addClass('selected')
            }
            let idCustomerTask = $(this).data("task-management");
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
                message: "Are you sure you want to delete this task data?",
                position: 'center',
                icon: 'bx bx-question-mark',
                buttons: [
                    ['<button><b>OK</b></button>', function(instance, toast) {
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('company.task-management.delete') }}/" +
                                idCustomerTask,
                            data: {

                            },
                            dataType: "json",
                            success: function(response) {
                                iziToast.success({
                                    id: 'alert-task-management-action',
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
                                    id: 'alert-task-management-action',
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
        });
        $('.evidence').click((e) => {
            evidenceTask(e)
        });
        $('.trash').click((e) => {
            removeTaskDetail(e)
        })
    }

    function endTask(e) {
        $('#modal-customer-task-evidence').modal('show');
        $('#task_id').val($(e.currentTarget).data('customer-task-detail'));
    }

    function removeTaskDetail(e) {
        let idDetailTask = $(e.currentTarget).data('customer-task-detail');
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
            message: "Are you sure you want to delete this detail task data?",
            position: 'center',
            icon: 'bx bx-question-mark',
            buttons: [
                ['<button><b>OK</b></button>', function(instance, toast) {
                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('company.task-management.delete-detail') }}/" +
                            idDetailTask,
                        data: {

                        },
                        dataType: "json",
                        success: function(response) {
                            iziToast.success({
                                id: 'alert-task-management-action',
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
                                id: 'alert-task-management-action',
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
    }

    function evidenceTask(e) {
        let files = [];
        $.each($(e.currentTarget).data('task-evidence'), function(indexInArray, valueOfElement) {
            files.push({
                src: `{{ asset('customer-task-evidence/' . md5(session('userLogged')['company']['id'])) }}/${md5(`${$(e.currentTarget).data('customer-task-detail')}`)}/${valueOfElement}`,
                caption: valueOfElement,
            })
        });
        Fancybox.show(files);
    }

    function startTask(e) {
        $.ajax({
            type: "PUT",
            url: `{{ route('company.task-management.start-task') }}/${$(e.currentTarget).data('customer-task-detail')??$(e.currentTarget).data('task-management')}/${$(e.currentTarget).data('customer-task-status')}`,
            data: {

            },
            dataType: "json",
            success: function(response) {
                window.dataTableCustomerTaskManagement.ajax.reload();
                window.filePondEvidence.destroy();
            }
        });
    }

    function generateDetailTask(data, type = 'new') {
        return (`<div class="accordion-item shadow-sm my-1 ${data.status?'border border-success' :type !== 'new' ? 'border border-warning' : ''}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${type}${kebabCase(data.name??data.master.name)}"
                                aria-expanded="false" aria-controls="${type}${kebabCase(data.name??data.master.name)}">
                                <span class="badge bg-label-primary mx-1">${data.priority?? data.master.priority}</span> ${data.status?'Finish':type !== 'new' ? 'Unfinish Task' : 'New Task'} ${data.name??data.master.name} ${(type !== 'new') ? moment(data.created_at).format('YYYY-MM-DD') : moment(`{{ $serverTime }}`).format('YYYY-MM-DD')} 
                            </button>
                        </h2>
                        <div id="${type}${kebabCase(data.name??data.master.name)}" class="accordion-collapse collapse" data-bs-parent="#accordionTaskDetail">
                            <div class="accordion-body">
                                <div class="row">
                                    ${type !== 'new' ? `<input type="hidden" name="details[${data.index}][id]" id="details[${data.index}][id]" value="${data.id}">`:`<input type="hidden" name="details[${data.index}][masterId]" id="details[${data.index}][masterId]" value="${data.id}">` }
                                    <input type="hidden" name="details[${data.index}][type]" id="details[${data.index}][type]" value="${type}">
                                    <div class="col">
                                        ${data.description?? data.master.description}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`);
    }
    const unfinishTask = () => {
        $.ajax({
            type: "GET",
            url: `{{ route('company.task-management.unfinish-task') }}`,
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
                    task.index = $('.container-detail-task .accordion-item.shadow-sm').length;
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
            url: `{{ route('company.task-management.new-task') }}`,
            data: {
                role_id: $('#role_id').val()
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
                    task.index = $('.container-detail-task .accordion-item.shadow-sm').length;
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
                `<div class="accordion-item shadow-sm my-1 ${detail.start_at !== null && detail.end_at !== null  ? 'border border-success' : (detail.start_at != null &&  detail.end_at === null)? 'border border-info': 'border border-warning'}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${kebabCase(detail.master.name)}${detail.id}"
                                aria-expanded="false" aria-controls="${kebabCase(detail.master.name)}${detail.id}">
                                <span class="badge bg-label-primary mx-1">${detail.master.priority}</span> ${detail.end_at !== null && detail.start_at != null ? 'Finish Task' : (detail.start_at !== null && detail.end_at === null)?'Unfinish Task' : 'New Task'} ${detail.master.name} ${(detail.end_at !== null) ? moment(detail?.created_at).format('YYYY-MM-DD') : moment(`{{ $serverTime }}`).format('YYYY-MM-DD')} 
                            </button>
                        </h2>
                        <div id="${kebabCase(detail.master.name)}${detail.id}" class="accordion-collapse collapse" data-bs-parent="#accordionDetailTable">
                            <div class="accordion-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-fill align-self-center">
                                        ${detail.master.description}
                                    </div>
                                    <div class="flex-fill align-self-center d-flex gap-1 justify-content-end">
                                        ${detail.start_at !== null && detail.end_at !== null  ? `<button class="btn btn-icon btn-outline-success evidence" data-customer-task-detail='${detail.id}' data-task-management='${detail.task_id}' data-task-evidence='${detail.evidence}'><i class='bx bxs-file-find'></i></button>`: (detail.start_at != null && detail.end_at === null)?`<button type="button" data-customer-task-detail='${detail.id}' data-task-management='${detail.task_id}' class="btn btn-icon btn-info end"><i class='bx bx-check-double'></i></button>`:`<button type="button" class="btn btn-icon btn-outline-warning start" data-customer-task-detail='${detail.id}' data-customer-task-status='unfinish' data-task-management='${detail.task_id}'><i class='bx bx-play'></i></button><button type="button" class="btn btn-icon btn-outline-danger trash" data-customer-task-detail='${detail.id}' data-customer-task-status='unfinish' data-task-management='${detail.task_id}'><i class='bx bxs-trash-alt'></i></button>`}
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
        window.dataTableCustomerTaskManagement = $("#table-task-management").DataTable({
            ajax: "{{ route('company.task-management.data-table') }}",
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
                $('.evidence').on('click', (e) => {
                    evidenceTask(e)
                });
                $('.trash').click((e) => {
                    removeTaskDetail(e)
                })
            }
        });
        $('#save-task-management').click(function() {
            let data = serializeObject($('#form-task-management'));
            $.ajax({
                type: "POST",
                url: `{{ route('company.task-management.store') }}`,
                data: data,
                dataType: "json",
                success: function(response) {
                    $('#modal-task-management').modal('hide')
                    iziToast.success({
                        id: 'alert-task-management-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                    window.dataTableCustomerTaskManagement.ajax.reload();
                },
                error: function(error) {
                    $('#modal-task-management .is-invalid').removeClass('is-invalid')
                    $.each(error.responseJSON.errors, function(indexInArray,
                        valueOfElement) {
                        $('#modal-task-management').find('[name=' + indexInArray +
                            ']').addClass('is-invalid')
                    });
                    iziToast.error({
                        id: 'alert-task-management-form',
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
        $('#edit-task-management').click(function() {
            let data = serializeObject($('#form-task-management'));
            $.ajax({
                type: "PUT",
                url: `{{ route('company.task-management.update') }}/${data.id}`,
                data: data,
                dataType: "json",
                success: function(response) {
                    $('#modal-task-management').modal('hide')
                    iziToast.success({
                        id: 'alert-task-management-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                    window.dataTableCustomerTaskManagement.ajax.reload()
                },
                error: function(error) {
                    $('#modal-task-management .is-invalid').removeClass('is-invalid')
                    $.each(error.responseJSON.errors, function(indexInArray,
                        valueOfElement) {
                        $('#modal-task-management').find('[name=' + indexInArray +
                            ']').addClass('is-invalid')
                    });
                    iziToast.error({
                        id: 'alert-task-management-form',
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
            $('#user_id').find(`option[data-role=${this.value}]`).removeAttr('disabled');
            $('#user_id').find(`option:not([data-role=${this.value}])`).attr('disabled', 'disabled');
        })
        $('#modal-task-management').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $(this).find('.modal-title').html(`Add New @yield('title')`);
            $('#save-task-management').removeClass('d-none');
            $('#edit-task-management').addClass('d-none');
            $('#role').removeAttr('disabled');
            $('#role').parents('.mb-3').removeClass('d-none');
            $('#modal-task-management .is-invalid').removeClass('is-invalid')
            $('#table-task-management tbody').find('tr').removeClass('selected');
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
        $('#modal-task-management').on('shown.bs.modal', function() {
            setTimeout(() => {
                $('.select2').select2({
                    dropdownParent: $('#modal-task-management'),
                });
            }, 140);
        });
        $('#modal-customer-task-evidence').on('shown.bs.modal', function() {
            setTimeout(() => {
                let id = $('#task_id').val();
                window.filePondEvidence = FilePond.create(
                    document.querySelector('.filepond'), {
                        maxParallelUploads: 3,
                        acceptedFileTypes: ['image/*'],
                        checkValidity: true,
                        credits: ['https://github.com/users/ArRahmaan17', 'DOGLEX'],
                        server: {
                            timeout: 7000,
                            process: {
                                url: `{{ route('company.task-management.finish-task') }}/${id}/upload`,
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                onload: (response) => {
                                    window.filePondEvidence.removeFiles();
                                    $('#modal-customer-task-evidence').modal('hide');
                                    window.dataTableCustomerTaskManagement.ajax.reload()
                                },
                                onerror: (response) => response.data,
                            },
                        }
                    }
                );
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
            parentEl: '#modal-task-management .modal-body'
        });
        formattedInput();
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize,
        );
    });
</script>
@endpush