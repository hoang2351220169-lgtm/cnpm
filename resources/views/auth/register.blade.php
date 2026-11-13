@extends('layouts.app')

@section('title', 'Tạo tài khoản')

@section('content')
<section class="container auth-wrap register-wrap" aria-labelledby="register-title">
    <div class="register-layout">
        <aside class="register-intro">
            <p class="eyebrow">Thành viên BAO</p>
            <h1>Mặc chất.<br>Sống chất.</h1>
            <p>Tạo tài khoản miễn phí để đặt hàng và theo dõi đơn hàng của bạn.</p>
            <ul class="register-benefits">
                <li><span aria-hidden="true">01</span> Lưu thông tin tài khoản an toàn</li>
                <li><span aria-hidden="true">02</span> Theo dõi đơn hàng dễ dàng</li>
                <li><span aria-hidden="true">03</span> Mua sắm nhanh hơn lần sau</li>
            </ul>
        </aside>

        <div class="form-card auth-card register-card">
            <p class="eyebrow">Đăng ký</p>
            <h2 id="register-title">Tạo tài khoản</h2>
            <p class="register-description">Điền thông tin bên dưới để bắt đầu.</p>
        @if($errors->any())
            <div class="error" role="alert" aria-live="polite">
                Vui lòng kiểm tra lại thông tin trong biểu mẫu.
            </div>
        @endif
        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="form-group">
                <label for="name">Họ và tên <span aria-hidden="true">*</span></label>
                <input class="form-input" id="name" type="text" name="name" value="{{ old('name') }}" maxlength="255" autocomplete="name" placeholder="Ví dụ: Nguyễn Minh Anh" @if($errors->has('name')) aria-describedby="name-error" aria-invalid="true" @endif required autofocus>
                @error('name')<span class="field-error" id="name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="email">Email <span aria-hidden="true">*</span></label>
                <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" placeholder="ban@example.com" aria-describedby="email-help @if($errors->has('email')) email-error @endif" @if($errors->has('email')) aria-invalid="true" @endif required>
                <span class="field-help" id="email-help">Dùng email bạn thường xuyên truy cập.</span>
                @error('email')<span class="field-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu <span aria-hidden="true">*</span></label>
                <input class="form-input" id="password" type="password" name="password" minlength="8" autocomplete="new-password" placeholder="Tối thiểu 8 ký tự" aria-describedby="password-help @if($errors->has('password')) password-error @endif" @if($errors->has('password')) aria-invalid="true" @endif required>
                <span class="field-help" id="password-help">Ít nhất 8 ký tự. Không dùng mật khẩu email của bạn.</span>
                @error('password')<span class="field-error" id="password-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu <span aria-hidden="true">*</span></label>
                <input class="form-input" id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" placeholder="Nhập lại mật khẩu" aria-describedby="@if($errors->has('password')) password-error @endif" @if($errors->has('password')) aria-invalid="true" @endif required>
            </div>
            <button class="button button-primary register-submit" type="submit">Tạo tài khoản miễn phí <span aria-hidden="true">↗</span></button>
        </form>
            <p class="auth-switch">Đã có tài khoản? <a class="text-link" href="{{ route('login') }}">Đăng nhập</a></p>
            <p class="register-privacy">Thông tin của bạn được dùng để quản lý tài khoản và đơn hàng.</p>
        </div>
    </div>
</section>
@endsection
