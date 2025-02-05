@extends('template.parent')
@section('title', 'App Menu')
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
                        <button class="btn btn-success" id="add-app-menu" data-bs-toggle="modal" data-bs-target="#modal-app-menu">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-app-menu">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Menu</th>
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
    <div class="modal fade" id="modal-app-menu" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl" menu="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-app-menu">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="parent" class="form-label">Menu Parent</label>
                                <select name="parent" id="parent" class="form-control select2">
                                    <option value="0">Kosong</option>
                                    @foreach ($menus as $menu)
                                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Menu Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Menu Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="route" class="form-label">Menu Route</label>
                                <input list="routes" type="text" id="route" name="route" class="form-control" placeholder="Enter Menu Route" />
                                <datalist id="routes">
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->getName() }}">{{ url($route->uri()) }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="icon" class="form-label">Menu Icon</label>
                                <input type="text" id="icon" name="icon" class="form-control" placeholder="Enter Menu Icon" />
                                <div id="passwordHelpBlock" class="form-text">
                                    compatible icon is on <a href="https://boxicons.com/">boxicons</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Menu Accessibility</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="dev_only" id="dev_only">
                                    <label class="form-check-label" for="dev_only">Only Developer</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Menu Place</label>
                                <div class="col">
                                    <div class="form-check form-check-inline">
                                        <input name="place" class="form-check-input" type="radio" value="0" id="place-sidebar">
                                        <label class="form-check-label" for="place-sidebar">
                                            Sidebar
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input name="place" class="form-check-input" type="radio" value="1" id="place-profile">
                                        <label class="form-check-label" for="place-profile">
                                            Profile
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="container-child-list" class="row container">
                            <div class="col-12 row">
                                Menu Child
                            </div>
                            <div id="child-menu-container" class="col-12 row">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-app-menu" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-app-menu" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script>
        window.dataTableAppMenu = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idAppMenu = $(this).data("app-menu");
                $("#edit-app-menu").data("app-menu", idAppMenu);
                if (window.dataTableAppMenu.rows('.selected').data().length == 0) {
                    $('#table-app-menu tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableAppMenu.rows('.selected').data()[0];

                $('#modal-app-menu').modal('show');
                $('#modal-app-menu').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-app-menu').addClass('d-none');
                $('#edit-app-menu').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('dev.app-menu.show') }}/" + idAppMenu,
                    dataType: "json",
                    success: function(response) {
                        let childHtml = '';
                        $('#modal-app-menu').find("form")
                            .find('input, select').map(function(index, element) {
                                if (response.data[element.name] != undefined) {
                                    if (element.name === 'dev_only') {
                                        $(`[name=${element.name}]`)
                                            .prop('checked', response.data[element
                                                .name] == 1 ? true : false)
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
                            id: 'alert-app-menu-action',
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
                let idAppMenu = $(this).data("app-menu");
                $("#edit-app-menu").data("app-menu", idAppMenu);
                if (window.dataTableAppMenu.rows('.selected').data().length == 0) {
                    $('#table-app-menu tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableAppMenu.rows('.selected').data()[0];

                $('#modal-app-menu').modal('show');
                $('#modal-app-menu').find('.modal-title').html(`Add Child @yield('title')`);
                $('#save-app-menu').removeClass('d-none');
                $('#edit-app-menu').addClass('d-none');
                $('#modal-app-menu')
                    .find('form select')
                    .val(idAppMenu)
                    .trigger('change');
                setTimeout(() => {
                    $('#modal-app-menu')
                        .find('form select')
                        .prop("disabled", true);
                }, 200);
                $.ajax({
                    type: "GET",
                    url: "{{ route('dev.app-menu.show') }}/" + idAppMenu,
                    dataType: "json",
                    success: function(response) {
                        let childHtml = '';

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
                            id: 'alert-app-menu-action',
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
                if (window.dataTableAppMenu.rows('.selected').data().length == 0) {
                    $('#table-app-menu tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppMenu = $(this).data("app-menu");
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
                                url: "{{ route('dev.app-menu.delete') }}/" +
                                    idAppMenu,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-app-menu-form',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.dataTableAppMenu.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-app-menu-action',
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
            window.dataTableAppMenu = $("#table-app-menu").DataTable({
                ajax: "{{ route('dev.app-menu.data-table') }}",
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
            $('#save-app-menu').click(function() {
                $('#modal-app-menu')
                    .find('form select')
                    .prop("disabled", false);
                let data = serializeObject($('#form-app-menu'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('dev.app-menu.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-menu').modal('hide')
                        iziToast.success({
                            id: 'alert-app-menu-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableAppMenu.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-app-menu .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-app-menu').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-app-menu-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-app-menu').click(function() {
                let data = serializeObject($('#form-app-menu'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('dev.app-menu.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-menu').modal('hide')
                        iziToast.success({
                            id: 'alert-app-menu-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableAppMenu.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-app-menu .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-app-menu').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-app-menu-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-app-menu').on('hidden.bs.modal', function() {
                window.state = 'add';
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-app-menu').removeClass('d-none');
                $('#edit-app-menu').addClass('d-none');
                $('#modal-app-menu .is-invalid').removeClass('is-invalid')
                $('#modal-app-menu select[disabled]').prop("disabled", false);
                $('#table-app-menu tbody').find('tr').removeClass('selected');
            });
            $('#modal-app-menu').on('shown.bs.modal', function() {
                if (window.state == 'add') {
                    $('#container-child-list').addClass('d-none');
                } else {
                    $('#container-child-list').removeClass('d-none');
                }
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-app-menu'),

                    });
                }, 140);
            });
        });
    </script>
@endpush
