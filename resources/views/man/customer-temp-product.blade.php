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
                                    <th scope="col">#</th>
                                    <th scope="col">Order Code</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Accepted</th>
                                    <th scope="col">Accepted By</th>
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
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.dataTableCustomerTemporaryProduct = null;
        window.dataTableCustomerCompanyGood = null;
        window.productImageSelection = [];
        window.lastProductAccordion = 1;
        window.state = 'add';

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let orderCode = $(this).data("customer-temp-product");
                $("#edit-customer-temp-product").data("customer-temp-product", orderCode)
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
                    success: function(response) {
                        let formElement = $('#modal-customer-temp-product').find("form");
                        formElement.find('[name=id]')
                            .val(response.data.id)
                            .trigger('change');
                        formElement.find('[name=name]')
                            .val(response.data.name)
                            .trigger('change');
                        formElement.find('[name=stock]')
                            .val(response.data.stock)
                            .trigger('change');
                        formElement.find('[name=price]')
                            .val(parseInt(response.data.price))
                            .trigger('change');
                        formElement.find('[name=unitId]')
                            .val(response.data.unitId)
                            .trigger('change');
                        formElement.find('[name=companyId]')
                            .val(response.data.companyId)
                            .trigger('change');
                        formElement.find('[name=buyPrice]')
                            .val(parseInt(response.data.buyPrice))
                            .trigger('change');
                        formElement.find('[name=status]').map((key, element) => {
                            if ($(element).val() == response.data.status) {
                                $(element).prop('checked', true);
                            } else {
                                $(element).prop('checked', false);
                            }
                        })
                        $("#uploadedAvatar").prop('src',
                            `{{ url('/') }}/customer-product/` + response.data.picture)
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

        function initializeDataTable() {
            if ($.fn.dataTable.isDataTable('#table-customer-temp-product')) {
                window.dataTableCustomerTemporaryProduct.off('draw.dt');
                window.dataTableCustomerTemporaryProduct.destroy();
                window.dataTableCustomerTemporaryProduct = undefined;
                $('#table-customer-temp-product').find('tbody').html('');
            }
            if ($.fn.dataTable.isDataTable('#table-customer-company-good')) {
                window.dataTableCustomerCompanyGood.off('draw.dt');
                window.dataTableCustomerCompanyGood.destroy();
                window.dataTableCustomerCompanyGood = undefined;
                $('#table-customer-company-good').find('tbody').html('');
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
                    name: 'orderCode',
                    data: 'orderCode',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 2,
                    name: 'created_at',
                    data: 'created_at',
                    orderable: true,
                    searchable: true,
                    render: $.fn.dataTable.render.date(),
                }, {
                    target: 3,
                    name: 'status',
                    data: 'status',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'accepted_by',
                    data: 'accepted_by',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
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
                }, ]
            });
            window.dataTableCustomerTemporaryProduct.on('draw.dt', function() {
                actionData();
            });
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
            window.dataTableCustomerCompanyGood.on('draw.dt', function() {
                actionData();
            });
        }

        function detailTableCustomerTemporaryProduct(d) {
            let html = ``;
            d.changedProduct.forEach(product => {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    ${product.name?? product.product.name}
                    <span class="badge bg-primary">${product.stock?? product.product.stock}</span>
                  </li>`
            });
            return (
                `<ul class="list-group">
                  ${html}
                </ul>`
            );
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
            accordionTemporaryProduct.append(`<div class="accordion-item shadow-sm" data-id=${window.lastProductAccordion}>
                                <h2 class="accordion-header">
                                    <button class="accordion-button ${status == 'IN'? '' :((status == 'RESTOCK')?'text-warning': 'text-danger')}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${window.lastProductAccordion}"
                                        aria-expanded="false" aria-controls="collapse${window.lastProductAccordion}">
                                        ${status == 'IN' ? `${status} Temporary Product` : `${status} ${data.name}`}
                                    </button>
                                </h2>
                                <div id="collapse${window.lastProductAccordion}" class="accordion-collapse collapse show">
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
                    `<form><input type="hidden" name="customerCompanyGoodId" value="${data.customerCompanyGoodId}"><input type="hidden" name="status" value="${status}"><input type="hidden" name="companyId" value="{{ session('userLogged')['company']['id'] }}"></form><div>Product will be remove</div>`
                );
            }
            if (status == 'RESTOCK') {
                $.each(data, function(key, value) {
                    if (key != 'status' && key != 'picture') {
                        if (key.split('rice').length > 1) {
                            container.find(`[name=${key}]`).val(parseInt(value)).trigger('change');
                        } else {
                            container.find(`[name=${key}]`).val(value).trigger('change');
                        }
                    }
                });
                container.find('.user-avatar').attr('src', `{{ asset('customer-product/${data.picture}') }}`);
            }
            container.find('[name=status]').val(status);
            window.lastProductAccordion++;
        }

        $(function() {
            window.dataTableCustomerTemporaryProduct = $("#table-customer-temp-product").DataTable({
                ajax: "{{ route('man.customer-temp-product.data-table') }}",
                processing: true,
                serverSide: true,
                order: [
                    [2, 'desc']
                ],
                columns: [{
                    class: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                }, {
                    target: 1,
                    name: 'orderCode',
                    data: 'orderCode',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 2,
                    name: 'created_at',
                    data: 'created_at',
                    orderable: true,
                    searchable: true,
                    render: $.fn.dataTable.render.date(),
                }, {
                    target: 3,
                    name: 'status',
                    data: 'status',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'accepted_by',
                    data: 'accepted_by',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-1'>${data}</div>`
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
                }, ]
            });
            window.dataTableCustomerTemporaryProduct.on('draw.dt', function() {
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
                    row.child(detailTableCustomerTemporaryProduct(row.data())).show();
                    if (idx === -1) {
                        detailRows.push(tr.id);
                    }
                }
            });
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
                        $.each(error.responseJSON.errors, function(indexInArray, valueOfProduct) {
                            let formContainer = $(
                                `#modal-customer-temp-product #collapse${parseInt(indexInArray.split('.')[1])+1}`);
                            formContainer.parents('.accordion-item.shadow-sm').addClass('border border-danger')
                            formContainer.find(`[name=${indexInArray.split('.')[2]}]`).addClass('is-invalid')
                        });
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
                let data = serializeFiles($('#form-customer-temp-product'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('man.customer-temp-product.update') }}/${$('#form-customer-temp-product').find('input[name=id]').val()}`,
                    data: data,
                    dataType: "json",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modal-customer-temp-product').modal('hide')
                        iziToast.success({
                            id: 'alert-customer-temp-product-form',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                        window.dataTableCustomerTemporaryProduct.ajax.reload()
                    },
                    error: function(error) {
                        $('#modal-customer-temp-product .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-customer-temp-product').find('[name=' +
                                indexInArray +
                                ']').addClass('is-invalid')
                        });
                        iziToast.error({
                            id: 'alert-customer-temp-product-form',
                            title: 'Error',
                            message: error.responseJSON.message,
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
        });
    </script>
@endpush
