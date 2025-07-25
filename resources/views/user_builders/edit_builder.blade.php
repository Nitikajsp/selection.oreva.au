@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
<div id="app" class="layout-wrapper">
  @include('include.sidebar') 

  <div class="layout-page">
    <div class="content-wrapper pl-30">
      <div class="flex-grow-1 container-fluid">
        <div class="row">
          <div class="col-md-12 d-flex justify-content-between align-items-center editpadding">
            <a href="{{ url()->previous() }}" class="float-left d-flex text-black">
              <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
            </a>
            <a href="{{ route('user_builders.show', $builders->id) }}" class="btn btn-primary btn-dark float-end rounded">
              View
            </a>
          </div>
        </div>

        <div class="container">
          <div class="inner-container">
            <div class="row">
              <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                  <h2>Edit Project</h2>
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

            <form action="{{ route('user_builders.update', $builders->id) }}" method="POST" id="buildereditForm">
              @csrf
              @method('PUT')

              <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                <div class="form-group">
                  <label for="customer_dropdown" class="text-secondary mb-1">Select Customer</label>
                  <input type="text" id="customer_autocomplete" class="form-control border border-white-50" placeholder="Type customer name" value="{{ old('customer_name', optional($builders->customer)->name) }}">
                  <input type="hidden" name="customer_id" id="customer_id" value="{{ $builders->customer_id }}">
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                <div class="form-group">
                  <p class="text-secondary mb-1">Builder Email</p>
                  <input type="email" name="contact_email" value="{{ $builders->contact_email }}" class="form-control border border-white-50">
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
                <button type="submit" class="btn btn-primary btn-dark me-1 rounded">Save</button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-dark waves-effect rounded">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
  // jQuery Validation
  $.validator.addMethod("validEmail", function(value, element) {
    return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }, "Please enter a valid email address.");

  $("#buildereditForm").validate({
    rules: {
      contact_email: {
        required: true,
        email: true,
        validEmail: true
      },
      builder_name: {
        required: true
      }
    },
    messages: {
      contact_email: {
        required: "Please enter the contact email",
        email: "Please enter a valid email address",
        validEmail: "Please enter a valid email address"
      },
      builder_name: {
        required: "Please enter the builder name"
      }
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      error.insertBefore(element);
    },
    highlight: function (element) {
      $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
      $(element).addClass('is-valid').removeClass('is-invalid');
    }
  });

  // Autocomplete for customer
  $("#customer_autocomplete").autocomplete({
    minLength: 1,
    source: function (request, response) {
      $.ajax({
        url: "/get-customers",
        type: "GET",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function (data) {
          response($.map(data, function (item) {
            return {
              label: item.name,
              value: item.name,
              builder: item.builder_name,
              email: item.contact_email,
              id: item.id
            };
          }));
        },
        error: function (xhr) {
          console.error("Error fetching customers:", xhr);
        }
      });
    },
    select: function (event, ui) {
      $("#customer_autocomplete").val(ui.item.value);

      if (ui.item.builder) {
        $("#builder").val(ui.item.builder);
      }

      if (ui.item.email) {
        $("input[name='contact_email']").val(ui.item.email);
      }

      if (ui.item.id) {
        $("#customer_id").val(ui.item.id);
      }

      return false;
    }
  });
});
</script>
@endpush
{{-- @endpush --}}
