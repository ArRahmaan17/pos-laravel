@extends('template.parent')
@section('title', 'Permissions')
@push('resource-css')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
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
                    <button class="btn btn-outline-success" id="add-permission" data-bs-toggle="modal" data-bs-target="#modal-permission">Add <i
                            class='bx bxs-file-plus pb-1'></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="table-permission">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Permission</th>
                                <th scope="col">child</th>
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
<div class="modal fade" id="modal-permission" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-fullscreen" menu="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel3">Add New @yield('title')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" id="form-permission">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <select id="name" class="form-select select2">
                                <option value="">Pilih Salah Satu</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="accordion mt-3" id="accordionExample">
                            <div class="card shadow-none border-2 p-0 accordion-item active">
                                <h2 class="accordion-header" id="headingOne">
                                    <button type="button" class="accordion-button p-2" data-bs-toggle="collapse" data-bs-target="#accordionOne"
                                        aria-expanded="true" aria-controls="accordionOne">
                                        Application Menu
                                    </button>
                                </h2>
                                <div id="accordionOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                    <div class="container-fluid">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="all" id="all">
                                            <label for="all" class="form-check-label">
                                                ALL
                                            </label>
                                        </div>
                                        <div class="container-fluid">
                                            {!! buildMenuRoleAccessibillity($routes) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" id="save-permission" class="btn btn-outline-success">Save
                    changes</button>
                <button type="button" id="edit-permission" class="btn btn-warning d-none">Update
                    changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('resource-js')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endpush
@push('resource-js')
<script>
    window.dataTablePermission = null;
    window.state = 'add';

    function actionData() {
        $('.edit').click(function() {
            window.state = 'update';
            let idAppMenu = $(this).data("permission");
            $("#edit-permission").data("permission", idAppMenu);
            if (window.dataTableAppMenu.rows('.selected').data().length === 0) {
                $('#table-permission tbody').find('tr').removeClass('selected');
                $(this).parents('tr').addClass('selected')
            }

            var data = window.dataTableAppMenu.rows('.selected').data()[0];

            $('#modal-permission').modal('show');
            $('#modal-permission').find('.modal-title').html(`Edit @yield('title')`);
            $('#save-permission').addClass('d-none');
            $('#edit-permission').removeClass('d-none');

            $.ajax({
                type: "GET",
                url: "{{ route('settings.permission.show') }}/" + idAppMenu,
                dataType: "json",
                success: function(response) {
                    let childHtml = '';
                    $('#modal-permission').find("form")
                        .find('input, select').map(function(index, element) {
                            if (response.data[element.name] != undefined) {
                                if (element.name === 'dev_only') {
                                    $(`[name=${element.name}]`)
                                        .prop('checked', response.data[element
                                            .name] === 1 ? true : false)
                                } else if (element.name === 'place') {
                                    $(`[name=${element.name}][value=${response.data[element
                                                .name]}]`).prop('checked', true)
                                } else {
                                    $(`[name=${element.name}]`).val(response.data[element
                                        .name]);
                                }
                            }
                        });
                    if (response.data.child.length != 0) {
                        response.data.child.map((element) => {
                            childHtml += `<div class="col-12">${element.name}</div>`;
                        })
                    } else {
                        childHtml += `<div class="col-12">Not Found</div>`;
                    }
                    $('#child-menu-container').html(childHtml)
                },
                error: function(error) {
                    iziToast.error({
                        id: 'alert-permission-action',
                        title: 'Error',
                        message: error.responseJSON.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
        });
        $('.parent').click(function() {
            window.state = 'add';
            let idAppMenu = $(this).data("permission");
            $("#edit-permission").data("permission", idAppMenu);
            if (window.dataTableAppMenu.rows('.selected').data().length === 0) {
                $('#table-permission tbody').find('tr').removeClass('selected');
                $(this).parents('tr').addClass('selected')
            }

            var data = window.dataTableAppMenu.rows('.selected').data()[0];

            $('#modal-permission').modal('show');
            $('#modal-permission').find('.modal-title').html(`Add Child @yield('title')`);
            $('#save-permission').removeClass('d-none');
            $('#edit-permission').addClass('d-none');
            $('#modal-permission')
                .find('form select')
                .val(idAppMenu)
                .trigger('change');
            if (data.place) {
                $('#modal-permission')
                    .find('form input#place-profile')
                    .attr('checked', 'checked')
            } else {
                $('#modal-permission')
                    .find('form input#place-sidebar')
                    .attr('checked', 'checked')
            }
            let childHtml = '';

            if (data.child.length != 0) {
                data.child.map((element) => {
                    childHtml += `<div class="col-12">${element.name}</div>`;
                })
            } else {
                childHtml += `<div class="col-12">Not Found</div>`;
            }
            $('#child-menu-container').html(childHtml)
            setTimeout(() => {
                $('#modal-permission')
                    .find('form select')
                    .prop("disabled", true);
            }, 200);
        })

        $('.delete').click(function() {
            if (window.dataTableAppMenu.rows('.selected').data().length === 0) {
                $('#table-permission tbody').find('tr').removeClass('selected');
                $(this).parents('tr').addClass('selected')
            }
            let idAppMenu = $(this).data("permission");
            var data = window.dataTableAppMenu.rows('.selected').data()[0];
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
                message: "Are you sure you want to delete this application's menu data?",
                position: 'center',
                icon: 'bx bx-question-mark',
                buttons: [
                    ['<button><b>OK</b></button>', function(instance, toast) {
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('settings.permission.delete') }}/" +
                                idAppMenu,
                            data: {

                            },
                            dataType: "json",
                            success: function(response) {
                                iziToast.success({
                                    id: 'alert-permission-form',
                                    title: 'Success',
                                    message: message,
                                    position: 'topRight',
                                    layout: 2,
                                    displayMode: 'replace'
                                });
                                window.dataTableAppMenu.ajax.reload()
                            },
                            error: function(error) {
                                iziToast.error({
                                    id: 'alert-permission-action',
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

    function containerChecker(firstContainer = $('.modal-body #accordionExample .container-fluid:first').children('.container-fluid')) {
        $.map(firstContainer, function(element, index) {
            if ($(element).children('.container-fluid').length > 0) {
                containerChecker($(element).children('.container-fluid'))
            } else {
                $(element).addClass('my-2 d-flex flex-wrap justify-content-left gap-3')
            }
        });
    }

    $(function() {
        window.dataTableAppMenu = $("#table-permission").DataTable({
            ajax: "{{ route('settings.permission.data-table') }}",
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
                name: 'child',
                data: 'child',
                orderable: false,
                searchable: false,
                render: (data, type, row, meta) => {
                    return `<div class='text-wrap'><div class='row'>${data.map((element, index)=>{
                                return `<div class='col-12 border-bottom'>${index+1}. ${element.name}</div>`
                            })}</div></div>`
                }
            }, {
                target: 3,
                name: 'action',
                data: 'action',
                orderable: false,
                searchable: false,
                render: (data, type, row, meta) => {
                    return `<div class='d-flex gap-1'>${data}</div>`
                }
            }]
        });
        window.dataTableAppMenu.on('draw.dt', function() {
            actionData();
        });
        $('#save-permission').click(function() {
            $('#modal-permission')
                .find('form select')
                .prop("disabled", false);
            let data = serializeObject($('#form-permission'));
            $.ajax({
                type: "POST",
                url: `{{ route('settings.permission.store') }}`,
                data: data,
                dataType: "json",
                success: function(response) {
                    $('#modal-permission').modal('hide')
                    iziToast.success({
                        id: 'alert-permission-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                    window.dataTableAppMenu.ajax.reload();

                },
                error: function(error) {
                    $('#modal-permission .is-invalid').removeClass('is-invalid')
                    $.each(error.responseJSON.errors, function(indexInArray,
                        valueOfElement) {
                        $('#modal-permission').find('[name=' + indexInArray +
                            ']').addClass('is-invalid')
                    });
                    iziToast.error({
                        id: 'alert-permission-form',
                        title: 'Error',
                        message: error.responseJSON.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
        });
        $('#edit-permission').click(function() {
            let data = serializeObject($('#form-permission'));
            $.ajax({
                type: "PUT",
                url: `{{ route('settings.permission.update') }}/${data.id}`,
                data: data,
                dataType: "json",
                success: function(response) {
                    $('#modal-permission').modal('hide')
                    iziToast.success({
                        id: 'alert-permission-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                    window.dataTableAppMenu.ajax.reload()
                },
                error: function(error) {
                    $('#modal-permission .is-invalid').removeClass('is-invalid')
                    $.each(error.responseJSON.errors, function(indexInArray,
                        valueOfElement) {
                        $('#modal-permission').find('[name=' + indexInArray +
                            ']').addClass('is-invalid')
                    });
                    iziToast.error({
                        id: 'alert-permission-form',
                        title: 'Error',
                        message: error.responseJSON.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
        });
        $('#all').change(function() {
            let parent = $(this).parents('.container-fluid:first');
            $(parent).find('.container-fluid input.form-check-input').attr('checked', $(this).prop('checked'))
        });
        $('#modal-permission').on('hidden.bs.modal', function() {
            window.state = 'add';
            $(this).find('form')[0].reset();
            $(this).find('.modal-title').html(`Add New @yield('title')`);
            $('#save-permission').removeClass('d-none');
            $('#edit-permission').addClass('d-none');
            $('#modal-permission .is-invalid').removeClass('is-invalid')
            $('#modal-permission select[disabled]').prop("disabled", false);
            $('#table-permission tbody').find('tr').removeClass('selected');
            $('#modal-permission')
                .find('form input')
                .removeAttr('checked');
        });
        $('#modal-permission').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $('#modal-permission'),
            });
        });
        containerChecker();
    });
</script>
@endpush