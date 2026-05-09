@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <style>
        .customer-card-layout {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 28px;
            align-items: stretch;
        }

        .customer-detail-container {
            background: transparent;
            padding: 0;
            max-width: 86%;
        }

        .customer-profile-card,
        .customer-projects-card,
        .customer-project-card,
        .customer-new-project-card {
            background: #fff;
            border: 1px solid #e7e7e7;
            border-radius: 8px;
        }

        .customer-profile-card {
            padding: 28px;
        }

        .customer-avatar {
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: #f5f5f5;
            color: #630660;
            font-family: "Inter-Bold";
            font-size: 28px;
        }

        .customer-profile-name {
            text-align: center;
            font-family: "Inter-Bold";
            font-size: 20px;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .customer-profile-id {
            text-align: center;
            color: #1C001B66;
            font-size: 12px;
            margin-bottom: 24px;
        }

        .customer-info-box {
            padding: 12px 14px;
            margin-bottom: 12px;
            border-radius: 6px;
            background: #f8f8f8;
        }

        .customer-info-label {
            display: block;
            color: #1C001B66;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .customer-info-value {
            color: #000;
            font-size: 14px;
            word-break: break-word;
        }

        .customer-profile-actions {
            display: block;
            margin-top: 24px;
        }

        .customer-profile-actions .btn {
            width: 100%;
        }

        .customer-projects-card {
            padding: 24px;
        }

        .customer-projects-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
        }

        .customer-projects-title h3 {
            margin-bottom: 4px;
        }

        .customer-projects-title p {
            color: #1C001B66;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .customer-project-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 16px;
        }

        .customer-project-grid.has-project-scroll {
            max-height: 318px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .customer-project-grid.has-project-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .customer-project-grid.has-project-scroll::-webkit-scrollbar-track {
            background: #f8f8f8;
            border-radius: 999px;
        }

        .customer-project-grid.has-project-scroll::-webkit-scrollbar-thumb {
            background: #630660;
            border-radius: 999px;
        }

        .customer-project-card {
            position: relative;
            min-height: 135px;
            padding: 18px;
        }

        .customer-project-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px;
        }

        .customer-project-icon {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #f8f8f8;
            color: #630660;
            font-size: 18px;
        }

        .customer-project-status {
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef8ee;
            color: #28b804;
            font-size: 10px;
        }

        .customer-project-name {
            display: inline-block;
            color: #000;
            font-family: "Inter-Bold";
            font-size: 16px;
            margin-bottom: 14px;
            word-break: break-word;
        }

        .customer-project-count {
            color: #1C001B66;
            font-size: 12px;
        }

        .customer-project-actions {
            position: absolute;
            right: 12px;
            bottom: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .customer-project-action {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #000;
        }

        .customer-project-action:hover {
            background: #f8f8f8;
            color: #630660;
        }

        .customer-new-project-card {
            min-height: 135px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-style: dashed;
            color: #1C001B66;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .customer-new-project-card i {
            font-size: 24px;
        }

        .customer-empty-projects {
            grid-column: 1 / -1;
            min-height: 135px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #e7e7e7;
            border-radius: 8px;
            color: #1C001B66;
        }

        @media (max-width: 1199.98px) {
            .customer-project-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 991.98px) {
            .customer-card-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .content-wrapper {
                padding: 12px !important;
            }

            .customer-detail-container {
                max-width: 100%;
                width: 100%;
            }

            .customer-profile-card,
            .customer-projects-card {
                padding: 16px;
            }

            .customer-info-box {
                padding: 10px 12px;
            }

            .customer-projects-header {
                align-items: stretch;
                flex-direction: column;
            }

            .customer-projects-header .btn {
                width: 100%;
            }

            .customer-project-grid {
                grid-template-columns: 1fr;
            }

            .customer-project-grid.has-project-scroll {
                max-height: 452px;
                padding-right: 4px;
            }

            .customer-project-card {
                min-height: 168px;
                padding: 16px;
            }

            .customer-project-top {
                margin-bottom: 14px;
            }

            .customer-project-name {
                max-width: 100%;
                margin-bottom: 10px;
                padding-right: 0;
            }

            .customer-project-count {
                margin-bottom: 12px;
            }

            .customer-project-actions {
                position: static;
                justify-content: flex-end;
                gap: 8px;
                margin-top: 8px;
            }

            .customer-project-action {
                width: 32px;
                height: 32px;
                background: #f8f8f8;
            }

            .customer-new-project-card {
                min-height: 128px;
            }
        }
    </style>
@endpush
@section('content')
    <div id="app" class="layout-wrapper">
        @include('include.sidebar')

        <div class="layout-page">
            <div class="content-wrapper">
                <div class="flex-grow-1 container-fluid">
                    <div class="page-header">
                        <a href="{{ url()->previous() }}" class="back-btn"><i
                                class="ti ti-arrow-narrow-left border border-dark rounded-circle mx-1 me-2"></i>Back</a>
                    </div>

                    <div class="inner-container customer-detail-container">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="project-alert-placeholder"></div>

                        <div class="customer-card-layout">
                            <div class="customer-profile-card">
                                <div class="customer-avatar">
                                    {{ strtoupper(substr(trim($customer->name), 0, 1)) ?: 'C' }}
                                </div>
                                <h2 class="customer-profile-name">{{ $customer->name }}</h2>
                                {{-- <div class="customer-profile-id">Client Profile #{{ $customer->id }}</div> --}}

                                <div class="customer-info-box">
                                    <span class="customer-info-label">Email ID</span>
                                    <a href="mailto:{{ $customer->email }}" class="customer-info-value">{{ $customer->email }}</a>
                                </div>

                                <div class="customer-info-box">
                                    <span class="customer-info-label">Primary Phone</span>
                                    <a href="tel:{{ $customer->phone }}" class="customer-info-value">{{ $customer->phone }}</a>
                                </div>

                                <div class="customer-profile-actions">
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-dark">
                                        <i class="ti ti-pencil me-1"></i>Edit
                                    </a>
                                </div>

                                <form id="deleteCustomerForm" action="{{ route('customers.destroy', $customer->id) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>

                            <div class="customer-projects-card">
                                <div class="customer-projects-header">
                                    <div class="customer-projects-title">
                                        <h3>All Projects</h3>
                                        <p>Active Site Selections</p>
                                    </div>
                                    <a href="{{ route('createlist', ['customer_id' => $customer->id]) }}"
                                        class="btn btn-dark">
                                        <i class="ti ti-plus me-1"></i>Create Site
                                    </a>
                                </div>

                                <div class="customer-project-grid {{ $lists->count() > 3 ? 'has-project-scroll' : '' }}">
                                    @forelse ($lists as $list)
                                        <div class="customer-project-card" data-project-id="{{ $list->id }}">
                                            <div class="customer-project-top">
                                                <div class="customer-project-icon">
                                                    <i class="ti ti-building-store"></i>
                                                </div>
                                                <span class="customer-project-status">Active Site</span>
                                            </div>

                                            <a href="{{ route('showlistcustomer', ['listId' => $list->id, 'customerId' => $customer->id]) }}"
                                                class="customer-project-name">
                                                {{ $list->name }}
                                            </a>

                                            <div class="customer-project-count">
                                                <i class="ti ti-cube me-1"></i>{{ $list->orders->count() }} Selections
                                            </div>

                                            <div class="customer-project-actions">
                                                <a href="{{ route('lists.edit', $list->id) }}"
                                                    class="customer-project-action" title="Edit project"
                                                    aria-label="Edit project">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <a href="{{ route('showlistcustomer', ['listId' => $list->id, 'customerId' => $customer->id]) }}"
                                                    class="customer-project-action" title="View project"
                                                    aria-label="View project">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('lists.addcartproduct', ['list' => $list->id, 'customer' => $list->customer_id]) }}"
                                                    class="customer-project-action" title="Add selection"
                                                    aria-label="Add selection">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>
                                                <button type="button"
                                                    class="customer-project-action text-danger project-delete-btn"
                                                    data-delete-url="{{ route('lists.destroy', $list->id) }}"
                                                    title="Delete project" aria-label="Delete project">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="customer-empty-projects">No projects found.</div>
                                    @endforelse

                                    <a href="{{ route('createlist', ['customer_id' => $customer->id]) }}"
                                        class="customer-new-project-card">
                                        <i class="ti ti-plus"></i>
                                        New Project
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="projectDeleteModal" tabindex="-1" aria-labelledby="projectDeleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectDeleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this project?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmProjectDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                let projectDeleteUrl;
                let projectDeleteCard;

                $(document).on('click', '.project-delete-btn', function() {
                    projectDeleteUrl = $(this).data('delete-url');
                    projectDeleteCard = $(this).closest('.customer-project-card');
                    $('#projectDeleteModal').modal('show');
                });

                $('#confirmProjectDeleteBtn').on('click', function() {
                    if (!projectDeleteUrl) {
                        return;
                    }

                    const button = $(this);
                    button.prop('disabled', true).text('Deleting...');

                    $.ajax({
                        url: projectDeleteUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            $('#projectDeleteModal').modal('hide');

                            if (response.success) {
                                projectDeleteCard.remove();
                                const projectCount = $('.customer-project-card').length;
                                $('.customer-project-grid').toggleClass('has-project-scroll', projectCount > 3);
                                $('.project-alert-placeholder').html(`
                                    <div class="alert alert-success">
                                        <p>${response.message || 'Project delete successfully.'}</p>
                                    </div>
                                `);

                                setTimeout(function() {
                                    $('.project-alert-placeholder').html('');
                                }, 3000);
                            } else {
                                alert(response.message || 'Project delete failed.');
                            }
                        },
                        error: function() {
                            alert('Project delete failed.');
                        },
                        complete: function() {
                            button.prop('disabled', false).text('Delete');
                            projectDeleteUrl = null;
                            projectDeleteCard = null;
                        }
                    });
                });
            });
        </script>
    @endsection
