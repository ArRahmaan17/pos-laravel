@extends('template.parent')
@section('title', 'Product Transaction')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
@endpush
@section('content')
    <div class="card">
        <div class="card-header d-flex align-middle">
            <div class="col-6">
                <h3>@yield('title')</h3>
            </div>
            <div class="col-6 text-end">
                <button class="btn btn-success" id="add-customer-company" data-bs-toggle="modal"
                    data-bs-target="#modal-customer-company">Add <i class='bx bxs-file-plus pb-1'></i></button><button
                    class="btn btn-success" id="add-customer-company" data-bs-toggle="modal"
                    data-bs-target="#modal-customer-company">Add <i class='bx bxs-file-plus pb-1'></i></button><button
                    class="btn btn-success" id="add-customer-company" data-bs-toggle="modal"
                    data-bs-target="#modal-customer-company">Add <i class='bx bxs-file-plus pb-1'></i></button>
            </div>
        </div>
        <div class="card-body">
            <form id="form-product-transaction">
                <div class="row mb-2">
                    <label class="col-12 col-md-2 col-form-label" for="officer">Officer</label>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control" readonly name="officer" id="officer"
                            value="{{ session('userLogged')['user']['name'] }}">
                    </div>
                </div>
                <div class="row mb-2">
                    <label class="col-12 col-md-2 col-form-label" for="orderCode">Transaction Code</label>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control" readonly name="orderCode" id="orderCode"
                            value="{{ lastCompanyOrderCode() }}">
                    </div>
                </div>
                <div class="row mb-2">
                    <label class="col-12 col-md-2 col-form-label" for="discountCode">Discount</label>
                    <div class="col-12 col-md-10 d-flex justify-content-between">
                        <div class="col-7 col-md-10">
                            <input type="text" class="form-control" name="discountCode" id="discountCode" value="">
                        </div>
                        <button type="button" class="btn btn-success col-4 col-md-1 btn-sm add-discount">Ok</button>
                    </div>
                </div>
            </form>
            <div class="col-12 text-end">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-customer-product"
                        class="btn btn-icon btn-outline-success"><i class="bx bx-plus"></i></button>
                    <button type="button" class="btn btn-icon btn-outline-danger clear-cart"><i
                            class="bx bx-x"></i></button>
                </div>
            </div>
            <div class="mt-3">
                <ul class="list-group cart-product py-1 px-2"
                    style="min-height: 350px;max-height: 350px; overflow-y:scroll;">
                </ul>
                <div class="text-end mt-3">
                    <p><strong>Subtotal: </strong><span class="subtotal-all">0</span></p>
                    <p><strong>Discount: </strong><span class="discount-price">0</span>
                    </p>
                    <h4><strong>Total: </strong><span class="total">0</span><sub
                            class="total-after-discount d-none">0</sub>
                    </h4>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-outline-success save-cart">Save <i
                            class="bx bx-save"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-customer-product" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-customer-product-title">Modal Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="table-customer-product" class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>stock</th>
                                    <th>price</th>
                                    <th>unit</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tdody></tdody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-customer-discount" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-customer-product-title">Modal Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="table-customer-discount" class="table">
                            <thead>
                                <tr>
                                    <th>Discount code</th>
                                    <th>discount percentage</th>
                                    <th>Min trasaction price</th>
                                    <th>Max transaction discount</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tdody></tdody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script>
        window.datatableCustomerCompanyGood = null;
        window.datatableCustomerCompanyDiscount = null;
        window.discount = {}

        function actionCustomerDiscount() {
            $('.use-discount').click(function() {
                if (window.datatableCustomerCompanyDiscount.rows('.selected').data().length == 0) {
                    $('#table-customer-product tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableCustomerCompanyDiscount.rows('.selected').data()[0];
                delete data.action;
                window.discount = data;
                $('#table-customer-discount tbody').find('tr').removeClass('selected');
            })
        }

        function quantityChange(element) {
            let quantity = $(element).val();
            let container = $(element).parents('.cart-item');
            let data = container.data('product');
            data.quantity = quantity;
            data.subtotal = parseFloat(data.price) * parseInt(quantity);
            container.find('.stock').html(numberToAlphabet(parseInt(data.stock) - parseInt(data.quantity)))
            container.find('.subtotal').html(numberToAlphabet(data.subtotal))
            container.data('product', data);
            countAll()
        }

        function countAll() {
            let arrayOfSubtotal = $('.cart-product').find('div.cart-item').map(function(index, element) {
                return $(element).data('product').subtotal;
            });
            let subtotal = arrayOfSubtotal.toArray().reduce((acc, curr) => {
                return parseInt(acc) + parseInt(curr)
            }, 0);
            if (Object.keys(window.discount).length != 0) {
                if (subtotal >= parseFloat(window.discount.minTransactionPrice)) {
                    $('.discount-percentage').html(`${window.discount.percentage}%`);
                    let discount = (subtotal - (window.discount.hasOwnProperty('maxTransactionDiscount') ? (window.discount
                            .maxTransactionDiscount * window.discount.percentage / 100) : 0)) * window.discount.percentage /
                        100;
                    $('.total-after-discount').removeClass('d-none')
                    $('.total').addClass('text-decoration-line-through fst-italic text-muted');
                    if ((window.discount.hasOwnProperty('maxTransactionDiscount')) ? discount <= window.discount
                        .maxTransactionDiscount : true) {
                        $('.discount-price').html(`${numberToAlphabet(discount)}`);
                        $('.total-after-discount').html(numberToAlphabet(subtotal - discount));
                        $('.total').html(numberToAlphabet(subtotal));
                    } else {
                        $('.discount-price').html(`${window.discount.maxTransactionDiscount}`);
                        $('.total-after-discount').html(numberToAlphabet(subtotal - window.discount
                            .maxTransactionDiscount));
                        $('.total').html(numberToAlphabet(subtotal));
                    }
                }
            } else {
                $('.total').html(numberToAlphabet(subtotal));
            }
            $('.subtotal-all').html(numberToAlphabet(subtotal));
        }

        function generateCartElement(data) {
            if ($('.cart-product').find('div.cart-item').length != 0) {
                let matchId = false
                $('.cart-product').find('div.cart-item').map(function(indexOrKey, elementOrValue) {
                    if (elementOrValue.id == data.id) {
                        matchId = true;
                        $(elementOrValue).find('input.quantity').val(parseInt($(elementOrValue).find(
                            'input.quantity').val()) + 1);
                        quantityChange($(elementOrValue).find('input.quantity'))
                    }
                });
                if (!matchId) {
                    delete data.action;
                    data.subtotal = parseFloat(data.price * 1);
                    data.quantity = 1;
                    $('.cart-product').append(`<div id="${data.id}" data-product='${JSON.stringify(data)}' class="list-group-item cart-item d-flex justify-content-between">
                        <div class="row align-items-center">
                            <div class="col-1">
                                <input class="form-check-input me-2" type="checkbox">
                            </div>
                            <div class="col-3">
                                <img src="../customer-product/${data.picture}" width="45px" alt="${data.name}" class="rounded">
                            </div>
                            <div class="col-7 align-self-center pt-2">
                                <h5 class="mb-1">${data.name}</h5>
                                <p class="mb-1">Unit: ${data.unit}</p>
                            </div>
                        </div>
                        <div class="row col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center justify-content-end">
                                <button class="btn btn-icon btn-outline-secondary decrease" type="button">
                                    <span>-</span>
                                </button>
                                 <input type="text" class="form-control mx-2 quantity" value="1"
                                    style="width: 60px; text-align: center;">
                                <button class="btn btn-icon btn-outline-secondary increase" type="button">
                                    <span>+</span>
                                </button>
                            </div>
                            <div
                                class="d-block d-md-flex d-lg-block align-items-center justify-content-end text-end text-md-start text-lg-end">
                                <div>Stok: <strong class='stock'>${numberToAlphabet(data.stock-1)}</strong></div>
                                <div>Subtotal: <strong class='subtotal'>${numberToAlphabet(data.subtotal)}</strong></div>
                            </div>
                        </div>
                    </div>`);
                    countAll()
                }
            } else {
                delete data.action;
                data.subtotal = parseFloat(data.price * 1);
                data.quantity = 1;
                $('.cart-product').append(`<div id="${data.id}" data-product='${JSON.stringify(data)}' class="list-group-item cart-item d-flex justify-content-between">
                        <div class="row align-items-center">
                            <div class="col-1">
                                <input class="form-check-input me-2" type="checkbox">
                            </div>
                            <div class="col-3">
                                <img src="../customer-product/${data.picture}" width="45px" alt="${data.name}" class="rounded">
                            </div>
                            <div class="col-7 align-self-center pt-2">
                                <h5 class="mb-1">${data.name}</h5>
                                <p class="mb-1">Unit: ${data.unit}</p>
                            </div>
                        </div>
                        <div class="row col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center justify-content-end">
                                <button class="btn btn-icon btn-outline-secondary decrease" type="button">
                                    <span>-</span>
                                </button>
                                 <input type="text" class="form-control mx-2 quantity" value="1"
                                    style="width: 60px; text-align: center;">
                                <button class="btn btn-icon btn-outline-secondary increase" type="button">
                                    <span>+</span>
                                </button>
                            </div>
                            <div
                                class="d-block d-md-flex d-lg-block align-items-center justify-content-end text-end text-md-start text-lg-end">
                                <div>Stok: <strong class='stock'>${numberToAlphabet(data.stock-1)}</strong></div>
                                <div>Subtotal: <strong class='subtotal'>${numberToAlphabet(data.subtotal)}</strong></div>
                            </div>
                        </div>
                    </div>`);
                countAll()
            }
        }

        function numberToAlphabet(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1).replace(/\.0$/, '') + ' jt';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1).replace(/\.0$/, '') + ' rb';
            } else {
                return num.toString();
            }
        }

        function actionCustomerProduct() {
            $('.add-cart').click(function() {
                if (window.datatableCustomerCompanyGood.rows('.selected').data().length == 0) {
                    $('#table-customer-product tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.datatableCustomerCompanyGood.rows('.selected').data()[0];
                generateCartElement(data);
                $('#table-customer-product tbody').find('tr').removeClass('selected');
            });
        }

        function saveTransactionProduct() {
            let data = $('.cart-product').find('div.cart-item').map(function(index, element) {
                return $(element).data('product');
            });
            data = {
                _token: `{{ csrf_token() }}`,
                ...serializeObject($('#form-product-transaction')),
                transactions: data.toArray(),
                discount: window.discount
            }
            $.ajax({
                type: "POST",
                url: `{{ route('man.customer-product-transaction.store') }}`,
                data: data,
                dataType: "json",
                success: function(response) {
                    $('#orderCode').val(response.orderCode);
                    clearAll();
                    $('[name=discountCode]').val('');
                    window.discount = null;
                }
            });
        }

        function clearAll() {
            $('.cart-product').html('');
            $('.subtotal-all').html('0');
            $('.discount-price').html('0');
            $('.total').removeClass('text-decoration-line-through fst-italic text-muted').html('0');
            $('.total-after-discount').addClass('d-none').html('0');
        }

        $(function() {
            $('#modal-customer-product').on('shown.bs.modal', function() {
                if (!$.fn.dataTable.isDataTable("#table-customer-product")) {
                    window.datatableCustomerCompanyGood = $("#table-customer-product").DataTable({
                        ajax: "{{ route('man.customer-product-transaction.product-data-table') }}",
                        processing: true,
                        serverSide: true,
                        order: [
                            [1, 'desc']
                        ],
                        columns: [{
                            target: 0,
                            name: 'name',
                            data: 'name',
                            orderable: true,
                            searchable: true,
                            render: (data, type, row, meta) => {
                                return `<div class='text-wrap'>${data}</div>`
                            }
                        }, {
                            target: 1,
                            name: 'stock',
                            data: 'stock',
                            orderable: true,
                            searchable: true,
                            render: $.fn.dataTable.render.number('.', ',', 0, '')
                        }, {
                            target: 2,
                            name: 'price',
                            data: 'price',
                            orderable: true,
                            searchable: true,
                            render: $.fn.dataTable.render.number('.', ',', 2, 'Rp.')
                        }, {
                            target: 3,
                            name: 'unit',
                            data: 'unit',
                            orderable: false,
                            searchable: false,
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
                        }, ]
                    });
                    window.datatableCustomerCompanyGood.on('draw.dt', function() {
                        actionCustomerProduct();
                    });
                } else {
                    $('.increase').off('click');
                    $('.decrease').off('click');
                }
            });

            $('#modal-customer-product').on('hidden.bs.modal', function() {
                $('.increase').click(function(e) {
                    let currentValue = parseInt($(this).siblings('.quantity').val());
                    if (currentValue >= 0) {
                        $(this).siblings('.quantity').val(currentValue += 1);
                        quantityChange($(this).siblings('.quantity')[0])
                    }
                });
                $('.decrease').click(function(e) {
                    let currentValue = parseInt($(this).siblings('.quantity').val());
                    if (currentValue >= 0) {
                        $(this).siblings('.quantity').val(currentValue -= 1);
                        quantityChange($(this).siblings('.quantity')[0])
                    }
                });
                $('.quantity').change(function(e) {
                    quantityChange(e.currentTarget)
                });
            });
            $('#modal-customer-discount').on('shown.bs.modal', function() {
                if (!$.fn.dataTable.isDataTable("#table-customer-discount")) {
                    window.datatableCustomerCompanyDiscount = $("#table-customer-discount").DataTable({
                        ajax: "{{ route('man.customer-product-transaction.discount-data-table') }}",
                        processing: true,
                        serverSide: true,
                        order: [
                            [1, 'desc']
                        ],
                        columns: [{
                            target: 0,
                            name: 'code',
                            data: 'code',
                            orderable: true,
                            searchable: true,
                            render: (data, type, row, meta) => {
                                return `<div class='text-wrap'>${data}</div>`
                            }
                        }, {
                            target: 1,
                            name: 'percentage',
                            data: 'percentage',
                            orderable: true,
                            searchable: true,
                            render: (data, type, row, meta) => {
                                return `<div class='text-wrap'>${data}%</div>`
                            }
                        }, {
                            target: 2,
                            name: 'minTransactionPrice',
                            data: 'minTransactionPrice',
                            orderable: false,
                            searchable: false,
                            render: $.fn.dataTable.render.number('.', ',', 2, 'Rp.')
                        }, {
                            target: 3,
                            name: 'maxTransactionDiscount',
                            data: 'maxTransactionDiscount',
                            orderable: false,
                            searchable: false,
                            render: $.fn.dataTable.render.number('.', ',', 2, 'Rp.')
                        }, {
                            target: 4,
                            name: 'action',
                            data: 'action',
                            orderable: false,
                            searchable: false,
                            render: (data, type, row, meta) => {
                                return `<div class='d-flex gap-1'>${data}</div>`
                            }
                        }, ]
                    });
                    window.datatableCustomerCompanyDiscount.on('draw.dt', function() {
                        actionCustomerDiscount();
                    });
                } else {
                    $('.increase').off('click');
                    $('.decrease').off('click');
                }
            });
            $('.clear-cart').click(function() {
                if ($('.cart-product').find('div.cart-item').length > 0) {
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
                        message: "Are you sure you want to delete this cart data?",
                        position: 'center',
                        icon: 'bx bx-question-mark',
                        buttons: [
                            ['<button><b>OK</b></button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');
                                clearAll()
                            }, true],
                            ['<button>CANCEL</button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');
                            }],
                        ],
                    });
                } else {
                    iziToast.info({
                        id: 'alert-customer-company-good-clear',
                        title: 'Information',
                        message: 'Cart is empty, nothing to clear',
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
            $('.save-cart').click(function() {
                if ($('.cart-product').find('div.cart-item').length > 0) {
                    iziToast.question({
                        timeout: 5000,
                        layout: 2,
                        close: false,
                        overlay: true,
                        color: 'green',
                        displayMode: 'once',
                        id: 'question-save',
                        zindex: 9999,
                        title: 'Confirmation',
                        message: "Are you sure you want to save this cart data?",
                        position: 'center',
                        icon: 'bx bx-question-mark',
                        buttons: [
                            ['<button><b>OK</b></button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');
                                saveTransactionProduct();
                            }, true],
                            ['<button>CANCEL</button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');
                            }],
                        ],
                    });
                } else {
                    iziToast.info({
                        id: 'alert-customer-company-good-save',
                        title: 'Information',
                        message: 'Cart is empty, nothing to save',
                        position: 'topRight',
                        layout: 2,
                        displayMode: 'replace'
                    });
                }
            });
            $('.add-discount').click(function() {
                let discountCode = $('#discountCode').val();
                if (discountCode != '') {
                    $.ajax({
                        type: "get",
                        url: `{{ route('man.customer-product-transaction.validate-discount-code') }}/${discountCode}`,
                        dataType: "JSON",
                        success: function(response) {
                            if (response.data.maxTransactionDiscount == null) {
                                delete response.data.maxTransactionDiscount;
                            }
                            window.discount = response.data;
                            countAll();
                        },
                        error: function(error) {
                            window.discount = error.responseJSON.data
                        }
                    });
                } else {
                    $('#modal-customer-discount').modal('show');
                }
            });
        });
    </script>
@endpush
