@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
<div id="app" class="layout-wrapper">
    @include('include.sidebar')

    <div class="container">
        @include('include.navbar')
        
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center editpadding">
                <a href="{{ route('customers.index') }}" class="float-left d-flex text-black">
                    <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                </a>
                <a href="{{ route('customers.show', $customer->id) }}" 
   class="btn btn-primary btn-dark float-end rounded">
    View
</a>

            </div>
        </div>
        
        <div class="container mt-5">
            <div class="inner-container custmrmt0">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Edit Customer</h2>
                        </div>
                        <div class="pull-left">
                            <h5>Please update the details below</h5>
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

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('customers.update', $customer->id) }}" method="POST" id="editCustomerForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">Name</p>
                                <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-control border border-white-50" placeholder="Name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">Phone</p>
                                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control border border-white-50" placeholder="Phone">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">E-mail Address</p>
                                <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control border border-white-50" placeholder="Email">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">Property Address</p>
                                <input type="text" name="street" value="{{ old('street', $customer->street) }}" class="form-control border border-white-50" placeholder="Street">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <!-- <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">House Number</p>
                                <input type="text" name="house_number" value="{{ old('house_number', $customer->house_number) }}" class="form-control border border-white-50" placeholder="House Number">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div> -->
                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">Suburb</p>
                                <input type="text" name="suburb" value="{{ old('suburb', $customer->suburb) }}" class="form-control border border-white-50" placeholder="Suburb">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">

                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">State</p>
                                <select name="state" class="form-control border border-white-50">
                                    <option value="New South Wales (NSW)" {{ old('state', $customer->state) == 'New South Wales (NSW)' ? 'selected' : '' }}>New South Wales (NSW)</option>
                                    <option value="Victoria (VIC)" {{ old('state', $customer->state) == 'Victoria (VIC)' ? 'selected' : '' }}>Victoria (VIC)</option>
                                    <option value="Queensland (QLD)" {{ old('state', $customer->state) == 'Queensland (QLD)' ? 'selected' : '' }}>Queensland (QLD)</option>
                                    <option value="Western Australia (WA)" {{ old('state', $customer->state) == 'Western Australia (WA)' ? 'selected' : '' }}>Western Australia (WA)</option>
                                    <option value="South Australia (SA)" {{ old('state', $customer->state) == 'South Australia (SA)' ? 'selected' : '' }}>South Australia (SA)</option>
                                    <option value="Tasmania (TAS)" {{ old('state', $customer->state) == 'Tasmania (TAS)' ? 'selected' : '' }}>Tasmania (TAS)</option>
                                    <option value="Australian Capital Territory (ACT)" {{ old('state', $customer->state) == 'Australian Capital Territory (ACT)' ? 'selected' : '' }}>Australian Capital Territory (ACT)</option>
                                    <option value="Northern Territory (NT)" {{ old('state', $customer->state) == 'Northern Territory (NT)' ? 'selected' : '' }}>Northern Territory (NT)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">Pincode</p>
                                <input type="text" name="pincod" value="{{ old('pincod', $customer->pincod) }}" class="form-control border border-white-50" placeholder="Pincode">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="pull-right mt-1 text-center">
                            <button type="submit" class="btn btn-primary btn btn-dark me-1 rounded">Save</button>
                            <a class="btn btn-outline-dark waves-effect rounded" href="{{ route('customers.index') }}">Cancel</a>
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
    // Custom validation methods
    $.validator.addMethod("validEmail", function(value, element) {
    // General regex for email validation
    return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}, "Please enter a valid email address.");

 

    $.validator.addMethod("validName", function(value, element) {
        return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
    }, "Name should contain only letters and spaces.");

    // Initialize form validation
    $("#editCustomerForm").validate({
        rules: {
            name: {
                required: true,
                validName: true,
            },
            email: {
                required: true,
                email: true,
                validEmail: true
            },
            phone: {
                required: true,
            },
            street: {
                required: true
            },
         
            suburb: {
                required: true
            },
            state: {
                required: true
            },
            pincod: {
                required: true,
      
            }
        },
        messages: {
            name: {
                required: "Please enter your name",
                minlength: "Name must consist of at least 3 characters"
            },
            email: {
                required: "Please enter your email address"
            },
            phone: {
                required: "Please enter your phone number"
            },
            street: {
                required: "Please enter your street address"
            },
         
            suburb: {
                required: "Please enter your suburb"
            },
            state: {
                required: "Please enter your state"
            },
            pincod: {
                required: "Please enter your pincode",
                digits: "Please enter only numbers for the pincode"
            }
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.after(error);
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
