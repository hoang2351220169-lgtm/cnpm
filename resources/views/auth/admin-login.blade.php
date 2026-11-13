@extends('layouts.app')

@section('title', 'Đăng nhập quản trị')

@section('content')
<div class="container auth-wrap">
    <div class="form-card auth-card">
        <p class="eyebrow">BAO FASHION · Quản trị</p>
        <h1>Đăng nhập admin.</h1>
        <p>Đăng nhập bằng tài khoản đã được cấp quyền quản trị.</p>
        @if($errors->has('email') && !$errors->has('password'))
            <div class="error" role="alert" aria-live="polite">{{ $errors->first('email') }}</div>
        @elseif($errors->has('password'))
            <div class="error" role="alert" aria-live="polite">Vui lòng kiểm tra thông tin đăng nhập quản trị.</div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email quản trị</label>
                <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" @if($errors->has('email')) aria-invalid="true" @endif required autofocus>
                @if($errors->has('email'))<span class="field-error" role="alert">{{ $errors->first('email') }}</span>@endif
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input class="form-input" id="password" type="password" name="password" autocomplete="current-password" @if($errors->has('password')) aria-invalid="true" @endif required>
                @if($errors->has('password'))<span class="field-error" role="alert">{{ $errors->first('password') }}</span>@endif
            </div>
            <button class="button button-primary" type="submit">Đăng nhập quản trị ↗</button>
        </form>
        <p class="auth-switch"><a class="text-link" href="{{ route('login') }}">Đăng nhập khách hàng</a></p>
    </div>
</div>
@endsection
