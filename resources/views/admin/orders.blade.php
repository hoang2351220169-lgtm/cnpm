@extends('layouts.app')

@section('title', 'Theo dõi đơn hàng')

@section('content')
<div class="container">
    <div class="page-title">
        <p class="eyebrow">BAO FASHION · Quản trị</p>
        <h1>Theo dõi đơn hàng</h1>
        <p style="color:var(--muted)">Danh sách các đơn đã mua và thông tin giao hàng.</p>
    </div>

    <div class="admin-toolbar">
        <span>{{ $orders->total() }} đơn hàng</span>
        <a class="button button-primary" href="{{ route('admin.products.index') }}">Quản lý sản phẩm</a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Giao đến</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $statusLabels = [
                            'pending' => 'Đang xử lý',
                            'processing' => 'Đang chuẩn bị',
                            'shipped' => 'Đang giao',
                            'delivered' => 'Đã giao',
                            'cancelled' => 'Đã hủy',
                        ];
                    @endphp
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name }}<br><small>{{ $order->user->email }}</small></td>
                        <td>
                            @foreach($order->items as $item)
                                <div>{{ $item->product_name }} × {{ $item->quantity }}</div>
                            @endforeach
                        </td>
                        <td>{{ $order->phone }}<br><small>{{ $order->address }}</small></td>
                        <td>{{ number_format($order->total, 0, ',', '.') }} đ</td>
                        <td>{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</td>
                        <td>{{ $order->created_at->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="table-actions">
                                @csrf
                                @method('PATCH')
                                <select class="form-input" name="status" aria-label="Trạng thái đơn #{{ $order->id }}">
                                    @foreach($statusLabels as $value => $label)
                                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="button button-small button-primary" type="submit">Lưu</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-state">Chưa có đơn hàng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin:24px 0">{{ $orders->links() }}</div>
</div>
@endsection
