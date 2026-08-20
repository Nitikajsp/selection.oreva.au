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
                            <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
                        </a>
                        <a href="{{ route('customers.show', $customer_id) }}" class="btn btn-primary btn-dark rounded">
                            View
                        </a>
                    </div>

                    <div class="inner-container">
                        <div class="page-wrapper-title">
                            <h1>Create Project</h1>
                            <h6>Please enter detail</h6>
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
                                    minLength: 2,
                                    max: 10,
                                    source: function(request, response) {
                                        $.ajax({
                                            url: "/get-customers",
                                            type: "GET",
                                            dataType: "json",
                                            data: {
                                                term: request.term
                                            },
                                            success: function(data) {
                                                let filteredResults = data.filter(customer =>
                                                    customer.name.toLowerCase().includes(request.term
                                                        .toLowerCase())
                                                );
                                                response($.map(filteredResults, function(customer) {
                                                    return {
                                                        value: customer.name,
                                                        email: customer.email
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
                                        return false;
                                    }
                                });
                            });
                        </script>

                        <form action="{{ route('lists.store') }}" method="POST" id="createBranchForm">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer_id }}">

                            <div class="row">
                                {{-- 1. PROPERTY ADDRESS --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <p class="text-secondary mb-1">Property Address <span class="text-danger">*</span></p>
                                        {{-- 🔥 FIXED: Added old() helper --}}
                                        <input type="text" name="list_name"
                                            class="form-control border border-white-50 @error('list_name') is-invalid @enderror"
                                            value="{{ old('list_name') }}">
                                        @error('list_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 2. SUBURB --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="suburb" class="text-secondary mb-1">Suburb</label>
                                        {{-- 🔥 FIXED: Added old() helper --}}
                                        <input type="text" id="suburb" name="suburb"
                                            class="form-control border border-white-50 @error('suburb') is-invalid @enderror"
                                            value="{{ old('suburb') }}">
                                        @error('suburb')
                                            <span class="text-danger error-text suburb-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 3. STATE --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="State" class="text-secondary mb-1">State</label>
                                        {{-- 🔥 FIXED: Added old() helper in select --}}
                                        <select name="state" class="form-control border border-white-50 @error('state') is-invalid @enderror">
                                            <option value="" disabled {{ old('state') ? '' : 'selected' }}>Select State</option>
                                            <option value="New South Wales (NSW)" {{ old('state') == 'New South Wales (NSW)' ? 'selected' : '' }}>New South Wales (NSW)</option>
                                            <option value="Victoria (VIC)" {{ old('state') == 'Victoria (VIC)' ? 'selected' : '' }}>Victoria (VIC)</option>
                                            <option value="Queensland (QLD)" {{ old('state') == 'Queensland (QLD)' ? 'selected' : '' }}>Queensland (QLD)</option>
                                            <option value="Western Australia (WA)" {{ old('state') == 'Western Australia (WA)' ? 'selected' : '' }}>Western Australia (WA)</option>
                                            <option value="South Australia (SA)" {{ old('state') == 'South Australia (SA)' ? 'selected' : '' }}>South Australia (SA)</option>
                                            <option value="Tasmania (TAS)" {{ old('state') == 'Tasmania (TAS)' ? 'selected' : '' }}>Tasmania (TAS)</option>
                                            <option value="Australian Capital Territory (ACT)" {{ old('state') == 'Australian Capital Territory (ACT)' ? 'selected' : '' }}>Australian Capital Territory (ACT)</option>
                                            <option value="Northern Territory (NT)" {{ old('state') == 'Northern Territory (NT)' ? 'selected' : '' }}>Northern Territory (NT)</option>
                                        </select>
                                        @error('state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 4. PINCODE --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="pincod" class="text-secondary mb-1">Pincode</label>
                                        {{-- 🔥 FIXED: Added old() helper --}}
                                        <input type="text" id="pincod" name="pincod"
                                            class="form-control border border-white-50 @error('pincod') is-invalid @enderror"
                                            value="{{ old('pincod') }}">
                                        @error('pincod')
                                            <span class="text-danger error-text pincod-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 5. DESCRIPTION --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <p class="text-secondary mb-1">Description</p>
                                        {{-- 🔥 FIXED: Added old() helper for textarea --}}
                                        <textarea class="form-control border border-white-50 @error('list_description') is-invalid @enderror" 
                                            style="height:150px !important;" 
                                            name="list_description">{{ old('list_description') }}</textarea>
                                        @error('list_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 6. CONTACT NUMBER --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <p class="text-secondary mb-1">Contact Number</p>
                                        {{-- 🔥 FIXED: Added old() helper --}}
                                        <input type="text" name="contact_number"
                                            class="form-control border border-white-50 @error('contact_number') is-invalid @enderror"
                                            value="{{ old('contact_number') }}">
                                        @error('contact_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 7. SELECT BUILDER (Autocomplete) --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="customer_dropdown" class="text-secondary mb-1">Select Builder</label>
                                        {{-- 🔥 FIXED: Added old() helper --}}
                                        <input type="text" id="customer_autocomplete"
                                            class="form-control border border-white-50 @error('builder_name') is-invalid @enderror"
                                            placeholder="Type customer name"
                                            value="{{ old('builder_name') }}">
                                        @error('builder_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 8. BUILDER EMAIL --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <p class="text-secondary mb-1">Builder Email</p>
                                        {{-- 🔥 FIXED: Added old() helper --}}
                                        <input type="email" name="contact_email"
                                            class="form-control border border-white-50 @error('contact_email') is-invalid @enderror"
                                            value="{{ old('contact_email') }}">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 9. BUILDER NAME --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="builder" class="text-secondary mb-1">Builder Name</label>
                                        {{-- 🔥 FIXED: Added old() helper --}}
                                        <input type="text" id="builder" name="builder_name"
                                            class="form-control border border-white-50 @error('builder_name') is-invalid @enderror"
                                            value="{{ old('builder_name') }}">
                                        @error('builder_name')
                                            <span class="text-danger error-text builder-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 10. SELECTION FOR --}}
                                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="status" class="text-secondary mb-1">Selection For</label>
                                        <div class="input-group">
                                            {{-- 🔥 FIXED: Added old() helper in select --}}
                                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                                <option value="">Select...</option>
                                                <option value="First Home" {{ old('status') == 'First Home' ? 'selected' : '' }}>First Home</option>
                                                <option value="Investment" {{ old('status') == 'Investment' ? 'selected' : '' }}>Investment</option>
                                            </select>
                                        </div>
                                        @error('status')
                                            <span class="text-danger error-text status-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- BUTTONS --}}
                                <div class="pull-right mt-1 text-center">
                                    <button type="submit"
                                        class="btn btn-primary btn btn-dark me-1 rounded">Save</button>
                                    <button type="reset" class="btn btn-outline-dark waves-effect rounded"
                                        data-bs-dismiss="modal" aria-label="Close">Cancel</button>
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
    $(document).ready(function() {
        $.validator.addMethod("validEmail", function(value, element) {
            return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }, "Please enter a valid email address.");
        
        $('input[name="contact_number"]').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $("#createBranchForm").validate({
            rules: {
                list_name: {
                    required: true,
                },
                contact_number: {
                    digits: true,
                },
                contact_email: {
                    email: true,
                    validEmail: true
                },
            },
            messages: {
                list_name: {
                    required: "Please enter the street name",
                },
                contact_number: {
                    digits: "Please enter only numbers for the contact number"
                },
                contact_email: {
                    email: "Please enter a valid email address",
                    validEmail: "Please enter a valid email address ending with '.com'"
                },
            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                error.insertAfter(element);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid is-valid');
            }
        });

        $('#createBranchForm input, #createBranchForm textarea, #createBranchForm select').on('blur', function() {
            $(this).valid();
        });
    });
</script>
@endpush