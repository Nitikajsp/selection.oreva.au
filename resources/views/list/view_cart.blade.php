@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
    <div id="app" class="layout-wrapper cart-card-page">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper pl-30">
                <div class="flex-grow-1 container-fluid">

                    <div class="page-header">
                        <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}"
                            class="back-btn">
                            <i
                                class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                        </a>
                    </div>

                    <div class="container cart-card-container">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div id="alert-container"></div>

                        @if (count($cartItems) > 0)
                            <div class="cart-card-header">
                                <div>
                                    <span>Selected Products</span>
                                    <h1>Review Cart</h1>
                                    <p>{{ count($cartItems) }} items ready for selection</p>
                                </div>
                                <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}"
                                    class="cart-add-more-btn">
                                    <i class="ti ti-plus"></i>Add More
                                </a>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="cart-items-panel">
                                        @foreach ($cartItems as $index => $item)
                                            <article class="cart-item-card">
                                                <div class="cart-item-image">
                                                    @if ($item['product']->product_image)
                                                        <img src="{{ asset('images/products/' . $item['product']->product_image) }}"
                                                            alt="{{ $item['product']->product_name }}">
                                                    @else
                                                        <span>No Image</span>
                                                    @endif
                                                    <small>{{ $item['product']->product_code }}</small>
                                                </div>

                                                <div class="cart-item-body">
                                                    <h3>{{ $item['product']->product_name }}</h3>
                                                    <form
                                                        action="{{ route('cart.updateqty', ['list' => $list->id, 'productId' => $item['product']->id, 'customerId' => $list->customer_id]) }}"
                                                        method="POST" class="cart-item-update-form qty-update-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] }}">

                                                        <div class="cart-item-qty">
                                                            <span>Qty:</span>
                                                            <input type="number" name="quantity"
                                                                value="{{ $item['quantity'] }}" min="0" required
                                                                class="form-control input-touchspin text-center border quantity-input">
                                                        </div>

                                                        <div class="cart-item-comment">
                                                            <label for="comment_{{ $item['product']->id }}">Comment</label>
                                                            <textarea id="comment_{{ $item['product']->id }}" name="comment"
                                                                class="form-control border comment-input" rows="2">{{ old('comment', $item['comment'] ?? '') }}</textarea>
                                                        </div>

                                                        <button type="button" class="btn btn-primary rounded update-btn">
                                                            Update
                                                        </button>
                                                    </form>
                                                </div>

                                                <form class="delete-form cart-item-delete-form"
                                                    action="{{ route('cart.remove', ['list' => $list->id, 'productId' => $item['product']->id, 'customerId' => $list->customer_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="cart-item-delete-btn delete-btn"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-form-action="{{ route('cart.remove', ['list' => $list->id, 'productId' => $item['product']->id, 'customerId' => $list->customer_id]) }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <form action="{{ route('orders.save') }}" method="POST" enctype="multipart/form-data"
                                        id="orderForm" class="viewcardpad mt-3">
                                        @csrf

                                        <input type="hidden" name="list_id" value="{{ $list->id }}">
                                        <input type="hidden" name="customer_id" value="{{ $list->customer_id }} ">
                                        <input type="hidden" name="list_email" value="{{ $list->contact_email }}">
                                        <input type="hidden" name="customer_email" value="{{ $customer->email }}">

                                        @foreach ($cartItems as $index => $item)
                                            <input type="hidden" name="cart_items[{{ $index }}][product_id]"
                                                value="{{ $item['product']->id }}">
                                            <input type="hidden" name="cart_items[{{ $index }}][product_code]"
                                                value="{{ $item['product']->product_code }}">
                                            <input type="hidden" name="cart_items[{{ $index }}][product_name]"
                                                value="{{ $item['product']->product_name }}">
                                            <input type="hidden" name="cart_items[{{ $index }}][quantity]"
                                                class="quantity-hidden" value="{{ $item['quantity'] }}">
                                            <input type="hidden" name="cart_items[{{ $index }}][product_image]"
                                                value="{{ $item['product']->product_image }}">
                                            <input type="hidden" name="cart_items[{{ $index }}][comment]"
                                                class="comment-hidden" value="{{ $item['comment'] }}">
                                        @endforeach

                                        <input type="hidden" id="actionType" name="action_type" value="save">

                                        <div class="card cart-order-card">
                                            <div class="card-body">
                                                <h5 class="cart-order-title">Order Details</h5>

                                                <div class="mb-3">
                                                    <label for="order-customer-name" class="form-label">Customer name
                                                        (optional)</label>
                                                    <input type="text" name="customer_name" id="order-customer-name"
                                                        class="form-control pos-input" placeholder="Enter customer name">
                                                </div>

                                                <div class="mb-3">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="form-label mb-0">Digital signature (optional)</span>
                                                        <button type="button" class="btn btn-sm btn-link p-0"
                                                            id="clear-signature">Clear</button>
                                                    </div>
                                                    <div class="cart-signature-box">
                                                        <canvas id="signature-pad" height="140"
                                                            style="width: 100%; touch-action: none; cursor: crosshair; display: block;"></canvas>
                                                    </div>
                                                    <input type="hidden" id="signature-data" name="signature">
                                                </div>

                                                <div class="cart-order-actions">
                                                    <button type="submit" class="btn btn-primary btn-dark rounded"
                                                        onclick="setActionType('save')">Save</button>
                                                    <button type="submit" class="btn btn-primary btn-dark rounded"
                                                        onclick="setActionType('save_send')">Save & Send</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="cart-empty-state">
                                <h2>Your cart is empty.</h2>
                                <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}"
                                    class="cart-add-more-btn">
                                    <i class="ti ti-plus"></i>Add Products
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this item from the cart?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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
                    step: 1,
                    max: Infinity,
                    boostat: 5,
                    maxboostedstep: 10,
                    postfix: ' items'
                });

                function displayAlert(message, type) {
                    var alertHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                    $('#alert-container').html(alertHTML);
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 3000);
                }

                $('#orderForm').on('submit', function(e) {
                    e.preventDefault();

                    // Sync latest quantities into hidden inputs
                    $('.quantity-input').each(function(index) {
                        $('.quantity-hidden').eq(index).val($(this).val());
                    });

                    // Sync latest comments into hidden inputs
                    $('.comment-input').each(function(index) {
                        $('.comment-hidden').eq(index).val($(this).val());
                    });

                    this.submit();
                });

                let formToSubmit;
                $(document).on('click', '.delete-btn', function() {
                    formToSubmit = $(this).closest('form');
                    $('#deleteModal').modal('show');
                });

                $('#confirmDeleteBtn').on('click', function() {
                    if (formToSubmit) formToSubmit.submit();
                });

                $('.update-btn').click(function() {
                    var form = $(this).closest('form');
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function() {
                            displayAlert('Quantity and comment updated successfully.', 'success');
                        },
                        error: function() {
                            displayAlert('Failed to update quantity and comment.', 'danger');
                        }
                    });
                });

                const signatureCanvas = document.getElementById('signature-pad');
                if (signatureCanvas) {
                    let signatureCtx = signatureCanvas.getContext('2d');
                    let isDrawing = false;
                    let lastX = 0;
                    let lastY = 0;

                    function initSignatureCanvas() {
                        const width = signatureCanvas.offsetWidth || 300;
                        const heightAttr = signatureCanvas.getAttribute('height');
                        const height = heightAttr ? parseInt(heightAttr, 10) || 140 : 140;
                        signatureCanvas.width = width;
                        signatureCanvas.height = height;
                        signatureCtx = signatureCanvas.getContext('2d');
                        signatureCtx.lineWidth = 2;
                        signatureCtx.lineCap = 'round';
                        signatureCtx.strokeStyle = '#000000';
                        $('#signature-data').val('');
                    }

                    function getSignaturePos(e) {
                        const rect = signatureCanvas.getBoundingClientRect();
                        let clientX;
                        let clientY;

                        if (e.touches && e.touches.length) {
                            clientX = e.touches[0].clientX;
                            clientY = e.touches[0].clientY;
                        } else {
                            clientX = e.clientX;
                            clientY = e.clientY;
                        }

                        return {
                            x: clientX - rect.left,
                            y: clientY - rect.top,
                        };
                    }

                    function updateSignatureData() {
                        try {
                            const dataUrl = signatureCanvas.toDataURL('image/png');
                            $('#signature-data').val(dataUrl);
                        } catch (err) {
                        }
                    }

                    function startSignature(e) {
                        e.preventDefault();
                        isDrawing = true;
                        const pos = getSignaturePos(e);
                        lastX = pos.x;
                        lastY = pos.y;
                    }

                    function drawSignature(e) {
                        if (!isDrawing) {
                            return;
                        }
                        e.preventDefault();
                        const pos = getSignaturePos(e);
                        signatureCtx.beginPath();
                        signatureCtx.moveTo(lastX, lastY);
                        signatureCtx.lineTo(pos.x, pos.y);
                        signatureCtx.stroke();
                        lastX = pos.x;
                        lastY = pos.y;
                    }

                    function endSignature(e) {
                        if (!isDrawing) {
                            return;
                        }
                        e.preventDefault();
                        isDrawing = false;
                        updateSignatureData();
                    }

                    initSignatureCanvas();

                    signatureCanvas.addEventListener('mousedown', startSignature);
                    signatureCanvas.addEventListener('mousemove', drawSignature);
                    signatureCanvas.addEventListener('mouseup', endSignature);
                    signatureCanvas.addEventListener('mouseleave', endSignature);

                    signatureCanvas.addEventListener('touchstart', startSignature, { passive: false });
                    signatureCanvas.addEventListener('touchmove', drawSignature, { passive: false });
                    signatureCanvas.addEventListener('touchend', endSignature, { passive: false });
                    signatureCanvas.addEventListener('touchcancel', endSignature, { passive: false });

                    $('#clear-signature').on('click', function() {
                        signatureCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
                        $('#signature-data').val('');
                    });

                    $(window).on('resize', function() {
                        initSignatureCanvas();
                    });
                }

                window.setActionType = function(action) {
                    document.getElementById('actionType').value = action;
                };
            });
        </script>
    @endpush
