@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush
@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper">

                <div class="flex-grow-1  container-fluid">
                    <div class="listpadding">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url()->previous() }}" class="float-left d-flex text-black"><i
                                        class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 "></i>Back</a>
                            </div>
                        </div>


                        <div class="container mt-5">

                            <div class="page-wrapper-title">
                                <h2>View Customer Detail</h2>



                                @if (session('success'))
                                    <div id="success-message-email" class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                            </div>

                            <div id="success-message" class="alert alert-success d-none" role="alert">
                                Quantity updated successfully!
                            </div>

                            <div class="card px-3 py-4 table_scroll customer_table_width">
                                <div class="d-flex justify-content-end gap-2 ms-auto mb-2 mb-md-0">
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                        class="btn btn-icon btn-sm btn-label-primary waves-effect">
                                        <i class="ti ti-pencil"></i>
                                    </a>

                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                        style="display:inline;">
                                        
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn btn-icon btn-sm btn-label-danger waves-effect delete-btn"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-form-id="customer-form" data-delete-type="customer">
                                            <i class="ti ti-trash me-1"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="d-flex">

                                    <div class=" d-flex flex-column justify-content-center w-100">
                                        <div class="row mb-2">
                                            <div class="col-6 col-sm-4 data-text">Customer Name:</div>
                                            <div class="col-6 col-sm-8">
                                                <h6 class="data-value">{{ $customer->name }} </h6>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-6 col-sm-4 data-text">Customer ID:</div>
                                            <div class="col-6 col-sm-8">
                                                <h6 class="data-value">{{ $customer->id }} </h6>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-6 col-sm-4 data-text">Email ID:</div>
                                            <div class="col-6 col-sm-8"><a href="mailto:{{ $customer->email }}"
                                                    class="data-value">{{ $customer->email }}</a> </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-6 col-sm-4 data-text">Phone Number:</div>
                                            <div class="col-6 col-sm-8">
                                                <a href="tel:{{ $customer->phone }}"
                                                    class="data-value">{{ $customer->phone }}</a>
                                            </div>
                                        </div>
                                    </div>


                                </div>




                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}"
                                        class="btn btn-outline-dark text-dark rounded" tabindex="0"
                                        aria-controls="DataTables_Table_0">
                                        <span><i class="ti ti-plus me-sm-1"></i> Add New Product</span>
                                    </a>
                                    {{-- <a href="{{ route('send.email', ['list_id' => $list->id, 'customer_id' => $list->customer_id]) }}"
                                                class="btn btn-outline-dark text-dark rounded ms-2"><span>
                                                    <i class="ti ti-email me-1"></i> Send Selection</span>
                                            </a> --}}
                                    @if (!empty($list))
                                        <!-- Send Selection Button -->
                                        <button type="button" class="btn btn-outline-dark text-dark rounded ms-2"
                                            data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                                            <i class="ti ti-email me-1"></i> Send Selection
                                        </button>
                                    @endif

                                </div>


                                <!-- Confirm Order (bulk update) -->
                                <div class="row mt-3 customr_btn_centr">
                                    <div class="col-lg-12 margin-tb">
                                        <div class="pull-right text-end">
                                            <form id="bulkUpdateForm" action="{{ route('orders.bulkUpdateQuantities') }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" id="bulkSaveBtn"
                                                    class="btn btn-primary btn-dark me-1 rounded" disabled>Save</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <table id="customerListsTable" class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Product Image</th>
                                            <th>Product Category</th>
                                            <th>Code</th>
                                            <th>Product Name/Qty.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $index => $order)
                                            <tr>
                                                <td class="border">
                                                    @if ($order->product && $order->product->product_image)
                                                        <img src="{{ asset('images/products/' . $order->product->product_image) }}"
                                                            alt="{{ $order->product->product_image }}" width="100">
                                                    @else
                                                        No Image
                                                    @endif
                                                </td>
                                                <td class="border">
                                                    @if ($order->product)
                                                        @foreach (explode(',', $order->product->product_category) as $categoryId)
                                                            {{ $categories[$categoryId] ?? 'Unknown' }}
                                                            @if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        Unknown
                                                    @endif

                                                </td>

                                                <td class="border">{{ $order->product->product_code }}</td>

                                                {{-- <td class="d-flex">

                                                                    <div>
                                                                        <div class="text-dark fs-6 fw-bold text-capitalize">{{ $order->product->product_name ?? 'Unknown Product' }}
                                                                </div>
                                                                <div><strong class="text-secondary fs-8">Property Address:</strong><span class="text-secondary">{{ $list->name }},{{ $list->suburb }},{{ $list->state }},{{ $list->pincod }}</span></div>
                                                                <div>
                                                                    <strong class="text-secondary">Comment :</strong> <span class="text-secondary">{{ $order->comment }}</span>


                                                                    <form action="{{ route('orders.updateQuantity', ['order' => $order->id]) }}" method="POST" class="d-flex qty-update-form">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="quantity" value="{{ $order->quantity }}">
                                                                        <div class="input-group align-items-center">
                                                                            <span class="d-flex align-items-center">
                                                                                <span class="me-3">Qty:</span>
                                                                                <input type="number" name="quantity" value="{{ $order->quantity }}" min="0" required class="form-control input-touchspin text-center border quantity-input">
                                                                            </span>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <form action="{{ route('orders.destroyOrders', ['order' => $order->id]) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn p-0 delete-btn text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-form-id="order-form-{{ $order->id }}" data-delete-type="item">
                                                                        <i class="ti ti-trash me-1"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                </td> --}}

                                                <td class="d-flex flex-column flex-md-row">
                                                    <div class="flex-grow-1" style="word-break: break-word;">
                                                        <!-- Product Name -->
                                                        <div class="text-dark fs-6 fw-bold text-capitalize">
                                                            {{ $order->product->product_name ?? 'Unknown Product' }}
                                                        </div>

                                                        <!-- Property Address -->
                                                        <div class="mt-1">
                                                            <strong class="text-secondary fs-8">Property Address:</strong>
                                                            <span class="text-secondary d-block"
                                                                style="word-break: break-word; white-space: normal;">
                                                                {{ $list->name }}, {{ $list->suburb }},
                                                                {{ $list->state }}, {{ $list->pincod }}
                                                            </span>
                                                        </div>

                                                        <!-- Comment -->
                                                        <div class="mt-2">
                                                            <strong class="text-secondary">Comment:</strong>
                                                            <div class="text-secondary"
                                                                style="word-break: break-word; white-space: normal;">
                                                                {{ $order->comment }}
                                                            </div>
                                                        </div>

                                                        <!-- Quantity -->
                                                        <div class="mt-2">
                                                            <div class="input-group align-items-center">
                                                                <span class="d-flex align-items-center">
                                                                    <span class="me-3 qty-label">Qty:</span>
                                                                    <input type="number"
                                                                        data-order-id="{{ $order->id }}"
                                                                        data-initial="{{ $order->quantity }}"
                                                                        value="{{ $order->quantity }}" min="0"
                                                                        required
                                                                        class="form-control input-touchspin text-center border quantity-input">
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Delete Button -->
                                                    <div
                                                        class="d-flex align-items-start justify-content-end ms-md-3 mt-3 mt-md-0">
                                                        <form
                                                            action="{{ route('orders.destroyOrders', ['order' => $order->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn p-0 delete-btn text-danger"
                                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                data-form-id="order-form-{{ $order->id }}"
                                                                data-delete-type="item">
                                                                <i class="ti ti-trash me-1"></i>
                                                            </button>
                                                        </form>
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

            $('#customerListsTable').DataTable();

            $('.input-touchspin').TouchSpin({
                buttondown_class: 'btn btn-secondary',
                buttonup_class: 'btn btn-secondary',
                min: 0,
                max: Infinity,
                step: 1,
                boostat: 5,
                postfix: 'items'
            });

            // Enable Save button only if there are changes in any quantity
            var $saveBtn = $('#bulkSaveBtn');

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

            // Listen for manual typing and spinner changes
            $(document).on('input change', '.quantity-input', updateSaveButtonState);
            // Initialize state on load
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
