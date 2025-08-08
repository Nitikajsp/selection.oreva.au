@extends('layouts.app')

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper pl-30 ">

                <div class="flex-grow-1  container-fluid">


                    <div class="page-header">
                        <h1>All Builders</h1>
                        <a href="{{ route('user_builders.create') }}"
                            class="btn btn-primary create-new waves-effect waves-light btn-dark rounded" tabindex="0"
                            aria-controls="DataTables_Table_0">
                            <span><i class="ti ti-plus me-sm-1"></i> Add Builder</span>
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
                                <table class="table" id="builderlist">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody> <!-- Empty tbody -->
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
                        Are you sure you want to delete this builder?
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
                $(function() {
                    // Datatable load
                    var builderTable = $('#builderlist').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('user_builders.index') }}",
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
                                data: 'builder_name',
                                name: 'builder_name'
                            },
                            {
                                data: 'contact_email',
                                name: 'contact_email'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    // DELETE CONFIRM WITH AJAX
                    let deleteUrl = null;

                    $(document).on('click', '.delete-btn', function(e) {
                        e.preventDefault();
                        deleteUrl = $(this).closest('form').attr('action'); // Get form action URL
                        $('#deleteModal').modal('show');
                    });

                    $('#confirmDeleteBtn').on('click', function() {
                        if (!deleteUrl) return;

                        $.ajax({
                            url: deleteUrl,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                $('#deleteModal').modal('hide');

                                // Remove any old alerts
                                $('.alert').remove();

                                // Show success message
                                $('.page-header').after(
                                    `<div class="alert alert-success mt-2">${response.message}</div>`
                                );

                                // Reload DataTable
                                builderTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                $('#deleteModal').modal('hide');
                                alert('Something went wrong while deleting the builder.');
                            }
                        });
                    });

                    // SET BUTTON AJAX
                    $(document).on('click', '.set-btn', function() {
                        const builderId = $(this).data('builder-id');
                        $('#selectedCustomerId').val(builderId);

                        $.ajax({
                            url: '/get-lists',
                            method: 'GET',
                            data: {
                                customer_id: builderId
                            },
                            success: function(response) {
                                let options = '<option value="" disabled selected>Select...</option>';
                                if (response.length) {
                                    response.forEach(list => {
                                        options +=
                                            `<option value="${list.id}">${list.name}</option>`;
                                    });
                                } else {
                                    options = '<option value="" disabled>No lists available</option>';
                                }
                                $('#dropdownList').html(options);

                                const createUrl = '{{ route('createlist', ':id') }}'.replace(':id',
                                    builderId);
                                $('#createListLink').attr('href', createUrl);

                                $('#setModal').modal('show');
                            }
                        });
                    });

                    // SET FORM SUBMIT
                    $('#setCustomerForm').on('submit', function(e) {
                        e.preventDefault();
                        const listId = $('#dropdownList').val();
                        const builderId = $('#selectedCustomerId').val();

                        if (!listId) {
                            alert('Please select a list.');
                            return;
                        }

                        const url =
                            '{{ route('lists.addcartproduct', ['list' => 'LIST_ID', 'customer' => 'CUSTOMER_ID']) }}'
                            .replace('LIST_ID', listId)
                            .replace('CUSTOMER_ID', builderId);

                        window.location.href = url;
                    });
                });
            </script>
        @endpush
    @endsection
    {{-- @endsection --}}
