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

                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h1 class="mb-0">Point of Sale</h1>
                    </div>

                    <div class="container-fluid addcustomer_pad pos-page">
                        <div class="pos-shell">
                            <div id="pos-alert" class="mb-3"></div>

                            <div class="row g-4 mb-4">
                                <div class="col-lg-8 d-flex flex-column gap-4 ps-lg-0">
                                    <div class="pos-card h-100">
                                        <div class="pos-card-header">
                                            <h5 class="pos-card-title mb-0">Select customer</h5>
                                        </div>
                                        <div class="pos-card-body">
                                            <div id="customer-selection-section">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label for="customer-search" class="form-label pos-label mb-0">Customer</label>
                                                    <a href="{{ route('customers.create', ['from_pos' => 1]) }}" target="_blank" class="btn btn-sm btn-outline-primary">Create customer</a>
                                                </div>
                                                <input type="text" id="customer-search" class="form-control pos-input mb-2"
                                                       placeholder="Search customer by name, email or phone">
                                                <div class="list-group pos-customer-results" id="customer-results" style="max-height: 200px; overflow-y: auto;"></div>
                                                <div class="mt-3 d-flex align-items-center justify-content-between">
                                                    <div class="small text-muted">Selected customer</div>
                                                    <div class="fw-semibold" id="selected-customer-wrapper">
                                                        <span id="selected-customer-text" class="text-primary">None</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="selected-customer-id">

                                                {{-- 🔥 UPDATED: Project Section --}}
                                                <div class="mt-3">
                                                    <div class="mb-1">
                                                        <label for="project-select" class="form-label pos-label mb-0">Project</label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <select id="project-select" class="form-select pos-input" disabled style="flex: 1;">
                                                            <option value="">Select customer first</option>
                                                        </select>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="reload-projects" disabled title="Reload projects" style="flex-shrink: 0; padding: 6px 10px;">
                                                            <i class="ti ti-refresh"></i>
                                                        </button>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-2">
                                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary" id="create-project-link">Create project</a>
                                                    </div>
                                                    <input type="hidden" id="selected-project-id">
                                                </div>

                                                <div class="mt-3 d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-primary d-none" id="customer-project-save">Save selection</button>
                                                </div>
                                            </div>

                                            <div class="mt-3 border rounded-3 p-3 d-none" id="customer-preview">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="fw-semibold">Customer details</div>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <div class="text-muted small mb-1">Customer Name</div>
                                                        <div class="fw-semibold" id="preview-customer-name"></div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="text-muted small mb-1">Customer Phone</div>
                                                        <div class="fw-semibold" id="preview-customer-phone"></div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="text-muted small mb-1">Customer Email</div>
                                                        <div class="fw-semibold" id="preview-customer-email"></div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="text-muted small mb-1">Project</div>
                                                        <div class="fw-semibold" id="preview-project-name"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pos-card h-100">
                                        <div class="pos-card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                            <h5 class="pos-card-title mb-0">Products</h5>
                                            <div class="pos-search flex-grow-1">
                                                <input type="text" id="product-search" class="form-control pos-input"
                                                       placeholder="Search product by name or code">
                                            </div>
                                        </div>
                                        <div class="pos-card-body p-0">
                                            <div class="table_scroll pos-table-wrapper">
                                                <table class="table table-borderless mb-0 pos-table" id="pos-products-table">
                                                    <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Name</th>
                                                        <th>Category</th>
                                                        <th>Code</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-end">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <nav class="mt-3 pos-pagination-wrapper">
                                                <ul class="pagination pagination-sm mb-0" id="products-pagination"></ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 pe-lg-0">
                                    <div class="pos-card">
                                        <div class="pos-card-header d-flex justify-content-between align-items-center">
                                            <h5 class="pos-card-title mb-0">Cart</h5>
                                        </div>
                                        <div class="pos-card-body p-0">
                                            <div class="table_scroll pos-table-wrapper">
                                                <table class="table table-borderless mb-0 pos-table" id="pos-cart-table">
                                                    <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Product</th>
                                                        <th class="text-center">Qty</th>
                                                        <th>Comment</th>
                                                        <th class="text-end d-none">Total</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr id="cart-empty-row">
                                                        <td colspan="6" class="text-center text-muted py-4">No items in cart.</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="pos-card-body border-top mt-3 pt-3">
                                            <h6 class="pos-card-title mb-3">Summary</h6>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="pos-label">Total items</span>
                                                <span class="pos-summary-value" id="summary-items">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3 d-none">
                                                <span class="pos-label">Total amount</span>
                                                <span class="pos-summary-amount">
                                                    $
                                                    <span id="summary-amount">0.00</span>
                                                </span>
                                            </div>

                                            <div class="mb-3">
                                                <label for="order-customer-name" class="pos-label">Customer name (optional)</label>
                                                <input type="text" id="order-customer-name" class="form-control pos-input" placeholder="Enter customer name">
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="pos-label">Digital signature (optional)</span>
                                                    <button type="button" class="btn btn-sm btn-link p-0" id="clear-signature">Clear</button>
                                                </div>
                                                <div class="border rounded-3 bg-white" style="padding: 6px;">
                                                    <canvas id="signature-pad" height="140" style="width: 100%; touch-action: none; cursor: crosshair; display: block;"></canvas>
                                                </div>
                                                <input type="hidden" id="signature-data">
                                            </div>

                                            <div class="d-flex gap-2 pos-actions">
                                                <button id="save-order" class="btn pos-complete-btn flex-fill" disabled>Save</button>
                                                <button id="save-send-order" class="btn pos-complete-btn flex-fill d-flex justify-content-center align-items-center" disabled>
                                                    <span id="save-send-text">Save &amp; Send</span>
                                                    <span id="save-send-loader" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
            let cart = {};
            let productsCache = [];
            let currentPage = 1;
            let perPage = 10;
            let currentTerm = '';
            const productImageBaseUrl = "{{ asset('images/products') }}/";
            let selectedProjectId = '';
            let selectedCustomerData = null;

            const initialCustomer = @json($initialCustomer ?? null);
            const initialList = @json($initialList ?? null);

            function showAlert(message, type = 'success') {
                const html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                $('#pos-alert').html(html);
                setTimeout(() => { $('.alert').alert('close'); }, 3000);
            }

            function loadProjects(customerId) {
                const $select = $('#project-select');
                const $reloadBtn = $('#reload-projects');
                const $createLink = $('#create-project-link');

                $('#selected-project-id').val('');
                selectedProjectId = '';

                if (!customerId) {
                    $select.prop('disabled', true).html('<option value="">Select customer first</option>');
                    $reloadBtn.prop('disabled', true);
                    $createLink.attr('href', '#');
                    refreshSummary();
                    return;
                }

                $select.prop('disabled', true).html('<option value="">Loading projects...</option>');
                $reloadBtn.prop('disabled', true);
                $createLink.attr('href', '{{ url('/createlist') }}/' + customerId + '?from_pos=1');

                $.ajax({
                    url: '{{ route('get-lists') }}',
                    type: 'GET',
                    data: { customer_id: customerId },
                    success: function (lists) {
                        $select.empty();
                        if (!lists.length) {
                            $select.append('<option value="">No projects found</option>');
                        } else {
                            $select.append('<option value="">Select project</option>');
                            lists.forEach(function (list) {
                                $select.append('<option value="' + list.id + '">' + list.name + '</option>');
                            });
                        }
                        $select.prop('disabled', false);
                        $reloadBtn.prop('disabled', false);
                    },
                    error: function () {
                        $select.prop('disabled', true).html('<option value="">Failed to load projects</option>');
                    }
                });
            }

            // 🔥 NEW: Refresh projects with spinning animation
            function refreshProjects() {
                const customerId = $('#selected-customer-id').val();
                if (!customerId) {
                    showAlert('Please select a customer first.', 'warning');
                    return;
                }

                const $btn = $('#reload-projects');
                const $icon = $btn.find('i');

                // Add spinning animation
                $icon.addClass('ti-spin');
                $btn.prop('disabled', true);

                const $select = $('#project-select');
                $select.prop('disabled', true).html('<option value="">Refreshing projects...</option>');

                $.ajax({
                    url: '{{ route('get-lists') }}',
                    type: 'GET',
                    data: { customer_id: customerId },
                    success: function (lists) {
                        $select.empty();
                        if (!lists.length) {
                            $select.append('<option value="">No projects found</option>');
                        } else {
                            $select.append('<option value="">Select project</option>');
                            lists.forEach(function (list) {
                                $select.append('<option value="' + list.id + '">' + list.name + '</option>');
                            });
                        }
                        $select.prop('disabled', false);
                        $btn.prop('disabled', false);
                        $icon.removeClass('ti-spin');
                        showAlert('Projects refreshed successfully!', 'success');
                    },
                    error: function () {
                        $select.prop('disabled', true).html('<option value="">Failed to refresh projects</option>');
                        $btn.prop('disabled', false);
                        $icon.removeClass('ti-spin');
                        showAlert('Failed to refresh projects.', 'danger');
                    }
                });
            }

            function refreshSummary() {
                let totalItems = 0;
                let totalAmount = 0;
                Object.values(cart).forEach(item => {
                    totalItems += item.quantity;
                    totalAmount += item.quantity * item.price;
                });
                $('#summary-items').text(totalItems);
                $('#summary-amount').text(totalAmount.toFixed(2));

                const hasCustomer = !!$('#selected-customer-id').val();
                const hasProject = !!$('#selected-project-id').val();
                const hasItems = totalItems > 0;
                const disabled = !(hasCustomer && hasProject && hasItems);
                $('#save-order, #save-send-order').prop('disabled', disabled);
            }

            function resetCustomerPreview() {
                $('#customer-preview').addClass('d-none');
                $('#customer-selection-section').removeClass('d-none');
            }

            function toggleCustomerSaveButton() {
                const hasCustomer = !!$('#selected-customer-id').val();
                const hasProject = !!$('#selected-project-id').val();

                if (hasCustomer && hasProject) {
                    $('#customer-project-save').removeClass('d-none');
                } else {
                    $('#customer-project-save').addClass('d-none');
                }
            }

            function showCustomerPreview() {
                if (!selectedCustomerData) {
                    return;
                }

                $('#preview-customer-name').text(selectedCustomerData.name || '');
                $('#preview-customer-phone').text(selectedCustomerData.phone || '');
                $('#preview-customer-email').text(selectedCustomerData.email || '');
                $('#preview-project-name').text(selectedCustomerData.project_name || '');

                $('#customer-selection-section').addClass('d-none');
                $('#customer-preview').removeClass('d-none');
            }

            function renderCart() {
                const $tbody = $('#pos-cart-table tbody');
                $tbody.empty();

                const items = Object.values(cart);
                if (items.length === 0) {
                    $tbody.append('<tr id="cart-empty-row"><td colspan="6" class="text-center text-muted py-4">No items in cart.</td></tr>');
                    refreshSummary();
                    return;
                }

                items.forEach(item => {
                    const productImage = item.productImage || '';
                    const specImage = item.specImage || '';
                    const productImageBase = item.productImageBase || productImageBaseUrl.replace(/\/$/, '');
                    const specImageBase = item.specImageBase || "{{ asset('images/products/specification') }}";

                    const imageHtml = (productImage || specImage)
                        ? `<div class="d-flex gap-1">
                                ${productImage ? `<img src="${productImageBase}/${productImage}" alt="${item.name}" class="img-fluid rounded" width="40" height="40">` : ''}
                                ${specImage ? `<img src="${specImageBase}/${specImage}" alt="${item.name}" class="img-fluid rounded" width="40" height="40">` : ''}
                           </div>`
                        : '<span class="text-muted small">No image</span>';

                    const row = `<tr class="align-middle" data-product-id="${item.id}">
                            <td class="pos-cart-image" data-label="Image">${imageHtml}</td>
                            <td class="pos-cart-product" data-label="Product">${item.name}</td>
                            <td class="text-center" data-label="Qty">
                                <div class="pos-qty-control">
                                    <button type="button" class="pos-qty-btn pos-qty-minus">-</button>
                                    <input type="number" class="form-control form-control-sm text-center cart-qty pos-input" min="1" value="${item.quantity}">
                                    <button type="button" class="pos-qty-btn pos-qty-plus">+</button>
                                </div>
                            </td>
                            <td data-label="Comment">
                                <input type="text" class="form-control form-control-sm cart-comment pos-input" value="${item.comment || ''}" placeholder="Comment">
                            </td>
                            <td class="text-end pos-cart-total d-none" data-label="Total">$${(item.quantity * item.price).toFixed(2)}</td>
                            <td class="text-end" data-label="Remove">
                                <button class="btn btn-sm btn-outline-danger pos-remove-btn remove-from-cart">&times;</button>
                            </td>
                        </tr>`;
                    $tbody.append(row);
                });

                refreshSummary();
            }

            function renderProducts(products) {
                const $tbody = $('#pos-products-table tbody');
                $tbody.empty();

                if (!products.length) {
                    $tbody.append('<tr class="pos-empty-row"><td colspan="6" class="text-center text-muted py-4">No products found.</td></tr>');
                    return;
                }

                products.forEach(p => {
                    const rawPrice = parseFloat(p.product_price);
                    const priceValue = isNaN(rawPrice) ? 0 : rawPrice;
                    const priceDisplay = priceValue.toFixed(2);

                    const categoryName = (p.category && p.category.category_name)
                        ? p.category.category_name
                        : (p.product_name || '');

                    const productImage = p.product_image || '';
                    const specImage = p.specification_product_image || '';
                    const productImageBase = "{{ asset('images/products') }}";
                    const specImageBase = "{{ asset('images/products/specification') }}";

                    const row = `<tr class="align-middle" data-product-id="${p.id}" data-product-name="${p.product_name || ''}" data-product-price="${priceValue}" data-product-image="${productImage}" data-spec-image="${specImage}" data-product-image-base="${productImageBase}" data-spec-image-base="${specImageBase}">
                            <td class="pos-product-image" data-label="Image">
                                ${(productImage || specImage) ? `<div class="d-flex gap-1">
                                    ${productImage ? `<img src="${productImageBase}/${productImage}" alt="${p.product_name}" class="img-fluid rounded" width="56" height="56">` : ''}
                                    ${specImage ? `<img src="${specImageBase}/${specImage}" alt="${p.product_name}" class="img-fluid rounded" width="56" height="56">` : ''}
                                </div>` : '<span class="text-muted small">No image</span>'}
                            </td>
                            <td class="pos-product-name" data-label="Name">${p.product_name || ''}</td>
                            <td class="pos-product-category" data-label="Category">${categoryName || ''}</td>
                            <td class="pos-product-code" data-label="Code">${p.product_code || ''}</td>
                            <td class="text-center" data-label="Qty">
                                <div class="pos-qty-control">
                                    <button type="button" class="pos-qty-btn pos-qty-minus">-</button>
                                    <input type="number" class="form-control form-control-sm text-center product-qty pos-input" min="1" value="1">
                                    <button type="button" class="pos-qty-btn pos-qty-plus">+</button>
                                </div>
                            </td>
                            <td class="text-end" data-label="Action">
                                <button class="btn btn-sm pos-add-btn add-to-cart">Add</button>
                            </td>
                        </tr>`;
                    $tbody.append(row);
                });
            }

            function renderPagination(pagination) {
                const $pagination = $('#products-pagination');
                $pagination.empty();

                const total = pagination.total || 0;
                const lastPage = pagination.last_page || 1;
                const current = pagination.current_page || 1;

                if (total === 0 || lastPage <= 1) {
                    return;
                }

                const prevDisabled = current <= 1 ? ' disabled' : '';
                $pagination.append(
                    `<li class="page-item${prevDisabled}">
                        <a class="page-link" href="#" data-page="${current - 1}">&laquo;</a>
                    </li>`
                );

                const pages = [];

                if (lastPage <= 7) {
                    for (let page = 1; page <= lastPage; page++) {
                        pages.push(page);
                    }
                } else {
                    if (current <= 4) {
                            for (let page = 1; page <= current + 1 && page <= lastPage; page++) {
                                pages.push(page);
                            }
                        if (current + 2 < lastPage) {
                            pages.push('ellipsis');
                        }
                        if (current + 1 < lastPage) {
                            pages.push(lastPage);
                        }
                    } else if (current >= lastPage - 3) {
                        pages.push(1);
                        if (lastPage - 4 > 2) {
                            pages.push('ellipsis');
                        }
                        const start = Math.max(2, lastPage - 4);
                        for (let page = start; page <= lastPage; page++) {
                            pages.push(page);
                        }
                    } else {
                        pages.push(1);
                        if (current - 2 > 2) {
                            pages.push('ellipsis');
                        }
                        for (let page = current - 1; page <= current + 1; page++) {
                            pages.push(page);
                        }
                        if (current + 2 < lastPage - 1) {
                            pages.push('ellipsis');
                        }
                        pages.push(lastPage);
                    }
                }

                pages.forEach(item => {
                    if (item === 'ellipsis') {
                        $pagination.append(
                            `<li class="page-item disabled">
                                <span class="page-link">&hellip;</span>
                            </li>`
                        );
                    } else {
                        const page = item;
                        const active = page === current ? ' active' : '';
                        $pagination.append(
                            `<li class="page-item${active}">
                                <a class="page-link" href="#" data-page="${page}">${page}</a>
                            </li>`
                        );
                    }
                });

                const nextDisabled = current >= lastPage ? ' disabled' : '';
                $pagination.append(
                    `<li class="page-item${nextDisabled}">
                        <a class="page-link" href="#" data-page="${current + 1}">&raquo;</a>
                    </li>`
                );
            }

            function loadProducts(page = 1, term = '') {
                currentPage = page;
                currentTerm = term;

                $.ajax({
                    url: '{{ route('pos.products') }}',
                    type: 'GET',
                    data: {
                        term: currentTerm,
                        page: currentPage,
                        per_page: perPage,
                    },
                    success: function (response) {
                        const products = response.data || [];
                        const pagination = response.pagination || {};

                        productsCache = products;

                        renderProducts(products);
                        renderPagination(pagination);
                    },
                    error: function () {
                        showAlert('Failed to load products.', 'danger');
                    }
                });
            }


            function searchCustomers(term = '') {
                $('#customer-results').empty();
                if (term.length < 1) {
                    return;
                }

                $.ajax({
                    url: '{{ route('pos.customers') }}',
                    type: 'GET',
                    data: {term},
                    success: function (data) {
                        const $list = $('#customer-results');
                        $list.empty();
                        if (!data.length) {
                            $list.append('<div class="list-group-item">No customers found.</div>');
                            return;
                        }

                        data.forEach(c => {
                            const item = `<button type="button" class="list-group-item list-group-item-action customer-item" data-id="${c.id}" data-name="${c.name}" data-email="${c.email}" data-phone="${c.phone || ''}">
                                        ${c.name} (${c.email}) ${c.phone ? ' - ' + c.phone : ''}
                                    </button>`;
                            $list.append(item);
                        });
                    },
                    error: function () {
                        showAlert('Failed to search customers.', 'danger');
                    }
                });
            }
            // Initial load
            loadProducts(1, '');

            // If we came back from create customer / project and have initial data, pre-select them
            if (initialCustomer) {
                $('#selected-customer-id').val(initialCustomer.id);
                $('#selected-customer-text').text(initialCustomer.name + (initialCustomer.email ? ' (' + initialCustomer.email + ')' : ''));
                selectedCustomerData = {
                    id: initialCustomer.id,
                    name: initialCustomer.name || '',
                    email: initialCustomer.email || '',
                    phone: initialCustomer.phone || '',
                };

                // Load projects for this customer, and if initialList matches, select it after load
                $.ajax({
                    url: '{{ route('get-lists') }}',
                    type: 'GET',
                    data: { customer_id: initialCustomer.id },
                    success: function (lists) {
                        const $select = $('#project-select');
                        const $reloadBtn = $('#reload-projects');
                        const $createLink = $('#create-project-link');

                        $select.empty();
                        if (!lists.length) {
                            $select.append('<option value="">No projects found</option>');
                        } else {
                            $select.append('<option value="">Select project</option>');
                            lists.forEach(function (list) {
                                $select.append('<option value="' + list.id + '">' + list.name + '</option>');
                            });
                        }

                        $select.prop('disabled', false);
                        $reloadBtn.prop('disabled', false);
                        $createLink.attr('href', '{{ url('/createlist') }}/' + initialCustomer.id + '?from_pos=1');

                        if (initialList && initialList.id) {
                            $('#project-select').val(initialList.id).trigger('change');
                        }
                    },
                    error: function () {
                        loadProjects(initialCustomer.id);
                    }
                });
            }

            // Product search
            $('#product-search').on('keyup', function () {
                const term = $(this).val();
                loadProducts(1, term);
            });

            // Product pagination
            $('#products-pagination').on('click', 'a.page-link', function (e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'), 10);

                if (!page || page === currentPage || page < 1) {
                    return;
                }

                loadProducts(page, currentTerm);
            });

            // Customer search
            $('#customer-search').on('keyup', function () {
                const term = $(this).val();
                searchCustomers(term);
            });

            // Select customer
            $('#customer-results').on('click', '.customer-item', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const email = $(this).data('email');
                const phone = $(this).data('phone') || '';

                $('#selected-customer-id').val(id);
                $('#selected-customer-text').text(name + ' (' + email + ')');
                $('#customer-results').empty();

                selectedCustomerData = { id, name, email, phone };
                resetCustomerPreview();

                loadProjects(id);
                refreshSummary();
                toggleCustomerSaveButton();
            });

            // Select project
            $('#project-select').on('change', function () {
                const value = $(this).val();
                $('#selected-project-id').val(value);
                selectedProjectId = value;
                refreshSummary();
                resetCustomerPreview();
                toggleCustomerSaveButton();
            });

            // 🔥 UPDATED: Reload projects click handler
            $('#reload-projects').on('click', function () {
                refreshProjects();
            });

            $('#customer-project-save').on('click', function () {
                const customerId = $('#selected-customer-id').val();
                const projectId = $('#selected-project-id').val();

                if (!customerId || !projectId) {
                    showAlert('Please select a customer and project before saving.', 'warning');
                    return;
                }

                if (!selectedCustomerData) {
                    const text = $('#selected-customer-text').text() || '';
                    selectedCustomerData = {
                        id: customerId,
                        name: text,
                        email: '',
                        phone: '',
                        project_name: '',
                    };
                } else {
                    selectedCustomerData.id = customerId;
                }

                const projectName = $('#project-select option:selected').text() || '';
                selectedCustomerData.project_name = projectName;

                showCustomerPreview();
            });

            // Product quantity plus/minus buttons
            $('#pos-products-table').on('click', '.pos-qty-plus, .pos-qty-minus', function () {
                const $control = $(this).closest('.pos-qty-control');
                const $input = $control.find('.product-qty');

                let current = parseInt($input.val(), 10);
                if (isNaN(current) || current < 1) {
                    current = 1;
                }

                if ($(this).hasClass('pos-qty-plus')) {
                    current += 1;
                } else {
                    current = Math.max(1, current - 1);
                }

                $input.val(current);
            });

            // Add to cart
            $('#pos-products-table').on('click', '.add-to-cart', function () {
                const $row = $(this).closest('tr');
                const id = $row.data('product-id');
                const name = $row.data('product-name');
                const price = parseFloat($row.data('product-price')) || 0;
                const qty = parseInt($row.find('.product-qty').val()) || 1;
                const productImage = $row.data('product-image') || '';
                const specImage = $row.data('spec-image') || '';
                const productImageBase = $row.data('product-image-base') || "{{ asset('images/products') }}";
                const specImageBase = $row.data('spec-image-base') || "{{ asset('images/products/specification') }}";

                if (!id || qty <= 0) {
                    return;
                }

                if (!cart[id]) {
                    cart[id] = {id, name, price, quantity: qty, productImage, specImage, productImageBase, specImageBase, comment: ''};
                } else {
                    cart[id].quantity += qty;
                }

                renderCart();
            });

            // Cart quantity plus/minus buttons
            $('#pos-cart-table').on('click', '.pos-qty-plus, .pos-qty-minus', function () {
                const $control = $(this).closest('.pos-qty-control');
                const $input = $control.find('.cart-qty');

                let current = parseInt($input.val(), 10);
                if (isNaN(current) || current < 1) {
                    current = 1;
                }

                if ($(this).hasClass('pos-qty-plus')) {
                    current += 1;
                } else {
                    current = Math.max(1, current - 1);
                }

                $input.val(current).trigger('change');
            });

            // Change quantity in cart
            $('#pos-cart-table').on('change', '.cart-qty', function () {
                const $row = $(this).closest('tr');
                const id = $row.data('product-id');
                const qty = parseInt($(this).val()) || 1;

                if (cart[id]) {
                    cart[id].quantity = qty;
                    renderCart();
                }
            });

            // Change comment in cart (per product)
            $('#pos-cart-table').on('input', '.cart-comment', function () {
                const $row = $(this).closest('tr');
                const id = $row.data('product-id');

                if (cart[id]) {
                    cart[id].comment = $(this).val();
                }
            });

            // Remove from cart
            $('#pos-cart-table').on('click', '.remove-from-cart', function () {
                const $row = $(this).closest('tr');
                const id = $row.data('product-id');

                if (cart[id]) {
                    delete cart[id];
                    renderCart();
                }
            });

            function submitOrder(actionType) {
                const customerId = $('#selected-customer-id').val();
                const projectId = $('#selected-project-id').val();
                const items = Object.values(cart).map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    comment: item.comment || '',
                }));

                const customerNameInput = $('#order-customer-name').val() || '';
                const signatureData = $('#signature-data').val() || '';

                if (!customerId) {
                    showAlert('Please select a customer.', 'warning');
                    return;
                }

                if (!projectId) {
                    showAlert('Please select a project.', 'warning');
                    return;
                }

                if (!items.length) {
                    showAlert('Please add at least one item to the cart.', 'warning');
                    return;
                }

                const $saveBtn = $('#save-order');
                const $saveSendBtn = $('#save-send-order');
                const $saveSendText = $('#save-send-text');
                const $saveSendLoader = $('#save-send-loader');

                // Disable buttons while request is in progress
                $saveBtn.prop('disabled', true);
                $saveSendBtn.prop('disabled', true);

                if (actionType === 'save_send') {
                    $saveSendLoader.removeClass('d-none');
                }

                $.ajax({
                    url: '{{ route('pos.orders.store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: customerId,
                        list_id: projectId,
                        items: items,
                        action_type: actionType,
                        customer_name: customerNameInput,
                        signature: signatureData,
                    },
                    success: function (response) {
                        if (response.status) {
                            cart = {};
                            renderCart();

                            if (response.redirect_url) {
                                const projectName = response.project_name || '';
                                const baseMessage = (response.message || 'Order saved successfully.') + (projectName ? ' Project: ' + projectName : '');

                                // For Save & Send, email is triggered after response; for Save, show normal success
                                if (actionType === 'save_send') {
                                    showAlert(response.message || 'Order saved successfully. Email will be sent shortly.', 'success');
                                } else {
                                    showAlert(baseMessage, 'success');
                                }

                                setTimeout(function () {
                                    window.location.href = response.redirect_url;
                                }, 1500);
                                return;
                            }

                            const projectName = response.project_name || '';
                            showAlert((response.message || 'Order saved successfully.') + (projectName ? ' Project: ' + projectName : ''));
                        } else {
                            showAlert(response.message || 'Failed to save order.', 'danger');
                        }
                    },
                    error: function (xhr) {
                        let message = 'Failed to save order.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showAlert(message, 'danger');
                    },
                    complete: function () {
                        // Re-enable buttons and hide loader
                        refreshSummary();
                        $saveSendLoader.addClass('d-none');
                    },
                });
            }

            const signatureCanvas = document.getElementById('signature-pad');
            if (signatureCanvas) {
                let signatureCtx = signatureCanvas.getContext('2d');
                let isDrawing = false;
                let lastX = 0;
                let lastY = 0;

                function initSignatureCanvas() {
                    const width = signatureCanvas.offsetWidth || 300;
                    const heightAttr = signatureCanvas.getAttribute('height');
                    const height = heightAttr ? parseInt(heightAttr, 10) || 140 : 140;
                    signatureCanvas.width = width;
                    signatureCanvas.height = height;
                    signatureCtx = signatureCanvas.getContext('2d');
                    signatureCtx.lineWidth = 2;
                    signatureCtx.lineCap = 'round';
                    signatureCtx.strokeStyle = '#000000';
                    $('#signature-data').val('');
                }

                function getSignaturePos(e) {
                    const rect = signatureCanvas.getBoundingClientRect();
                    let clientX;
                    let clientY;

                    if (e.touches && e.touches.length) {
                        clientX = e.touches[0].clientX;
                        clientY = e.touches[0].clientY;
                    } else {
                        clientX = e.clientX;
                        clientY = e.clientY;
                    }

                    return {
                        x: clientX - rect.left,
                        y: clientY - rect.top,
                    };
                }

                function updateSignatureData() {
                    try {
                        const dataUrl = signatureCanvas.toDataURL('image/png');
                        $('#signature-data').val(dataUrl);
                    } catch (err) {
                    }
                }

                function startSignature(e) {
                    e.preventDefault();
                    isDrawing = true;
                    const pos = getSignaturePos(e);
                    lastX = pos.x;
                    lastY = pos.y;
                }

                function drawSignature(e) {
                    if (!isDrawing) {
                        return;
                    }
                    e.preventDefault();
                    const pos = getSignaturePos(e);
                    signatureCtx.beginPath();
                    signatureCtx.moveTo(lastX, lastY);
                    signatureCtx.lineTo(pos.x, pos.y);
                    signatureCtx.stroke();
                    lastX = pos.x;
                    lastY = pos.y;
                }

                function endSignature(e) {
                    if (!isDrawing) {
                        return;
                    }
                    e.preventDefault();
                    isDrawing = false;
                    updateSignatureData();
                }

                initSignatureCanvas();

                signatureCanvas.addEventListener('mousedown', startSignature);
                signatureCanvas.addEventListener('mousemove', drawSignature);
                signatureCanvas.addEventListener('mouseup', endSignature);
                signatureCanvas.addEventListener('mouseleave', endSignature);

                signatureCanvas.addEventListener('touchstart', startSignature, {passive: false});
                signatureCanvas.addEventListener('touchmove', drawSignature, {passive: false});
                signatureCanvas.addEventListener('touchend', endSignature, {passive: false});
                signatureCanvas.addEventListener('touchcancel', endSignature, {passive: false});

                $('#clear-signature').on('click', function () {
                    signatureCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
                    $('#signature-data').val('');
                });

                $(window).on('resize', function () {
                    initSignatureCanvas();
                });
            }

            // Save only
            $('#save-order').on('click', function () {
                submitOrder('save');
            });

            // Save and send email
            $('#save-send-order').on('click', function () {
                submitOrder('save_send');
            });
        });
    </script>
@endpush