@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}"/>
@endpush

@section('content')
<div id="app" class="layout-wrapper">
    @include('include.sidebar') 

    <div class="container">
        @include('include.navbar') 
        <div class="listpadding">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ url()->previous() }}" class="float-left d-flex text-black">
                        <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2"></i>Back
                    </a>
                </div>
            </div>

            <div class="container mt-5">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left head-label">
                            <h2>View Orders</h2>
                        </div>
                    </div>
                </div>

               
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="mt-3 card p-2 table_scroll">
            <table id="orderTable" class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                   
                        <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Product Order Image</th>
                                    <th>Customer Email</th>
                    </tr>
                </thead>
                <tbody>

                @foreach ($orders  as $order)
                <tr>
            <td>{{ $order->product->product_name }}</td>
            <td>{{ $order->quantity }}</td>
            <td>
                <img src="{{ asset('images/products/' . $order->product->product_image) }}" alt="{{ $order->product->product_name }}" width="100">
            </td>
            <td>{{ $order->customer->email }}</td>
        </tr>
        @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  

@push('scripts')

<script>

    $(document).ready(function() {
        $('#orderTable').DataTable();
    });

</script>

@endpush

@endsection
