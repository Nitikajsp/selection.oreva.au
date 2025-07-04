@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
<div id="app" class="layout-wrapper">
  @include('include.sidebar')

  <div class="container-customerlist">
    @include('include.navbar')
    <div class="row mb-3">
      <div class="col-12 editpadding">
        <a href="{{ route('home') }}" class="d-flex align-items-center text-dark">
            <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2"></i> Back 
        </a>
      </div>
    </div>
    
    <!-- Alert container -->
    <div id="alert-container"></div>
    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
      <div class="d-flex justify-content-between productlistcenter">
        <div class="card-header flex-column flex-md-row">
          <div class="head-label text-center">
            <h2 class="card-title mb-0">Products Listing Page</h2>
          </div>
        </div>
        <div class="d-flex justify-content-between">
          <div class="ms-auto displaycenter">
            <a href="{{ route('products.create') }}" 
              class="btn btn-primary create-new waves-effect waves-light btn-dark rounded" 
              tabindex="0" 
              aria-controls="DataTables_Table_0">
              <span><i class="ti ti-plus me-sm-1"></i> Add Product</span>
            </a>
          </div>
        </div>
      </div>

      @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
      @endif

      <div class="mt-3 card p-2 table_scroll">
        <table id="productTable" class="table table-bordered">
          <thead class="table-dark">
            <tr>
              <th>Product Image</th>
              <th>Product Category</th>
              <th>Product Name</th>
              <th>Product Code</th>
              <th>Qty</th>
              <th>Stock</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($products as $product)
              <tr>
                <td><img src="{{ asset('images/products/' . $product->product_image) }}" alt="{{ $product->product_name }}" width="100"></td>
                <td>
                  @if (isset($product->category_names))
                    {{ implode(', ', $product->category_names) }}
                  @else
                    N/A
                  @endif
                </td>       
                <td>{{ $product->product_name }}</td>
                <td >{{ $product->product_code }}</td>
                <td>{{ $product->product_stock }}</td>
                
                <td>
                  <div class="form-check form-switch">
                    <input class="form-check-input stock-toggle on-off-setbutton" type="checkbox" role="switch" id="stockSwitch{{ $product->id }}" data-id="{{ $product->id }}" {{ $product->in_stock ? 'checked' : '' }}>
                    <label class="form-check-label" for="stockSwitch{{ $product->id }}"></label>
                  </div>
                </td>

                <td class="d-flex justify-content-center align-items-center">
                 
                  <div class="d-inline-block">
                    <a href="javascript:;" class="btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow show text-black" data-bs-toggle="dropdown" aria-expanded="true">
                      <i class="ti ti-dots-vertical ti-md"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end m-0">
                      <a href="{{ route('products.edit', $product->id) }}" class="btn p-0 edit-btn dropdown-item">
                        <i class="ti ti-pencil me-1"></i> Edit
                      </a>
                      <a href="{{ route('products.show', $product->id) }}" class="btn p-0 view-btn dropdown-item">
                        <i class="ti ti-eye me-1"></i> View
                      </a>

                      <div class="dropdown-divider"></div>
                      <form id="deleteForm{{ $product->id }}" action="{{ route('products.destroy', $product->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn p-0 delete-btn text-danger dropdown-item" data-id="{{ $product->id }}">
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

  <!-- Delete Confirmation Modal -->

  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this product?
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

$(document).ready(function() {

    // Initialize DataTable
    let table = new DataTable('#productTable', {
        order: [[0, 'desc']]
    });

    // Use event delegation for handling the toggle switch change
    $('#productTable').on('change', '.stock-toggle', function() {
        const productId = $(this).data('id');
        const inStock = $(this).is(':checked');
        
        $.ajax({
            url: '{{ route('products.updateStock') }}', 
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: productId,
                in_stock: inStock ? 1 : 0 
            },
            success: function(response) {
                const alertType = response.success ? 'success' : 'danger';
                const message = response.success ? 'Stock status updated successfully!' : 'Failed to update stock status.';
                
                // Append Bootstrap alert message
                $('#alert-container').html(`
                    <div class="alert alert-${alertType} alert-dismissible fade show" role="alert">
                        <strong>${message}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                
                if (!response.success) {
                    // Optionally, revert the toggle state if update fails
                    $(this).prop('checked', !inStock);
                }
            }.bind(this)
        });
    });

    // Handle delete button click with event delegation
    let deleteForm; // Variable to hold the form reference
    $('#productTable').on('click', '.delete-btn', function() {
        const productId = $(this).data('id');
        deleteForm = $(`#deleteForm${productId}`); // Assign the correct form to deleteForm
        $('#deleteModal').modal('show');
    });

    // Confirm delete action
    $('#confirmDeleteBtn').on('click', function() {
        if (deleteForm) {
            deleteForm.submit(); // Submit the form
        }
    });
});


</script>

@endpush
