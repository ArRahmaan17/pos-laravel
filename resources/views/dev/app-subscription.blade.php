@extends('template.parent')
@section('title', 'App Subscription')
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
                        <button class="btn btn-success" id="add-app-subscription" data-bs-toggle="modal" data-bs-target="#modal-app-subscription">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-app-subscription">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Price/month</th>
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
    <div class="modal fade" id="modal-app-subscription" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-app-subscription">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Role Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-0">
                                <label for="description" class="form-label">Role Description</label>
                                <textarea name="description" placeholder="Enter Description Role" class="form-control" style="resize:none" id="description" cols="10" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-app-subscription" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-app-subscription" class="btn btn-warning d-none">Update
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
        window.datatableAppSubscription = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idAppSubscription = $(this).data("app-subscription");
                $("#edit-app-subscription").data("app-subscription", idAppSubscription);
                if (window.datatableAppSubscription.rows('.selected').data().length == 0) {
                    $('#table-app-subscription tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableAppSubscription.rows('.selected').data()[0];

                $('#modal-app-subscription').modal('show');
                $('#modal-app-subscription').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-app-subscription').addClass('d-none');
                $('#edit-app-subscription').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('dev.app-subscription.show') }}/" + idAppSubscription,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-subscription').find("form")
                            .find('input, textarea').map(function(index, element) {
                                if (response.data[element.name]) {
                                    $(`[name=${element.name}]`).val(response.data[element
                                        .name])
                                }
                            });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-app-subscription-action',
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
                if (window.datatableAppSubscription.rows('.selected').data().length == 0) {
                    $('#table-app-subscription tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppSubscription = $(this).data("app-subscription");
                var data = window.datatableAppSubscription.rows('.selected').data()[0];
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
                                url: "{{ route('dev.app-subscription.delete') }}/" +
                                    idAppSubscription,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-app-subscription-form',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.datatableAppSubscription.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-app-subscription-action',
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

        function detail_table(d) {
            let html = ``;
            d.plans.forEach(element => {
            html += `<li class="list-group-item d-flex align-items-center">
                        <i class='bx bxs-check-circle me-3 text-success' ></i>
                        ${element.planFeature}
                    </li>`
            });
            return (
                `<div class='mx-0 my-3 p-1'>
                    <ul class="list-group">
                        ${html}
                    </ul>
                </div>`
            );
        }
        $(function() {
            window.datatableAppSubscription = $("#table-app-subscription").DataTable({
                ajax: "{{ route('dev.app-subscription.data-table') }}",
                processing: true,
                serverSide: true,
                order: [
                    [2, 'desc']
                ],
                columns: [{
                    name: '',
                    className: 'dt-control parent',
                    data: null,
                    defaultContent: '',
                    orderable: false,
                    searchable: false,
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
                    name: 'price',
                    data: 'price',
                    orderable: true,
                    searchable: true,
                    render: $.fn.dataTable.render.number('.', ',', 2, 'Rp.')
                },{
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
            window.datatableAppSubscription.on('draw.dt', function() {
                actionData();
            });
            window.datatableAppSubscription.on('click', 'td.parent', function(e) {
                let tr = e.target.closest('tr');
                let row = window.datatableAppSubscription.row(tr);
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(detail_table(row.data())).show();
                }
            });
            $('#save-app-subscription').click(function() {
                let data = serializeObject($('#form-app-subscription'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('dev.app-subscription.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-subscription').modal('hide')
                        iziToast.success({
                            id: 'alert-app-subscription-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppSubscription.ajax.reload();

                    },
                    error: function(error) {
                        $('#modal-app-subscription .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-app-subscription').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-app-subscription-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-app-subscription').click(function() {
                let data = serializeObject($('#form-app-subscription'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('dev.app-subscription.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-app-subscription').modal('hide')
                        iziToast.success({
                            id: 'alert-app-subscription-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableAppSubscription.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-app-subscription .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-app-subscription').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-app-subscription-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-app-subscription').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-app-subscription').removeClass('d-none');
                $('#edit-app-subscription').addClass('d-none');
                $('#modal-app-subscription .is-invalid').removeClass('is-invalid')
                $('#table-app-subscription tbody').find('tr').removeClass('selected');
            });
        });
    </script>
@endpush
