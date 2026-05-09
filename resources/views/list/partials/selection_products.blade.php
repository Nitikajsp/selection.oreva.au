<div class="selection-product-card-list">
    @forelse ($orders as $index => $order)
        @php
            $categoryNames = [];
            if ($order->product) {
                foreach (explode(',', $order->product->product_category) as $categoryId) {
                    $categoryNames[] = $categories[$categoryId] ?? 'Unknown';
                }
            }
        @endphp
        <article class="selection-product-card-item">
            <div class="selection-card-image">
                @if ($order->product && $order->product->product_image)
                    <img src="{{ asset('images/products/' . $order->product->product_image) }}"
                        alt="{{ $order->product->product_name ?? 'Product image' }}">
                @else
                    <span>No Image</span>
                @endif
                <small>{{ $order->product->product_code ?? $order->product_code }}</small>
            </div>

            <div class="selection-card-detail">
                <div class="selection-card-category">
                    {{ implode(', ', $categoryNames) ?: 'Unknown' }}
                </div>
                <h3>{{ $order->product->product_name ?? $order->product_name ?? 'Unknown Product' }}</h3>

                <div class="selection-card-meta">
                    <div>
                        <span>Property Address</span>
                        <p>{{ $projectAddress ?: 'No site address provided.' }}</p>
                    </div>
                    <div>
                        <span>Comment</span>
                        <p>{{ $order->comment ?: 'No notes provided for this item.' }}</p>
                    </div>
                </div>

                <div class="selection-card-qty">
                    <span>Qty:</span>
                    <input type="number" data-order-id="{{ $order->id }}" data-initial="{{ $order->quantity }}"
                        value="{{ $order->quantity }}" min="0" required
                        class="form-control input-touchspin text-center border quantity-input">
                </div>
            </div>

            <form action="{{ route('orders.destroyOrders', ['order' => $order->id]) }}" method="POST"
                class="selection-delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="selection-card-delete delete-btn" data-bs-toggle="modal"
                    data-bs-target="#deleteModal" data-form-id="order-form-{{ $order->id }}" data-delete-type="item">
                    <i class="ti ti-trash"></i>
                </button>
            </form>
        </article>
    @empty
        <div class="selection-empty-state">
            {{ !empty($search) ? 'No products found for this search.' : 'No products added yet.' }}
        </div>
    @endforelse
</div>

@if (method_exists($orders, 'links') && $orders->hasPages())
    <div class="selection-card-pagination">
        <div class="selection-pagination-summary">
            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
        </div>
        <nav class="selection-pagination-nav" aria-label="Product pagination">
            @if ($orders->onFirstPage())
                <span class="selection-pagination-btn disabled">Previous</span>
            @else
                <a class="selection-pagination-btn" href="{{ $orders->previousPageUrl() }}">Previous</a>
            @endif

            @for ($page = 1; $page <= $orders->lastPage(); $page++)
                @if ($page === $orders->currentPage())
                    <span class="selection-pagination-btn active">{{ $page }}</span>
                @else
                    <a class="selection-pagination-btn" href="{{ $orders->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($orders->hasMorePages())
                <a class="selection-pagination-btn" href="{{ $orders->nextPageUrl() }}">Next</a>
            @else
                <span class="selection-pagination-btn disabled">Next</span>
            @endif
        </nav>
    </div>
@endif
