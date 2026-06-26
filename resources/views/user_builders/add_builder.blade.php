@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="flex-grow-1 container-fluid">

                    <div class="page-header">
                        <a href="{{ url()->previous() }}" class="back-btn">
                            <i
                                class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                        </a>
                    </div>


                    <div class="inner-container">

                        <div class="page-wrapper-title">
                            <h1>Add Builder</h1>
                            <h6>Please enter your details</h6>
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



                        <form id="builderForm" action="{{ route('user_builders.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label for="customer_autocomplete" class="text-secondary mb-1">Select
                                            Customer</label>
                                        {{-- <input type="text" id="customer_autocomplete"
                                                class="form-control border border-white-50"
                                                placeholder="Type customer name"> --}}

                                        <input type="text" id="customer_autocomplete"
                                            class="form-control border border-white-50" placeholder="Type customer name">
                                        <input type="hidden" name="customer_id" id="customer_id">

                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 mb-3">
                                    <div class="form-group">
                                        <p class="text-secondary mb-1">
                                            Builder Email <span class="text-danger">*</span>
                                        </p> <input type="email" name="contact_email"
                                            class="form-control border border-white-50">
                                        <div class="invalid-feedback"></div>
                                        <span class="text-danger error-text"></span>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label for="builder" class="text-secondary mb-1">
                                            Builder Name <span class="text-danger">*</span>
                                        </label> <input type="text" id="builder" name="builder_name"
                                            class="form-control border border-white-50">
                                        <span class="text-danger error-text builder-error"></span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary rounded">Save</button>
                                    <a href="{{ url()->previous() }}"
                                        class="btn btn-outline-dark waves-effect rounded">Cancel</a>
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
    <script>
        // Autocomplete for customer + builder + email
        $("#customer_autocomplete").autocomplete({
            minLength: 1,
            source: function(request, response) {
                $.ajax({
                    url: "/get-customers",
                    type: "GET",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name,
                                builder: item.builder_name,
                                email: item.contact_email,
                                id: item.id // ✅ You are passing `id` correctly here
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $("#customer_autocomplete").val(ui.item.value); // customer name
                $("#builder").val(ui.item.builder); // builder name
                $("input[name='contact_email']").val(ui.item.email); // email
                $("#customer_id").val(ui.item.id);
                return false;
            }
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.validator.addMethod("validName", function(value, element) {
                return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
            }, "Name should contain only letters.");

            $.validator.addMethod("validEmail", function(value, element) {
                return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            }, "Please enter a valid email address.");

            $('#builderForm').validate({
                rules: {
                    builder_name: {
                        required: true,
                        validName: true,
                        minlength: 3
                    },
                    contact_email: {
                        required: true,
                        validEmail: true,
                        remote: {
                            url: "{{ route('check.email') }}",
                            type: "POST",
                            data: {
                                email: function() {
                                    return $('[name="contact_email"]').val();
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
                    builder_name: {
                        required: "Please enter builder name"
                    },
                    contact_email: {
                        required: "Please enter builder email",
                        remote: "The email address has already been taken"
                    }
                },

                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    error.appendTo(element.parent().find('.error-text'));
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
{{-- @endpush --}}
