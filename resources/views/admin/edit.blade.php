@extends('layouts.app')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container">
    <div class="page-title">
        <p class="eyebrow">Quản lý cửa hàng</p>
        <h1>Sửa sản phẩm</h1>
        <p style="color:var(--muted)">Cập nhật thông tin cho {{ $product->name }}.</p>
    </div>

    <div class="form-card" style="width:100%;max-width:760px;margin-bottom:50px">
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('admin.products.update', $product) }}">
            @csrf
            @method('PUT')
            @include('admin._form')
            <div style="display:flex;gap:12px;margin-top:24px">
                <button class="button button-primary" type="submit">Lưu thay đổi</button>
                <a class="button" style="background:var(--line)" href="{{ route('admin.products.index') }}">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
