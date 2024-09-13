@extends('template.parent')
@section('title', 'Shelf Product')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dragula.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="card">
        <div class="card-header d-flex align-middle">
            <div class="col-10">
                <h3>@yield('title')</h3>
            </div>
            <div class="col-2">
                <select id="companyId" class="form-control select2">
                    <option value="">Select Company</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">Please Select Your Warehouse</div>
            <div class="row g-3 warehouse-container">
                <div class="col-12 border shadow rounded py-1">
                    <p>Warehouse 1</p>
                    <div class="row row-cols-3 g-1 shelf-container">
                        <div class="col-4 border rounded">
                            <div class="col-12 border-bottom text-capitalize fs-3">
                                shelf 1
                            </div>
                            <div id="${data.id ? 'shelf-'+data.id : 'shelfless'}" class="col-12 product-container"
                                style="min-height:100px;">
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                            </div>
                        </div>
                        <div class="col-4 border rounded">
                            <div class="col-12 border-bottom text-capitalize fs-3">
                                shelf 1
                            </div>
                            <div id="${data.id ? 'shelf-'+data.id : 'shelfless'}" class="col-12 product-container"
                                style="min-height:100px;">
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                            </div>
                        </div>
                        <div class="col-4 border rounded">
                            <div class="col-12 border-bottom text-capitalize fs-3">
                                shelf 1
                            </div>
                            <div id="${data.id ? 'shelf-'+data.id : 'shelfless'}" class="col-12 product-container"
                                style="min-height:100px;">
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 border shadow rounded py-1">
                    <p>Warehouse 1</p>
                    <div class="row row-cols-3 g-1 shelf-container">
                        <div class="col-4 border rounded">
                            <div class="col-12 border-bottom text-capitalize fs-3">
                                shelf 1
                            </div>
                            <div id="${data.id ? 'shelf-'+data.id : 'shelfless'}" class="col-12 product-container"
                                style="min-height:100px;">
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                            </div>
                        </div>
                        <div class="col-4 border rounded">
                            <div class="col-12 border-bottom text-capitalize fs-3">
                                shelf 1
                            </div>
                            <div id="${data.id ? 'shelf-'+data.id : 'shelfless'}" class="col-12 product-container"
                                style="min-height:100px;">
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                            </div>
                        </div>
                        <div class="col-4 border rounded">
                            <div class="col-12 border-bottom text-capitalize fs-3">
                                shelf 1
                            </div>
                            <div id="${data.id ? 'shelf-'+data.id : 'shelfless'}" class="col-12 product-container"
                                style="min-height:100px;">
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                                <div class="col">...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/dragula.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script>
        function generateShelf(data) {
            let childHtml = ``;
            data.products.forEach(element => {
                childHtml +=
                    `<div class="${data.id ? 'col-12': 'col-4'} border rounded-sm my-1" id="product-${element.hasOwnProperty('product') ? element.product.id : element.id}"><div class="row p-1"><div class="col-2 align-self-center"><div class="avatar"><img src="../customer-product/${element.hasOwnProperty('product') ? element.product.picture : element.picture}" alt="" class="w-px-40 h-auto rounded-circle"></div></div><div class="col"><p class="fs-5 mb-0">${element.hasOwnProperty('product') ? element.product.name : element.name}</p><p class="text-muted mb-0">${element.hasOwnProperty('product') ? element.product.stock : element.stock} (Rp.${element.hasOwnProperty('product') ? element.product.price : element.price})</p></div></div></div>`
            });
            let html = `<div class="${data.id ? 'col-4' : 'col-12'} border rounded">
                            <div class="col-12 border-bottom text-capitalize fs-3">
                                ${data.name ?? 'shelfless'}
                            </div>
                            <div id="${data.id ? 'shelf-'+data.id : 'shelfless'}" class="col-12 product-container ${data.id ? '': 'row'}" style="min-height:100px;">
                                ${childHtml}
                            </div>
                        </div>`;
            return html;
        }

        function generateWarehouse(data) {
            let htmlShelf = ``;
            data.racks.forEach(element => {
                htmlShelf += generateShelf(element);
            });
            let html = `<div class="col-12 border shadow rounded py-1">
                    <p>Warehouse 1</p>
                    <div class="row row-cols-3 g-1 shelf-container">${htmlShelf}</div></div>`;
            $('.warehouse-container').append(html)
        }

        function generateDragula(container = [...$('.product-container')]) {
            dragula(container, {
                direction: 'all', // Y axis is considered when determining where an element would be dropped
                copy: false, // elements are moved by default, not copied
                copySortSource: false, // elements in copy-source containers can be reordered
                revertOnSpill: false, // spilling will put the element back where it was dragged from, if this is true
                removeOnSpill: false, // spilling will `.remove` the element, if this is true
                mirrorContainer: document.body, // set the element that gets mirror elements appended
                ignoreInputTextSelection: true, // allows users to select input text, see details below
                slideFactorX: 0, // allows users to select the amount of movement on the X axis before it is considered a drag instead of a click
                slideFactorY: 0, // allows users to select the amount of movement on the Y axis before it is considered a drag instead of a click
            }).on('drag', function(el) {
                el.className = el.className.replace('ex-moved', '');
            }).on('drop', function(el) {
                el.className += ' ex-moved';
            }).on('over', function(el, container) {
                container.className += ' ex-over';
            }).on('out', debounce(function(el, container) {
                container.className = container.className.replace('ex-over', '');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': `{{ csrf_token() }}`
                    },
                    type: "put",
                    url: `{{ route('man.customer-warehouse-rack-good.update') }}/${container.id.split('shelf-').join('')}/${el.id.split('product-').join('')}`,
                    dataType: "json",
                    success: function(response) {
                        iziToast.success({
                            id: 'alert-customer-company-action',
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    },
                    error: function(error) {
                        iziToast.error({
                            id: 'alert-customer-company-action',
                            title: 'Error',
                            message: error.responseJSON.message,
                            position: 'topRight',
                            layout: 2,
                            displayMode: 'replace'
                        });
                    }
                });
            }, 1000));
        }
        $(function() {
            $('#companyId').change(function(e) {
                if (this.value != '') {
                    $('.alert.alert-warning').addClass('d-none').removeClass('d-block');
                    $.ajax({
                        type: "GET",
                        url: `{{ route('man.customer-warehouse-rack-good.show') }}/${this.value}`,
                        dataType: "json",
                        beforeSend: function() {
                            $('.warehouse-container').html(`<div class="col-12 border shadow rounded py-1">
                                <p class="col-3 placeholder"></p> 
                                <div class="row row-cols-3 g-1 shelf-container">
                                    <div class="card" aria-hidden="true">
                                        <div class="card-body">
                                            <h5 class="card-title placeholder-glow">
                                                <span class="placeholder col-3"></span>
                                            </h5>
                                            <p class="card-text placeholder-glow">
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card" aria-hidden="true">
                                        <div class="card-body">
                                            <h5 class="card-title placeholder-glow">
                                                <span class="placeholder col-3 rounded-lg"></span>
                                            </h5>
                                            <p class="card-text placeholder-glow">
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card" aria-hidden="true">
                                        <div class="card-body">
                                            <h5 class="card-title placeholder-glow">
                                                <span class="placeholder col-3 rounded-lg"></span>
                                            </h5>
                                            <p class="card-text placeholder-glow">
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                                <span class="placeholder placeholder-lg col-12 p-4 mb-2"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `);
                        },
                        success: function(response) {
                            $('.warehouse-container').html('');
                            $('.warehouse-container').append(generateShelf({
                                products: [...response.shelf_less],
                            }));
                            response.data.forEach(element => {
                                generateWarehouse(element);
                            });
                            generateDragula();
                        },
                    });
                } else {
                    $('.alert.alert-warning').addClass('d-block').removeClass('d-none');
                    $('.warehouse-container').html('');
                }
            });
            $('.select2').select2();
        });
    </script>
@endpush
