@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="container">
    <div class="page-title">
        <p class="eyebrow">BAO FASHION · Quản trị</p>
        <h1>Quản lý sản phẩm</h1>
        <p style="color:var(--muted)">Thêm, chỉnh sửa hoặc xóa sản phẩm trong cửa hàng.</p>
    </div>

    <div class="admin-toolbar">
        <span>{{ $products->total() }} sản phẩm</span>
        <a class="button button-primary" href="{{ route('admin.products.create') }}">+ Thêm sản phẩm</a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <img class="table-image" src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? 'Chưa phân loại' }}</td>
                        <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>
                        <td>{{ $product->quantity }}</td>
                        <td>
                            <div class="table-actions">
                                <a class="button button-small" style="background:var(--line)" href="{{ route('admin.products.edit', $product) }}">Sửa</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button button-danger button-small" type="submit">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Chưa có sản phẩm nào. Hãy thêm sản phẩm đầu tiên.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin:24px 0">{{ $products->links() }}</div>
</div>
@endsection
