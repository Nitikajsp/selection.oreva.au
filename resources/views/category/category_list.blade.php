@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.1/jquery.bootstrap-touchspin.min.css">
@endpush
@section('content')
<div id="app" class="layout-wrapper">
    @include('include.sidebar')

 <div class="container addcartwidth">
        @include('include.navbar')


        <div class="row">
        <div class="col-md-12 d-flex justify-content-between align-items-center mt-3 p-5">
            <a href="{{ route('home') }}" class="float-left d-flex text-black">
                <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
            </a>
        </div>
    </div>

        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label text-center">
                    <h2 class="card-title mb-0">All Category</h2>
                </div>
                <div class="dt-action-buttons text-end pt-6 pt-md-0">
                    <div class="dt-buttons flex-wrap">
                        <a href="{{ route('addcategory') }}" class="btn btn-primary create-new waves-effect waves-light btn-dark rounded" tabindex="0" aria-controls="DataTables_Table_0">
                            <span><i class="ti ti-plus me-sm-1"></i> Add Category</span>
                        </a>
                    </div>
                </div>
            </div>

            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif
            

            <div class="card mt-4 p-2">
                <div class="customerscroll">
                    <table class="table datatables-projects" id="categorylist">
                        <thead class="table-dark">
                            <tr>
                                <th >ID</th>
                                <th >Category Name</th>
                                <th >Created At</th>
                                <!-- <th>Updated At</th> -->
                                <th >Action</th>
                            </tr>
                        </thead>

                        <tbody class="table-border-bottom-0">
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->category_name }}</td>
                                    <td>{{ $category->created_at->format('d M Y') }}</td>
                                    <!-- <td>{{ $category->updated_at->format('Y-m-d H:i:s') }}</td> -->
                                    <td class="d-flex justify-content-start align-items-center">
                                    <a href="{{ route('editcategory', $category->id) }}" class="btn p-0 edit-btn dropdown-item">
                                        <i class="ti ti-pencil me-1"></i>
                                    </a>

                             <form action="{{ route('destroycategory', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn p-0 delete-btn text-danger dropdown-item" data-category-id="{{ $category->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="ti ti-trash me-1"></i>
                            </button>
                        </form>
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
            $(document).ready(function () {
                $('#categorylist').DataTable({
                    order: [[0, 'desc']]
                });

                let categoryIdToDelete;

                // Store the form to submit on confirmation
                $(document).on('click', '.delete-btn', function () {
                    categoryIdToDelete = $(this).data('category-id');
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
    @endpush
@endsection
