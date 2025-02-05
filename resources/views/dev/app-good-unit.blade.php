@extends('template.parent')
@section('title', 'App Good Unit')
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
                        <button class="btn btn-success" id="add-app-good-unit" data-bs-toggle="modal" data-bs-target="#modal-app-good-unit">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-app-good-unit">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Description</th>
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
    <div class="modal fade" id="modal-app-good-unit" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-app-good-unit">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Unit Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Unit Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-0">
                                <label for="description" class="form-label">Unit Description</label>
                                <textarea name="description" placeholder="Enter Description Unit" class="form-control" style="resize:none" id="description" cols="10" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-app-good-unit" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-app-good-unit" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script>
        window.dataTableAppUnit = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idAppUnit = $(this).data("app-good-unit");
                $("#edit-app-good-unit").data("app-good-unit", idAppUnit);
                if (window.dataTableAppUnit.rows('.selected').data().length == 0) {
                    $('#table-app-good-unit tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableAppUnit.rows('.selected').data()[0];

                $('#modal-app-good-unit').modal('show');
                $('#modal-app-good-unit').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-app-good-unit').addClass('d-none');
                $('#edit-app-good-unit').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('dev.app-good-unit.show') }}/" + idAppUnit,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-good-unit').find("form")
                            .find('input, textarea').map(function(index, element) {
                                if (response.data[element.name]) {
                                    $(`[name=${element.name}]`).val(response.data[element
                                        .name])
                                }
                            });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-app-good-unit-action',
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
                if (window.dataTableAppUnit.rows('.selected').data().length == 0) {
                    $('#table-app-good-unit tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppUnit = $(this).data("app-good-unit");
                var data = window.dataTableAppUnit.rows('.selected').data()[0];
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
                        ['<button><b>OK</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('dev.app-good-unit.delete') }}/" +
                                    idAppUnit,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-app-good-unit-form',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.dataTableAppUnit.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-app-good-unit-action',
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
            window.dataTableAppUnit = $("#table-app-good-unit").DataTable({
                ajax: "{{ route('dev.app-good-unit.data-table') }}",
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
                    name: 'action',
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }]
            });
            window.dataTableAppUnit.on('draw.dt', function() {
                actionData();
            });
            $('#save-app-good-unit').click(function() {
                let data = serializeObject($('#form-app-good-unit'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('dev.app-good-unit.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-good-unit').modal('hide')
                        iziToast.success({
                            id: 'alert-app-good-unit-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableAppUnit.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-app-good-unit .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-app-good-unit').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-app-good-unit-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-app-good-unit').click(function() {
                let data = serializeObject($('#form-app-good-unit'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('dev.app-good-unit.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-good-unit').modal('hide')
                        iziToast.success({
                            id: 'alert-app-good-unit-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableAppUnit.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-app-good-unit .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-app-good-unit').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-app-good-unit-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-app-good-unit').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-app-good-unit').removeClass('d-none');
                $('#edit-app-good-unit').addClass('d-none');
                $('#modal-app-good-unit .is-invalid').removeClass('is-invalid')
                $('#table-app-good-unit tbody').find('tr').removeClass('selected');
            });
        });
    </script>
@endpush
