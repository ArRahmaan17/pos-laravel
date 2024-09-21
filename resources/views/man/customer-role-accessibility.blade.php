@extends('template.parent')
@section('title', 'Role Permission')
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
                        <button class="btn btn-success" id="add-customer-role-accessibility" data-bs-toggle="modal"
                            data-bs-target="#modal-customer-role-accessibility">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-role-accessibility">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Application Menus</th>
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
    <div class="modal fade" id="modal-customer-role-accessibility" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-role-accessibility">
                        @csrf
                        <div class="row">
                            <div class="col mb-3">
                                @if (getRole() === 'Developer')
                                    <label for="userId" class="form-label">Customer User</label>
                                    <select class="form-control select2" name="userId" id="userId">
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="userId" value="{{ session('userLogged')['user']['id'] }}">
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="roleId" class="form-label">Customer Role</label>
                                <select class="form-control select2" name="roleId" id="roleId">
                                    <option value="">Select Role</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-6">Role Permissions</h5>
                                <div class="table-responsive">
                                    <table class="table table-flush-spacing mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-nowrap fw-medium text-heading">Manager Access <i
                                                        class="bx bx-info-circle" data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        aria-label="Allows a full access to the system"
                                                        data-bs-original-title="Allows a full access to the system"></i>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-check mb-0 form-check-reverse">
                                                            <label class="form-check-label" for="selectAll">
                                                                All
                                                            </label>
                                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach ($menus as $menu)
                                                <tr>
                                                    <td class="text-nowrap fw-medium text-heading">{{ $menu->name }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-end">
                                                            <div class="form-check form-check-reverse mb-0">
                                                                <label class="form-check-label"
                                                                    for="access{{ $menu->id }}">
                                                                    Access
                                                                </label>
                                                                <input class="form-check-input menu-access" name="menuId[]"
                                                                    type="checkbox" value="{{ $menu->id }}"
                                                                    id="access{{ $menu->id }}">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-role-accessibility" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-role-accessibility" class="btn btn-warning d-none">Update
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
    <script>
        window.datatableAppRole = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idAppRole = $(this).data("customer-role-accessibility");
                $("#edit-customer-role-accessibility").data("customer-role-accessibility", idAppRole);
                if (window.datatableAppRole.rows('.selected').data().length == 0) {
                    $('#table-customer-role-accessibility tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableAppRole.rows('.selected').data()[0];
                $('#modal-customer-role-accessibility').modal('show');
                $('#modal-customer-role-accessibility').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-role-accessibility').addClass('d-none');
                $('#edit-customer-role-accessibility').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-role-accessibility.show') }}/" + idAppRole,
                    dataType: "json",
                    success: function(response) {
                        if (response.data.role_menus.length == $('#modal-customer-role-accessibility')
                            .find("form").find('input.menu-access').length) {
                            $('#selectAll').click();
                        } else {
                            response.data.role_menus.map((menu) => {
                                $('#modal-customer-role-accessibility')
                                    .find("form").find(`input#access${menu.menuId}`)
                                    .click()
                            })
                        }
                        $('#modal-customer-role-accessibility').find("form")
                            .find('select, input').map(function(index, element) {
                                if (response.data[`${element.name}`] != undefined) {
                                    if (element.name == 'roleId') {
                                        setTimeout(() => {
                                            $(`[name="${element.name}"]`).val(response.data[
                                                `${element.name}`]).trigger('change');
                                        }, 500);
                                    } else {
                                        $(`[name="${element.name}"]`).val(response.data[
                                            `${element.name}`]).trigger('change');
                                    }
                                }
                            });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-role-accessibility-action',
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
                if (window.datatableAppRole.rows('.selected').data().length == 0) {
                    $('#table-customer-role-accessibility tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppRole = $(this).data("customer-role-accessibility");
                var data = window.datatableAppRole.rows('.selected').data()[0];
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
                    message: "Are you sure you want to delete this role accessibility data?",
                    position: 'center',
                    icon: 'bx bx-question-mark',
                    buttons: [
                        ['<button><b>OK</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('man.customer-role-accessibility.delete') }}/" +
                                    idAppRole,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-role-accessibility-action',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.datatableAppRole.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-customer-role-accessibility-action',
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
            window.datatableAppRole = $("#table-customer-role-accessibility").DataTable({
                ajax: "{{ route('man.customer-role-accessibility.data-table') }}",
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
                    name: 'menu',
                    data: 'menu',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data.map((element)=>{
                                    return `<div class='d-inline'>${element.name}</div>`
                                })}</div>`
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
            window.datatableAppRole.on('draw.dt', function() {
                actionData();
            });
            $('#userId').change(function() {
                $.ajax({
                    type: "GET",
                    url: `{{ route('man.customer-role.role') }}/${this.value}`,
                    dataType: "json",
                    success: function(response) {
                        $('#roleId').html(response.data)
                    }
                });
            });
            $('#selectAll').click(function(e) {
                $('#form-customer-role-accessibility').find('[type=checkbox]').map((index, element) => {
                    $(element).attr('checked', this.checked);
                })
            });
            $('#save-customer-role-accessibility').click(function() {
                let data = serializeObject($('#form-customer-role-accessibility'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-role-accessibility.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-role-accessibility').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-role-accessibility-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppRole.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-customer-role-accessibility .is-invalid').removeClass(
                            'is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-role-accessibility').find('[name=' +
                                indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-role-accessibility-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-role-accessibility').click(function() {
                let data = serializeObject($('#form-customer-role-accessibility'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('man.customer-role-accessibility.update') }}/${data.roleId}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-role-accessibility').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-role-accessibility-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppRole.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-role-accessibility .is-invalid').removeClass(
                            'is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-role-accessibility').find('[name=' +
                                indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-role-accessibility-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-customer-role-accessibility').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-role-accessibility').removeClass('d-none');
                $('#edit-customer-role-accessibility').addClass('d-none');
                $('#modal-customer-role-accessibility .is-invalid').removeClass('is-invalid')
                $('#table-customer-role-accessibility tbody').find('tr').removeClass('selected');
            });
            $('#modal-customer-role-accessibility').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-role-accessibility'),
                    });
                }, 140);
            });
        });
    </script>
@endpush
