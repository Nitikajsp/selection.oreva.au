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
    </div>

    <div class="card px-3 py-4 table_scroll customer_table_width">
        <div class="d-flex flex-end ms-auto">
        <a href="{{ route('customers.edit', $customer->id) }}" class="btn p-0 edit-btn text-info">
    <i class="ti ti-pencil me-1"></i>
</a>


            <form id="deleteCustomerForm" action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            <button type="button" class="btn p-0 delete-btn text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="ti ti-trash me-1"></i>
                </button>

        </div>

        <div class="d-flex">
            <div class=" d-flex flex-column justify-content-center w-100">
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
                <a href="{{ route('createlist', ['customer_id' => $customer->id]) }}" class="btn btn-outline-dark text-dark rounded" tabindex="0" aria-controls="DataTables_Table_0">
              <span><i class="ti ti-plus me-sm-1"></i> Create Project</span>
            </a>
                </div>
            </div>
        </div>

        <table id="customerListsTable" class="table table-bordered mt-3 show_custmer " style="border: 1px solid #DDDDDD; border-spacing: 0 10px;">
            <thead class="table-dark">
                <tr>
                    <th>Street Name</th>
                    <th>Description</th>
                    <th>Product Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer->lists as $list)
                    <tr class="mt-2">
                        <td style="border: 1px solid #DDDDDD !important">{{ $list->name }}</td>
                        <td style="border: 1px solid #DDDDDD !important;">{{ $list->description }}</td>
                        <td  style="border: 1px solid #DDDDDD !important;">
                            {{ $list->orders->count() }}
                        </td>
                        <td class="p-2" style="border: 1px solid #DDDDDD !important;">
                            <div class="d-flex justify-content-between">
                            <a href="{{ route('lists.edit', $list->id) }}" class="btn p-2 edit-btn text-dark me-1 show-customer-btn">
                           <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <a href="{{ route('showlistcustomer', ['listId' => $list->id, 'customerId' => $customer->id]) }}" class="btn p-2 view-btn text-dark me-1 show-customer-btn">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}" class="btn p-2 view-btn text-dark show-customer-btn">
                                                <span><i class="fa-solid fa-plus"></i></span>
                                       </a>

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

<script>

$(document).ready(function () {
  let formToSubmit;

  // Open the modal and store the form to submit
  $(document).on('click', '.delete-btn', function () {
    // Find the form associated with the button
    formToSubmit = $(this).siblings('form');
    // Show the modal
    $('#deleteModal').modal('show');
  });

  // Submit the form when the confirm button is clicked
  $('#confirmDeleteBtn').on('click', function () {
    if (formToSubmit) {
      formToSubmit.submit();
    }
  });
});


</script>

<script>

        $('#customerListsTable').DataTable();

</script>
@endsection
