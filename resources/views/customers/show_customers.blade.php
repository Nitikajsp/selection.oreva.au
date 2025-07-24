@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush
@section('content')
<div id="app" class="layout-wrapper">
  @include('include.sidebar') 

    <div class="layout-page">
        <div class="content-wrapper pl-30 ">

            <div class="flex-grow-1  container-fluid">

            <div class="page-header">
           
                 <a href="{{ url()->previous() }}" class="back-btn"><i
                    class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 "></i>Back</a>
              

            </div>
<div class="container mt-5">


      <div class="inner-container ">
            <div class="page-wrapper-title" >
                <h2>View Customer Detail</h2>
       
    </div>
        <div class="d-flex justify-content-end gap-2 ms-auto">
    
        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-icon btn-sm btn-label-primary waves-effect">
    <i class="ti ti-pencil "></i>
</a>


            <form id="deleteCustomerForm" action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            <button type="button" class="btn btn-icon btn-sm btn-label-danger waves-effect delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="ti ti-trash"></i>
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
<div class="table-responsive">
        <table id="customerListsTable" class="datatables-projects table dataTable"  style="min-width:600px" >
            <thead >
                <tr>
                    <th>Street Name</th>
                    <th>Description</th>
                    <th>Product Count</th>
                    <th class="text-end" >Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer->lists as $list)
                    <tr class="mt-2">
                        <td >{{ $list->name }}</td>
                        <td >{{ $list->description }}</td>
                        <td  >
                            {{ $list->orders->count() }}
                        </td>
                        <td class="p-2" >
                            <div class="d-flex justify-content-end">
                            <a href="{{ route('lists.edit', $list->id) }}" class="btn btn-icon">
                           <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <a href="{{ route('showlistcustomer', ['listId' => $list->id, 'customerId' => $customer->id]) }}" class="btn btn-icon">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}" class="btn btn-icon">
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
