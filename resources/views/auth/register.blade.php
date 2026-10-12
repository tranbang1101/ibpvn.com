@extends('layouts.storefront')

@section('title', 'Tạo tài khoản | IBP Technology')

@section('content')
<main class="auth-page">
    <div class="auth-wrap">
        <aside class="auth-aside">
            <img src="{{ asset('assets/images/logo_IBP.png') }}" alt="IBP Technology">
            <span class="auth-eyebrow">IBP MEMBER</span>
            <h1>Chăm sóc nguồn nước, chăm sóc tổ ấm.</h1>
            <p>Tạo tài khoản để theo dõi đơn hàng và gửi yêu cầu tư vấn thuận tiện hơn.</p>
            <div class="auth-aside-points">
                <span><i class="bi bi-patch-check"></i> Sản phẩm chính hãng</span>
                <span><i class="bi bi-truck"></i> Giao hàng tận tâm</span>
                <span><i class="bi bi-headset"></i> Hỗ trợ trọn vòng đời</span>
            </div>
            <div class="auth-aside-orb"></div>
        </aside>
        <section class="auth-card">
            <a class="auth-back" href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Về trang chủ</a>
            <span class="auth-eyebrow">BẮT ĐẦU CÙNG IBP</span>
            <h2>Tạo tài khoản</h2>
            <p class="auth-subtitle">Đăng ký nhanh để trải nghiệm mua sắm tốt hơn.</p>
            @if(session('auth_error'))
                <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ session('auth_error') }}</div>
            @endif
            @if($errors->any())
                <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('register') }}" class="auth-form" autocomplete="off">
                @csrf
                <input type="hidden" name="redirect" value="{{ $redirect }}">
                <label>
                    Tên người dùng
                    <input type="text" name="username" value="{{ old('username') }}" autocomplete="username" maxlength="150" placeholder="Ví dụ: minh anh" required>
                </label>
                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="190" placeholder="ban@email.com" required>
                </label>
                <label>
                    Số điện thoại <span>(không bắt buộc)</span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="25" placeholder="090 123 4567">
                </label>
                <label>
                    Mật khẩu
                    <input type="password" name="password" autocomplete="new-password" minlength="8" placeholder="Ít nhất 8 ký tự" required>
                </label>
                <label>
                    Nhập lại mật khẩu
                    <input type="password" name="confirm_password" autocomplete="new-password" minlength="8" required>
                </label>
                <label class="auth-terms">
                    <input type="checkbox" name="terms" value="1" required @checked(old('terms'))>
                    <span>Tôi đồng ý với <a href="{{ route('contact') }}">Điều khoản sử dụng</a> và chính sách bảo mật.</span>
                </label>
                <button class="button-primary auth-submit" type="submit">Tạo tài khoản <i class="bi bi-arrow-right"></i></button>
            </form>
            <p class="auth-switch">Bạn đã có tài khoản? <a href="{{ route('login', ['redirect' => $redirect]) }}">Đăng nhập</a></p>
            <div class="auth-secure"><i class="bi bi-shield-lock"></i>Thông tin của bạn được bảo mật an toàn</div>
        </section>
    </div>
</main>
@endsection
