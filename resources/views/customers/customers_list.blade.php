@extends('layouts.app')

@section('content')
<div id="app" class="layout-wrapper">
  @include('include.sidebar')

  <div class="container-customerlist">

  @include('include.navbar')

    <div class="row mb-3">
      <div class="col-12">
        
        <!-- <a href="{{ route('home') }}">
          <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2"></i> Back
        </a> -->
        
      </div>  
    </div>

    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
      <div class="card-header flex-column flex-md-row">
        <div class="head-label text-center">
          <h2 class="card-title mb-0">All Customers</h2>
        </div>
        <div class="dt-action-buttons text-end pt-6 pt-md-0">
          <div class="dt-buttons flex-wrap">
          <a href="{{ route('customers.create') }}" class="btn btn-primary create-new waves-effect waves-light btn-dark rounded" 
   tabindex="0" aria-controls="DataTables_Table_0">
    <span><i class="ti ti-plus me-sm-1"></i> Add Customer</span>
</a>

          </div>
        </div>
      </div>

      @if ($message = Session::get('success'))
        <div class="alert alert-success">
          <p>{{ $message }}</p>
        </div>
      @endif

      <div class="card mt-4 p-2 ">
        <div class="customerscroll">
        <table class="table datatables-projects" id="customerlist">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Customer Name</th>
              <th>Email</th>
              <!-- <th>Status</th> -->
              <th>Action</th>
            </tr>
          </thead>

          <tbody class="table-border-bottom-0">

            @foreach ($customers as $customer)
            
              <tr>

                <td >{{ $customer->id }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <!-- <td>{{ $customer->status }}</td> -->

                <td class="d-flex justify-content-center align-items-center">
                  <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary me-md-2 rounded set-btn set-btn-class" data-customer-id="{{ $customer->id }}" type="button">Set</button>
                  </div>
                  <div class="d-inline-block">
                    <a href="javascript:;" class="btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow show text-black" data-bs-toggle="dropdown" aria-expanded="true">
                      <i class="ti ti-dots-vertical ti-md"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end m-0">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn p-0 edit-btn dropdown-item">
                      <i class="ti ti-pencil me-1"></i> Edit
                  </a>
                  <a href="{{ route('customers.show', $customer->id) }}" class="btn p-0 view-btn dropdown-item">
                      <i class="ti ti-eye me-1"></i> View
                  </a>

                      <div class="dropdown-divider"></div>
                      <form id="deleteCustomerForm" action="{{ route('customers.destroy', $customer->id) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn p-0 delete-btn text-danger dropdown-item" data-customer-id="{{ $customer->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
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
        Are you sure you want to delete this customer?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>


  <div class="modal fade" id="setModal" tabindex="-1" aria-labelledby="setModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="setModalLabel"></h5>
        <a href="#" id="createListLink" class="ms-auto">Create Project</a>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="setCustomerForm">
          <div class="mb-3">
            <label for="dropdownList" class="form-label">Select Project</label>
            <select id="dropdownList" class="form-select" aria-label="Select an Option">
              <option value="" disabled selected>Select...</option>
            </select>
          </div>
          <input type="hidden" id="selectedCustomerId" name="customer_id" />
          <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary rounded" id="selectButton">Select</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


  @push('scripts')


            <script>

          $(document).ready(function () {

            let customerIdToDelete;

            // Store the form to submit on confirmation
            $(document).on('click', '.delete-btn', function () {

              customerIdToDelete = $(this).data('customer-id');

              var form = $(this).closest('form');

              $('#confirmDeleteBtn').data('form', form);

            });

            // Submit the form when the confirm button is clicked
            $('#confirmDeleteBtn').on('click', function () {

              var form = $(this).data('form');

              form.submit();

            });
            
          });

          </script>

            
    <script>

      $(document).ready(function () {
        $('#customerlist').DataTable({
          order: [[0, 'desc']]
        });

        // Handle "Set" button click
        $(document).on('click', '.set-btn', function () {
          var customerId = $(this).data('customer-id');
          $('#selectedCustomerId').val(customerId);

          // Fetch the list options from the server
          $.ajax({
            url: '/get-lists',
            method: 'GET',
            data: { customer_id: customerId },
            success: function (response) {
              // Populate dropdown with options or show a message if no lists are available
              var options = '<option value="" disabled selected required>Select...</option>';
              if (response.length > 0) {
                response.forEach(function (list) {
                  options += `<option value="${list.id}">${list.name}</option>`;
                });
                $('#dropdownList').html(options);
              } else {
                $('#dropdownList').html('<option value="" disabled>No lists available</option>');
              }
              // Set the create list link
              var createUrl = '{{ route('createlist', ['customer_id' => ':customer_id']) }}';
              createUrl = createUrl.replace(':customer_id', customerId);
              $('#createListLink').attr('href', createUrl);

              $('#setModal').modal('show');
            },
            error: function (xhr, status, error) {
              console.error('Error fetching lists:', error);
            }
          });
        });

        // Handle form submission
        $('#setCustomerForm').on('submit', function (event) {
          event.preventDefault();
          var customerId = $('#selectedCustomerId').val();
          var selectedOption = $('#dropdownList').val();

          if (!selectedOption) {
            alert('Please select a list.');
            return;
          }

          // Construct the URL
          var url = '{{ route('lists.addcartproduct', ['list' => 'LIST_ID', 'customer' => 'CUSTOMER_ID']) }}';
          url = url.replace('LIST_ID', selectedOption).replace('CUSTOMER_ID', customerId);

          // Redirect to the constructed URL
          window.location.href = url;
          
        });
      });
      
    </script>

  @endpush
@endsection
