@extends('template.parent')
@section('title', 'Discount')
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
                        <button class="btn btn-success" id="add-customer-company-discount" data-bs-toggle="modal"
                            data-bs-target="#modal-customer-company-discount">Add <i
                                class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-company-discount">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Percentage</th>
                                    <th scope="col">Max Discount</th>
                                    <th scope="col">Min Transaction</th>
                                    <th scope="col">Status</th>
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
    <div class="modal fade" id="modal-customer-company-discount" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add @yield('title')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-company-discount" method="POST" class="container mt-2">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <label class="form-label" for="">Status</label>
                            <div class="col">
                                <div class="btn-group col-12" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="status" value="archive" id="archive-btn">
                                    <label class="btn btn-outline-danger" for="archive-btn"><i class='bx bx-archive-in'></i>
                                        Archive</label>
                                    <input type="radio" class="btn-check" name="status" value="draft" id="draft-btn"
                                        checked="">
                                    <label class="btn btn-outline-warning" for="draft-btn"><i
                                            class='bx bx-hourglass'></i>Draft</label>
                                    <input type="radio" class="btn-check" name="status" value="publish" id="publish-btn">
                                    <label class="btn btn-outline-success" for="publish-btn"><i
                                            class='bx bxs-slideshow'></i>Publish</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code *</label>
                            <input type="text" id="code" name="code" class="form-control discount-code"
                                placeholder="{{ buatSingkatan(session('userLogged')['company']['name'] ?? 'Doglex Code') }}DISCOUNT">
                            <div class="invalid-feedback">test</div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <input type="text" id="description" name="description" class="form-control"
                                placeholder="Discount Description">
                            <div class="invalid-feedback">test</div>
                        </div>
                        <div class="mb-3">
                            <label for="percentage" class="form-label">Percentage *</label>
                            <input type="text" id="percentage" name="percentage"
                                class="form-control discount-percentage" placeholder="10%">
                            <div class="invalid-feedback">test</div>
                        </div>
                        <div class="mb-3">
                            <label for="maxTransactionDiscount" class="form-label">Max Transaction Discount *</label>
                            <input type="text" id="maxTransactionDiscount" name="maxTransactionDiscount"
                                class="form-control price-discount" placeholder="10.000,00">
                            <div class="invalid-feedback">test</div>
                        </div>
                        <div class="mb-3">
                            <label for="minTransactionPrice" class="form-label">Min Transaction Price *</label>
                            <input type="text" id="minTransactionPrice" name="minTransactionPrice"
                                class="form-control price-discount" placeholder="50.000,00">
                            <div class="invalid-feedback">test</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-company-discount" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-company-discount" class="btn btn-warning d-none">Update
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
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.datatableCustomerCompanyDiscount = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idCustomerCompany = $(this).data("customer-company-discount");
                $("#edit-customer-company-discount").data("customer-company-discount", idCustomerCompany);
                if (window.datatableCustomerCompanyDiscount.rows('.selected').data().length == 0) {
                    $('#table-customer-company-discount tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableCustomerCompanyDiscount.rows('.selected').data()[0];

                $('#modal-customer-company-discount').modal('show');
                $('#modal-customer-company-discount').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-company-discount').addClass('d-none');
                $('#edit-customer-company-discount').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-company-discount.show') }}/" + idCustomerCompany,
                    dataType: "json",
                    success: function(response) {
                        let formElement = $('#modal-customer-company-discount').find("form");
                        $('#modal-customer-company-discount').find("form")
                            .find('input:not(input[name=status])').map(function(index, element) {
                                formElement.find(`[name='${element.name}']`)
                                    .val((`${response.data[element.name]}`.split('.')
                                            .length > 1) ? parseFloat(response.data[element.name]) :
                                        response.data[element.name])
                                    .trigger('change');
                                formElement.find('[name=status]').map((key, element) => {
                                    if ($(element).val() == response.data.status) {
                                        $(element).prop('checked', true);
                                    } else {
                                        $(element).prop('checked', false);
                                    }
                                })
                            });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-company-discount-action',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        setTimeout(() => {
                            $('#modal-customer-company-discount').modal('hide');
                        }, 400);
                    }
                });
            })

            $('.delete').click(function() {
                if (window.datatableCustomerCompanyDiscount.rows('.selected').data().length == 0) {
                    $('#table-customer-company-discount tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idCustomerCompany = $(this).data("customer-company-discount");
                var data = window.datatableCustomerCompanyDiscount.rows('.selected').data()[0];
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
                    message: "Are you sure you want to delete this company data?",
                    position: 'center',
                    icon: 'bx bx-question-mark',
                    buttons: [
                        ['<button><b>OK</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('man.customer-company-discount.delete') }}/" +
                                    idCustomerCompany,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-company-discount-action',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.datatableCustomerCompanyDiscount.ajax
                                        .reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-customer-company-discount-action',
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
            window.datatableCustomerCompanyDiscount = $("#table-customer-company-discount").DataTable({
                ajax: "{{ route('man.customer-company-discount.data-table') }}",
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
                    name: 'code',
                    data: 'code',
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
                    name: 'percentage',
                    data: 'percentage',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}%</div>`
                    }
                }, {
                    target: 4,
                    name: 'max_discount',
                    data: 'max_discount',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${(data == null)? 'Tak Terbatas' : $.fn.dataTable.render.number('.', ',', 2, 'Rp.').display(data)}</div>`
                    }
                }, {
                    target: 5,
                    name: 'min_transaction',
                    data: 'min_transaction',
                    orderable: true,
                    searchable: true,
                    render: $.fn.dataTable.render.number('.', ',', 2, 'Rp.')
                }, {
                    target: 6,
                    name: 'status',
                    data: 'status',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 7,
                    name: 'action',
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }]
            });
            window.datatableCustomerCompanyDiscount.on('draw.dt', function() {
                actionData();
            });
            $('#save-customer-company-discount').click(function() {
                let data = serializeObject($('#form-customer-company-discount'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-company-discount.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-company-discount').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-company-discount-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableCustomerCompanyDiscount.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-customer-company-discount .is-invalid').removeClass(
                            'is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            let name = (indexInArray
                                    .split('.').length > 1) ?
                                `${indexInArray.split('.').join('[')}]` :
                                indexInArray
                            $('#modal-customer-company-discount').find("[name='" +
                                name +
                                "']").addClass('is-invalid');
                            $('#modal-customer-company-discount').find("[name='" +
                                name +
                                "']").siblings('.invalid-feedback').html(
                                valueOfElement[0])
                        });
                        iziToast.error({
                            id: 'alert-customer-company-discount-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-company-discount').click(function() {
                let data = serializeObject($('#form-customer-company-discount'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('man.customer-company-discount.update') }}/${data.id}`,
                    data: {
                        _token: `{{ csrf_token() }}`,
                        ...data
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-company-discount').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-company-discount-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.datatableCustomerCompanyDiscount.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-company-discount .is-invalid').removeClass(
                            'is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            let name = (indexInArray
                                    .split('.').length > 1) ?
                                `${indexInArray.split('.').join('[')}]` :
                                indexInArray
                            $('#modal-customer-company-discount').find("[name='" +
                                name +
                                "']").addClass('is-invalid');
                            $('#modal-customer-company-discount').find("[name='" +
                                name +
                                "']").siblings('.invalid-feedback').html(
                                valueOfElement[0])
                        });
                        iziToast.error({
                            id: 'alert-customer-company-discount-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-customer-company-discount').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-company-discount').removeClass('d-none');
                $('#edit-customer-company-discount').addClass('d-none');
                $('#modal-customer-company-discount .is-invalid').removeClass('is-invalid')
                $('#table-customer-company-discount tbody').find('tr').removeClass('selected');
                $('#uploadedAvatar').prop('src', `{{ asset('cp/default-picture.png') }}`);
            });
            $('#modal-customer-company-discount').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-company-discount'),
                    });
                    $('.select2-container').addClass('form-control')
                }, 140);
            });
            $('.discount-code')
                .inputmask(
                    `{{ buatSingkatan(session('userLogged')['company']['name'] ?? 'Doglex Code') }}A{1,30}`);
            $('.discount-percentage').inputmask({
                regex: '^([1-9]%|[1-9][0-9]%|100%)$',
            });
            $('.price-discount').inputmask('currency', {
                radixPoint: ',',
                groupSeparator: ".",
                rightAlign: false,
                allowMinus: false
            });
        });
    </script>
@endpush
