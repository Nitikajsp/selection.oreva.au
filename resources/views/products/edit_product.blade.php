@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper ">
                <div class="flex-grow-1 container-fluid">

                    <div class="page-header">
                        <a href="{{ url()->previous() }}" class="back-btn">
                            <i
                                class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                        </a>
                        <a href="{{ route('showproduct') }}" class="btn btn-primary btn-dark rounded">View</a>
                        {{-- <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-dark rounded">
                            View
                        </a> --}}
                    </div>

                        <div class="inner-container">
                            <div class="page-wrapper-title">
                                <h1>Edit Product</h1>
                                <h6 >Please enter product detail</h6>
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

                            <form action="{{ route('products.update', $product->id) }}" method="POST"
                                enctype="multipart/form-data" id="editProductForm">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Name</p>
                                            <input type="text" name="product_name" value="{{ $product->product_name }}"
                                                class="form-control border border-white-50" placeholder="Name">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Existing Product Image</p>
                                            <img src="{{ asset('images/products/' . $product->product_image) }}"
                                                alt="Product Image" class="img-fluid" width="150">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">New Product Image</p>
                                            <input type="file" name="product_image"
                                                class="form-control border border-white-50" id="productImageInput">
                                            <img id="imagePreview" src="#" alt="New Product Image"
                                                class="img-fluid mt-3" style="display: none;" width="150">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 mb-3">
                                        <p class="text-secondary mb-1">Product Categories</p>
                                        @foreach ($categories as $category)
                                            <div class="form-check">
                                                <input type="checkbox" name="product_category[]" value="{{ $category->id }}"
                                                    class="form-check-input" id="category{{ $category->id }}"
                                                    @if (!empty($product->product_category) && in_array($category->id, explode(',', $product->product_category))) checked @endif>
                                                <label class="form-check-label" for="category{{ $category->id }}">
                                                    {{ $category->category_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="col-sm-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Code</p>
                                            <input type="text" name="product_code" value="{{ $product->product_code }}"
                                                class="form-control border border-white-50" placeholder="Code">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Stock</p>
                                            <input type="text" name="product_stock"
                                                value="{{ $product->product_stock }}"
                                                class="form-control border border-white-50" placeholder="Stock">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 mb-3">
                                        <div class="form-group">
                                            <p class="text-secondary mb-1">Product Description</p>
                                            <textarea class="form-control border border-white-50" name="product_description" style="height: 150px;"
                                                placeholder="Description">{{ $product->product_description }}</textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center gap-2 mt-2">
                                        <button type="submit" class="btn btn-primary btn-dark rounded">Save</button>
                                        <a class="btn btn-outline-dark waves-effect rounded"
                                            href="{{ route('showproduct') }}">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $.validator.addMethod("validPrice", function(value, element) {
                return this.optional(element) || /^\d+(\.\d{1,2})?$/.test(value);
            }, "Please enter a valid price.");

            $("#editProductForm").validate({
                rules: {
                    product_name: {
                        required: true,
                        minlength: 3
                    },
                    product_image: {
                        accept: "image/*"
                    },
                    product_code: {
                        required: true,
                        minlength: 3
                    },
                    product_description: {
                        required: true
                    },
                    product_stock: {
                        required: true,
                        digits: true
                    },
                    product_category: {
                        required: true
                    }
                },
                messages: {
                    product_name: {
                        required: "Please enter the product name",
                        minlength: "Product name must consist of at least 3 characters"
                    },
                    product_image: {
                        accept: "Please upload a valid image file"
                    },
                    product_code: {
                        required: "Please enter the product code",
                        minlength: "Product code must consist of at least 3 characters"
                    },
                    product_description: {
                        required: "Please enter the product description"
                    },
                    product_stock: {
                        required: "Please enter the product stock",
                        digits: "Stock must be a positive number"
                    },
                    product_category: {
                        required: "Please select at least one category"
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.after(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                }
            });

            $('#editProductForm input, #editProductForm textarea').on('focus', function() {
                $(this).valid();
            });

            $('#productImageInput').on('change', function() {
                const [file] = this.files;
                if (file) {
                    $('#imagePreview').attr('src', URL.createObjectURL(file)).show();
                } else {
                    $('#imagePreview').hide();
                }
            });
        });
    </script>
@endpush
