@extends('layouts.app')

@section('title', 'Thời trang hiện đại')

@section('content')
<section class="hero">
    <div class="hero-copy">
        <div class="hero-copy-inner">
            <p class="eyebrow">Không theo khuôn mẫu · Drop mới</p>
            <h1>Mặc chất.<br><span>Sống chất.</span></h1>
            <p class="hero-text">Đồ basic có gu, streetwear có chất. Chọn outfit nói lên đúng con người bạn — không cần phải giống ai.</p>
            <a class="button button-light" href="{{ route('products.index') }}">Khám phá bộ sưu tập <span aria-hidden="true">↗</span></a>
            <div class="hero-meta">LOCAL STYLE, BIG ENERGY</div>
        </div>
    </div>
</section>

<section class="benefits" aria-label="Dịch vụ của BAO">
    <div class="benefit"><span class="benefit-icon" aria-hidden="true">✳</span><div><strong>Chất liệu tuyển chọn</strong><span>Thoải mái từ sáng đến tối</span></div></div>
    <div class="benefit"><span class="benefit-icon" aria-hidden="true">↗</span><div><strong>Giao hàng toàn quốc</strong><span>Đơn giản, nhanh chóng</span></div></div>
    <div class="benefit"><span class="benefit-icon" aria-hidden="true">♡</span><div><strong>Mặc theo cách của bạn</strong><span>Không giới hạn phong cách</span></div></div>
</section>

<section class="container section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Pick của tụi mình</p>
            <h2>Đang được để ý.</h2>
        </div>
        <a class="text-link" href="{{ route('products.index') }}">Xem tất cả sản phẩm ↗</a>
    </div>
    <div class="product-grid">
        @forelse($products as $product)
            @include('partials.product-card', ['product' => $product])
        @empty
            <p class="empty-state">Chưa có sản phẩm. Hãy quay lại sau nhé.</p>
        @endforelse
    </div>
</section>

<section class="container">
    <div class="statement">
        <p>Outfit của bạn. Luật chơi của bạn. <span>Chọn chất riêng ngay hôm nay.</span></p>
        <a class="button" href="{{ route('products.index') }}">Khám phá BAO ↗</a>
    </div>
</section>
@endsection
