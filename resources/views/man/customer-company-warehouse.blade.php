@extends('template.parent')
@section('title', 'Warehouse')
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
                        <button class="btn btn-success" id="add-customer-company-warehouse" data-bs-toggle="modal"
                            data-bs-target="#modal-customer-company-warehouse">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-company-warehouse">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Racks</th>
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
    <div class="modal fade" id="modal-customer-company-warehouse" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-company-warehouse">
                        <div class="divider">
                            <div class="divider-text">Warehouse</div>
                        </div>
                        @csrf
                        <input type="hidden" name="id">
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
                                <label for="companyId" class="form-label">Your Company</label>
                                <select class="form-control select2" name="companyId" id="companyId">
                                    <option value=''>Mohon Pilih</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Warehouse Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="Enter Warehouse Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-0">
                                <label for="description" class="form-label">Warehouse Description</label>
                                <textarea name="description" placeholder="Enter Description Warehouse" class="form-control" style="resize:none"
                                    id="description" cols="10" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                    <div class="mx-1 divider">
                        <div class="divider-text">Warehouse Racks</div>
                    </div>
                    <div class="mx-1">
                        <div id="container-rack" class="list-group mb-3">
                        </div>
                        <input type="hidden" name="rack[id]" id="rack[id]">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="rack[name]" class="form-label">Rack Name</label>
                                <input type="text" id="rack[name]" name="rack[name]" class="form-control"
                                    placeholder="Enter Rack Name" />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col mb-0">
                                <label for="rack[description]" class="form-label">Rack Description</label>
                                <textarea name="rack[description]" placeholder="Enter Description Rack" class="form-control" style="resize:none"
                                    id="rack[description]" cols="10" rows="3"></textarea>
                            </div>
                        </div>
                        <button id="add-rack" type="button" class="btn btn-icon btn-success"><i
                                class='bx bx-add-to-queue'></i></button>
                        <button id="remove-rack" type="button" class="btn btn-icon btn-danger d-none"><i
                                class='bx bx-trash'></i></button>
                        <button id="edit-rack" type="button" class="btn btn-icon btn-warning d-none"><i
                                class='bx bx-pencil'></i></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-company-warehouse" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-company-warehouse" class="btn btn-warning d-none">Update
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
                let idAppRole = $(this).data("customer-company-warehouse");
                $("#edit-customer-company-warehouse").data("customer-company-warehouse", idAppRole);
                if (window.datatableAppRole.rows('.selected').data().length == 0) {
                    $('#table-customer-company-warehouse tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableAppRole.rows('.selected').data()[0];
                $('#modal-customer-company-warehouse').modal('show');
                $('#modal-customer-company-warehouse').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-company-warehouse').addClass('d-none');
                $('#edit-customer-company-warehouse').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-company-warehouse.show') }}/" + idAppRole,
                    dataType: "json",
                    success: function(response) {
                        $('#userId').val(response.data.company.userId).trigger('change')
                        setTimeout(() => {
                            $('#companyId').val(response.data.company.id).trigger('change')
                        }, 750);
                        $('#name').val(response.data.name).trigger('change')
                        $('[name=id]').val(response.data.id).trigger('change')
                        $('#description').val(response.data.description).trigger('change')
                        response.data.racks.forEach(element => {
                            $('[name="rack[name]"]').val(element.name).trigger('change')
                            $('[name="rack[id]"]').val(element.id).trigger('change')
                            $('[name="rack[description]"]').val(element.description).trigger(
                                'change')
                            $('#add-rack').click()
                        });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-company-warehouse-action',
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
                    $('#table-customer-company-warehouse tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppRole = $(this).data("customer-company-warehouse");
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
                    message: "Are you sure you want to delete this warehouse data?",
                    position: 'center',
                    icon: 'bx bx-question-mark',
                    buttons: [
                        ['<button><b>OK</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('man.customer-company-warehouse.delete') }}/" +
                                    idAppRole,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-company-warehouse-action',
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
                                        id: 'alert-customer-company-warehouse-action',
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
            window.datatableAppRole = $("#table-customer-company-warehouse").DataTable({
                ajax: "{{ route('man.customer-company-warehouse.data-table') }}",
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
                    name: 'racks',
                    data: 'racks',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data.map((element)=>{
                            return `<span>${element.name}</span>`
                        })}</div>`
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
            $('#userId').change(function() {
                $.ajax({
                    type: "GET",
                    url: `{{ route('man.customer-company.company') }}`,
                    dataType: "json",
                    success: function(response) {
                        $('#companyId').html(dataToOption(response.data, true))
                    }
                });
            });
            $('#save-customer-company-warehouse').click(function() {
                let data = serializeObject($('#form-customer-company-warehouse'));
                let racks = [];
                $('#container-rack').find('.list-group-item').map((index, element) => {
                    racks.push($(element).data('rack'));
                });
                if (racks.length > 0) {
                    data.racks = racks
                }
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-company-warehouse.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-company-warehouse').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-company-warehouse-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppRole.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-customer-company-warehouse .is-invalid')
                            .removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            if (indexInArray.split('racks.').length > 1) {
                                $('#modal-customer-company-warehouse .border-danger')
                                    .removeClass('border-danger')
                                let index = indexInArray.split('racks.').join('').split(
                                    '.name').join('');
                                $('#container-rack')
                                    .find(
                                        `.list-group-item:nth-child(${parseInt(index)+1})`
                                    )
                                    .addClass('border border-danger')
                            } else {
                                $('#modal-customer-company-warehouse').find('[name=' +
                                    indexInArray +
                                    ']').addClass('is-invalid')
                            }
                        });
                        iziToast.error({
                            id: 'alert-customer-company-warehouse-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-company-warehouse').click(function() {
                let data = serializeObject($('#form-customer-company-warehouse'));
                let racks = [];
                $('#container-rack').find('.list-group-item').map((index, element) => {
                    racks.push($(element).data('rack'));
                });
                if (racks.length > 0) {
                    data.racks = racks
                }
                $.ajax({
                    type: "PUT",
                    url: `{{ route('man.customer-company-warehouse.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-company-warehouse').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-company-warehouse-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppRole.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-company-warehouse .is-invalid').removeClass(
                            'is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-company-warehouse').find('[name=' +
                                indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-company-warehouse-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#add-rack').click(function() {
                $('.is-invalid').removeClass('is-invalid')
                let data = {
                    id: $('[name="rack[id]"]').val() ?? null,
                    name: $('[name="rack[name]"]').val(),
                    description: $('[name="rack[description]"]').val(),
                }
                if (data.name != '' && data.description != '') {
                    $('#container-rack').append(`<label data-rack='${JSON.stringify(data)}' class="list-group-item">
                                    <input class="form-check-input me-3 rack-action" type="checkbox">
                                    ${data.name} (${data.description})
                                </label>`);
                    $('[name="rack[name]"]').val('');
                    $('[name="rack[description]"]').val('');
                    $('[name="rack[id]"]').val('');
                    $('.rack-action').click(function() {
                        if ($('input.rack-action:checked').length == 1) {
                            $('#remove-rack').removeClass('d-none');
                            $('#edit-rack').removeClass('d-none');
                        } else if ($('input.rack-action:checked').length != 0) {
                            $('#remove-rack').removeClass('d-none');
                            $('#edit-rack').addClass('d-none');
                        } else {
                            $('#remove-rack').addClass('d-none');
                            $('#edit-rack').addClass('d-none');
                        }
                    });
                } else {
                    if (data.name == '') {
                        $('[name="rack[name]"]').addClass('is-invalid')
                    } else if (data.description == '') {
                        $('[name="rack[description]"]').addClass('is-invalid')
                    }
                }
            });
            $('#remove-rack').click(function() {
                $('.rack-action:checked').parent('label.list-group-item').remove();
                if ($('input.rack-action:checked').length != 0) {
                    $('#remove-rack').removeClass('d-none');
                } else {
                    $('#remove-rack').addClass('d-none');
                }
            });
            $('#edit-rack').click(function() {
                $('#remove-rack').addClass('d-none');
                $('#edit-rack').addClass('d-none');
                let data = $('.rack-action:checked').parent('label.list-group-item').data('rack');
                $('[name="rack[name]"]').val(data.name);
                $('[name="rack[description]"]').val(data.description);
                $('.rack-action:checked').parent('label.list-group-item').remove();
            });
            $('#modal-customer-company-warehouse').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-company-warehouse').removeClass('d-none');
                $('#edit-customer-company-warehouse').addClass('d-none');
                $('#modal-customer-company-warehouse .is-invalid').removeClass('is-invalid')
                $('#table-customer-company-warehouse tbody').find('tr').removeClass('selected');
            });
            $('#modal-customer-company-warehouse').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-company-warehouse'),
                    });
                }, 140);
            });
        });
    </script>
@endpush
