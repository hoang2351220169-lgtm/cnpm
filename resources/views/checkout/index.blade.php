@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container">
    <div class="page-title">
        <p class="eyebrow">Bước cuối trước khi lên đồ</p>
        <h1>Thanh toán.</h1>
        <p>Kiểm tra thông tin nhận hàng và chọn cách thanh toán phù hợp.</p>
    </div>

    @if($errors->any())
        <div class="error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="checkout-grid checkout-layout">
        <form class="panel checkout-form" method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <div class="checkout-step"><span>01</span><div><h2>Thông tin nhận hàng</h2><p>Để BAO gửi đơn hàng đến đúng địa chỉ.</p></div></div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input class="form-input" id="phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="30" autocomplete="tel" inputmode="tel" placeholder="Ví dụ: 0901 234 567" required>
                @error('phone')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="address">Địa chỉ giao hàng</label>
                <textarea class="form-input" id="address" name="address" rows="3" maxlength="500" autocomplete="street-address" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành" required>{{ old('address') }}</textarea>
                @error('address')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="checkout-step checkout-payment-step"><span>02</span><div><h2>Thanh toán</h2><p>Thanh toán khi nhận hàng hoặc chuyển khoản.</p></div></div>
            <div class="form-group">
                <label for="payment_method">Phương thức thanh toán</label>
                <select class="form-input" id="payment_method" name="payment_method" required>
                    <option value="cod" @selected(old('payment_method', 'cod') === 'cod')>Thanh toán khi nhận hàng (COD)</option>
                    <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Chuyển khoản ngân hàng</option>
                </select>
                @error('payment_method')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <button class="button button-primary checkout-submit" type="submit">Xác nhận đặt hàng · {{ number_format($total, 0, ',', '.') }} đ</button>
            <a class="text-link checkout-back" href="{{ route('cart.index') }}">← Quay lại giỏ hàng</a>
        </form>

        <aside class="panel checkout-summary">
            <p class="eyebrow">Outfit sắp về với bạn</p>
            <h2>Đơn hàng.</h2>
            @foreach($cart as $id => $quantity)
                @php($product = $products[$id])
                <div class="checkout-product">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                    <div><strong>{{ $product->name }}</strong><span>Số lượng: {{ $quantity }}</span></div>
                    <strong>{{ number_format($product->price * $quantity, 0, ',', '.') }} đ</strong>
                </div>
            @endforeach
            <div class="summary-row"><span>Tạm tính</span><strong>{{ number_format($total, 0, ',', '.') }} đ</strong></div>
            <div class="summary-row summary-total"><span>Tổng tiền sản phẩm</span><span>{{ number_format($total, 0, ',', '.') }} đ</span></div>
            <p class="checkout-hint">✓ Thông tin của bạn được dùng chỉ để xử lý đơn hàng.</p>
        </aside>
    </div>
</div>
@endsection
