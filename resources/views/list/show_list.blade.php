@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush
@section('content')
<div id="app" class="layout-wrapper">
    @include('include.sidebar') 

    <div class="container">
        @include('include.navbar') 
        <div class="listpadding">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ url()->previous() }}" class="float-left d-flex text-black"><i
                            class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 "></i>Back</a>
                </div>
            </div>

         
            <div class="container mt-5">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left head-label">
                            <h2>View Customer Detail</h2>
                        </div>
                    </div>

                                @if(session('success'))
                <div id="success-message-email" class="alert alert-success">
                    {{ session('success') }}
                </div>

            @endif
                </div>
                
                <div id="success-message" class="alert alert-success d-none" role="alert">
                  Quantity updated successfully!
                </div>

                <div class="card px-3 py-4 table_scroll customer_table_width">
                    <div class="d-flex flex-end ms-auto">
                                <a href="{{ route('customers.edit', $customer->id) }}" 
            class="btn p-0 edit-btn text-info">
                <i class="ti ti-pencil me-1"></i>
            </a>

                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn p-0 delete-btn text-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal" data-form-id="customer-form" data-delete-type="customer">
                                <i class="ti ti-trash me-1"></i>
                            </button>
                        </form>
                    </div>

                    <div class="d-flex">
                        
                        <div class="ms-4 d-flex flex-column justify-content-center w-100">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Customer Name:</div>     
                                <div class="col-sm-8">{{ $customer->name }}</div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Customer ID:</div>
                                <div class="col-sm-8">{{ $customer->id }}</div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Email ID:</div>
                                <div class="col-sm-8">{{ $customer->email }}</div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Phone Number:</div>
                                <div class="col-sm-8">
                                    <a href="tel:{{ $customer->phone }}" class="text-dark">{{ $customer->phone }}</a>
                                </div>

                            </div>
                        </div>

        
                    </div>

                    <div class="row mt-3 customr_btn_centr">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-right text-end">
                            <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}" 
                                class="btn btn-outline-dark text-dark rounded" 
                                tabindex="0" 
                                aria-controls="DataTables_Table_0">
                                    <span><i class="ti ti-plus me-sm-1"></i> Add New Product</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 customr_btn_centr">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-right text-end">
                            <a href="{{ route('send.email', ['list_id' => $list->id, 'customer_id' => $list->customer_id]) }}" class="btn btn-outline-dark text-dark rounded ms-2"><span>
                                        <i class="ti ti-email me-1"></i> Send Selection</span>
                                         </a>
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
                    @foreach($orders as $index => $order)
                        <tr>
                            <td class="border">
                    @if($order->product && $order->product->product_image)
                        <img src="{{ asset('images/products/' . $order->product->product_image) }}" alt="{{ $order->product->product_image }}" width="100">
                    @else
                        No Image
                    @endif
                </td>
                <td class="border">
                @if($order->product)
                    @foreach(explode(',', $order->product->product_category) as $categoryId)
                        {{ $categories[$categoryId] ?? 'Unknown' }}
                        @if(!$loop->last), @endif
                    @endforeach
                @else
                    Unknown
                @endif

            </td>

                <td class="border">{{ $order->product->product_code }}</td>

                <td class="d-flex">
                    
                    <div>
                        <div class="text-dark fs-6 fw-bold text-capitalize">{{ $order->product->product_name ?? 'Unknown Product' }}</div>
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

<script>

    $(document).ready(function () {
        
        let formToSubmit;
        let deleteType;

        // Open the modal and store the form to submit
        $(document).on('click', '.delete-btn', function () {


            // Find the form associated with the button
            formToSubmit = $(this).closest('form');
            deleteType = $(this).data('delete-type');

            // Update modal message
            let modalMessage = deleteType === 'customer' ? 'Are you sure you want to delete this customer?' : 'Are you sure you want to delete this order?';
            $('#deleteModalBody').text(modalMessage);

            // Show the modal
            $('#deleteModal').modal('show');
        });

        // Submit the form when the confirm button is clicked
        $('#confirmDeleteBtn').on('click', function () {

            if (formToSubmit) {
                formToSubmit.submit();
            }
        });

        $('#customerListsTable').DataTable();

        $('.input-touchspin').TouchSpin({

            min: 0,
            max: Infinity,
            step: 1,
            boostat: 5,
            postfix: 'items'
        });

        // Handle plus button click
        $('.bootstrap-touchspin-up').click(function() {

            var input = $(this).closest('.input-group').find('.quantity-input');
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal)) {
                input.val(currentVal + 0);
                updateQuantity(input);

            }
        });

        // Handle minus button click
        $('.bootstrap-touchspin-down').click(function() {

            var input = $(this).closest('.input-group').find('.quantity-input');
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal) && currentVal > 0) {
                input.val(currentVal - 0);
                updateQuantity(input);
                
            }
        });

        function updateQuantity(input) {
            var form = input.closest('form');
            var quantity = input.val();

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#success-message').removeClass('d-none').fadeIn().delay(2000).fadeOut();
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
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

@endsection
