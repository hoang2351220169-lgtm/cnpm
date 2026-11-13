<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BAO FASHION') - BAO FASHION</title>
    <meta name="theme-color" content="#f6f6f2">
    <meta name="description" content="BAO — thời trang streetwear hiện đại, tạo dấu ấn riêng mỗi ngày.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="announcement"><span>DROP MỚI ĐÃ LÊN KỆ</span><span>Phong cách của bạn, luật chơi của bạn.</span><a href="{{ route('products.index') }}">Khám phá ngay ↗</a></div>
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}" aria-label="BAO, về trang chủ">BAO<span>.</span></a>
        <a class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Trang chủ</a>
        <a class="nav-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}" href="{{ route('products.index') }}">Cửa hàng</a>
        <form class="search" action="{{ route('products.index') }}" method="GET">
            <input name="q" value="{{ request('q') }}" placeholder="Tìm sản phẩm..." aria-label="Tìm sản phẩm">
            <button type="submit" aria-label="Tìm kiếm">↗</button>
        </form>
        <a class="cart-link" href="{{ route('cart.index') }}"><span class="cart-icon" aria-hidden="true">↗</span> Giỏ hàng <span class="count">{{ array_sum(session('cart', [])) }}</span></a>
        @auth
            <span class="nav-user">Xin chào, {{ auth()->user()->name }}</span>
            @if(auth()->user()->isAdmin())
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}" href="{{ route('admin.orders.index') }}">Đơn mua</a>
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}" href="{{ route('admin.products.index') }}">Quản lý</a>
            @else
                <a class="nav-link {{ request()->routeIs('orders.*') ? 'is-active' : '' }}" href="{{ route('orders.index') }}">Đơn hàng</a>
            @endif
            <form class="nav-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link nav-button" type="submit">Đăng xuất</button>
            </form>
        @else
            <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
            <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
        @endauth
    </div>
</header>
@if(session('success'))<div class="container"><div class="alert">{{ session('success') }}</div></div>@endif
@if(session('error'))<div class="container"><div class="error">{{ session('error') }}</div></div>@endif
<main>@yield('content')</main>
<footer class="site-footer">
    <div class="container footer-main">
        <a class="brand footer-brand" href="{{ route('home') }}">BAO<span>.</span></a>
        <p>Không cần theo trend.<br><strong>Tạo trend của riêng bạn.</strong></p>
        <a class="footer-shop" href="{{ route('products.index') }}">Mua sắm ngay ↗</a>
    </div>
    <div class="container footer-bottom"><span>© {{ date('Y') }} BAO. Mặc chất, sống chất.</span><span>Thiết kế cho thế hệ không ngại khác biệt.</span></div>
</footer>
</body>
</html>
