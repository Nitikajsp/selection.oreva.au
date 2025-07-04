@extends('layouts.app')

@section('content')
<div id="app" class="layout-wrapper">
    @include('include.sidebar')

    <!-- Including the sidebar partial -->

    <div class="content-wrapper pl-30 ">
        @include('include.navbar')

        <div class="container px-0">
            <div class="row g-3">
                <!-- section 1 start -->
                <div class="col-12 col-sm-6  col-lg-4">
                    <a href="{{ route('customers.index') }}" class="menu-link d-block">
                        <div class="dashboard-card card">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class=" mb-0 fs-4 fw-bold">{{ $customerCount  }}</h5>
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-report">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
                                    <path d="M18 14v4h4" />
                                    <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2" />
                                    <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                    <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M8 11h4" />
                                    <path d="M8 15h3" />
                                </svg>
                            </div>
                            <p class="text-secondary">Customers</p>
                        </div>
                    </a>
                </div>

                <div class="col-12 col-sm-6  col-lg-4">
                    <a href="{{ route('showproduct') }}" class="menu-link d-block">
                        <div class="dashboard-card card">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class=" mb-0 fs-4 fw-bold">{{ $productCount }}</h5>
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-report">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
                                    <path d="M18 14v4h4" />
                                    <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2" />
                                    <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                    <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M8 11h4" />
                                    <path d="M8 15h3" />
                                </svg>
                            </div>
                            <p class="text-secondary">Products</p>
                        </div>
                    </a>
                </div>

                <div class="col-12 col-sm-6  col-lg-4 ">
                    <a href="{{ route('showorder') }}" class="menu-link d-block">
                        <div class="dashboard-card card  ">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class=" mb-0 fs-4 fw-bold">{{$userorderCount}}</h5>
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-invoice">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25" />
                                </svg>
                            </div>
                            <p class="text-secondary">Orders</p>
                        </div>
                    </a>
                </div>


                <!-- Add this in your Blade view, inside the container where you want the graph to appear -->

                <div class="col-xl-7 col-12">
                    <div class="card-box">
                        <h6 class="mb-1 text-center">Monthly Orders</h6>
                        <canvas id="monthlyOrdersChart"></canvas>
                    </div>
                </div>

                <div class="col-xl-5 col-12">
                    <div class=" card-box h-100">
                        <div class="px-3 py-3 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 small f-600 fw-bold">Recent Order</h5>
                        </div>

                        <div class="d-flex flex-column resent-orders gap-3">
                            <div class="recent-card">

                                <!-- <div class="border">
                 <img src="{{ asset('img/layer 1 1.png') }}" width="35px" height="35px" class="bg-light"/><span >Lorem Ipsum</span>
                </div> -->

                                <table class="w-100">
                                    @foreach($recentOrders as $order)

                                    <tr class="border">
                                        <td class="py-1 px-2 text-center">
                                            {{ $order->list_id }}
                                        </td>
                                        <td class="py-1 px-2 customertext fw-bold">{{ $order->customer->name }}<br />{{ $order->created_at->format('d , M , Y ') }}
                                        </td>

                                        <!-- <td class="customertext fw-bold">$120.00</td> -->

                                    </tr>

                                    @endforeach
                                </table>
                            </div>
                        </div>
                        </table>

                    </div>
                </div>
                <div class="col-12">
                    <div class="card-box  ">
                        <div class="card-datatable table-responsive  rounded-top">
                            <table class="datatables-projects table border-top" id="orderstabale">

                                <thead class="">
                                    <tr>
                                        <th class="customerlist_text  text-white ">
                                            Customer
                                        </th>

                                        <th class="customerlist_text text-white ">Email</th>
                                        <!-- <th class="customerlist_text text-white">Status</th> -->
                                        <th class="customerlist_text text-white ">Orders</th>
                                        <!-- <th class="w-px-50 customerlist_text text-white">Estimate</th> -->
                                        <th class="customerlist_text text-white ">Actions</th> <!-- New column for Actions -->

                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($customers as $customer)

                                    <tr>
                                        <td>
                                            <div>
                                                <span>{{ $customer->name }}</span><br>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $customer->email }}
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                {{ $customer->orders->count() }}
                                            </div>
                                        </td>

                                        <td class="d-flex justify-content-center align-items-center">

                                            <div class="d-inline-block">
                                                <a href="javascript:;" class="btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow show text-black" data-bs-toggle="dropdown" aria-expanded="true">
                                                    <i class="ti ti-dots-vertical ti-md"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end m-0">
                                                    <a href="{{ route('customers.edit', $customer->id) }}" class=" dropdown-item">
                                                        <i class="ti ti-pencil me-1"></i> Edit
                                                    </a>
                                                    <a href="{{ route('customers.show', $customer->id) }}" class=" dropdown-item">
                                                        <i class="ti ti-eye me-1"></i> View
                                                    </a>

                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class=" text-danger dropdown-item" onclick="this.closest('form').submit();">
                                                            <i class="ti ti-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- section 2 end -->
        </div>
        <!--/ Projects table -->
    </div>
</div>

<!-- Add this in the <head> section of your layout or directly in the Blade view -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        let table = new DataTable('#orderstabale', {
            order: [
                [0, 'desc']
            ]

        });

        // Chart.js setup
        const ctx = document.getElementById('monthlyOrdersChart').getContext('2d');
        const monthlyOrdersChart = new Chart(ctx, {
            type: 'bar', // Changed to bar chart
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                , datasets: [{
                    label: 'Order Percentage'
                    , data: @json(array_values($monthlyDataPercentages))
                    , backgroundColor: 'rgba(75, 192, 192, 0.2)'
                    , borderColor: 'rgba(75, 192, 192, 1)'
                    , borderWidth: 1
                }]
            }
            , options: {
                responsive: true
                , scales: {
                    y: {
                        beginAtZero: true
                        , max: 100, // Set maximum value of y-axis to 100%
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
