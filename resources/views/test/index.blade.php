@extends('template.parent')
@section('content')
    @push('css')
    @endpush

    {{-- Start Header Content --}}
    <div class="row">
        <div class="col-4">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Product</span>
                        <h3 class="card-title mb-2">4</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success"
                                    class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Discount</span>
                        <h3 class="card-title mb-2">4</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success"
                                    class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Employees</span>
                        <h3 class="card-title mb-2">4</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- End Header Content --}}

    {{-- Start Data Diagram --}}
    <div class="row">
        <!-- Product Statistics -->
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="mb-1 me-2">Best Selling Products</h5>
                        <small class="text-muted">Total product : 4</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <h3 class="mb-1">8,258</h3>
                            <small>Total Number of Products Sold</small>
                        </div>
                        <div id="chart1"></div>
                    </div>
                    <div class="card-body mt-3">
                        <div class="table-responsive">
                            <table class="table" id="table-customer-user">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Product 1</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Product 2</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Product 3</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-mobile-alt"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small class="text-muted">Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">82.5k</small>
                                </div>
                            </div>
                        </li>
                    </ul> --}}
                    </div>
                    {{-- <ul class="p-0 m-0">
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class='bx bx-mobile-alt'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small>Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">82.5k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class='bx bx-closet'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Fashion</h6>
                                    <small>T-shirt, Jeans, Shoes</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">23.8k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class='bx bx-home-alt'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Decor</h6>
                                    <small>Fine Art, Dining</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">849k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary"><i
                                        class='bx bx-football'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Sports</h6>
                                    <small>Football, Cricket Kit</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">99</h6>
                                </div>
                            </div>
                        </li>
                    </ul> --}}
                </div>
            </div>
        </div>
        <!--/ Product Statistics -->

        <!-- Discount Statistics -->
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="mb-1 me-2">Widely Used Discounts</h5>
                        <small class="text-muted">Total Discount: 4</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <h3 class="mb-1">8,258</h3>
                            <small>Amount of discount coupons used</small>
                        </div>
                        <div id="chart2"></div>
                    </div>
                    <div class="card-body mt-3">
                        <div class="table-responsive">
                            <table class="table" id="table-customer-user">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Name Discount</th>
                                        <th scope="col">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Discount 22</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Discount 23</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Discount 24</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-mobile-alt"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small class="text-muted">Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">82.5k</small>
                                </div>
                            </div>
                        </li>
                    </ul> --}}
                    </div>
                    {{-- <ul class="p-0 m-0">
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class='bx bx-mobile-alt'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small>Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">82.5k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class='bx bx-closet'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Fashion</h6>
                                    <small>T-shirt, Jeans, Shoes</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">23.8k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class='bx bx-home-alt'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Decor</h6>
                                    <small>Fine Art, Dining</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">849k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary"><i
                                        class='bx bx-football'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Sports</h6>
                                    <small>Football, Cricket Kit</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">99</h6>
                                </div>
                            </div>
                        </li>
                    </ul> --}}
                </div>
            </div>
        </div>
        <!--/ Discount Statistics -->

        <!-- Employee Statistics -->
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="mb-1 me-2">Best Employee in Transaction</h5>
                        <small class="text-muted">Total Employee : 4</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <h3 class="mb-1">8,258</h3>
                            <small>Employees who make frequent transactions </small>
                        </div>
                        <div id="chart3"></div>
                    </div>
                    <div class="card-body mt-3">
                        <div class="table-responsive">
                            <table class="table" id="table-customer-user">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Name Employee</th>
                                        <th scope="col">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Employee 1</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Employee 2</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Employee 3</td>
                                        <td>Rp. 100.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-mobile-alt"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small class="text-muted">Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">82.5k</small>
                                </div>
                            </div>
                        </li>
                    </ul> --}}
                    </div>
                    {{-- <ul class="p-0 m-0">
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class='bx bx-mobile-alt'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small>Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">82.5k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class='bx bx-closet'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Fashion</h6>
                                    <small>T-shirt, Jeans, Shoes</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">23.8k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class='bx bx-home-alt'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Decor</h6>
                                    <small>Fine Art, Dining</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">849k</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary"><i
                                        class='bx bx-football'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Sports</h6>
                                    <small>Football, Cricket Kit</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">99</h6>
                                </div>
                            </div>
                        </li>
                    </ul> --}}
                </div>
            </div>
        </div>
        <!--/ Employee Statistics -->
    </div>
    {{-- End Data Diagram --}}
@endsection
{{-- JS --}}
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script>
        let cardColor, headingColor, axisColor, shadeColor, borderColor;

        cardColor = config.colors.white;
        headingColor = config.colors.headingColor;
        axisColor = config.colors.axisColor;
        borderColor = config.colors.borderColor;
        const totalRevenueChartEl = document.querySelector('#totalRevenueChart'),
            totalRevenueChartOptions = {
                series: [{
                        name: '{{ env('TAHUN_APLIKASI') ?? 2024 }}',
                        data: [parseInt(`{{ $countBaNow ?? 10 }}`)]
                    },
                    {
                        name: '{{ intval(env('TAHUN_APLIKASI') ?? 2024) - 1 }}',
                        data: [parseInt(`{{ $countBaPast ?? 15 }}`)]
                    }
                ],
                chart: {
                    height: 300,
                    stacked: true,
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '33%',
                        borderRadius: 12,
                        startingShape: 'rounded',
                        endingShape: 'rounded'
                    }
                },
                colors: [config.colors.primary, config.colors.info],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 6,
                    lineCap: 'round',
                    colors: [cardColor]
                },
                legend: {
                    show: true,
                    horizontalAlign: 'left',
                    position: 'top',
                    markers: {
                        height: 8,
                        width: 8,
                        radius: 12,
                        offsetX: -3
                    },
                    labels: {
                        colors: axisColor
                    },
                    itemMargin: {
                        horizontal: 10
                    }
                },
                grid: {
                    borderColor: borderColor,
                    padding: {
                        top: 0,
                        bottom: -8,
                        left: 20,
                        right: 20
                    }
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    labels: {
                        style: {
                            fontSize: '13px',
                            colors: axisColor
                        }
                    },
                    axisTicks: {
                        show: false
                    },
                    axisBorder: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '13px',
                            colors: axisColor
                        }
                    }
                },
                responsive: [{
                        breakpoint: 1700,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '32%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 1580,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '35%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 1440,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '42%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 1300,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '48%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 1200,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '40%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 1040,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 11,
                                    columnWidth: '48%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 991,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '30%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 840,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '35%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 768,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '28%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 640,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '32%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 576,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '37%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 480,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '45%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 420,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '52%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 380,
                        options: {
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '60%'
                                }
                            }
                        }
                    }
                ],
                states: {
                    hover: {
                        filter: {
                            type: 'none'
                        }
                    },
                    active: {
                        filter: {
                            type: 'none'
                        }
                    }
                }
            };
        if (typeof totalRevenueChartEl !== undefined && totalRevenueChartEl !== null) {
            const totalRevenueChart = new ApexCharts(totalRevenueChartEl, totalRevenueChartOptions);
            totalRevenueChart.render();
        }
    </script>
    <script>
        function createCustomDonutChart(chartId, data) {
            const chartElement = document.querySelector(chartId);
            if (!chartElement) {
                console.error(`Element with ID ${chartId} not found`);
                return;
            }

            const orderChartConfig = {
                chart: {
                    height: 165,
                    width: 130,
                    type: 'donut'
                },
                labels: data.labels,
                series: data.series,
                colors: data.colors || [config.colors.primary, config.colors.secondary, config.colors.info, config
                    .colors.success
                ],
                stroke: {
                    width: 5,
                    colors: data.cardColor || cardColor
                },
                dataLabels: {
                    enabled: false,
                    formatter: function(val, opt) {
                        return parseInt(val) + '%';
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    padding: {
                        top: 0,
                        bottom: 0,
                        right: 15
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                value: {
                                    fontSize: '1.5rem',
                                    fontFamily: 'Public Sans',
                                    color: data.headingColor || headingColor,
                                    offsetY: -15,
                                    formatter: function(val) {
                                        return parseInt(val) + '%';
                                    }
                                },
                                name: {
                                    offsetY: 20,
                                    fontFamily: 'Public Sans'
                                },
                                total: {
                                    show: true,
                                    fontSize: '0.8125rem',
                                    color: data.axisColor || axisColor,
                                    label: data.totalLabel || 'Weekly',
                                    formatter: function(w) {
                                        return data.totalFormatter ? data.totalFormatter(w) : '38%';
                                    }
                                }
                            }
                        }
                    }
                }
            };

            const statisticsChart = new ApexCharts(chartElement, orderChartConfig);
            statisticsChart.render();
        }

        document.addEventListener('DOMContentLoaded', function() {
            createCustomDonutChart('#chart1', {
                labels: ['Electronic', 'Sports', 'Decor', 'Fashion'],
                series: [85, 15, 50, 50],
                colors: ['#696cff', '#8592a3', '#03c3ec', '#71dd37'],
                cardColor: '#fff',
                headingColor: '#566a7f',
                axisColor: '#697a8d',
                totalLabel: 'Monthly',
                totalFormatter: function(w) {
                    return '42%';
                }
            });

            // Anda bisa membuat chart lain dengan memanggil fungsi lagi
            createCustomDonutChart('#chart2', {
                labels: ['Food', 'Transport', 'Entertainment', 'Bills'],
                series: [30, 25, 20, 25],
                totalLabel: 'Weekly',
                totalFormatter: function(w) {
                    return '100%';
                }
            });

            createCustomDonutChart('#chart3', {
                labels: ['Food', 'Transport', 'Entertainment', 'Bills'],
                series: [30, 25, 20, 25],
                totalLabel: 'Weekly',
                totalFormatter: function(w) {
                    return '100%';
                }
            });
        });
    </script>
@endpush
