@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset_url('css/custom.css') }}" />
@endpush
@section('content')
<div id="app" class="layout-wrapper">
  @include('include.sidebar') 

<div class="container">
    @include('include.navbar') 
    <div class="row">
        <div class="col-md-12 d-flex justify-content-between align-items-center editpadding">
            <a href="{{ url()->previous() }}" class="float-left d-flex text-black"><i
                    class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back</a>
            <a href="{{ route('showproduct') }}" class="btn btn-primary btn-dark float-end rounded">
                View
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="inner-container">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Edit Category</h2>
                    </div>
                    <div class="pull-left">
                        <h5>Please update category detail</h5>
                    </div>
                </div>
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

            <form action="{{ route('updatecategory', $category->id) }}" method="POST" id="editcategory">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                        <div class="form-group">
                            <p class="text-secondary mb-1">Category Name</p>
                            <input type="text" name="category_name" class="form-control border border-white-50" 
                                   placeholder="Name" value="{{ old('category_name', $category->category_name) }}" required>
                        </div>
                    </div>

                    <div class="pull-right mt-1 text-center">
                        <button type="submit" class="btn btn-primary btn btn-dark me-1 rounded">Update</button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-dark waves-effect rounded">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#editcategory').validate({
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
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    error.insertAfter(element);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                }
            });
        });
    </script>
@endpush
