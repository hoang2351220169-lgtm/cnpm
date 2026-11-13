@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container">
    <div class="page-title">
        <p class="eyebrow">Mua sắm của bạn</p>
        <h1>Giỏ hàng.</h1>
        <p>{{ array_sum($cart) }} item đang giữ chỗ cho outfit tiếp theo.</p>
    </div>

    @if(empty($cart))
        <div class="panel empty-cart">
            <span class="empty-cart-icon" aria-hidden="true">＋</span>
            <h2>Giỏ hàng đang trống.</h2>
            <p>Một outfit mới chỉ cách bạn vài cú click.</p>
            <a class="button button-primary" href="{{ route('products.index') }}">Khám phá sản phẩm ↗</a>
        </div>
    @else
        <div class="checkout-grid cart-layout">
            <section class="cart-items" aria-label="Sản phẩm trong giỏ">
                @foreach($cart as $id => $quantity)
                    @php($product = $products[$id])
                    <article class="cart-item">
                        <a class="cart-item-image" href="{{ route('products.show', $product) }}">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                        </a>
                        <div class="cart-item-info">
                            <p class="product-category">{{ $product->category?->name ?? 'BAO collection' }}</p>
                            <h2><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h2>
                            <p class="cart-item-price">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                            <p class="stock-note">Còn {{ $product->quantity }} sản phẩm</p>
                            <div class="cart-item-actions">
                                <form class="cart-quantity-form" method="POST" action="{{ route('cart.update', $product) }}">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only" for="quantity-{{ $product->id }}">Số lượng {{ $product->name }}</label>
                                    <input class="form-input" id="quantity-{{ $product->id }}" type="number" name="quantity" value="{{ $quantity }}" min="0" max="{{ $product->quantity }}">
                                    <button class="small-button" type="submit">Cập nhật</button>
                                </form>
                                <form method="POST" action="{{ route('cart.remove', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="cart-remove" type="submit">Xóa item</button>
                                </form>
                            </div>
                        </div>
                        <strong class="cart-item-subtotal">{{ number_format($product->price * $quantity, 0, ',', '.') }} đ</strong>
                    </article>
                @endforeach
                <a class="text-link cart-continue" href="{{ route('products.index') }}">← Tiếp tục khám phá</a>
            </section>
            <aside class="panel cart-summary">
                <p class="eyebrow">Đơn hàng</p>
                <h2>Tóm tắt giỏ.</h2>
                <div class="summary-row"><span>Tạm tính</span><strong>{{ number_format($total, 0, ',', '.') }} đ</strong></div>
                <div class="summary-row summary-total"><span>Tổng tiền sản phẩm</span><span>{{ number_format($total, 0, ',', '.') }} đ</span></div>
                @auth
                    <a class="button button-primary" href="{{ route('checkout.index') }}">Tiến hành thanh toán ↗</a>
                @else
                    <a class="button button-primary" href="{{ route('login') }}">Đăng nhập để thanh toán ↗</a>
                    <p class="checkout-hint">Chưa có tài khoản? Bạn có thể tạo miễn phí trong vài giây.</p>
                @endauth
                <div class="cart-trust">✓ Thanh toán an toàn <span>·</span> ✓ Hỗ trợ tận tình</div>
            </aside>
        </div>
    @endif
</div>
@endsection
