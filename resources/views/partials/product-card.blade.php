<article class="product-card">
    <a class="product-image" href="{{ route('products.show', $product) }}" aria-label="Xem {{ $product->name }}">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
        <span class="product-tag">{{ $product->quantity > 0 ? 'BAO pick' : 'Hết hàng' }}</span>
    </a>
    <div class="product-info">
        @if($product->category)
            <p class="product-category">{{ $product->category->name }}</p>
        @endif
        <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        <p>{{ $product->description ?: 'Một item dễ phối, dành cho outfit mang dấu ấn riêng.' }}</p>
        <div class="product-foot">
            <span class="price">{{ number_format($product->price, 0, ',', '.') }} đ</span>
            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button class="small-button" type="submit" @disabled($product->quantity < 1)>
                    {{ $product->quantity > 0 ? '+ Thêm giỏ' : 'Hết hàng' }}
                </button>
            </form>
        </div>
    </div>
</article>
