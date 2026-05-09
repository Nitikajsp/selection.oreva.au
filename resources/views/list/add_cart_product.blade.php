@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.1/jquery.bootstrap-touchspin.min.css">
@endpush

@section('content')
    @php
        $cart = session('cart', []);
        $customerId = session('customer_id');
        $cartCount = 0;

        if (isset($cart[$list->id][$customerId])) {
            $cartCount = count($cart[$list->id][$customerId]);
        }
    @endphp

    <div id="app" class="layout-wrapper add-product-card-page">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper pl-30">
                <div class="flex-grow-1 container-fluid">
                    <div class="page-header add-product-back-row">
                        <a href="{{ route('customers.show', $list->customer_id) }}" class="back-btn">
                            <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                        </a>
                    </div>

                    <div class="add-product-header-card">
                        <div>
                            {{-- <span>Product Selection</span> --}}
                            <h1>Product Selection</h1>
                            <p>{{ method_exists($products, 'total') ? $products->total() : $products->count() }} available products</p>
                        </div>
                        <form action="{{ route('lists.view-cart', ['list' => $list->id, 'customer_id' => $list->customer_id]) }}"
                            method="post">
                            @csrf
                            <button type="submit" class="add-product-cart-btn" id="view-cart-btn">
                                <i class="ti ti-shopping-cart"></i>
                                <span id="cart-count-badge">{{ $cartCount }}</span>
                            </button>
                        </form>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div id="alert-placeholder"></div>

                    <section class="add-product-panel">
                        <div class="add-product-panel-head">
                            <h2><span></span>Products</h2>
                            <div class="add-product-search">
                                <i class="ti ti-search"></i>
                                <input type="search" id="product-card-search" placeholder="Search product name or code">
                            </div>
                        </div>

                        <div class="add-product-card-grid" id="product-card-grid">
                            @foreach ($products as $product)
                                <article class="add-product-card"
                                    data-search="{{ strtolower($product->product_name . ' ' . $product->product_code) }}">
                                    <div class="add-product-image">
                                        @if ($product->product_image)
                                            <img src="{{ asset('images/products/' . $product->product_image) }}"
                                                alt="{{ $product->product_name }}">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                        <small>{{ $product->product_code }}</small>
                                    </div>

                                    <div class="add-product-info">
                                        <div class="add-product-category">
                                            @if (isset($product->category_names))
                                                {{ implode(', ', $product->category_names) }}
                                            @else
                                                Product
                                            @endif
                                        </div>
                                        <h3>{{ $product->product_name }}</h3>

                                        <div class="add-product-controls">
                                            <div class="add-product-qty">
                                                <span>Qty:</span>
                                                <input type="number" name="quantity" value="1" min="0" required
                                                    class="form-control input-touchspin text-center"
                                                    data-product-id="{{ $product->id }}">
                                            </div>
                                            <textarea name="comment" class="form-control" rows="2" data-product-id="{{ $product->id }}"
                                                placeholder="Enter a comment..."></textarea>
                                            <button type="button" class="btn btn-primary add-to-cart rounded"
                                                data-product-id="{{ $product->id }}">
                                                <i class="ti ti-shopping-cart-plus me-1"></i>Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="add-product-empty d-none" id="product-card-empty">No products found.</div>

                        @if (method_exists($products, 'links') && $products->hasPages())
                            <div class="add-product-pagination">
                                <div class="add-product-pagination-summary">
                                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                                </div>
                                <nav class="add-product-pagination-nav" aria-label="Product pagination">
                                    @if ($products->onFirstPage())
                                        <span class="add-product-pagination-btn disabled">Previous</span>
                                    @else
                                        <a class="add-product-pagination-btn" href="{{ $products->previousPageUrl() }}">Previous</a>
                                    @endif

                                    @php
                                        $currentPage = $products->currentPage();
                                        $lastPage = $products->lastPage();
                                        $pages = collect([1, 2, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage - 1, $lastPage])
                                            ->filter(fn ($page) => $page >= 1 && $page <= $lastPage)
                                            ->unique()
                                            ->sort()
                                            ->values();
                                        $previousPage = 0;
                                    @endphp

                                    @foreach ($pages as $page)
                                        @if ($previousPage && $page > $previousPage + 1)
                                            <span class="add-product-pagination-ellipsis">...</span>
                                        @endif

                                        @if ($page === $currentPage)
                                            <span class="add-product-pagination-btn active">{{ $page }}</span>
                                        @else
                                            <a class="add-product-pagination-btn" href="{{ $products->url($page) }}">{{ $page }}</a>
                                        @endif

                                        @php $previousPage = $page; @endphp
                                    @endforeach

                                    @if ($products->hasMorePages())
                                        <a class="add-product-pagination-btn" href="{{ $products->nextPageUrl() }}">Next</a>
                                    @else
                                        <span class="add-product-pagination-btn disabled">Next</span>
                                    @endif
                                </nav>
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.input-touchspin').TouchSpin({
                min: 0,
                max: Infinity,
                step: 1,
                boostat: 5,
                postfix: 'items'
            });

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
                    success: function() {
                        var currentCount = parseInt($('#cart-count-badge').text()) || 0;
                        $('#cart-count-badge').text(currentCount + 1);
                        showAlert('Product added to cart successfully', 'success');
                    },
                    error: function() {
                        showAlert('An error occurred while adding the product to the cart', 'danger');
                    },
                    complete: function() {
                        button.attr('disabled', false);
                    }
                });
            });

            $('#product-card-search').on('input', function() {
                var search = $(this).val().trim().toLowerCase();
                var visibleCount = 0;

                $('.add-product-card').each(function() {
                    var matched = $(this).data('search').indexOf(search) !== -1;
                    $(this).toggle(matched);
                    if (matched) {
                        visibleCount++;
                    }
                });

                $('#product-card-empty').toggleClass('d-none', visibleCount !== 0);
            });

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
        });
    </script>
@endpush
