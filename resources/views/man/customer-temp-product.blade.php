@extends('template.parent')
@section('title', 'Temporary Product')
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
                        <button class="btn btn-success" id="add-customer-temp-product" data-bs-toggle="modal" data-bs-target="#modal-customer-temp-product">
                            Add <i class='bx bxs-file-plus pb-1'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-customer-temp-product">
                            <thead>
                                <tr>
                                    <th scope="col" rowspan="2">#</th>
                                    <th scope="col" rowspan="2">transaction created</th>
                                    <th scope="col" colspan="2" data-dt-order="disable" class="text-center">Accepted</th>
                                    <th scope="col" colspan="3" data-dt-order="disable" class="text-center">Prodcut</th>
                                    <th scope="col" rowspan="2">Action</th>
                                </tr>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Yes</th>
                                    <th scope="col">In</th>
                                    <th scope="col">Restock</th>
                                    <th scope="col">Remove</th>
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
    <div class="modal fade" id="modal-customer-temp-product" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Add New @yield('title')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card shadow-none">
                        <div class="card-title">
                            <div class="block float-end mb-5">
                                <button id="add-temporary-product" class="btn btn-warning"><i class="bx bx-plus"></i> Add Temporary Product</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-3 px-1">
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
                            <div class="accordion" id="accordion-temporary-product">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-customer-temp-product" class="btn btn-success">Save
                        changes</button>
                    <button type="button" id="edit-customer-temp-product" class="btn btn-warning d-none">Update
                        changes</button>
                </div>
            </div>
        </div>
    </div>
    <template id="template-form-product">
        <form autocomplete="off">
            <div class="d-flex">
                <button type="button" class="btn btn-danger remove-temp ms-auto"><i class='bx bxs-trash-alt'></i> Remove Temporary Product</button>
            </div>
            <input type="hidden" name="id">
            <input type="hidden" name="customerCompanyGoodId">
            <input type="hidden" name="status">
            <input type="hidden" name="companyId" value="{{ session('userLogged')['company']['id'] }}">
            <div class="row">
                <div class="col mb-3">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{ asset('customer-product/default-product.png') }}" alt="user-avatar" class="d-block rounded user-avatar" height="100"
                            width="100" />
                        <div class="button-wrapper">
                            <label class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" name="picture" class="account-file-input" hidden accept="image/png, image/jpeg" />
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>
                            <p class="text-muted mb-0">Allowed JPG or PNG and Square Ratio Photo. Max size of 800K</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name" />
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="text" id="stock" name="stock" class="form-control number" placeholder="Enter Stock" />
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="buyPrice" class="form-label">Buy Price</label>
                    <input type="text" id="buyPrice" name="buyPrice" class="form-control price" placeholder="Enter Price" />
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" id="price" name="price" class="form-control price" placeholder="Enter Buy Price" />
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="unitId" class="form-label">Unit</label>
                    <select class="form-control select2" name="unitId" id="unitId">
                        <option value="">Not selected</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->description }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </template>
    <template id="template-table-detail-product-temporary">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">OrderCode</th>
                        <th scope="col">Creater</th>
                        <th scope="col">Name</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Price</th>
                        <th scope="col">Buy Price</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Accepted</th>
                        <th scope="col">Accepter</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </template>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.dataTableCustomerTemporaryProduct = undefined;
        window.dataTableCustomerCompanyGood = undefined;
        window.productImageSelection = [];
        window.lastProductAccordion = 1;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let orderCode = $(this).data("customer-temporary-product");
                $("#edit-customer-temp-product").data("customer-temporary-product", orderCode)
                if (window.dataTableCustomerTemporaryProduct.rows('.selected').data().length == 0) {
                    $('#table-customer-temp-product tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.dataTableCustomerTemporaryProduct.rows('.selected').data()[0];

                $('#modal-customer-temp-product').modal('show');
                $('#modal-customer-temp-product').find('.modal-title').html(`Edit @yield('title')`);
                $('#save-customer-temp-product').addClass('d-none');
                $('#edit-customer-temp-product').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('man.customer-temp-product.show') }}/" + orderCode,
                    dataType: "json",
                    success: function({
                        data
                    }) {
                        data.forEach(temp => {
                            generateProductAccordion(temp.status, temp);
                        });
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
            });
            $('.delete').click(function() {
                if (window.dataTableCustomerTemporaryProduct.rows('.selected').data().length == 0) {
                    $('#table-customer-temp-product tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idAppRole = $(this).data("customer-temp-product");
                var data = window.dataTableCustomerTemporaryProduct.rows('.selected').data()[0];
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
                                url: "{{ route('man.customer-temp-product.delete') }}/" +
                                    idAppRole,
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
                                    window.dataTableCustomerTemporaryProduct.ajax.reload()
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
                        }],
                    ],
                });
            });
            $('.edit-temp').click(debounce(function() {
                window.state = 'update';
                let goodId = $(this).data("customer-company-good");
                if (window.dataTableCustomerCompanyGood.rows('.selected').data().length == 0) {
                    $('#table-customer-company-good tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected');
                }

                var data = window.dataTableCustomerCompanyGood.rows('.selected').data()[0];
                generateProductAccordion('RESTOCK', {
                    ...data,
                    customerCompanyGoodId: goodId
                });
                $('#table-customer-company-good tbody').find('tr').removeClass('selected');
            }, 500));
            $('.delete-temp').click(debounce(function() {
                if (window.dataTableCustomerCompanyGood.rows('.selected').data().length == 0) {
                    $('#table-customer-company-good tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let goodId = $(this).data("customer-company-good");
                var data = window.dataTableCustomerCompanyGood.rows('.selected').data()[0];
                generateProductAccordion('REMOVE', {
                    ...data,
                    customerCompanyGoodId: goodId
                });
                $('#table-customer-company-good tbody').find('tr').removeClass('selected');
            }, 500));
        }

        function initializeDataTable(context = $('#modal-customer-temp-product')) {
            if (context.hasClass('show')) {
                if ($.fn.dataTable.isDataTable('#table-customer-company-good')) {
                    window.dataTableCustomerCompanyGood.off('draw');
                    window.dataTableCustomerCompanyGood.clear().destroy();
                    window.dataTableCustomerCompanyGood = undefined;
                    $('#table-customer-company-good').find('tbody').html('');
                }
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
                        name: 'action_temp',
                        data: 'action_temp',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => {
                            return `<div class='d-flex gap-1'>${data}</div>`
                        }
                    }, ]
                });
                window.dataTableCustomerCompanyGood.on('draw', function() {
                    actionData();
                });
            } else {
                if ($.fn.dataTable.isDataTable('#table-customer-temp-product')) {
                    window.dataTableCustomerTemporaryProduct.off('draw');
                    window.dataTableCustomerTemporaryProduct.clear().destroy();
                    window.dataTableCustomerTemporaryProduct = undefined;
                    $('#table-customer-temp-product').find('tbody').html('');
                }
                window.dataTableCustomerTemporaryProduct = $("#table-customer-temp-product").DataTable({
                    ajax: "{{ route('man.customer-temp-product.data-table') }}",
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
                        name: 'transaction_created',
                        data: 'transaction_created',
                        orderable: true,
                        searchable: true,
                        render: $.fn.dataTable.render.date(),

                    }, {
                        target: 2,
                        name: 'sum_not_accepted',
                        data: 'sum_not_accepted',
                        orderable: true,
                        searchable: true,
                        render: (data, type, row, meta) => {
                            return `<div class='text-wrap'>${data}</div>`
                        }
                    }, {
                        target: 3,
                        name: 'sum_accepted',
                        data: 'sum_accepted',
                        orderable: true,
                        searchable: true,
                        render: (data, type, row, meta) => {
                            return `<div class='d-flex gap-1'>${data}</div>`
                        }
                    }, {
                        target: 4,
                        name: 'sum_product_in',
                        data: 'sum_product_in',
                        orderable: true,
                        searchable: true,
                        render: (data, type, row, meta) => {
                            return `<div class='d-flex gap-1'>${data}</div>`
                        }
                    }, {
                        target: 5,
                        name: 'sum_product_restock',
                        data: 'sum_product_restock',
                        orderable: true,
                        searchable: true,
                        render: (data, type, row, meta) => {
                            return `<div class='d-flex gap-1'>${data}</div>`
                        }
                    }, {
                        target: 6,
                        name: 'sum_product_remove',
                        data: 'sum_product_remove',
                        orderable: true,
                        searchable: true,
                        render: (data, type, row, meta) => {
                            return `<div class='d-flex gap-1'>${data}</div>`
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
                    }, ]
                });
                window.dataTableCustomerTemporaryProduct.on('draw', function() {
                    actionData();
                });
                const detailRows = [];
                window.dataTableCustomerTemporaryProduct.on('click', 'tbody td.dt-control', function() {
                    let tr = event.target.closest('tr');
                    let row = window.dataTableCustomerTemporaryProduct.row(tr);
                    let idx = detailRows.indexOf(tr.id);

                    if (row.child.isShown()) {
                        tr.classList.remove('details');
                        row.child.hide();
                        detailRows.splice(idx, 1);
                    } else {
                        tr.classList.add('details');
                        row.child(detailTableCustomerTemporaryProduct()).show();
                        let containerDetail = $(`tr[data-dt-row=${[row[0][0]]}]`);
                        containerDetail.find('tbody').append(dataDetailTableCustomerTemporaryProduct(row.data()))
                        if (idx === -1) {
                            detailRows.push(tr.id);
                        }
                    }
                });
            }
        }

        function dataDetailTableCustomerTemporaryProduct(d) {
            let contentTableBody = ``;
            d.changedProduct.forEach(changed => {
                contentTableBody +=
                    `<tr><td>${changed.orderCode}</td><td>${changed.creater.name}</td><td>${changed?.reference?.name?? changed.name}</td><td>${changed?.reference?.stock??changed.stock}</td><td>${changed?.reference?.price??changed.price}</td><td>${changed?.reference?.buyPrice?? changed.buyPrice}</td><td>${changed?.reference?.unit.name??changed.unit.name}</td><td>${changed.accepted==0 ? '<span class="badge bg-label-danger"><i class="bx bx-x"></i></span>' : '<span class="badge bg-label-success"><i class="bx bx-check"></i></span>'}</td><td>${changed.accepter?.name??'-'}</td></tr>`
            });
            return contentTableBody
        }

        function detailTableCustomerTemporaryProduct() {
            const template = $('#template-table-detail-product-temporary')
            const html = template[0].content.cloneNode(true);
            return (html);
        }

        function changeProductPhoto() {
            let indexAccordion = undefined;
            $('label.btn.btn-primary').hover(function(e) {
                indexAccordion = $(this).parents('.accordion-item').data('id') - 1;
                if (indexAccordion != undefined && window.productImageSelection[indexAccordion].productImage) {
                    const resetImage = window.productImageSelection[indexAccordion].productImage.attr('src');
                    window.productImageSelection[indexAccordion].fileInput.change(function(e) {
                        if (window.productImageSelection[indexAccordion].fileInput[0].files[0]) {
                            window.productImageSelection[indexAccordion].productImage.attr('src', window.URL
                                .createObjectURL(window.productImageSelection[indexAccordion].fileInput[0].files[0]));
                        }
                    });
                    window.productImageSelection[indexAccordion].resetInput.click(function(e) {
                        window.productImageSelection[indexAccordion].fileInput[0].value = '';
                        window.productImageSelection[indexAccordion].productImage.attr('src', resetImage);
                    });
                }
            })
        }

        function generateProductAccordion(status = 'IN', data = null) {
            const accordionTemporaryProduct = $('#accordion-temporary-product');
            accordionTemporaryProduct.append(`<div class="accordion-item shadow-sm mb-3" data-id=${window.lastProductAccordion}>
                                <h2 class="accordion-header">
                                    <button class="accordion-button ${data?.id ? 'collapsed': ''} ${status == 'IN'? '' :((status == 'RESTOCK')?'text-warning': 'text-danger')}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${window.lastProductAccordion}"
                                        aria-expanded="false" aria-controls="collapse${window.lastProductAccordion}">
                                        ${status == 'IN' ? `${status} Temporary Product` : `${status} ${data?.reference?.name??data.name}`}
                                    </button>
                                </h2>
                                <div id="collapse${window.lastProductAccordion}" class="accordion-collapse collapse ${data?.id ? '': 'show'}">
                                    <div class="accordion-body px-1">
                                    </div>
                                </div>
                            </div>`);
            const container = accordionTemporaryProduct.find(`#collapse${window.lastProductAccordion} > .accordion-body`);
            if (status != 'REMOVE') {
                const template = $('#template-form-product')
                const clone = template[0].content.cloneNode(true);
                container.append(clone);
                formattedInput();
                container.find(`.select2`).attr('id', `unitId${window.lastProductAccordion}`)
                setTimeout(() => {
                    if (container.find(`.select2`).hasClass("select2-hidden-accessible")) {
                        container.find(`.select2`).select2('destroy');
                    }
                    container.find(`.select2`).select2({
                        dropdownParent: $('#modal-customer-temp-product'),
                    });
                }, 140);
                window.productImageSelection.push({
                    productImage: container.find('.user-avatar'),
                    fileInput: container.find('.account-file-input'),
                    resetInput: container.find('.account-image-reset')
                });
                const indexAccordion = window.lastProductAccordion - 1;
                changeProductPhoto(indexAccordion);
            } else {
                container.append(
                    `<form><div class="d-flex"><button type="button" class="btn btn-danger remove-temp ms-auto"><i class='bx bxs-trash-alt'></i> Remove Temporary Product</button></div><input type="hidden" name="id" value="${data?.id??''}"><input type="hidden" name="customerCompanyGoodId" value="${data.customerCompanyGoodId}"><input type="hidden" name="status" value="${status}"><input type="hidden" name="companyId" value="{{ session('userLogged')['company']['id'] }}"></form><div>Product will be remove</div>`
                );
            }
            if (data != null && status != 'REMOVE') {
                $.each(data, function(key, value) {
                    if (key != 'status' && key != 'picture') {
                        container.find(`[name=${key}]`).val(key.split('rice').length > 1 ? parseInt(value) : value).trigger('change');
                    }
                });
                container.find('.user-avatar').attr('src', (status == 'RESTOCK' ?
                    `{{ asset('customer-product/${data?.reference?.picture ?? data?.picture}') }}` :
                    `{{ asset('temp-customer-product/${data?.picture}') }}`));
            }
            container.find('[name=status]').val(status);
            container.find('.remove-temp').click(function() {
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
                    message: "Are you sure you want to delete this temporary product?",
                    position: 'center',
                    icon: 'bx bx-question-mark',
                    buttons: [
                        ['<button><b>OK</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            container.find('.remove-temp').parents('.accordion-item').remove()
                        }, true],
                        ['<button>CANCEL</button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                        }],
                    ],
                });

            });
            window.lastProductAccordion++;
        }

        $(function() {
            $('#add-temporary-product').click(debounce(function() {
                generateProductAccordion()
            }, 500));
            $('#save-customer-temp-product').click(function() {
                let data = new FormData();
                let products = [];
                data.append('_token', `{{ csrf_token() }}`);
                $('#accordion-temporary-product .accordion-item').each(function(index, product) {
                    let form = $(product).find('form');
                    let formParams = form.serializeArray();
                    let productData = {};

                    formParams.forEach(function(item) {
                        if (item.value != '') {
                            data.append(`products[${index}][${item.name}]`, item.value);
                        }
                    });
                    form.find('input[type="file"]').each(function(i, tag) {
                        let files = $(tag)[0].files;
                        if (files.length > 0) {
                            data.append(`products[${index}][${tag.name}]`, files[0]);
                        }
                    });
                });
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-temp-product.store') }}`,
                    data: data,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modal-customer-temp-product').modal('hide');
                        iziToast.success({
                            id: 'alert-customer-temp-product-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerTemporaryProduct.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-customer-temp-product .is-invalid').removeClass('is-invalid')
                        $('#modal-customer-temp-product .border.border-danger').removeClass('border border-danger')
                        if (error.responseJSON.errors.length > 0) {
                            $.each(error.responseJSON.errors, function(indexInArray, valueOfProduct) {
                                let formContainer = $(
                                    `#modal-customer-temp-product #collapse${parseInt(indexInArray.split('.')[1])+1}`);
                                formContainer.parents('.accordion-item.shadow-sm').addClass('border border-danger')
                                formContainer.find(`[name=${indexInArray.split('.')[2]}]`).addClass('is-invalid')
                            });
                        }
                        iziToast.error({
                            id: 'alert-customer-temp-product-form',
                            title: 'Error',
                            message: error.responseJSON.message.replace(/\./g, ' '),
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#edit-customer-temp-product').click(function() {
                let data = new FormData();
                let products = [];
                data.append('_token', `{{ csrf_token() }}`);
                $('#accordion-temporary-product .accordion-item').each(function(index, product) {
                    let form = $(product).find('form');
                    let formParams = form.serializeArray();
                    let productData = {};

                    formParams.forEach(function(item) {
                        if (item.value != '') {
                            data.append(`products[${index}][${item.name}]`, item.value);
                        }
                    });
                    form.find('input[type="file"]').each(function(i, tag) {
                        let files = $(tag)[0].files;
                        if (files.length > 0) {
                            data.append(`products[${index}][${tag.name}]`, files[0]);
                        }
                    });
                });
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-temp-product.update') }}/{{ now()->format('Y-m-d') }}`,
                    data: data,
                    dataType: "json",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modal-customer-temp-product').modal('hide');
                        iziToast.success({
                            id: 'alert-customer-temp-product-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerTemporaryProduct.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-customer-temp-product .is-invalid').removeClass('is-invalid')
                        $('#modal-customer-temp-product .border.border-danger').removeClass('border border-danger')
                        if (error.responseJSON.errors.length > 0) {
                            $.each(error.responseJSON.errors, function(indexInArray, valueOfProduct) {
                                let formContainer = $(
                                    `#modal-customer-temp-product #collapse${parseInt(indexInArray.split('.')[1])+1}`);
                                formContainer.parents('.accordion-item.shadow-sm').addClass('border border-danger')
                                formContainer.find(`[name=${indexInArray.split('.')[2]}]`).addClass('is-invalid')
                            });
                        }
                        iziToast.error({
                            id: 'alert-customer-temp-product-form',
                            title: 'Error',
                            message: error.responseJSON.message.replace(/\./g, ' '),
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            });
            $('#modal-customer-temp-product').on('hidden.bs.modal', function() {
                $(this).find('.modal-title').html(`Add New @yield('title')`);
                $('#accordion-temporary-product').html('');
                $('#save-customer-temp-product').removeClass('d-none');
                $('#edit-customer-temp-product').addClass('d-none');
                $('#modal-customer-temp-product .is-invalid').removeClass('is-invalid')
                $('#table-customer-temp-product tbody').find('tr').removeClass('selected');
                initializeDataTable()
            });
            $('#modal-customer-temp-product').on('shown.bs.modal', function() {
                setTimeout(() => {
                    $('.select2').select2({
                        dropdownParent: $('#modal-customer-temp-product'),
                    });
                    initializeDataTable();
                }, 140);
            });
            formattedInput();
            $(window).resize(debounce(function() {
                initializeDataTable();
            }, 1500));
            initializeDataTable();
        });
    </script>
@endpush
