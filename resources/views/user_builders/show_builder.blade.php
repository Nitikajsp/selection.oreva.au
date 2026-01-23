@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush
@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper ">

                <div class="flex-grow-1  container-fluid">

                    <div class="page-header">

                        <a href="{{ url()->previous() }}" class="back-btn"><i
                                class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 "></i>Back</a>


                    </div>
                

                        <div class="inner-container ">
                            <div class="page-wrapper-title">
                                <h2>View Builder Detail</h2>

                            </div>
                            <div class="d-flex justify-content-end gap-2 ms-auto mb-2 mb-md-0">

                                <a href="{{ route('user_builders.edit', $userBuilder->id) }}"
                                    class="btn btn-icon btn-sm btn-label-primary waves-effect">
                                    <i class="ti ti-pencil "></i>
                                </a>


                                <form id="deleteCustomerForm"
                                    action="{{ route('user_builders.destroy', $userBuilder->id) }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button" class="btn btn-icon btn-sm btn-label-danger waves-effect delete-btn"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="ti ti-trash"></i>
                                </button>

                            </div>

                            <div class="d-flex">
                                <div class=" d-flex flex-column justify-content-center w-100">
                                    <div class="row mb-2">
                                        <div class="col-6 col-sm-4 data-text">Customer Name:</div>
                                        <div class="col-6 col-sm-8"><h6 class="data-value">
                                            {{ optional($userBuilder->customer)->name ?? '-' }}</h6>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-6 col-sm-4 data-text">Builder Name:</div>
                                        <div class="col-6 col-sm-8"><h6 class="data-value"> {{ $userBuilder->builder_name }}</h6> </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6 col-sm-4 data-text">Email ID:</div>
                                        <div class="col-6 col-sm-8"><a href="mailto:{{ $userBuilder->contact_email }} " class="data-value">  {{ $userBuilder->contact_email }} </h6> </div>
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // On delete button click, show modal
                    document.querySelector('.delete-btn').addEventListener('click', function() {
                        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                        deleteModal.show();
                    });

                    // On confirm delete button click, submit the form
                    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                        document.getElementById('deleteCustomerForm').submit();
                    });

                });
            </script>


            <script>
                $('#customerListsTable').DataTable();
            </script>
        @endsection
        {{-- @endsection --}}
