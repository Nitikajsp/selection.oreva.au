@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')
        <div class="layout-page">
            <div class="content-wrapper pl-30 ">

                <div class="flex-grow-1  container-fluid">


                    <div class="page-header flex-wrap gap-2">
                        <h1 class="mb-0">Product</h1>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('products.create') }}"
                                class="btn btn-primary btn-dark rounded d-flex align-items-center">
                                <i class="ti ti-plus me-1"></i>
                                <span>Add Product</span>
                            </a>

                            <a href="#" class="btn btn-primary btn-dark rounded d-flex align-items-center"
                                data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="ti ti-upload me-1"></i>
                                <span>Import Products</span>
                            </a>



                            <a href="{{ route('products.sample.download') }}"
                                class="btn btn-sm btn-light border rounded d-flex align-items-center px-2">
                                <i class="bi bi-arrow-down-circle-fill text-dark fs-5"></i>

                            </a>


                        </div>
                    </div>


                    <!-- Alert container -->
                    <div id="alert-container"></div>
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">


                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class=" card p-2 table_scroll">

                            <table id="productTable" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Product Info</th>
                                        <th>Qty</th>
                                        <th>Stock</th>
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

        <!-- Import Products Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <form id="importForm" action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-content shadow-lg border-0 rounded-3">

                        <!-- Header -->
                        <div class="modal-header" style="background-color: #630660;">
                            <h5 class="modal-title fw-semibold text-white" id="importModalLabel">
                                <i class="ti ti-upload me-2"></i> Import Products
                            </h5>
                            <button type="button" class="btn btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-4">
                                <label for="import_file" class="form-label fw-medium">Choose CSV or Excel File</label>
                                <input class="form-control" type="file" id="import_file" name="import_file" required
                                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                <div class="form-text mt-1">Supported formats: .csv, .xls, .xlsx</div>
                            </div>
                        </div>

                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i> Cancel
                            </button>
                            {{-- <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i> Import
                            </button> --}}

                            <button type="submit" class="btn btn-success d-flex align-items-center justify-content-center"
                                id="importSubmitBtn">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"
                                    id="importSpinner"></span>
                                <span id="importBtnText"><i class="ti ti-check me-1"></i> Import</span>
                            </button>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(function() {
                let table = $('#productTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('showproduct') }}",
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
                            data: 'product_image',
                            name: 'product_image',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'product_info',
                            name: 'product_name',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'product_stock',
                            name: 'product_stock'
                        },
                        {
                            data: 'stock',
                            name: 'stock',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // Track ID for deletion
                let deleteId = null;

                // When delete button is clicked, open modal
                $('#productTable').on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    deleteId = $(this).data('id');
                    $('#deleteModal').modal('show');
                });

                // When confirm delete button is clicked in modal
                $('#confirmDeleteBtn').on('click', function() {
                    if (!deleteId) return;

                    $.ajax({
                        url: `/products/${deleteId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            $('#deleteModal').modal('hide');
                            if (res.success) {
                                showAlert('success', 'Product deleted successfully.');
                                table.ajax.reload(null, false);
                            } else {
                                showAlert('danger', 'Error deleting product.');
                            }
                        },
                        error: function() {
                            $('#deleteModal').modal('hide');
                            showAlert('danger', 'Error deleting product.');
                        }
                    });
                });

                // Stock toggle AJAX
                $('#productTable').on('change', '.stock-toggle', function() {
                    const productId = $(this).data('id');
                    const inStock = $(this).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: "{{ route('products.updateStock') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: productId,
                            in_stock: inStock
                        },
                        success: function(res) {
                            if (res.success) {
                                showAlert('success', 'Stock status updated successfully.');
                            } else {
                                showAlert('danger', 'Error updating stock.');
                            }
                        },
                        error: function() {
                            showAlert('danger', 'Error updating stock.');
                        }
                    });
                });

                // Alert function
                function showAlert(type, message) {
                    const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
                    $('#alert-container').html(alertHtml);
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 3000);
                }
            });
        </script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const importModal = document.getElementById('importModal');
                const importForm = document.getElementById('importForm');
                const importBtn = document.getElementById('importSubmitBtn');
                const importSpinner = document.getElementById('importSpinner');
                const importBtnText = document.getElementById('importBtnText');

                // Reset form on modal hide (X button or Cancel)
                importModal.addEventListener('hidden.bs.modal', function() {
                    importForm.reset();
                    // Reset button state
                    importSpinner.classList.add('d-none');
                    importBtnText.innerHTML = `<i class="ti ti-check me-1"></i> Import`;
                    importBtn.disabled = false;
                });

                // Show loader on submit
                importForm.addEventListener('submit', function() {
                    importSpinner.classList.remove('d-none');
                    importBtnText.innerHTML = 'Importing...';
                    importBtn.disabled = true; // Disable button
                });
            });
        </script>
    @endpush
