@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset_url('css/custom.css') }}" />
@endpush

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper ">

                <div class="flex-grow-1  container-fluid">
              
                        <div class="page-header">
                            <a href="{{ url()->previous() }}" class="back-btn">
                        <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2"></i>Back
                            <a href="{{ route('showproduct') }}" class="btn btn-primary ">
                                View
                            </a>

                        </div>
             

                        <div class="inner-container">
                            <div class="page-wrapper-title">
                           
                                        <h1>Add Product</h1>
                             
                                        <h6>Please enter product detail</h6>
                                 
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

                            <form action="{{ route('addproduct') }}" method="POST" enctype="multipart/form-data"
                                id="addProductForm">
                                @csrf

                                <div class="row">
                                    <!-- Product Name -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Name <span class="text-danger">*</span></p>
                                            <input type="text" name="product_name"
                                                class="form-control border border-white-50" placeholder="Name"
                                                value="{{ old('product_name') }}">
                                        </div>
                                    </div>

                                    <!-- Product Image (cannot preserve value) -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Image <span class="text-danger">*</span></p>
                                            <input type="file" name="product_image"
                                                class="form-control border border-white-50" placeholder="Upload Image"
                                                onchange="previewImage(event, 'imagePreview')">
                                            <img id="imagePreview"
                                                style="display:none; max-width: 25%; height: auto; margin-top: 10px;" />
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Specification Product Image</p>
                                            <input type="file" name="specification_product_image"
                                                class="form-control border border-white-50" placeholder="Upload Image"
                                                onchange="previewImage(event, 'specificationImagePreview')">
                                            <img id="specificationImagePreview"
                                                style="display:none; max-width: 25%; height: auto; margin-top: 10px;" />
                                        </div>
                                    </div>

                                    <!-- Product Category (checkboxes will be filled via JS below) -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Category <span class="text-danger">*</span></p>
                                            <div id="category-container"></div>
                                        </div>
                                    </div>

                                    <!-- Product Code -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Code: <span class="text-danger">*</span></p>
                                            <input type="text" name="product_code"
                                                class="form-control border border-white-50" placeholder="Product Code"
                                                value="{{ old('product_code') }}">
                                        </div>
                                    </div>

                                    <!-- Product Stock -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Stock: <span class="text-danger">*</span></p>
                                            <input type="text" name="product_stock"
                                                class="form-control border border-white-50" placeholder="Stock"
                                                value="{{ old('product_stock') }}">
                                        </div>
                                    </div>

                                    <!-- Product Description -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Description: <span class="text-danger">*</span></p>
                                            <textarea class="form-control border border-white-50" style="height:150px !important;" name="product_description"
                                                placeholder="Description">{{ old('product_description') }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="pull-right mt-1 text-center">
                                        <button type="submit"
                                            class="btn btn-primary btn btn-dark me-1 rounded">Save</button>
                                        {{-- <button type="reset" class="btn btn-outline-dark waves-effect rounded" data-bs-dismiss="modal" aria-label="Close">Cancel</button> --}}
                                        <a href="{{ url()->previous() }}"
                                            class="btn btn-outline-dark waves-effect rounded">Cancel</a>

                                    </div>
                                </div>
                            </form>

                        </div>
                  
                </div>
            </div>
        @endsection

        @push('scripts')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

            <script>
                function previewImage(event, previewElementId) {

                    var file = event.target.files && event.target.files[0];
                    var output = document.getElementById(previewElementId);

                    if (!file) {
                        if (output) {
                            output.style.display = 'none';
                        }
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function() {
                        if (!output) {
                            return;
                        }

                        output.src = reader.result;
                        output.style.display = 'block';

                    };

                    reader.readAsDataURL(file);
                }

                $(document).ready(function() {

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
                                filesize: 2 * 1024 * 1024
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
                                filesize: "File size must be less than 2MB"
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
                        errorPlacement: function(error, element) {
                            error.addClass('invalid-feedback');
                            error.insertAfter(element); // Places the error message above the input field
                        },
                        highlight: function(element, errorClass, validClass) {
                            $(element).addClass('is-invalid').removeClass('is-valid');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            $(element).removeClass('is-invalid is-valid');
                        },
                        submitHandler: function(form) {
                            $('button[type="submit"]').prop('disabled', true).text('Saving...');
                            form.submit();
                        }

                    });

                    // Trigger validation when an input field gains focus
                    $('#addProductForm input, #addProductForm textarea').on('blur', function() {
                        $(this).valid();
                    });
                });
            </script>

            <script>
                $(document).ready(function() {

                    $.ajax({
                        url: "{{ route('getCategories') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(categories) {
                            var $categoryContainer = $('#category-container');
                            $categoryContainer.empty(); // Clear any existing content

                            $.each(categories, function(key, category) {
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
                        error: function(xhr, status, error) {
                            console.error('Error fetching categories:', error);
                        }
                    });

                    $.validator.addMethod("filesize", function(value, element, param) {
                        if (element.files.length === 0) {
                            return true; // Skip if no file selected (handled by 'required')
                        }
                        return element.files[0].size <= param;
                    }, "File size must be less than 2MB.");
                });
            </script>
        @endpush
