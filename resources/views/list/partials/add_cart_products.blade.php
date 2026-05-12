<div class="add-product-card-grid" id="product-card-grid">
    @forelse ($products as $product)
        <article class="add-product-card"
            data-search="{{ strtolower($product->product_name . ' ' . $product->product_code) }}">
            <div class="add-product-image">
                @if ($product->product_image)
                    <img src="{{ asset('images/products/' . $product->product_image) }}"
                        alt="{{ $product->product_name }}">
                @else
                    <span>No Image</span>
                @endif
                <small>{{ $product->product_code }}</small>
            </div>

            <div class="add-product-info">
                <div class="add-product-category">
                    @if (isset($product->category_names))
                        {{ implode(', ', $product->category_names) }}
                    @else
                        Product
                    @endif
                </div>
                <h3>{{ $product->product_name }}</h3>

                <div class="add-product-controls">
                    <div class="add-product-qty">
                        <span>Qty:</span>
                        <input type="number" name="quantity" value="1" min="0" required
                            class="form-control input-touchspin text-center"
                            data-product-id="{{ $product->id }}">
                    </div>
                    <textarea name="comment" class="form-control" rows="2" data-product-id="{{ $product->id }}"
                        placeholder="Enter a comment..."></textarea>
                    <button type="button" class="btn btn-primary add-to-cart rounded"
                        data-product-id="{{ $product->id }}">
                        <i class="ti ti-shopping-cart-plus me-1"></i>Add to Cart
                    </button>
                </div>
            </div>
        </article>
    @empty
        <div class="add-product-empty">
            {{ !empty($search) ? 'No products found for this search.' : 'No products found.' }}
        </div>
    @endforelse
</div>

@if (method_exists($products, 'links') && $products->hasPages())
    <div class="add-product-pagination">
        <div class="add-product-pagination-summary">
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
        </div>
        <nav class="add-product-pagination-nav" aria-label="Product pagination">
            @if ($products->onFirstPage())
                <span class="add-product-pagination-btn disabled">Previous</span>
            @else
                <a class="add-product-pagination-btn" href="{{ $products->previousPageUrl() }}">Previous</a>
            @endif

            @php
                $currentPage = $products->currentPage();
                $lastPage = $products->lastPage();
                $pages = collect([1, 2, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage - 1, $lastPage])
                    ->filter(fn ($page) => $page >= 1 && $page <= $lastPage)
                    ->unique()
                    ->sort()
                    ->values();
                $previousPage = 0;
            @endphp

            @foreach ($pages as $page)
                @if ($previousPage && $page > $previousPage + 1)
                    <span class="add-product-pagination-ellipsis">...</span>
                @endif

                @if ($page === $currentPage)
                    <span class="add-product-pagination-btn active">{{ $page }}</span>
                @else
                    <a class="add-product-pagination-btn" href="{{ $products->url($page) }}">{{ $page }}</a>
                @endif

                @php $previousPage = $page; @endphp
            @endforeach

            @if ($products->hasMorePages())
                <a class="add-product-pagination-btn" href="{{ $products->nextPageUrl() }}">Next</a>
            @else
                <span class="add-product-pagination-btn disabled">Next</span>
            @endif
        </nav>
    </div>
@endif
