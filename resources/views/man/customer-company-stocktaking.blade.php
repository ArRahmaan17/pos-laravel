@extends('template.parent')
@section('title', 'Product Stocktaking')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-middle">
                    <div class="col-6">
                        <h3>@yield('title')</h3>
                    </div>
                    <div class="col-6 text-end">
                        <button class="btn btn-success" id="add-customer-product-stocktaking" data-bs-toggle="modal"
                            data-bs-target="#modal-customer-product-stocktaking">Add <i class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-product-stocktaking">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Expected Stock</th>
                                    <th scope="col">Real Stock</th>
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
    <div class="modal fade" id="modal-customer-product-stocktaking" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add New @yield('title')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="form-customer-product-stocktaking" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table" id="table-customer-company-good">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Stock</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Units</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="accordion my-2" id="accordion-customer-product-stocktaking">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-product-stocktaking" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-product-stocktaking" class="btn btn-warning d-none">Update
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
    <script>
        window.dataTableCustomerCompany = null;
        window.dataTableCustomerCompanyGood = null;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idStocktaking = $(this).data("customer-product-stocktaking");
                $("#edit-customer-product-stocktaking").data("customer-product-stocktaking", idStocktaking);
                if (window.dataTableCustomerCompany.rows('.selected').data().length == 0) {
                    $('#table-customer-product-stocktaking tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerCompany.rows('.selected').data()[0];

                $('#modal-customer-product-stocktaking').modal('show');
                $('#modal-customer-product-stocktaking').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-product-stocktaking').addClass('d-none');
                $('#edit-customer-product-stocktaking').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-product-stocktaking.show') }}/" + idStocktaking,
                    dataType: "json",
                    success: function(response) {
                        generateProductAccordion({
                            ...response.data,
                            customerCompanyGoodId: response.data.goodId
                        }, window.state);
                        $('#modal-customer-product-stocktaking').find(`form,  #collapse-${response.data.goodId}`)
                            .find('input').map(function(index, element) {
                                let name = (element.name.split('product[' + response.data.goodId + '][' + element.id + ']').length >
                                    1) ? element.id : element.name;
                                if (response.data[name] !== undefined && $("[name='product[" + response.data.goodId + "][" + name +
                                        "]']").length != 0) {
                                    $("[name='product[" + response.data.goodId + "][" + name + "]']").val(response.data[name])
                                        .trigger('change')
                                }
                            });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-product-stocktaking-action',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        setTimeout(() => {
                            $('#modal-customer-product-stocktaking').modal('hide');
                        }, 400);
                    }
                });
            });
            $('.show-stocktaking').click(function() {
                window.state = 'show';
                let idStocktaking = $(this).data("customer-product-stocktaking");
                $("#edit-customer-product-stocktaking").data("customer-product-stocktaking", idStocktaking);
                if (window.dataTableCustomerCompany.rows('.selected').data().length == 0) {
                    $('#table-customer-product-stocktaking tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerCompany.rows('.selected').data()[0];

                $('#modal-customer-product-stocktaking').modal('show');
                $('#modal-customer-product-stocktaking').find('.modal-title').html(`Show @yield('title')`);
                $('#save-customer-product-stocktaking').addClass('d-none');
                $('#edit-customer-product-stocktaking').addClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-product-stocktaking.show') }}/" + idStocktaking,
                    dataType: "json",
                    success: function(response) {
                        let data = response.data;
                        generateProductAccordion({
                            ...data,
                            customerCompanyGoodId: idStocktaking
                        }, window.state);
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-product-stocktaking-action',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        setTimeout(() => {
                            $('#modal-customer-product-stocktaking').modal('hide');
                        }, 400);
                    }
                });
            });
            $('.edit-stock').click(debounce(function() {
                window.state = 'update';
                let goodId = $(this).data("customer-company-good");
                if (window.dataTableCustomerCompanyGood.rows('.selected').data().length == 0) {
                    $('#table-customer-company-good tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected');
                }

                var data = window.dataTableCustomerCompanyGood.rows('.selected').data()[0];
                generateProductAccordion({
                    ...data,
                    customerCompanyGoodId: goodId
                }, window.state);
                $('#table-customer-company-good tbody').find('tr').removeClass('selected');
            }, 500));
            $('.approve').click(function() {
                window.state = 'update';
                let idStocktaking = $(this).data("customer-product-stocktaking");
                $("#edit-customer-product-stocktaking").data("customer-product-stocktaking", idStocktaking);
                if (window.dataTableCustomerCompany.rows('.selected').data().length == 0) {
                    $('#table-customer-product-stocktaking tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerCompany.rows('.selected').data()[0];
                $.ajax({
                    type: "POST",
                    url: "{{ route('man.customer-product-stocktaking.approve') }}/" + idStocktaking,
                    data: {
                        _token: `{{ csrf_token() }}`
                    },
                    dataType: "json",
                    success: function(response) {
                        window.dataTableCustomerCompany.ajax.reload();
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-product-stocktaking-action',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerCompany.ajax.reload();
                    }
                });
            });
            $('.delete').click(function() {
                if (window.dataTableCustomerCompany.rows('.selected').data().length == 0) {
                    $('#table-customer-product-stocktaking tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idStocktaking = $(this).data("customer-product-stocktaking");
                var data = window.dataTableCustomerCompany.rows('.selected').data()[0];
                iziToast.question({
                    timeout: 5000,
                    layout: 2,
                    close: false,
                    overlay: true,
                    color: 'red',
                    displayMode: 'once',
                    id: 'delete-question',
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
                                url: "{{ route('man.customer-product-stocktaking.delete') }}/" +
                                    idStocktaking,
                                data: {
                                    _token: `{{ csrf_token() }}`,
                                },
                                dataType: "json",
                                success: function(response) {
                                    iziToast.success({
                                        id: 'alert-customer-temp-product-action',
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight',
                                        layout: 2,
                                        displayMode: 'replace'
                                    });
                                    window.dataTableCustomerCompany.ajax.reload()
                                },
                                error: function(error) {
                                    iziToast.error({
                                        id: 'alert-customer-temp-product-action',
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
                            $('#table-customer-temp-product tbody').find('tr').removeClass('selected')
                        }],
                    ],
                });
            });
        }

        function generateProductAccordion(data = null, state = 'new') {
            const accordionTemporaryProduct = $('#accordion-customer-product-stocktaking');
            if (accordionTemporaryProduct.find(`#collaps-${data.customerCompanyGoodId}`).length == 0) {
                accordionTemporaryProduct.append(`<div class="accordion-item shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collaps-${data.customerCompanyGoodId}"
                            aria-expanded="false" aria-controls="collaps-${data.customerCompanyGoodId}">
                            Stocktaking Product ${data.name}
                        </button>
                    </h2>
                    <div id="collaps-${data.customerCompanyGoodId}" class="accordion-collapse collapse" data-bs-parent="#accordion-customer-product-stocktaking">
                        <div class="accordion-body">
                            <input type="hidden" id="goodId" name="product[${data.customerCompanyGoodId}][goodId]" value="${data.customerCompanyGoodId}"/>
                            <input type="hidden" id="id" name="product[${data.customerCompanyGoodId}][id]" value="${data.id}"/>
                            <div class="col-12 mb-3">
                                <label for="expect_stock">Expect Stock</label>
                                <input type="text" class="form-control number" readonly id="expect_stock" name="product[${data.customerCompanyGoodId}][expect_stock]" value="${data?.expect_stock??data.stock}"/>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="real_stock">Real Stock</label>
                                <input type="text" class="form-control number" ${(state=='show')?'readonly':''} id="real_stock" name="product[${data.customerCompanyGoodId}][real_stock]" value="${data?.real_stock}"/>
                            </div>
                            ${(state=='show')? '':`<div class="col-12 mb-3 d-flex justify-content-end"><button type="button" class="btn btn-danger btn-icon trash-stocktaking"><i class='bx bxs-trash-alt'></i></button></div>`}
                        </div>
                    </div>
                </div>`);
                formattedInput();
                $('.trash-stocktaking').click(function() {
                    $(this).parents('.accordion-item').remove();
                })
            }
        }

        function initialTableCompanyStocktaking() {
            window.dataTableCustomerCompany = $("#table-customer-product-stocktaking").DataTable({
                ajax: "{{ route('man.customer-product-stocktaking.data-table') }}",
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
                    name: 'customer_company_goods.name',
                    data: 'name',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 2,
                    name: 'expect_stock',
                    data: 'expect_stock',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 3,
                    name: 'real_stock',
                    data: 'real_stock',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'status',
                    data: 'status',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 5,
                    name: 'action',
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }]
            });
            window.dataTableCustomerCompany.on('draw.dt', function() {
                actionData();
            });
        }

        function initialTableCompanyGood() {
            window.dataTableCustomerCompanyGood = $("#table-customer-company-good").DataTable({
                ajax: "{{ route('man.customer-company-good.data-table') }}",
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
                    name: 'stock',
                    data: 'stock',
                    orderable: true,
                    searchable: true,
                    render: $.fn.dataTable.render.number('.', ',', 0, '')
                }, {
                    target: 3,
                    name: 'price',
                    data: 'price',
                    orderable: true,
                    searchable: true,
                    render: $.fn.dataTable.render.number('.', ',', 2, 'Rp.')
                }, {
                    target: 4,
                    name: 'unit',
                    data: 'unit',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }, {
                    target: 5,
                    name: 'status',
                    data: 'status',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }, {
                    target: 6,
                    name: 'action_stocktaking',
                    data: 'action_stocktaking',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }, ]
            });
            window.dataTableCustomerCompanyGood.on('draw.dt', function() {
                actionData();
            });
        }

        $(function() {
            initialTableCompanyStocktaking();
            $('#save-customer-product-stocktaking').click(function() {
                let data = serializeObject($('#form-customer-product-stocktaking'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-product-stocktaking.store') }}`,
                    data: {
                        ...data
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-product-stocktaking').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-product-stocktaking-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerCompany.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-customer-product-stocktaking .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            let name = (indexInArray
                                    .split('.').length > 1) ?
                                `${indexInArray.split('.').join('[')}]` :
                                indexInArray
                            $('#modal-customer-product-stocktaking').find("[name='" + name +
                                "']").addClass('is-invalid');
                            $('#modal-customer-product-stocktaking').find("[name='" + name +
                                "']").siblings('.invalid-feedback').html(
                                valueOfElement[0])
                        });
                        iziToast.error({
                            id: 'alert-customer-product-stocktaking-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-product-stocktaking').click(function() {
                let data = serializeObject($('#form-customer-product-stocktaking'));
                $.ajax({
                    type: "PUT",
                    url: `{{ route('man.customer-product-stocktaking.update') }}/${$('#form-customer-product-stocktaking .accordion-item').find('[id=id]').val()}`,
                    data: {
                        ...data
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#modal-customer-product-stocktaking').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-product-stocktaking-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerCompany.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-product-stocktaking .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            let name = (indexInArray
                                    .split('.').length > 1) ?
                                `${indexInArray.split('.').join('[')}]` :
                                indexInArray
                            $('#modal-customer-product-stocktaking').find("[name='" + name +
                                "']").addClass('is-invalid');
                            $('#modal-customer-product-stocktaking').find("[name='" + name +
                                "']").siblings('.invalid-feedback').html(
                                valueOfElement[0])
                        });
                        iziToast.error({
                            id: 'alert-customer-product-stocktaking-form',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-customer-product-stocktaking').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#save-customer-product-stocktaking').removeClass('d-none');
                $('#edit-customer-product-stocktaking').addClass('d-none');
                $('#modal-customer-product-stocktaking .is-invalid').removeClass('is-invalid')
                $('#table-customer-product-stocktaking tbody').find('tr').removeClass('selected');
                $('#accordion-customer-product-stocktaking').html('');
                if ($.fn.dataTable.isDataTable('#table-customer-company-good')) {
                    window.dataTableCustomerCompanyGood.off('draw');
                    window.dataTableCustomerCompanyGood.clear().destroy();
                    window.dataTableCustomerCompanyGood = undefined;
                    $('#table-customer-company-good').find('tbody').html('');
                }
                initialTableCompanyStocktaking();
            });
            $('#modal-customer-product-stocktaking').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-product-stocktaking'),
                    });
                    if (window.state != 'show') {
                        initialTableCompanyGood();
                    }
                    if ($.fn.dataTable.isDataTable('#table-customer-product-stocktaking')) {
                        window.dataTableCustomerCompany.off('draw');
                        window.dataTableCustomerCompany.clear().destroy();
                        window.dataTableCustomerCompany = undefined;
                        $('#table-customer-product-stocktaking').find('tbody').html('');
                    }
                }, 170);
            });
            formattedInput();
        });
    </script>
@endpush
