@extends('template.parent')
@section('title', 'User Management')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
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
                        <button class="btn btn-success" id="add-user-customer" data-bs-toggle="modal"
                            data-bs-target="#modal-user-customer">Add <i class='bx bxs-file-plus pb-1'></i></button>
                        <button class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modal-create-registration-link">Generate Registration Link <i
                                class='bx bx-link-alt pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-user-customer">
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
    <div class="modal fade" id="modal-user-customer" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-user-customer">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="userId" class="form-label">Customer User</label>
                                @if (getRole() === 'Developer')
                                    <select class="form-control select2" name="userId" id="userId">
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="userId" value="{{ session('userLogged')->user->id }}">
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="Enter Role Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-0">
                                <label for="description" class="form-label">Role Description</label>
                                <textarea name="description" placeholder="Enter Description Role" class="form-control" style="resize:none"
                                    id="description" cols="10" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-user-customer" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-user-customer" class="btn btn-warning d-none">Update
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
                let idAppRole = $(this).data("user-customer");
                $("#edit-user-customer").data("user-customer", idAppRole);
                if (window.datatableAppRole.rows('.selected').data().length == 0) {
                    $('#table-user-customer tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableAppRole.rows('.selected').data()[0];

                $('#modal-user-customer').modal('show');
                $('#modal-user-customer').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-user-customer').addClass('d-none');
                $('#edit-user-customer').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('dev.user-customer.show') }}/" + idAppRole,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-user-customer').find("form")
                            .find('select, input, textarea').map(function(index, element) {
                                if (response.data[element.name]) {
                                    $(`[name=${element.name}]`).val(response.data[element
                                        .name])
                                }
                            });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-user-customer-action',
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
                    $('#table-user-customer tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppRole = $(this).data("user-customer");
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
                    message: "Are you sure you want to delete this application's role data?",
                    position: 'center',
                    icon: 'bx bx-question-mark',
                    buttons: [
                        ['<button><b>IYA</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('dev.user-customer.delete') }}/" +
                                    idAppRole,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-user-customer-action',
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
                                        id: 'alert-user-customer-action',
                                        title: 'Error',
                                        message: error.responseJSON.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                }
                            });
                        }, true],
                        ['<button>TIDAK</button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                        }],
                    ],
                });
            });
        }
        $(function() {
            window.datatableAppRole = $("#table-user-customer").DataTable({
                ajax: "{{ route('dev.user-customer.data-table') }}",
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
            window.datatableAppRole.on('draw.dt', function() {
                actionData();
            });
            $('#save-user-customer').click(function() {
                let data = serializeObject($('#form-user-customer'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('dev.user-customer.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-user-customer').modal('hide')
                        iziToast.success({
                            id: 'alert-user-customer-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppRole.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-user-customer .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-user-customer').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-user-customer-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-user-customer').click(function() {
                let data = serializeObject($('#form-user-customer'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('dev.user-customer.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-user-customer').modal('hide')
                        iziToast.success({
                            id: 'alert-user-customer-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppRole.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-user-customer .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-user-customer').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-user-customer-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-user-customer').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-user-customer').removeClass('d-none');
                $('#edit-user-customer').addClass('d-none');
                $('#modal-user-customer .is-invalid').removeClass('is-invalid')
                $('#table-user-customer tbody').find('tr').removeClass('selected');
            });
            $('#modal-user-customer').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-user-customer')
                    });
                }, 140);
            });
        });
    </script>
@endpush
