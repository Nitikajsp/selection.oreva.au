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

                    <div class="row mb-3">
                        <div class="col-12 editpadding">
                            <a href="{{ route('home') }}" class="d-flex align-items-center text-dark">
                                <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2"></i> Back
                            </a>
                        </div>
                    </div>

                    <!-- Alert container -->
                    <div id="alert-container"></div>

                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <div class="d-flex justify-content-between productlistcenter">
                            <div class="card-header flex-column flex-md-row">
                                <div class="head-label text-center">
                                    <h2 class="card-title mb-0">Projects Listing Page</h2>
                                </div>
                            </div>
                        </div>

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="mt-3 card p-2 table_scroll">
                            <table id="orderTable" class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Street Name</th>
                                        <th class="order-description-col">Description</th>
                                        <th>Email</th>
                                        <th class="order-action-col text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($list as $lists)
                                        <tr>

                                            <!-- Access the product image using the relationship -->
                                            <td>{{ $lists->name }}</td>

                                            <!-- Access the product image using the relationship -->
                                            <td class="order-description-col">
                                                <span class="description-toggle text-truncate d-block" style="cursor: pointer;"
                                                    data-description="{{ $lists->description }}">
                                                    {{ \Illuminate\Support\Str::limit($lists->description, 80) }}
                                                </span>
                                            </td>

                                            <!-- Access the product name using the relationship -->
                                            <td>{{ $lists->contact_email }}</td>

                                            <td class="order-action-col text-center">

                                                <a href="{{ route('vieworders', $lists->id) }}"
                                                    class="btn px-1 py-0 view-btn me-1 text-secondary">
                                                    <i class="ti ti-eye me-1"></i>
                                                </a>


                                                <button type="button" class="btn p-2 delete-btn text-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $lists->id }}">
                                                    <i class="ti ti-trash me-1"></i>
                                                </button>

                                                <form action="{{ route('lists.destroy', ['id' => $lists->id]) }}"
                                                    method="POST" id="deleteForm{{ $lists->id }}"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
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
        </div>

        <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="descriptionModalLabel">Description</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="descriptionModalBody"></div>
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
                        Are you sure you want to delete this project?
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
                $(document).ready(function() {
                    $('#orderTable').DataTable({
                        order: [
                            [0, 'desc']
                        ],
                        responsive: true,
                        columnDefs: [{
                            targets: -1,
                            orderable: false,
                            className: 'text-center',
                            width: '80px'
                        }]
                    });

                    let deleteForm;

                    $('.delete-btn').on('click', function() {
                        const orderId = $(this).data('id');
                        deleteForm = $(`#deleteForm${orderId}`);
                        $('#deleteModal').modal('show');
                    });

                    $('#confirmDeleteBtn').on('click', function() {
                        if (deleteForm) {
                            deleteForm.submit();
                        }
                    });

                    $(document).on('click', '.description-toggle', function() {
                        const description = $(this).data('description');
                        $('#descriptionModalBody').text(description);
                        $('#descriptionModal').modal('show');
                    });
                });
            </script>
        @endpush
    @endsection
