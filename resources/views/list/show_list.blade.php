@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" />
@endpush
@section('content')
    @php
        $itemCount = method_exists($orders, 'total') ? $orders->total() : $orders->count();
        $projectAddress = trim(collect([$list->name, $list->suburb, $list->state, $list->pincod])->filter()->implode(', '));
    @endphp

    <div id="app" class="layout-wrapper selection-card-page">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper">

                <div class="flex-grow-1 container-fluid">
                    <div class="listpadding">
                        <a href="{{ url()->previous() }}" class="selection-page-back">
                            <i class="ti ti-arrow-narrow-left"></i>Back
                        </a>

                        @if (session('success'))
                            <div id="success-message-email" class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div id="success-message" class="alert alert-success d-none mt-3" role="alert">
                            Quantity updated successfully!
                        </div>

                        <div class="container mt-5 selection-card-container">
                            <div class="page-wrapper-title">
                                <h2>View Project Detail</h2>
                            </div>

                            <div class="selection-summary-card customer_table_width">
                                <div class="selection-summary-icon">
                                    <img src="{{ asset('images/oreva_pdf_logo.png') }}" alt="Oreva">
                                </div>
                                <div class="selection-summary-copy">
                                    <h3>{{ $list->name }}</h3>
                                    <div class="selection-summary-tags">
                                        
                                        <span>Customer Name: {{ $customer->name }}</span>
                                    </div>
                                </div>
                                <div class="selection-summary-count">
                                    <span>Items</span>
                                    <strong>{{ $itemCount }}</strong>
                                </div>
                                {{-- <a href="{{ route('customers.edit', $customer->id) }}" class="selection-summary-edit"
                                    aria-label="Edit customer">
                                    <i class="ti ti-pencil"></i>
                                </a> --}}
                            </div>

                            <section class="selection-product-card-panel">
                                <div class="selection-product-card-head">
                                    <h3>Products</h3>
                                    <div class="selection-product-actions">
                                        <form method="GET" action="{{ url()->current() }}" class="selection-product-search">
                                            <i class="ti ti-search"></i>
                                            <input type="search" name="search" value="{{ $search ?? '' }}"
                                                placeholder="Search product name or code">
                                            <a href="{{ url()->current() }}" class="selection-search-clear {{ empty($search) ? 'd-none' : '' }}"
                                                aria-label="Clear search">
                                                <i class="ti ti-x"></i>
                                            </a>
                                        </form>
                                    <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}"
                                            class="btn btn-outline-dark text-dark rounded selection-purple-outline">
                                            <i class="ti ti-plus me-sm-1"></i> Add New Product
                                    </a>
                                        @if (!empty($list))
                                            <button type="button" class="btn btn-outline-dark text-dark rounded selection-purple-outline"
                                                data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                                                <i class="ti ti-email me-1"></i> Send Selection
                                            </button>
                                        @endif
                                        <form id="bulkUpdateForm" action="{{ route('orders.bulkUpdateQuantities') }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" id="bulkSaveBtn" class="btn btn-primary btn-dark rounded selection-save-purple" disabled>
                                                Save
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div id="selection-products-results">
                                    @include('list.partials.selection_products')
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    Are you sure you want to delete this order?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sendEmailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4 rounded-3 shadow-lg">

                <!-- Close button -->
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>

                <!-- Title -->
                <h4 class="fw-bold mb-2">Send Selection</h4>
                <p class="text-muted mb-4">Enter customer email to send selection</p>

                <!-- Form -->
                <form method="POST"
                    action="{{ route('send.email', ['list_id' => $list->id, 'customer_id' => $list->customer_id]) }}"
                    onsubmit="showLoader(this)">
                    @csrf
                    <div class="mb-3">
                        <input type="email" name="customer_email" id="customer_email"
                            class="form-control border border-white-50 text-center" placeholder="Your email"
                            value="{{ $customer->email ?? '' }}" required>
                    </div>
                    {{-- <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Send Email</button> --}}
                    <button type="submit" id="sendEmailBtn"
                        class="btn btn-primary w-100 py-2 fs-5 d-flex justify-content-center align-items-center">
                        <span id="btnText">Send Email</span>
                        <span class="spinner-border spinner-border-sm ms-2 d-none" id="btnLoader"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {

            let formToSubmit;
            let deleteType;

            // Open the modal and store the form to submit
            $(document).on('click', '.delete-btn', function() {


                // Find the form associated with the button
                formToSubmit = $(this).closest('form');
                deleteType = $(this).data('delete-type');

                // Update modal message
                let modalMessage = deleteType === 'customer' ?
                    'Are you sure you want to delete this customer?' :
                    'Are you sure you want to delete this order?';
                $('#deleteModalBody').text(modalMessage);

                // Show the modal
                $('#deleteModal').modal('show');
            });

            // Submit the form when the confirm button is clicked
            $('#confirmDeleteBtn').on('click', function() {

                if (formToSubmit) {
                    formToSubmit.submit();
                }
            });

            // Enable Save button only if there are changes in any quantity
            var $saveBtn = $('#bulkSaveBtn');

            function initQuantityControls() {
                $('.input-touchspin').not('.selection-touchspin-ready').each(function() {
                    $(this).addClass('selection-touchspin-ready').TouchSpin({
                        buttondown_class: 'btn btn-secondary',
                        buttonup_class: 'btn btn-secondary',
                        min: 0,
                        max: Infinity,
                        step: 1,
                        boostat: 5,
                        postfix: 'items'
                    });
                });
            }

            function updateSaveButtonState() {
                var changed = false;
                $('.quantity-input').each(function() {
                    var initial = parseInt($(this).data('initial'));
                    var current = parseInt($(this).val());
                    if (!isNaN(initial) && !isNaN(current) && initial !== current) {
                        changed = true;
                        return false; // break
                    }
                });
                $saveBtn.prop('disabled', !changed);
            }

            function loadSelectionProducts(url) {
                var $results = $('#selection-products-results');
                $results.addClass('selection-results-loading');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $results.html(response.html || '');
                        if (typeof response.total !== 'undefined') {
                            $('.selection-summary-count strong').text(response.total);
                        }
                        initQuantityControls();
                        updateSaveButtonState();
                        window.history.replaceState({}, '', url);
                    },
                    complete: function() {
                        $results.removeClass('selection-results-loading');
                    }
                });
            }

            var searchTimer;
            var $searchForm = $('.selection-product-search');
            var $searchInput = $searchForm.find('input[name="search"]');
            var $searchClear = $searchForm.find('.selection-search-clear');

            function updateSearchClear() {
                $searchClear.toggleClass('d-none', !$searchInput.val().trim());
            }

            function buildSearchUrl() {
                var url = new URL($searchForm.attr('action'), window.location.origin);
                var search = $searchInput.val().trim();

                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }

                url.searchParams.delete('page');
                return url.toString();
            }

            $searchInput.on('input', function() {
                updateSearchClear();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadSelectionProducts(buildSearchUrl());
                }, 350);
            });

            $searchForm.on('submit', function(event) {
                event.preventDefault();
                clearTimeout(searchTimer);
                loadSelectionProducts(buildSearchUrl());
            });

            $(document).on('click', '.selection-product-search a', function(event) {
                event.preventDefault();
                $searchInput.val('');
                updateSearchClear();
                loadSelectionProducts(buildSearchUrl());
            });

            $(document).on('click', '#selection-products-results .selection-pagination-btn[href]', function(event) {
                event.preventDefault();
                loadSelectionProducts(this.href);
            });

            // Listen for manual typing and spinner changes
            $(document).on('input change', '.quantity-input', updateSaveButtonState);
            // Initialize state on load
            initQuantityControls();
            updateSearchClear();
            updateSaveButtonState();

            // Build payload on Confirm Order submit
            $('#bulkUpdateForm').on('submit', function() {
                var $form = $(this);
                // remove previously added dynamic fields
                $form.find('.dynamic-field').remove();

                $('.quantity-input').each(function() {
                    var orderId = $(this).data('order-id');
                    var qty = $(this).val();
                    $('<input>', {
                        type: 'hidden',
                        name: 'orders[' + orderId + '][quantity]',
                        value: qty,
                        class: 'dynamic-field'
                    }).appendTo($form);
                });
                // allow form to submit normally
                // Also disable Save button to prevent double submit
                $saveBtn.prop('disabled', true);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                var successMessage = document.getElementById('success-message-email');
                if (successMessage) {
                    successMessage.style.display = 'none';
                }
            }, 3000);
        });
    </script>

    <script>
        function showLoader(form) {
            const btn = form.querySelector("#sendEmailBtn");
            const loader = form.querySelector("#btnLoader");
            const text = form.querySelector("#btnText");

            btn.disabled = false;
            loader.classList.remove("d-none"); // show loader
            text.textContent = "Sending...";
        }
    </script>
@endsection
