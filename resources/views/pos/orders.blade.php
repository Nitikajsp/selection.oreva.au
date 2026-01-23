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

                    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <h1 class="mb-0">POS Orders</h1>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="d-flex">
                                <input type="text"
                                       id="pos-orders-search"
                                       class="form-control form-control-sm"
                                       placeholder="Search by customer or order #">
                            </div>
                            <a href="{{ route('pos.index') }}" class="btn btn-outline-primary ms-2">Back to POS</a>
                        </div>
                    </div>

                    <div class="container addcustomer_pad pos-page">
                        <div class="row g-3" id="pos-orders-container">
                            @forelse ($orders as $order)
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <div class="fw-bold">{{ $order['customer_name'] ?? 'Walk-in customer' }}</div>
                                                    <div class="text-muted small">Order #{{ $order['order_number'] ?? '' }}</div>
                                                </div>
                                                {{-- <span class="badge bg-primary">PROCESSING</span> --}}
                                            </div>
                                            <div class="row small mb-3">
                                                <div class="col-6">
                                                    <div class="text-muted">Date</div>
                                                    <div>{{ $order['order_date'] ?? '-' }}</div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="text-muted">Time</div>
                                                    <div>{{ $order['order_time'] ?? '-' }}</div>
                                                </div>
                                                <div class="col-3 text-end">
                                                    <div class="text-muted">Items</div>
                                                    <div>{{ $order['items_count'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="text-muted small mb-1">Items</div>
                                                @if (!empty($order['items']))
                                                    @foreach ($order['items'] as $item)
                                                        <div class="d-flex justify-content-between small mb-1">
                                                            <div>
                                                                <span class="fw-semibold">{{ sprintf('%02d', $item['quantity'] ?? 0) }}</span>
                                                                <span class="ms-1">{{ $item['product_name'] ?? '' }}</span>
                                                            </div>
                                                            <div class="fw-semibold">${{ number_format($item['price'] ?? 0, 2) }}</div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-muted small">No items.</div>
                                                @endif
                                            </div>
                                            <div class="border-top pt-2 small">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Subtotal</span>
                                                    <span>${{ number_format($order['subtotal'] ?? 0, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between fw-semibold">
                                                    <span>Grand total</span>
                                                    <span>${{ number_format($order['grand_total'] ?? ($order['subtotal'] ?? 0), 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center text-muted py-5">No POS orders found.</div>
                                </div>
                            @endforelse
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
            function renderPosOrderCards(orders) {
                const $container = $('#pos-orders-container');
                $container.empty();

                if (!orders.length) {
                    $container.append('<div class="col-12"><div class="text-center text-muted py-5">No POS orders found.</div></div>');
                    return;
                }

                orders.forEach(function (order) {
                    const subtotal = parseFloat(order.subtotal || 0);
                    const grandTotal = parseFloat(order.grand_total || subtotal || 0);

                    let itemsHtml = '';
                    (order.items || []).forEach(function (item) {
                        const qty = item.quantity || 0;
                        const name = item.product_name || '';
                        const price = parseFloat(item.price || 0).toFixed(2);
                        itemsHtml += `
                            <div class="d-flex justify-content-between small mb-1">
                                <div>
                                    <span class="fw-semibold">${qty.toString().padStart(2, '0')}</span>
                                    <span class="ms-1">${name}</span>
                                </div>
                                <div class="fw-semibold">$${price}</div>
                            </div>`;
                    });

                    const cardHtml = `
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-bold">${order.customer_name || 'Walk-in customer'}</div>
                                            <div class="text-muted small">Order #${order.order_number || ''}</div>
                                        </div>
                                    </div>
                                    <div class="row small mb-3">
                                        <div class="col-6">
                                            <div class="text-muted">Date</div>
                                            <div>${order.order_date || '-'}</div>
                                        </div>
                                        <div class="col-3">
                                            <div class="text-muted">Time</div>
                                            <div>${order.order_time || '-'}</div>
                                        </div>
                                        <div class="col-3 text-end">
                                            <div class="text-muted">Items</div>
                                            <div>${order.items_count || 0}</div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1">Items</div>
                                        ${itemsHtml || '<div class="text-muted small">No items.</div>'}
                                    </div>
                                    <div class="border-top pt-2 small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Subtotal</span>
                                            <span>$${subtotal.toFixed(2)}</span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-semibold">
                                            <span>Grand total</span>
                                            <span>$${grandTotal.toFixed(2)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    $container.append(cardHtml);
                });
            }

            function loadPosOrders(term) {
                $.ajax({
                    url: '{{ route('pos.orders.index') }}',
                    type: 'GET',
                    data: term ? { q: term } : {},
                    success: function (response) {
                        const orders = response.data || [];
                        renderPosOrderCards(orders);
                    }
                });
            }

            $('#pos-orders-search').on('keyup', function () {
                const term = $(this).val().trim();
                loadPosOrders(term);
            });
        });
    </script>
@endpush
