@extends('layouts.app')

@section('title', 'Cửa hàng')

@section('content')
<div class="container">
    <div class="page-title">
        <p class="eyebrow">Mặc đúng mood của bạn</p>
        <h1>Find your fit.</h1>
        <p>Đồ chất, dễ phối, luôn là chính mình.</p>
    </div>

    <div class="shop-toolbar">
        <span class="shop-count">{{ $products->total() }} sản phẩm đang chờ bạn</span>
        <form class="filters" method="GET" action="{{ route('products.index') }}">
            <input name="q" value="{{ request('q') }}" placeholder="Tìm tên hoặc mô tả..." aria-label="Tìm sản phẩm">
            <select name="category" aria-label="Lọc theo danh mục">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="button button-primary" type="submit">Lọc ↗</button>
            @if(request()->filled('q') || request()->filled('category'))
                <a class="button" href="{{ route('products.index') }}">Xóa lọc</a>
            @endif
        </form>
    </div>

    <div class="product-grid">
        @forelse($products as $product)
            @include('partials.product-card', ['product' => $product])
        @empty
            <div class="empty-state">
                <p>Chưa tìm thấy item phù hợp với bạn.</p>
                <a class="text-link" href="{{ route('products.index') }}">Xem tất cả sản phẩm ↗</a>
            </div>
        @endforelse
    </div>
    <div>{{ $products->links() }}</div>
</div>
@endsection
