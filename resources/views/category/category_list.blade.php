@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.1/jquery.bootstrap-touchspin.min.css">
@endpush

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper pl-30">
                <div class="flex-grow-1 container-fluid">

                    <div class="page-header">
                        <h1>All Category</h1>
                        <a href="{{ route('addcategory') }}" class="btn btn-primary" tabindex="0">
                            <span><i class="ti ti-plus me-sm-1"></i> Add Category</span>
                        </a>
                    </div>
                    <div class="container">


                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="card p-2">
                            <!-- <div class="customerscroll"> -->
                                <table class="table" id="categorylist">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Category Name</th>
                                            <th>Created At</th>
                                            <th >Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody> {{-- Empty tbody --}}
                                </table>
                            <!-- </div> -->
                        </div>
</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this category?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(function() {
                    var categoryTable = $('#categorylist').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('showcategory') }}",
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
                                data: 'category_name',
                                name: 'category_name'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    let deleteUrl = null;
                    $(document).on('click', '.delete-btn', function(e) {
                        e.preventDefault();
                        deleteUrl = $(this).closest('form').attr('action');
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
                                $('.alert').remove();
                                $('.page-header').after(
                                    `<div class="alert alert-success mt-2">${response.message}</div>`
                                    );
                                categoryTable.ajax.reload(null, false);
                            },
                            error: function() {
                                $('#deleteModal').modal('hide');
                                alert('Something went wrong while deleting the category.');
                            }
                        });
                    });
                });
            </script>
        @endpush
    @endsection
