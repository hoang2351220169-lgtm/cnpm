@extends('layouts.app')

@section('title', 'Đơn hàng #' . $order->id)

@section('content')
@php
    $statusLabels = [
        'pending' => 'Đang xử lý',
        'processing' => 'Đang chuẩn bị',
        'shipped' => 'Đang giao',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy',
    ];
@endphp
<div class="container">
    <div class="page-title">
        <p class="eyebrow">Cảm ơn bạn đã chọn BAO</p>
        <h1>Đơn #{{ $order->id }}.</h1>
        <p>Đặt lúc {{ $order->created_at->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y · H:i') }} · {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</p>
    </div>

    <div class="checkout-grid order-detail-layout">
        <section class="panel order-detail-items">
            <p class="eyebrow">Outfit đã chọn</p>
            <h2>Trong đơn hàng.</h2>
            @foreach($order->items as $item)
                <div class="order-line">
                    <div><strong>{{ $item->product_name }}</strong><span>Số lượng: {{ $item->quantity }}</span></div>
                    <strong>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</strong>
                </div>
            @endforeach
            <div class="summary-row summary-total"><span>Tổng cộng</span><span>{{ number_format($order->total, 0, ',', '.') }} đ</span></div>
        </section>
        <aside class="panel order-delivery">
            <p class="eyebrow">Giao đến</p>
            <h2>Thông tin nhận hàng.</h2>
            <p><strong>Người nhận</strong><span>{{ $order->user->name }}</span></p>
            <p><strong>Số điện thoại</strong><span>{{ $order->phone }}</span></p>
            <p><strong>Địa chỉ</strong><span>{{ $order->address }}</span></p>
            <p><strong>Thanh toán</strong><span>{{ $order->payment_method === 'cod' ? 'Khi nhận hàng' : 'Chuyển khoản ngân hàng' }}</span></p>
            <a class="button button-primary" href="{{ route('orders.index') }}">Tất cả đơn hàng</a>
        </aside>
    </div>
</div>
@endsection
