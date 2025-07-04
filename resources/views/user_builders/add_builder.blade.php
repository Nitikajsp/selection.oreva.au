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
        <div class="col-md-12">
          <a href="{{ url()->previous() }}" class="float-left d-flex text-black">
            <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black rounded"></i>Back
          </a>
        </div>
      </div>
    </div>
    <div class="container mt-5">
      <div class="inner-container custmrmt0">
        <div class="row">
          <div class="col-lg-12 margin-tb">
            <div class="pull-left">
              <h2>Add Builder</h2>
            </div>
            <div class="pull-left">
              <h5>Please enter your details</h5>
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
        <form id="builderForm" action="{{ route('user_builders.store') }}" method="POST">
          @csrf

          <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <label for="customer_dropdown" class="text-secondary mb-1">Select Customer</label>
                        <input type="text" id="customer_autocomplete" class="form-control border border-white-50" placeholder="Type customer name">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <p class="text-secondary mb-1">Builder Email</p>
                        <input type="email" name="contact_email" class="form-control border border-white-50">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <label for="builder" class="text-secondary mb-1">Builder Name</label>
                        <input type="text" id="builder" name="builder_name" class="form-control border border-white-50">
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
  </div>
@endsection

@push('scripts')


<script>
  
 $(document).ready(function () {
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.validator.addMethod("validName", function(value, element) {
        return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
    }, "Name should contain only letters.");

    $.validator.addMethod("validEmail", function(value, element) {
    // General regex for email validation
    return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}, "Please enter a valid email address.");
   
   

    $('#builderForm').validate({
        rules: {
            name: {
                required: true,
                validName: true,
                minlength: 3
            },
            email: {
                required: true,
                validEmail: true,
                remote: {
                    url: "{{ route('check.email') }}",
                    type: "POST",
                    data: {
                        email: function() {
                            return $('#email').val();
                        }
                    },
                    dataFilter: function(response) {
                        var json = JSON.parse(response);
                        return json.available ? 'true' : 'false';
                    }
                }
            },
        },
        messages: {
            name: {
                required: "Please enter your name"
            },
            email: {
                required: "Please enter your email address",
                remote: "The email address has already been taken"
            },
        },

        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            error.appendTo(element.parent().find('.error-text'));
        },

        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },

        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        },

        submitHandler: function (form) {
            form.submit();
        }
    });
});
</script>
@endpush
