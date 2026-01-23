@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset_url('css/custom.css') }}" />
@endpush
@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')
        <div class="layout-page">
            <div class="content-wrapper">

                <div class="flex-grow-1  container-fluid">
                 
                    <div class="page-header">
                            <a href="{{ url()->previous() }}"  class="back-btn">
<i  class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                        </a>         
                                           <a href="{{ route('showproduct') }}" class="btn btn-primary ">
                                View
                            </a>

                  
                    </div>

                  
                        <div class="inner-container">
                           
                                <div class="page-wrapper-title">
                                 
                                        <h1>Add Category</h1>
                                        <h6>Please enter category detail</h6>
                                   
                                </div>
                          

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('categorystore') }}" method="POST" enctype="multipart/form-data"
                                id="addcategory">
                                @csrf

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Category Name</p>
                                            <input type="text" name="category_name"
                                                class="form-control border border-white-50" placeholder="Name">
                                        </div>
                                    </div>

                                    <div class="pull-right mt-1 text-center">
                                        <button type="submit"
                                            class="btn btn-primary btn btn-dark me-1 rounded">Save</button>
                                        <button type="reset" class="btn btn-outline-dark waves-effect rounded"
                                            data-bs-dismiss="modal" aria-label="Close">Cancel</button>

                                    </div>
                                </div>
                            </form>
                        </div>
                  
                </div>
            </div>
        @endsection
        @push('scripts')
            <script>
                $(document).ready(function() {
                    $('#addcategory').validate({
                        rules: {
                            category_name: {
                                required: true,
                                minlength: 3
                            }
                        },
                        messages: {
                            category_name: {
                                required: "Please enter a category name",
                                minlength: "Category name must be at least 3 characters long"
                            }
                        },
                        errorElement: 'div',
                        errorPlacement: function(error, element) {
                            error.addClass('invalid-feedback');
                            error.insertAfter(element);
                        },
                        highlight: function(element, errorClass, validClass) {
                            $(element).addClass('is-invalid').removeClass('is-valid');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            $(element).removeClass('is-invalid').addClass('is-valid');
                        },
                        submitHandler: function(form) {
                            $('button[type="submit"]').prop('disabled', true).text('Saving...');
                            form.submit();
                        }
                    });
                });
            </script>
        @endpush
