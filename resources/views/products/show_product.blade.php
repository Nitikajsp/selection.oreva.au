@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
<div id="app" class="layout-wrapper">
    @include('include.sidebar')

    <div class="layout-page">
        <div class="content-wrapper pl-30">
            <div class="flex-grow-1 container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <a href="{{ url()->previous() }}" class="back-btn">
                        <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2"></i>Back
                    </a>
                </div>

                <div class="container mt-5">
                    <div class="inner-container">
                        <!-- Title -->
                        <div class="page-wrapper-title">
                            <h2>View Product Detail</h2>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 ms-auto mb-3">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-icon btn-sm btn-label-primary waves-effect">
                                <i class="ti ti-pencil"></i>
                            </a>

                            <form id="deleteForm" action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button type="button" class="btn btn-icon btn-sm btn-label-danger waves-effect delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>

                        <!-- Product Detail Section -->
                        <div class="d-flex">
                            <div class="d-flex flex-column justify-content-center w-100">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Product Name:</div>
                                    <div class="col-sm-8">{{ $product->product_name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Product Code:</div>
                                    <div class="col-sm-8">{{ $product->product_code }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Product Description:</div>
                                    <div class="col-sm-8">{{ $product->product_description }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Product Stock:</div>
                                    <div class="col-sm-8">{{ $product->product_stock }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /.container-fluid -->
        </div> <!-- /.content-wrapper -->
    </div> <!-- /.layout-page -->
</div> <!-- /.layout-wrapper -->

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

@push('scripts')
<script>
    $(document).ready(function () {
        let formToSubmit;

        $(document).on('click', '.delete-btn', function () {
            formToSubmit = $('#deleteForm');
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function () {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    });
</script>
@endpush

@endsection
