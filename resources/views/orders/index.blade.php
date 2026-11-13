@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container">
    <div class="page-title">
        <p class="eyebrow">Mỗi đơn hàng là một outfit mới</p>
        <h1>Đơn hàng.</h1>
        <p>Theo dõi những item đang trên đường đến với bạn.</p>
    </div>

    @if($orders->isEmpty())
        <div class="panel empty-cart">
            <span class="empty-cart-icon" aria-hidden="true">↗</span>
            <h2>Chưa có đơn hàng nào.</h2>
            <p>Khám phá bộ sưu tập và chọn item hợp gu bạn nhé.</p>
            <a class="button button-primary" href="{{ route('products.index') }}">Bắt đầu mua sắm ↗</a>
        </div>
    @else
        <div class="order-list">
            @foreach($orders as $order)
                @php
                    $statusLabels = [
                        'pending' => 'Đang xử lý',
                        'processing' => 'Đang chuẩn bị',
                        'shipped' => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'cancelled' => 'Đã hủy',
                    ];
                    $statusClass = in_array($order->status, ['delivered', 'cancelled']) ? $order->status : 'in-progress';
                @endphp
                <a class="order-card" href="{{ route('orders.show', $order) }}">
                    <div class="order-card-top">
                        <div>
                            <span class="product-category">Mã đơn #{{ $order->id }}</span>
                            <h2>{{ $order->created_at->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y · H:i') }}</h2>
                        </div>
                        <span class="order-status order-status-{{ $statusClass }}">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span>
                    </div>
                    <div class="order-card-bottom">
                        <span>{{ $order->items->sum('quantity') }} sản phẩm</span>
                        <strong>{{ number_format($order->total, 0, ',', '.') }} đ</strong>
                        <span class="order-open">Xem chi tiết ↗</span>
                    </div>
                </a>
            @endforeach
        </div>
        {{ $orders->links() }}
    @endif
</div>
@endsection
