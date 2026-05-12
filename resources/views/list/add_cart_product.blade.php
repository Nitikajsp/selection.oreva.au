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
                            <p><span id="available-products-count">{{ method_exists($products, 'total') ? $products->total() : $products->count() }}</span> available products</p>
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
                            <form method="GET" action="{{ url()->current() }}" class="add-product-search" id="product-card-search-form">
                                <i class="ti ti-search"></i>
                                <input type="search" id="product-card-search" name="search"
                                    value="{{ $search ?? '' }}" placeholder="Search product name or code">
                                <a href="{{ url()->current() }}" class="{{ empty($search) ? 'd-none' : '' }}"
                                    id="product-card-search-clear" aria-label="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                            </form>
                        </div>

                        <div id="product-card-results">
                            @include('list.partials.add_cart_products')
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function initTouchSpin() {
                $('.input-touchspin').each(function() {
                    if (!$(this).parent().hasClass('bootstrap-touchspin')) {
                        $(this).TouchSpin({
                            min: 0,
                            max: Infinity,
                            step: 1,
                            boostat: 5,
                            postfix: 'items'
                        });
                    }
                });
            }

            initTouchSpin();

            $(document).on('click', '.add-to-cart', function() {
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

            var searchTimer;
            var $searchForm = $('#product-card-search-form');
            var $searchInput = $('#product-card-search');
            var $searchClear = $('#product-card-search-clear');
            var pendingRequest = null;
            var activeRequestId = 0;

            function loadProducts(url, pushState) {
                var requestUrl = url || new URL($searchForm.attr('action'), window.location.origin).toString();
                var requestId = ++activeRequestId;

                if (pendingRequest) {
                    pendingRequest.abort();
                }

                $('#product-card-results').addClass('is-loading');

                pendingRequest = $.ajax({
                    url: requestUrl,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (requestId !== activeRequestId) {
                            return;
                        }

                        $('#product-card-results').html(response.html);
                        $('#available-products-count').text(response.total || 0);
                        initTouchSpin();

                        if (pushState) {
                            window.history.pushState({}, '', requestUrl);
                        }
                    },
                    error: function(xhr) {
                        if (requestId !== activeRequestId) {
                            return;
                        }

                        if (xhr.statusText !== 'abort') {
                            showAlert('Failed to load products.', 'danger');
                        }
                    },
                    complete: function() {
                        if (requestId !== activeRequestId) {
                            return;
                        }

                        $('#product-card-results').removeClass('is-loading');
                        pendingRequest = null;
                    }
                });
            }

            function submitProductSearch() {
                var search = $searchInput.val().trim();
                var url = new URL($searchForm.attr('action'), window.location.origin);

                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }

                url.searchParams.delete('page');
                loadProducts(url.toString(), true);
            }

            $searchInput.on('input', function() {
                $searchClear.toggleClass('d-none', !$searchInput.val().trim());
                clearTimeout(searchTimer);
                searchTimer = setTimeout(submitProductSearch, 450);
            });

            $searchForm.on('submit', function(event) {
                event.preventDefault();
                clearTimeout(searchTimer);
                submitProductSearch();
            });

            $(document).on('click', '#product-card-results .add-product-pagination-btn[href]', function(event) {
                event.preventDefault();
                clearTimeout(searchTimer);

                var url = new URL(this.href);
                var search = $searchInput.val().trim();

                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }

                loadProducts(url.toString(), true);
            });

            $searchClear.on('click', function(event) {
                event.preventDefault();
                $searchInput.val('');
                $searchClear.addClass('d-none');
                submitProductSearch();
            });

            window.addEventListener('popstate', function() {
                var url = new URL(window.location.href);
                $searchInput.val(url.searchParams.get('search') || '');
                $searchClear.toggleClass('d-none', !$searchInput.val().trim());
                loadProducts(url.toString(), false);
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
