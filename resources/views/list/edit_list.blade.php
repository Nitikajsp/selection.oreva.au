    @extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush
@section('content')
<div id="app" class="layout-wrapper">
  @include('include.sidebar') 


    <div class="layout-page">
        <div class="content-wrapper pl-30 ">

            <div class="flex-grow-1  container-fluid">
   

    <div class="page-header">
            <a href="{{ url()->previous() }}" class="back-btn">
                <i class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2 text-black"></i>Back
            </a>
            <div class="d-flex gap-2">
            <a href="javascript:;" class="btn btn-primary change-customer-btn"
                    data-current-customer="{{ $list->customer_id }}">
                    Change Project Customer
                </a>
                <a href="{{ route('customers.show', $list->customer_id) }}" 
                    class="btn btn-primary">
                    View
                </a>
                
            </div>
        </div>
 

<div class="container ">
    <div class="inner-container">
       
            <div class="page-wrapper-title">
                    <h1>Edit Project</h1>
                    <h6>Please enter details</h6>
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
                    minLength: 3,
                    source: function(request, response) {
                        $.ajax({
                            url: "/get-customers",
                            type: "GET",
                            dataType: "json",
                            data: { term: request.term },
                            success: function(data) {
                                console.log('data', data);
                                let filteredResults = data.filter(customer => 
                                    customer.name.toLowerCase().includes(request.term.toLowerCase())
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

                $(document).on('click', '.change-customer-btn', function() {
                    const currentCustomerId = $(this).data('current-customer');
                    $('#newCustomerSelect').val(currentCustomerId).trigger('change');
                    $('#changeCustomerModal').modal('show');
                });

                if ($.fn.select2) {
                    $('#newCustomerSelect').select2({
                        dropdownParent: $('#changeCustomerModal'),
                        width: '100%',
                        placeholder: 'Select customer'
                    });

                    $('#newCustomerSelect').on('select2:open', function() {
                        const $dropdown = $('.select2-container--open .select2-dropdown');
                        $dropdown.removeClass('select2-dropdown--above').addClass('select2-dropdown--below');
                    });
                }
            });
        </script>

        <form action="{{ route('lists.update', $list->id) }}" method="POST" id="editListForm">
            @csrf
            @method('PUT')

            <div class="row mt-3">
                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <p class="text-secondary mb-1">Property Address <span class="text-danger">*</span></p>
                        <input type="text" name="name" value="{{ $list->name }}" class="form-control border border-white-50" placeholder="Property Address">
                    </div>
                </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">Suburb</p>
                                <input type="text" name="suburb" value="{{ old('suburb', $list->suburb) }}" class="form-control border border-white-50" placeholder="Suburb">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">State</p>
                                <select name="state" class="form-control border border-white-50">
                                    <option value="New South Wales (NSW)" {{ old('state', $list->state) == 'New South Wales (NSW)' ? 'selected' : '' }}>New South Wales (NSW)</option>
                                    <option value="Victoria (VIC)" {{ old('state', $list->state) == 'Victoria (VIC)' ? 'selected' : '' }}>Victoria (VIC)</option>
                                    <option value="Queensland (QLD)" {{ old('state', $list->state) == 'Queensland (QLD)' ? 'selected' : '' }}>Queensland (QLD)</option>
                                    <option value="Western Australia (WA)" {{ old('state', $list->state) == 'Western Australia (WA)' ? 'selected' : '' }}>Western Australia (WA)</option>
                                    <option value="South Australia (SA)" {{ old('state', $list->state) == 'South Australia (SA)' ? 'selected' : '' }}>South Australia (SA)</option>
                                    <option value="Tasmania (TAS)" {{ old('state', $list->state) == 'Tasmania (TAS)' ? 'selected' : '' }}>Tasmania (TAS)</option>
                                    <option value="Australian Capital Territory (ACT)" {{ old('state', $list->state) == 'Australian Capital Territory (ACT)' ? 'selected' : '' }}>Australian Capital Territory (ACT)</option>
                                    <option value="Northern Territory (NT)" {{ old('state', $list->state) == 'Northern Territory (NT)' ? 'selected' : '' }}>Northern Territory (NT)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group">
                                <p class="text-secondary mb-1">Pincode</p>
                                <input type="text" name="pincod" value="{{ old('pincod', $list->pincod) }}" class="form-control border border-white-50" placeholder="Pincode">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <p class="text-secondary mb-1">Description</p>
                        <textarea class="form-control border border-white-50" style="height:150px !important;" name="description" placeholder="Description">{{ $list->description }}</textarea>
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <p class="text-secondary mb-1">Contact Number</p>
                        <input type="text" name="contact_number" value="{{ $list->contact_number }}" class="form-control border border-white-50" placeholder="Contact Number">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <label for="customer_dropdown" class="text-secondary mb-1">Select Builder</label>
                        <input type="text" id="customer_autocomplete" class="form-control border border-white-50" placeholder="Type customer name">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <p class="text-secondary mb-1">Builder Email</p>
                        <input type="email" name="contact_email" value="{{ $list->contact_email }}" class="form-control border border-white-50" placeholder="Contact Email">
                    </div>
                </div>
             

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <label for="builder" class="text-secondary mb-1">Builder Name</label>
                        <input type="text" id="builder" name="builder_name" value="{{ $list->builder_name }}" class="form-control border border-white-50" placeholder="Builder Name">
                        <span class="text-danger error-text builder-error"></span>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                    <div class="form-group">
                        <label for="status" class="text-secondary mb-1">Selection For</label>
                        <div class="input-group">
                            <select id="status" name="status" class="form-select">
                                <option value="">Select...</option>
                                <option value="First Home" {{ $list->status == 'First Home' ? 'selected' : '' }}>First Home</option>
                                <option value="Investment" {{ $list->status == 'Investment' ? 'selected' : '' }}>Investment</option>
                            </select>
                        </div>
                        <span class="text-danger error-text status-error"></span>
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
        </div>

        <div class="modal fade" id="changeCustomerModal" tabindex="-1"
            aria-labelledby="changeCustomerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeCustomerModalLabel">Change Project Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="changeCustomerForm" method="POST" action="{{ route('lists.reassignCustomer', $list->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="newCustomerSelect" class="form-label">Select Customer</label>
                                <select class="form-select select2" id="newCustomerSelect" name="new_customer_id" required>
                                    <option value="">Select customer</option>
                                    @foreach ($allCustomers as $c)
                                        <option value="{{ $c->id }}" {{ $c->id == $list->customer_id ? 'selected' : '' }}>
                                            {{ $c->name }} ({{ $c->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign</button>
                        </div>
                    </form>
                </div>
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

        $('input[name="contact_number"]').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $("#editListForm").validate({
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
                    digits: true,
                },
                contact_email: {
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
                    digits: "Please enter only numbers for the contact number"
                },
                contact_email: {
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
