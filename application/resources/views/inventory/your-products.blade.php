@extends('template.parent')
@section('title', 'Product Supply Management')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-middle">
                <div class="col-6">
                    <h3>@yield('title')</h3>
                </div>
                <div class="col-6 text-end">
                    <button class="btn btn-outline-success" id="add-customer-company-good" data-bs-toggle="modal" data-bs-target="#modal-customer-company-good">
                        Add <i class='bx bxs-file-plus pb-1'></i></button>
                    <button id="toggle-off-canvas-temporary-cart" class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#off-canvas-temporary-cart" aria-controls="off-canvas-temporary-cart">
                        <i class='bx bxs-folder-open pb-1'></i>
                        <span class="small">0</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
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
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-customer-company-good" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm-xl modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New @yield('title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" id="form-customer-company-good">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="company_id" value="{{ session('userLogged')['company']['id'] }}">
                    <div class="row">
                        <label class="form-label" for="">Status</label>
                        <div class="col">
                            <div class="btn-group col-12" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="status" value="archive" id="archive-btn">
                                <label class="btn btn-outline-danger" for="archive-btn"><i class='bx bx-archive-in'></i>
                                    Archive</label>
                                <input type="radio" class="btn-check" name="status" value="draft" id="draft-btn" checked="">
                                <label class="btn btn-outline-warning" for="draft-btn"><i class='bx bx-hourglass'></i>Draft</label>
                                <input type="radio" class="btn-check" name="status" value="publish" id="publish-btn">
                                <label class="btn btn-outline-success" for="publish-btn"><i class='bx bxs-slideshow'></i>Publish</label>
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
                            <label for="buy_price" class="form-label">Buy Price</label>
                            <input type="text" id="buy_price" name="buy_price" class="form-control price" placeholder="Enter Price" />
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
                            <label for="weight_id" class="form-label">Unit</label>
                            <select class="form-control select2" name="weight_id" id="weight_id">
                                <option value="">Not selected</option>
                                @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->description }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-control select2" name="category_id" id="category_id">
                                <option value="">Not selected</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->description }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                <img draggable="false" src="{{ asset('resources/default/product/default-product.png') }}" alt="user-avatar" class="d-block rounded" height="100"
                                    width="100" id="uploadedAvatar" />
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                        <span class="d-none d-sm-block">Upload new photo</span>
                                        <i class="bx bx-upload d-block d-sm-none"></i>
                                        <input type="file" id="upload" name="picture" class="account-file-input" hidden
                                            accept="image/png, image/jpeg" />
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" id="save-customer-company-good" class="btn btn-outline-success">Save
                    changes</button>
                <button type="button" id="edit-customer-company-good" class="btn btn-warning d-none">Update
                    changes</button>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="off-canvas-temporary-cart" aria-labelledby="off-canvas-temporary-cart-label">
    <div class="offcanvas-header">
        <h5 id="off-canvas-temporary-cart-label" class="offcanvas-title">Temporary Changed Product ({{ lastCompanyOrderCode('IN') }},
            {{ lastCompanyOrderCode('ADJ') }})
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body my-auto mx-0 flex-grow-0">
        <div class="accordion mt-3 overflow-y-hidden" id="accordionTempProduct">
        </div>
    </div>
    <div class="offcanvas-footer mb-1 mx-1">
        <button type="button" id="save-temporary-product"
            class="btn btn-primary mb-2 d-grid w-100 {{ in_array(getScope(), ['Manager', 'Developer']) ? '' : 'disabled' }}">Continue
            (Hanya bisa dilakukan oleh manager)</button>
        <button type="button" class="btn btn-outline-secondary d-grid w-100" data-bs-dismiss="offcanvas">
            Cancel
        </button>
    </div>
</div>
@endsection
@push('resource-js')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>

<script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
<script>
    window.dataTableCustomerCompanyGood = null;
    window.state = 'add';

    function actionData() {
        $('.edit').click(function() {
            window.state = 'update';
            let idAppRole = $(this).data("customer-company-good");
            $("#edit-customer-company-good").data("customer-company-good", idAppRole);
            if (window.dataTableCustomerCompanyGood.rows('.selected').data().length === 0) {
                $('#table-customer-company-good tbody').find('tr').removeClass('selected');
                $(this).parents('tr').addClass('selected');
            }

            var data = window.dataTableCustomerCompanyGood.rows('.selected').data()[0];

            $('#modal-customer-company-good').modal('show');
            $('#modal-customer-company-good').find('.modal-title').html(`Edit @yield('title')`);
            $('#save-customer-company-good').addClass('d-none');
            $('#edit-customer-company-good').removeClass('d-none');

            $.ajax({
                type: "GET",
                url: "{{ route('inventory.your-products.show') }}/" + idAppRole,
                dataType: "json",
                success: function(response) {
                    let formElement = $('#modal-customer-company-good').find("form");
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
                    formElement.find('[name=weight_id]')
                        .val(response.data.weight_id)
                        .trigger('change');
                    formElement.find('[name=category_id]')
                        .val(response.data.category_id)
                        .trigger('change');
                    formElement.find('[name=company_id]')
                        .val(response.data.company_id)
                        .trigger('change');
                    formElement.find('[name=buy_price]')
                        .val(parseInt(response.data.buy_price))
                        .trigger('change');
                    formElement.find('[name=status]').map((key, element) => {
                        if ($(element).val() === response.data.status) {
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
                        id: 'alert-customer-company-good-action',
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
            if (window.dataTableCustomerCompanyGood.rows('.selected').data().length === 0) {
                $('#table-customer-company-good tbody').find('tr').removeClass('selected');
                $(this).parents('tr').addClass('selected')
            }
            let idAppRole = $(this).data("customer-company-good");
            var data = window.dataTableCustomerCompanyGood.rows('.selected').data()[0];
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
                            url: "{{ route('inventory.your-products.delete') }}/" +
                                idAppRole,
                            data: {

                            },
                            dataType: "json",
                            success: function(response) {
                                iziToast.success({
                                    id: 'alert-customer-company-good-action',
                                    title: 'Success',
                                    message: response.message,
                                    position: 'topRight',
                                    layout: 2,
                                    displayMode: 'replace'
                                });
                                window.dataTableCustomerCompanyGood.ajax.reload()
                            },
                            error: function(error) {
                                iziToast.error({
                                    id: 'alert-customer-company-good-action',
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

    function renderAccordionElement(data) {
        return `<div class="card accordion-item">
                    <h2 class="accordion-header" id="heading${data.id}">
                        <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordion${data.id}"
                            aria-expanded="false" aria-controls="accordion${data.id}">
                            ${data.orderCode.split('IN').length > 1 ? 'Add': data.orderCode.split('RESTOCK').length > 1 ? 'Restock' : 'Delete'} Product ${data.product?data.product.name: data.name}
                        </button>
                    </h2>
                    <div id="accordion${data.id}" class="accordion-collapse collapse" aria-labelledby="heading${data.id}" data-bs-parent="#accordionTempProduct">
                        <div class="accordion-body">
                            ${data.orderCode.split('IN').length > 1 ? `Buy price ${data.buy_price}, sell price ${data.price}, and stock ${data.stock}` : data.orderCode.split('RESTOCK').length > 1 ?`Restock ${data.stock}, buying price ${data.buy_price} and price ${data.price}` : `Product will be remove`}
                        </div>
                    </div>
                </div>`;
    }

    function getAllTempProduct() {
        $.ajax({
            type: "GET",
            url: "{{ route('inventory.your-products.temp-product') }}",
            dataType: "JSON",
            success: function({
                data
            }) {
                $('#toggle-off-canvas-temporary-cart').find('.small').html(data.length);
                data.forEach(product => {
                    $('#accordionTempProduct').append(`${renderAccordionElement(product)}`);
                });
            }
        });
    }

    function initializeDataTable() {
        if ($.fn.dataTable.isDataTable('#table-customer-company-good')) {
            window.dataTableCustomerCompanyGood.destroy();
        }
        window.dataTableCustomerCompanyGood = $('#table-customer-company-good').DataTable();
    }

    $(function() {
        window.dataTableCustomerCompanyGood = $("#table-customer-company-good").DataTable({
            ajax: "{{ route('inventory.your-products.data-table') }}",
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
                name: 'action',
                data: 'action',
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
        $('#save-customer-company-good').click(function() {
            let data = serializeFiles($('#form-customer-company-good'));
            $.ajax({
                type: "POST",
                url: `{{ route('inventory.your-products.store') }}`,
                data: data,
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modal-customer-company-good').modal('hide');
                    iziToast.success({
                        id: 'alert-customer-company-good-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                    window.dataTableCustomerCompanyGood.ajax.reload();
                },
                error: function(error) {
                    $('#modal-customer-company-good .is-invalid').removeClass('is-invalid')
                    $.each(error.responseJSON.errors, function(indexInArray,
                        valueOfElement) {
                        $('#modal-customer-company-good').find('[name=' +
                            indexInArray +
                            ']').addClass('is-invalid')
                    });
                    iziToast.error({
                        id: 'alert-customer-company-good-form',
                        title: 'Error',
                        message: error.responseJSON.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
        });
        $('#save-temporary-product').click(function() {
            $.ajax({
                type: "POST",
                url: `{{ route('inventory.your-products.store-temporary-product', ['date' => now()->format('Y-m-d')]) }}`,
                data: {

                },
                dataType: "json",
                success: function(response) {
                    iziToast.success({
                        id: 'alert-customer-company-good-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                    $('#off-canvas-temporary-cart').offcanvas('hide');
                    window.dataTableCustomerCompanyGood.ajax.reload();
                },
                error: function(err) {
                    iziToast.error({
                        id: 'alert-customer-company-good-form',
                        title: 'Error',
                        message: error.responseJSON.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
        })
        $('#edit-customer-company-good').click(function() {
            let data = serializeFiles($('#form-customer-company-good'));
            $.ajax({
                type: "POST",
                url: `{{ route('inventory.your-products.update') }}/${$('#form-customer-company-good').find('input[name=id]').val()}`,
                data: data,
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modal-customer-company-good').modal('hide')
                    iziToast.success({
                        id: 'alert-customer-company-good-form',
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                    window.dataTableCustomerCompanyGood.ajax.reload()
                },
                error: function(error) {
                    $('#modal-customer-company-good .is-invalid').removeClass('is-invalid')
                    $.each(error.responseJSON.errors, function(indexInArray,
                        valueOfElement) {
                        $('#modal-customer-company-good').find('[name=' +
                            indexInArray +
                            ']').addClass('is-invalid')
                    });
                    iziToast.error({
                        id: 'alert-customer-company-good-form',
                        title: 'Error',
                        message: error.responseJSON.message,
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
        });
        $('#off-canvas-temporary-cart').on('shown.bs.offcanvas', function() {
            getAllTempProduct();
        });
        $('#off-canvas-temporary-cart').on('hidden.bs.offcanvas', function() {
            $('#accordionTempProduct').html('')
        });
        $('#modal-customer-company-good').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $(this).find('.modal-title').html(`Add New @yield('title')`);
            $('#save-customer-company-good').removeClass('d-none');
            $('#edit-customer-company-good').addClass('d-none');
            $('#modal-customer-company-good .is-invalid').removeClass('is-invalid')
            $('#table-customer-company-good tbody').find('tr').removeClass('selected');
            $('#uploadedAvatar').prop('src', `{{ asset('resources/default/product/default-product.png') }}`);
        });
        $('#modal-customer-company-good').on('shown.bs.modal', function() {
            setTimeout(() => {
                $('.select2').select2({
                    dropdownParent: $('#modal-customer-company-good'),

                });
            }, 140);
        });
        $('.price').inputmask('currency', {
            radixPoint: ',',
            groupSeparator: ".",
            rightAlign: false,
            allowMinus: false
        });
        $('.number').inputmask('integer', {
            groupSeparator: ".",
            rightAlign: false,
            allowMinus: false
        });
        formattedInput();
        let accountUserImage = document.getElementById('uploadedAvatar');
        const fileInput = document.querySelector('.account-file-input'),
            resetFileInput = document.querySelector('.account-image-reset');

        if (accountUserImage) {
            const resetImage = accountUserImage.src;
            fileInput.onchange = () => {
                if (fileInput.files[0]) {
                    accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
                }
            };
            resetFileInput.onclick = () => {
                fileInput.value = '';
                accountUserImage.src = resetImage;
            };
        }
        $(window).resize(debounce(function() {
            initializeDataTable();
        }, 1500));
    });
</script>
@endpush