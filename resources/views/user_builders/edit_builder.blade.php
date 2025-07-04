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
            <a href="{{ url()->previous() }}" class="float-left d-flex text-black">
                <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
            </a>
            <a href="{{ route('user_builders.show', $builders->id) }}" 
                class="btn btn-primary btn-dark float-end rounded">
                    View
                </a>
        </div>
    </div>

<div class="container mt-5">
    <div class="inner-container">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Edit Project</h2>
                </div>
                <div class="pull-left">
                    <h5>Please enter details</h5>
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
        <script>
            $(document).ready(function() {
                $("#customer_autocomplete").autocomplete({
                    minLength: 3, // Start filtering after 3 characters
                    source: function(request, response) {
                        $.ajax({
                            url: "/get-customers",
                            type: "GET",
                            dataType: "json",
                            data: { term: request.term }, // Send user input
                            success: function(data) {
                                console.log('data', data);
                                let filteredResults = data.filter(customer => 
                                    customer.builder_name.toLowerCase().includes(request.term.toLowerCase())
                                );
                                response($.map(filteredResults, function(customer) {
                                    return {
                                        value: customer.builder_name,
                                        email: customer.contact_email
                                    };
                                }));
                            },
                            error: function(xhr) {
                                console.log("Error fetching data:", xhr);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $("#customer_autocomplete").val(ui.item.value);
                        $("#builder").val(ui.item.value);
                        $("input[name='contact_email']").val(ui.item.email);
                        return false; // Prevent default form submission
                    }
                });
            });
        </script>
        <form action="{{ route('user_builders.update', $builders->id) }}" method="POST" id="buildereditForm">
          @csrf
          @method('PUT')
          <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <label for="customer_dropdown" class="text-secondary mb-1">Select Customer</label>
                        <input type="text" id="customer_autocomplete" class="form-control border border-white-50" placeholder="Type customer name">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <p class="text-secondary mb-1">Builder Email</p>
                        <input type="email" name="contact_email" value="{{ $builders->contact_email }}"  class="form-control border border-white-50">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <label for="builder" class="text-secondary mb-1">Builder Name</label>
                        <input type="text" id="builder" name="builder_name" value="{{ $builders->builder_name }}" class="form-control border border-white-50">
                        <span class="text-danger error-text builder-error"></span>
                    </div>
                </div>

            <div class="pull-right mt-1 text-center">
              <button type="submit" class="btn btn-primary btn btn-dark me-1 rounded">Save</button>
              <a href="{{ url()->previous() }}" class="btn btn-outline-dark waves-effect rounded">Cancel</a>
            </div>
          </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
 
    <script>

    $(document).ready(function () {
        
        $.validator.addMethod("validEmail", function(value, element) {
    // General regex for email validation
    return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}, "Please enter a valid email address.");

        $("#buildereditForm").validate({
            rules: {
                name: {
                    required: true,
                },
              
                // suburb: {
                //     required: true,

                // },
                // state: {
                //     required: true,

                // },
                // pincod: {
                //     required: true,

                // },
                // description: {
                //     required: true,
                // },
                contact_number: {
                    required: true,
                },
                contact_email: {
                    required: true,
                    email: true,
                    validEmail: true
                },
                // builder_name: {
                //     required: true,
                // },
                // status: {
                //     required: true
                // }
            },
            messages: {
                name: {
                    required: "Please enter the street name",
                },
             
                // suburb: {
                //     required: "Please enter the suburb",

                // },
                // state: {
                //     required: "Please enter the state",

                // },
                // pincod: {
                //     required: "Please enter the pincod",

                // },
                // description: {
                //     required: "Please enter the description",
                // },
                contact_number: {
                    required: "Please enter the contact number"
                },
                contact_email: {
                    required: "Please enter the contact email",
                    email: "Please enter a valid email address",
                    validEmail: "Please enter a valid email address ending with '.com'"
                },
                // builder_name: {
                //     required: "Please enter the builder name",
                // },
                // status: {
                //     required: "Please select a status"
                // }
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
        $('#editListForm input, #editListForm textarea, #editListForm select').on('focus', function() {
            $(this).valid();
        });
    });
    </script>
@endpush
