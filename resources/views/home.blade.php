@extends('layouts.app')

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <!-- Including the sidebar partial -->
        <div class="layout-page">
            <div class="content-wrapper pl-30 ">

                <div class="flex-grow-1  container-fluid">
                    <div class="page-header">
                        <h1>Dashboard</h1>
                    </div>
                    <div class="row g-3">
                        <!-- section 1 start -->
                        <div class="col-12 col-sm-6  col-lg-4">
                            <a href="{{ route('customers.index') }}" class="menu-link d-block">
                                <div class="dashboard-card card">
                                    <div class="icon-wrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-report">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
                                            <path d="M18 14v4h4" />
                                            <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2" />
                                            <path
                                                d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                            <path d="M8 11h4" />
                                            <path d="M8 15h3" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5>{{ $customerCount }}</h5>
                                        <p class="text-secondary">Customers</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-12 col-sm-6  col-lg-4">
                            <a href="{{ route('showproduct') }}" class="menu-link d-block">
                                <div class="dashboard-card card">
                                    <div class="icon-wrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-report">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
                                            <path d="M18 14v4h4" />
                                            <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2" />
                                            <path
                                                d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                            <path d="M8 11h4" />
                                            <path d="M8 15h3" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5>{{ $productCount }}</h5>
                                        <p class="text-secondary">Products</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-12 col-sm-6  col-lg-4 ">
                            <a href="{{ route('showorder') }}" class="menu-link d-block">
                                <div class="dashboard-card card">
                                    <div class="icon-wrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-invoice">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path
                                                d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class=" mb-0 fs-4 fw-bold">{{ $userorderCount }}</h5>
                                        <p class="text-secondary">Orders</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <!-- Add this in your Blade view, inside the container where you want the graph to appear -->

                        <div class="col-xl-7 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        Monthly Orders
                                    </div>
                                    <canvas id="monthlyOrdersChart"></canvas>
                                </div>
                            </div>

                        </div>

                        <div class="col-xl-5 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">Recent Orders
                                    </div>
                                    <table class="w-100 mt-3">
                                        @foreach ($recentOrders as $order)
                                            <tr class="border">
                                                <td class="py-1 px-2 ">
                                                    <h6 class="text-center">
                                                        {{ $order->list_id }}
                                                    </h6>
                                                </td>
                                                <td class="py-1 px-2  w-85 ">
                                                    <h6>
                                                        {{ $order->customer->name }}
                                                    </h6>


                                                    <span class="text-secondary small">
                                                        {{ $order->created_at->format('d , M , Y ') }}
                                                    </span>
                                                </td>

                                                <!-- <td class="customertext fw-bold">$120.00</td> -->

                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card-box  ">
                                <div class="card-datatable table-responsive  rounded-top">
                                    <table class="datatables-projects table " id="orderstabale">

                                        <thead>
                                            <tr>
                                                <th class="customerlist_text  ">
                                                    Customer
                                                </th>

                                                <th class="customerlist_text  ">Email</th>
                                                <!-- <th class="customerlist_text text-white">Status</th> -->
                                                <th class="customerlist_text ">Orders</th>
                                                <!-- <th class="w-px-50 customerlist_text text-white">Estimate</th> -->
                                                <th class="customerlist_text  ">Actions</th> <!-- New column for Actions -->

                                            </tr>
                                        </thead>

                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- section 2 end -->

            <!--/ Projects table -->
        </div>
    </div>

    <!-- Add this in the <head> section of your layout or directly in the Blade view -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable (server-side via Yajra)
            let table = $('#orderstabale').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('home.customers.data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'orders',
                        name: 'orders',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'asc']
                ]
            });

            // Chart.js setup
            const ctx = document.getElementById('monthlyOrdersChart').getContext('2d');
            const monthlyOrdersChart = new Chart(ctx, {
                type: 'bar', // Changed to bar chart
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    datasets: [{
                        label: 'Order Percentage',
                        data: @json(array_values($monthlyDataPercentages)),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100, // Set maximum value of y-axis to 100%
                            ticks: {
                                stepSize: 10, // Control the increments on y-axis
                                callback: function(value) {
                                    return value + '%'; // Append percentage sign
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
