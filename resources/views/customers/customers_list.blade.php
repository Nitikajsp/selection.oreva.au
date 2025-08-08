@extends('layouts.app')

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper pl-30 ">

                <div class="flex-grow-1  container-fluid">

                    <div class="page-header">
                        <h1>All Customers</h1>
                        <a href="{{ route('customers.create') }}"
                            class="btn btn-primary create-new waves-effect waves-light btn-dark rounded" tabindex="0"
                            aria-controls="DataTables_Table_0">
                            <span><i class="ti ti-plus me-sm-1"></i> Add Customer</span>
                        </a>
                    </div>

                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">


                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="card mt-4 p-2 ">
                            <div class="customerscroll">
                                <table class="datatables-projects table" id="customerlist">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer Name</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
                $(document).ready(function() {
                    let customerIdToDelete;

                    // DataTable
                    var table = $('#customerlist').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('customers.index') }}",
                        order: [
                            [0, 'desc']
                        ],
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'email',
                                name: 'email'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    // Delete button click
                    $(document).on('click', '.delete-btn', function() {
                        var form = $(this).closest('form');
                        $('#confirmDeleteBtn').data('form', form);
                        $('#deleteModal').modal('show');
                    });

                    // Confirm delete
                    $('#confirmDeleteBtn').on('click', function() {
                        var form = $(this).data('form');
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: form.serialize(),
                            success: function() {
                                $('#deleteModal').modal('hide');
                                table.ajax.reload(null, false);
                            }
                        });
                    });

                    // Set button click
                    $(document).on('click', '.set-btn', function() {
                        var customerId = $(this).data('customer-id');
                        $('#selectedCustomerId').val(customerId);

                        $.ajax({
                            url: '/get-lists',
                            method: 'GET',
                            data: {
                                customer_id: customerId
                            },
                            success: function(response) {
                                var options = '<option value="" disabled selected>Select...</option>';
                                if (response.length > 0) {
                                    response.forEach(function(list) {
                                        options +=
                                            `<option value="${list.id}">${list.name}</option>`;
                                    });
                                } else {
                                    options = '<option value="" disabled>No lists available</option>';
                                }
                                $('#dropdownList').html(options);

                                var createUrl =
                                    '{{ route('createlist', ['customer_id' => ':customer_id']) }}'
                                    .replace(':customer_id', customerId);
                                $('#createListLink').attr('href', createUrl);

                                $('#setModal').modal('show');
                            }
                        });
                    });

                    // Set form submit
                    $('#setCustomerForm').on('submit', function(event) {
                        event.preventDefault();
                        var customerId = $('#selectedCustomerId').val();
                        var selectedOption = $('#dropdownList').val();

                        if (!selectedOption) {
                            alert('Please select a list.');
                            return;
                        }

                        var url =
                            '{{ route('lists.addcartproduct', ['list' => 'LIST_ID', 'customer' => 'CUSTOMER_ID']) }}'
                            .replace('LIST_ID', selectedOption)
                            .replace('CUSTOMER_ID', customerId);

                        window.location.href = url;
                    });
                });
            </script>
        @endpush
    @endsection
