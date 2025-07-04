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
                    <h2>Add New Product</h2>
                </div>
                <div class="pull-left">
                    <h5>Please enter product detail</h5>
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

       <form action="{{ route('addproduct') }}" method="POST" enctype="multipart/form-data" id="addProductForm">
    @csrf

    <div class="row">
        <!-- Product Name -->
        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
            <div class="form-group">
                <p class="text-secondary mb-1">Product Name</p>
                <input type="text" name="product_name" class="form-control border border-white-50" placeholder="Name" value="{{ old('product_name') }}">
            </div>
        </div>

        <!-- Product Image (cannot preserve value) -->
        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
            <div class="form-group">
                <p class="text-secondary mb-1">Product Image</p>
                <input type="file" name="product_image" class="form-control border border-white-50" placeholder="Upload Image" onchange="previewImage(event)">
                <img id="imagePreview" style="display:none; max-width: 100%; height: auto; margin-top: 10px;" />
            </div>
        </div>

        <!-- Product Category (checkboxes will be filled via JS below) -->
        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
            <div class="form-group">
                <p class="text-secondary mb-1">Product Category</p>
                <div id="category-container"></div>
            </div>
        </div>

        <!-- Product Code -->
        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
            <div class="form-group">
                <p class="text-secondary mb-1">Product Code:</p>
                <input type="text" name="product_code" class="form-control border border-white-50" placeholder="Product Code" value="{{ old('product_code') }}">
            </div>
        </div>

        <!-- Product Stock -->
        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
            <div class="form-group">
                <p class="text-secondary mb-1">Product Stock:</p>
                <input type="text" name="product_stock" class="form-control border border-white-50" placeholder="Stock" value="{{ old('product_stock') }}">
            </div>
        </div>

        <!-- Product Description -->
        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
            <div class="form-group">
                <p class="text-secondary mb-1">Product Description:</p>
                <textarea class="form-control border border-white-50" style="height:150px !important;" name="product_description" placeholder="Description">{{ old('product_description') }}</textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="pull-right mt-1 text-center">
            <button type="submit" class="btn btn-primary btn btn-dark me-1 rounded">Save</button>
            <button type="reset" class="btn btn-outline-dark waves-effect rounded" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
        </div>
    </div>
</form>

    </div>
</div>
@endsection

@push('scripts')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script>
        
    function previewImage(event) {

        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';

        };

        reader.readAsDataURL(event.target.files[0]);
    }

    $(document).ready(function () {

        $.validator.addMethod("validPrice", function(value, element) {

            return this.optional(element) || /^\d+(\.\d{1,2})?$/.test(value);
            
        }, "Please enter a valid price.");

        $.validator.addMethod("uniqueProductCode", function(value, element) {
            var isUnique = false;

            $.ajax({

                type: "POST",
                url: "{{ route('checkProductCode') }}",

                data: {

                    product_code: value,
                    _token: "{{ csrf_token() }}"

                },

                async: false,
                success: function(response) {
                    isUnique = !response.exists;

                }
            });

            return isUnique;
        }, "This product code is already used.");

        $("#addProductForm").validate({
            rules: {
                product_name: {
                    required: true,
                    minlength: 3
                },
                product_image: {
                    required: true,
                },
                product_description: {
                    required: true,
                },
                product_category: {
                required: true
            },
                product_code: {
                    required: true,
                    minlength: 3,
                    uniqueProductCode: true
                },
               
                product_stock: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                product_name: {
                    required: "Please enter the product name",
                    minlength: "Product name must consist of at least 3 characters"
                },
                product_image: {
                    required: "Please upload a product image",
                },
                product_description: {
                    required: "Please enter the product description",
                },
                product_category: {
                required: "Please select a product category"
            },
                product_code: {
                    required: "Please enter the product code",
                    minlength: "Product code must consist of at least 3 characters"
                },
              
                product_stock: {
                    required: "Please enter the product stock",
                    digits: "Stock must be a positive number"
                }
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                error.insertBefore(element); // Places the error message above the input field
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).addClass('is-valid').removeClass('is-invalid');
            }
        });

        // Trigger validation when an input field gains focus
        $('#addProductForm input, #addProductForm textarea').on('focus', function() {
            $(this).valid();
        });
    });

    </script>

    <script>

     $(document).ready(function () {

    $.ajax({
        url: "{{ route('getCategories') }}",
        type: 'GET',
        dataType: 'json',
        success: function (categories) {
            var $categoryContainer = $('#category-container');
            $categoryContainer.empty(); // Clear any existing content

            $.each(categories, function (key, category) {
                var checkboxHtml = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="product_category[]" id="category${category.id}" value="${category.id}">
                        <label class="form-check-label" for="category${category.id}">
                            ${category.category_name}
                        </label>
                    </div>
                `;
                $categoryContainer.append(checkboxHtml);
            });
        },
        error: function (xhr, status, error) {
            console.error('Error fetching categories:', error);
        }
    });
});

    </script>

@endpush
