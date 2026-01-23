@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.1/jquery.bootstrap-touchspin.min.css">
@endpush

@section('content')
    <div id="app" class="layout-wrapper">

        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper pl-30 ">
                <div class="flex-grow-1 container-fluid">

                    <div class="page-header">
                        <a href="{{ route('customers.show', $list->customer_id) }}" class="back-btn">
                            <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                        </a>
                    </div>

                  <div class="page-header">
                                <h1 class="mb-0">Our Product</h1>
                                {{-- ✅ Dynamic Cart Count --}}
                                <form action="{{ route('lists.view-cart', ['list' => $list->id, 'customer_id' => $list->customer_id]) }}" method="post">
                                    @csrf

                                    @php
                                        $cart = session('cart', []);
                                        $customerId = session('customer_id');
                                        $cartCount = 0;

                                        if (isset($cart[$list->id][$customerId])) {
                                            $cartCount = count($cart[$list->id][$customerId]);
                                        }
                                    @endphp

                                    <button type="submit" class="border-0 position-relative" id="view-cart-btn">
                                        <i class="ti ti-shopping-cart ti-md"></i>
                                        <span id="cart-count-badge" class="badge bg-primary">{{ $cartCount }}</span>
                                    </button>
                                </form>
                            </div>
                        

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div id="alert-placeholder"></div>
    <div class="card  p-2">
                            <div class="customerscroll">
                            <table id="product-table" class="table ">
                                <thead >
                                    <tr>
                                        <th class="col-md-2">Product</th>
                                        {{-- <th class="col-md-2">Product Category</th> --}}
                                        <th>Code</th>
                                        <th class="col-md-3">Product Title</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody id="addtocartdatatabal">
                                    @foreach ($products as $product)
                                        <tr>
                                            <td >
                                                @if ($product->product_image)
                                                    <img src="{{ asset('images/products/' . $product->product_image) }}"
                                                        alt="{{ $product->product_name }}" width="70">
                                                @else
                                                    No Image
                                                @endif
                                            </td>
                                            {{-- <td >
                                                @if (isset($product->category_names))
                                                    {{ implode(', ', $product->category_names) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td> --}}
                                            <td >{{ $product->product_code }}</td>
                                            <td >
                                                <div>{{ $product->product_name }}</div>
                                            </td>
                                            <td >
                                                <div class="input-group justify-content-center">
                                                    <span class="d-flex align-items-center">
                                                        <span class="me-1">Qty: </span>
                                                        <input type="number" name="quantity" value="1" min="0"
                                                            required class="form-control input-touchspin text-center"
                                                            data-product-id="{{ $product->id }}">
                                                    </span>
                                                </div>
                                                <textarea name="comment" class="form-control mt-2" rows="2" data-product-id="{{ $product->id }}"
                                                    placeholder="Enter a comment..."></textarea>

                                                <button type="button"  class="btn btn-sm btn-primary d-block add-to-cart mt-2 mx-auto  rounded"
                                                    data-product-id="{{ $product->id }}">Add to Cart</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> </div>
                        </div>
                    </div> <!-- /.container -->
                </div> <!-- /.container-fluid -->
            </div> <!-- /.content-wrapper -->
        </div> <!-- /.layout-page -->
    </div> <!-- /.layout-wrapper -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize TouchSpin
            $('.input-touchspin').TouchSpin({
                min: 0,
                max: Infinity,
                step: 1,
                boostat: 5,
                postfix: 'items'
            });

            // Add to Cart AJAX
            $('.add-to-cart').click(function() {
                var button = $(this);
                var productId = button.data('product-id');
                var inputField = $('input[data-product-id="' + productId + '"]');
                var quantity = parseInt(inputField.val());
                var commentField = $('textarea[data-product-id="' + productId + '"]');
                var comment = commentField.val();

                button.attr('disabled', true);

                $.ajax({
                    url: "{{ route('lists.add-to-cart', ['list' => $list->id, 'customer' => $list->customer_id]) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: productId,
                        quantity: quantity,
                        comment: comment
                    },
                    success: function(response) {
                        var currentCount = parseInt($('#cart-count-badge').text());
                        $('#cart-count-badge').text(currentCount + 1);
                        showAlert('Product added to cart successfully', 'success');
                    },
                    error: function(response) {
                        showAlert('An error occurred while adding the product to the cart', 'danger');
                    },
                    complete: function() {
                        button.attr('disabled', false);
                    }
                });
            });

            // Bootstrap Alert Helper
            function showAlert(message, type) {
                var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';
                $('#alert-placeholder').html(alertHtml);

                setTimeout(function() {
                    $('.alert').alert('close');
                }, 2000);
            }

            $('#product-table').DataTable();
        });
    </script>
@endpush
