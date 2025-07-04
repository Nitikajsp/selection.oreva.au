@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
<div id="app" class="layout-wrapper">
    @include('include.sidebar')

    <div class="container">
        @include('include.navbar')
        
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center p-5">
                <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}" class="float-left d-flex text-black">
                    <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                </a>
            </div>
        </div>

        <div class="container mt-3 viewcardpad viewresponsivecard">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div id="alert-container"></div> <!-- JavaScript alerts container -->

            @if(count($cartItems) > 0)
                <div class="card p-2 table_scrl custmrmt0">
                    <table id="cartTable" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>Code</th>
                                <th>Product Name/Qty.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $index => $item)
                                <tr>
                                    <td class="border">
                                        @if($item['product']->product_image)
                                            <img src="{{ asset('images/products/' . $item['product']->product_image) }}"
                                                alt="{{ $item['product']->product_name }}" width="100">
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td class="border">{{ $item['product']->product_code }}</td>
                                    <td class="d-flex">
                                        <div>
                                            <div class="text-dark fs-5 fw-bold text-capitalize">{{ $item['product']->product_name }}</div>
                                            <!-- <div><strong class="text-secondary">Property Address:</strong><span class="text-secondary">{{ $list->name }},{{ $list->suburb }},{{ $list->state }},{{ $list->pincod }}</span></div> -->
                                            <div>
                                            <form action="{{ route('cart.updateqty', ['list' => $list->id, 'productId' => $item['product']->id, 'customerId' => $list->customer_id]) }}"
                                                    method="POST" class="d-flex flex-column qty-update-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] }}">
                                                    <div class="input-group align-items-center">
                                                        <span class="d-flex align-items-center">
                                                            <span class="me-3">Qty:</span>
                                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" required class="form-control input-touchspin text-center border quantity-input">
                                                        </span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label for="comment_{{ $item['product']->id }}">Comment:</label>
                                                        <textarea name="comment" class="form-control border comment-input" rows="2">{{ old('comment', $item['comment'] ?? '') }}</textarea>
                                                        <button type="button" class="btn btn-primary rounded btn-sm mt-2 update-btn">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        
                                                
                                            </div>
                                         <!-- Comment field -->
                                      
                                        <div class="d-flex ms-auto">
                                        <form class="delete-form" action="{{ route('cart.remove', ['list' => $list->id, 'productId' => $item['product']->id, 'customerId' => $list->customer_id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn p-0 delete-btn text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-form-action="{{ route('cart.remove', ['list' => $list->id, 'productId' => $item['product']->id, 'customerId' => $list->customer_id]) }}">
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

            <form action="{{ route('orders.save') }}" method="POST" enctype="multipart/form-data" id="orderForm" class="viewcardpad">
    @csrf

    <input type="hidden" name="list_id" value="{{ $list->id }}">
    <input type="hidden" name="customer_id" value="{{ $list->customer_id }}">
    <input type="hidden" name="list_email" value="{{ $list->contact_email }}">
    <input type="hidden" name="customer_email" value="{{ $customer->email }}">

    @foreach($cartItems as $index => $item)
        <input type="hidden" name="cart_items[{{ $index }}][product_id]" value="{{ $item['product']->id }}">
        <input type="hidden" name="cart_items[{{ $index }}][product_code]" value="{{ $item['product']->product_code }}">
        <input type="hidden" name="cart_items[{{ $index }}][product_name]" value="{{ $item['product']->product_name }}">
        <input type="hidden" name="cart_items[{{ $index }}][quantity]" class="quantity-hidden" value="{{ $item['quantity'] }}"> 
        <input type="hidden" name="cart_items[{{ $index }}][product_image]" value="{{ $item['product']->product_image }}">
        <input type="hidden" name="cart_items[{{ $index }}][comment]" class="comment-hidden" value="{{ $item['comment'] }}">

    @endforeach

    <input type="hidden" id="actionType" name="action_type" value="save"> <!-- Hidden input to track the action -->

    <div class="pull-right mt-4">
        <button type="submit" class="btn btn-primary btn-dark me-1 rounded" onclick="setActionType('save')">Save</button>   
        <button type="submit" class="btn btn-primary btn-dark me-1 rounded spacebtwn" onclick="setActionType('save_send')">Save & Send</button>
    </div>
</form>



        @else
            <p>Your cart is empty.</p>
        @endif

    </div>

    <!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this item from the cart?
      </div>
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
        $(document).ready(function () {
            // Initialize TouchSpin
            $('.input-touchspin').TouchSpin({
                min: 0,
                step: 1,
                max: Infinity,
                boostat: 5,
                maxboostedstep: 10,
                postfix: ' items'
            });

            // Initialize DataTable
            let table = new DataTable('#cartTable');
        
            // Display alert message
            function displayAlert(message, type) {
                var alertHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';
                $('#alert-container').html(alertHTML);

                setTimeout(function () {
                    $('.alert').alert('close');
                }, 3000);
            }

            // Handle form submission to ensure quantity inputs are correctly updated
            $('#orderForm').on('submit', function (e) {
                e.preventDefault();

                // Update hidden inputs with current quantity values
                $('.quantity-input').each(function (index) {
                    var quantity = $(this).val();
                    $('.quantity-hidden').eq(index).val(quantity);
                });

                // Submit the form
                this.submit();
            });

            let formToSubmit;

            // Open the modal and store the form to submit
            $(document).on('click', '.delete-btn', function () {
                formToSubmit = $(this).closest('form');
                $('#deleteModal').modal('show');
            });

            // Submit the form when the confirm button is clicked
            $('#confirmDeleteBtn').on('click', function () {
                if (formToSubmit) {
                    formToSubmit.submit();
                }
            });

            // Handle Update button action
            $('.update-btn').click(function () {
                var form = $(this).closest('form');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        displayAlert('Quantity and comment updated successfully.', 'success');
                    },
                    error: function (xhr, status, error) {
                        displayAlert('Failed to update quantity and comment.', 'danger');
                        console.error('Failed to update:', error);
                    }
                });
            });

            // Function to set action type
            window.setActionType = function(action) {
                document.getElementById('actionType').value = action;
            };
        });
    </script>
@endpush



