@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.1/jquery.bootstrap-touchspin.min.css">
@endpush

@section('content')
<div id="app" class="layout-wrapper">
  @include('include.sidebar')

  <div class="layout-page">
    <div class="content-wrapper pl-30">
      <div class="flex-grow-1 container-fluid">

        <div class="page-header">
          <h1>All Category</h1>
          <a href="{{ route('addcategory') }}"
            class="btn btn-primary create-new waves-effect waves-light btn-dark rounded"
            tabindex="0" aria-controls="DataTables_Table_0">
            <span><i class="ti ti-plus me-sm-1"></i> Add Category</span>
          </a>
        </div>

        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">

          @if ($message = Session::get('success'))
          <div class="alert alert-success">
            <p>{{ $message }}</p>
          </div>
          @endif

          <div class="card mt-4 p-2">
            <div class="customerscroll">
              <table class="datatables-projects table dataTable" id="categorylist">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach ($categories as $category)
                  <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->created_at->format('d M Y') }}</td>
                    <td class="d-flex justify-content-center align-items-center">
                      <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('editcategory', $category->id) }}"
                          class="btn btn-primary me-md-2 rounded set-btn set-btn-class">
                          Edit
                        </a>
                      </div>
                      <div class="d-inline-block">
                        <a href="javascript:;"
                          class="btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow show text-black"
                          data-bs-toggle="dropdown" aria-expanded="true">
                          <i class="ti ti-dots-vertical ti-md"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end m-0">
                          <a href="{{ route('editcategory', $category->id) }}" class="dropdown-item">
                            <i class="ti ti-pencil me-1"></i> Edit
                          </a>

                          <div class="dropdown-divider"></div>
                          <form action="{{ route('destroycategory', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-danger delete-btn dropdown-item"
                              data-category-id="{{ $category->id }}" data-bs-toggle="modal"
                              data-bs-target="#deleteModal">
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
    $(document).ready(function () {
      $('#categorylist').DataTable({
        order: [[0, 'desc']]
      });

      let categoryIdToDelete;

      $(document).on('click', '.delete-btn', function () {
        categoryIdToDelete = $(this).data('category-id');
        var form = $(this).closest('form');
        $('#confirmDeleteBtn').data('form', form);
      });

      $('#confirmDeleteBtn').on('click', function () {
        var form = $(this).data('form');
        form.submit();
      });
    });
  </script>
  @endpush
@endsection
