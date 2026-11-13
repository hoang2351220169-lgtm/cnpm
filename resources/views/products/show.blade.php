@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container section">
    <div class="checkout-grid">
        <div class="detail-image">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
        </div>
        <div class="detail-copy">
            <p class="eyebrow">{{ $product->category?->name ?? 'BAO collection' }}</p>
            <h1>{{ $product->name }}</h1>
            <p class="detail-description">{{ $product->description ?: 'Một item dễ phối, dành cho outfit mang dấu ấn riêng.' }}</p>
            <p class="price detail-price">{{ number_format($product->price, 0, ',', '.') }} đ</p>
            <p class="stock-note">
                {{ $product->quantity > 0 ? 'Còn ' . $product->quantity . ' sản phẩm · Sẵn sàng lên đồ' : 'Sản phẩm hiện đã hết hàng' }}
            </p>
            <form class="detail-form" method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <input class="form-input" type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->quantity) }}" aria-label="Số lượng" @disabled($product->quantity < 1)>
                <button class="button button-primary" type="submit" @disabled($product->quantity < 1)>
                    {{ $product->quantity > 0 ? 'Thêm vào giỏ hàng ↗' : 'Tạm hết hàng' }}
                </button>
            </form>
            <p class="stock-note" style="margin-top:22px">Thanh toán an toàn · Hỗ trợ giao hàng toàn quốc</p>
        </div>
    </div>
</div>
@endsection
