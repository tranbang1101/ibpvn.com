@extends('layouts.storefront')

@section('title', 'Đăng nhập | IBP Technology')

@section('content')
<main class="auth-page">
    <div class="auth-wrap">
        <aside class="auth-aside">
            <img src="{{ asset('assets/images/logo_IBP.png') }}" alt="IBP Technology">
            <span class="auth-eyebrow">WELCOME BACK</span>
            <h1>Giải pháp nước tốt hơn bắt đầu từ đây.</h1>
            <p>Đăng nhập để theo dõi đơn hàng và gửi yêu cầu hỗ trợ thuận tiện hơn.</p>
            <div class="auth-aside-points">
                <span><i class="bi bi-patch-check"></i> Sản phẩm chính hãng</span>
                <span><i class="bi bi-truck"></i> Giao hàng tận tâm</span>
                <span><i class="bi bi-headset"></i> Hỗ trợ trọn vòng đời</span>
            </div>
            <div class="auth-aside-orb"></div>
        </aside>
        <section class="auth-card">
            <a class="auth-back" href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Về trang chủ</a>
            <span class="auth-eyebrow">IBP MEMBER</span>
            <h2>Chào mừng trở lại</h2>
            <p class="auth-subtitle">Đăng nhập vào tài khoản IBP của bạn.</p>
            @if($checkoutRequired)
                <div class="form-alert" role="status"><i class="bi bi-info-circle"></i>Quý khách vui lòng đăng nhập hoặc đăng ký tài khoản để tiếp tục thanh toán đơn hàng.</div>
            @endif
            @if(session('auth_error'))
                <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ session('auth_error') }}</div>
            @endif
            @if($errors->any())
                <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('login') }}" class="auth-form" autocomplete="on">
                @csrf
                <input type="hidden" name="redirect" value="{{ $redirect }}">
                <label>
                    Tên người dùng hoặc Email
                    <input type="text" name="username" value="{{ old('username') }}" autocomplete="username" placeholder="Tên người dùng hoặc email" required>
                </label>
                <label>
                    Mật khẩu
                    <input type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu" required>
                </label>
                <div class="auth-options"><a href="mailto:info@ibpvn.com?subject=Password%20recovery">Liên hệ để khôi phục mật khẩu</a></div>
                <button class="button-primary auth-submit" type="submit">Đăng nhập <i class="bi bi-arrow-right"></i></button>
            </form>
            <p class="auth-switch">Chưa có tài khoản? <a href="{{ route('register', ['redirect' => $redirect]) }}">Đăng ký ngay</a></p>
            <div class="auth-secure"><i class="bi bi-shield-lock"></i>Thông tin của bạn được bảo mật an toàn</div>
        </section>
    </div>
</main>
@endsection
