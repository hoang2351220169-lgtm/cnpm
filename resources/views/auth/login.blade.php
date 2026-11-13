@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="container auth-wrap">
    <div class="form-card auth-card">
        <p class="eyebrow">Chào mừng trở lại</p>
        <h1>Vào BAO thôi.</h1>
        <p>Đăng nhập để theo dõi đơn hàng và tiếp tục hành trình mặc chất.</p>
        @if($errors->has('email') && !$errors->has('password'))
            <div class="error" role="alert" aria-live="polite">{{ $errors->first('email') }}</div>
        @elseif($errors->has('password'))
            <div class="error" role="alert" aria-live="polite">Vui lòng kiểm tra thông tin đăng nhập.</div>
        @endif
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" @if($errors->has('email')) aria-invalid="true" @endif required autofocus>
                @if($errors->has('email'))<span class="field-error" role="alert">{{ $errors->first('email') }}</span>@endif
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input class="form-input" id="password" type="password" name="password" autocomplete="current-password" @if($errors->has('password')) aria-invalid="true" @endif required>
                @if($errors->has('password'))<span class="field-error" role="alert">{{ $errors->first('password') }}</span>@endif
            </div>
            <button class="button button-primary" type="submit">Đăng nhập ↗</button>
        </form>
        <p class="auth-switch">Chưa có tài khoản? <a class="text-link" href="{{ route('register') }}">Đăng ký miễn phí</a></p>
        <p class="auth-switch">Quản trị viên? <a class="text-link" href="{{ route('admin.login') }}">Đăng nhập quản trị</a></p>
    </div>
</div>
@endsection
