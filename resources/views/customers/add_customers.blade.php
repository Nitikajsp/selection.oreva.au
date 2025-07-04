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
              <h2>Add Customer</h2>
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

        <form id="customerForm" action="{{ route('customers.store') }}" method="POST">
          @csrf

          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
                <label for="name" class="text-secondary mb-1">Name</label>
                <input type="text" id="name" name="name" class="form-control border border-white-50">
                <span class="text-danger error-text name-error"></span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
                <label for="phone" class="text-secondary mb-1">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control border border-white-50">
                <span class="text-danger error-text phone-error"></span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
                <label for="email" class="text-secondary mb-1">E-mail Address</label>
                <input type="email" id="email" name="email" class="form-control border border-white-50">
                <span class="text-danger error-text email-error"></span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
                <label for="street" class="text-secondary mb-1">Property Address</label>
                <input type="text" id="street" name="street" class="form-control border border-white-50">
                <span class="text-danger error-text street-error"></span>
              </div>
            </div>
            <!-- <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
                <label for="house_number" class="text-secondary mb-1">House Number</label>
                <input type="text" id="house_number" name="house_number" class="form-control border border-white-50">
                <span class="text-danger error-text street-error"></span>
              </div>
            </div> -->
            <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
                <label for="suburb" class="text-secondary mb-1">Suburb</label>
                <input type="text" id="suburb" name="suburb" class="form-control border border-white-50">
                <span class="text-danger error-text suburb-error"></span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
              <label for="State" class="text-secondary mb-1">State</label>
        <select name="state" class="form-control border border-white-50">
            <option value="" disabled selected>Select State</option>
            <option value="New South Wales (NSW)">New South Wales (NSW)</option>
            <option value="Victoria (VIC)" >Victoria (VIC)</option>
            <option value="Queensland (QLD)">Queensland (QLD)</option>
            <option value="Western Australia (WA)" >Western Australia (WA)</option>
            <option value="South Australia (SA)" >South Australia (SA)</option>
            <option value="Tasmania (TAS)" >Tasmania (TAS)</option>
            <option value="Australian Capital Territory (ACT)">Australian Capital Territory (ACT)</option>
            <option value="Northern Territory (NT)">Northern Territory (NT)</option>
        </select>
                <span class="text-danger error-text state-error"></span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
              <div class="form-group">
                <label for="pincod" class="text-secondary mb-1">Pincode</label>
                <input type="text" id="pincod" name="pincod" class="form-control border border-white-50">
                <span class="text-danger error-text pincod-error"></span>
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
   
   

    $('#customerForm').validate({
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
            street: {
                required: true,
            },
           
            phone: {
                required: true,
            },
            suburb: {
                required: true,
            },
            state: {
                required: true,
            },
            pincod: {
                required: true,
            }
        },
        messages: {
            name: {
                required: "Please enter your name"
            },
            email: {
                required: "Please enter your email address",
                remote: "The email address has already been taken"
            },
            street: {
                required: "Please enter your Address/Location"
            },
          
            phone: {
                required: "Please enter your phone number"
            },
            suburb: {
                required: "Please enter your suburb"
            },
            state: {
                required: "Please enter your state"
            },
            pincod: {
                required: "Please enter your pincod"
            }
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
