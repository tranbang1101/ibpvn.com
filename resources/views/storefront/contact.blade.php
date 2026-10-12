@extends('layouts.storefront')

@section('title', 'Liên hệ IBP Technology')

@section('content')
<main class="shop-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>Liên hệ</span>
        </nav>
        <div class="section-heading">
            <div>
                <span class="section-kicker">IBP TECHNOLOGY</span>
                <h1>{{ $sent ? 'Đã nhận yêu cầu tư vấn' : 'Liên hệ và đăng ký tư vấn' }}</h1>
            </div>
        </div>
        @if($demoMode)
            <section class="detail-card checkout-form-card">
                @if($demoNotice)
                    <div class="form-alert" role="status">Bản demo không lưu hoặc gửi yêu cầu tư vấn. Vui lòng gọi <a href="tel:0983537155">0983 537 155</a> để liên hệ IBP.</div>
                @else
                    <p>Biểu mẫu tư vấn đã tắt trong bản demo để bảo vệ thông tin cá nhân. Vui lòng gọi <a href="tel:0983537155">0983 537 155</a> nếu cần liên hệ IBP.</p>
                @endif
            </section>
        @elseif($sent)
            <section class="detail-card order-success-card" role="status">
                <p>Chuyên viên IBP sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
                <a class="button-primary" href="{{ route('products.index') }}">Xem sản phẩm</a>
            </section>
        @else
            <section class="detail-card checkout-form-card">
                <p>Gửi thông tin để đội ngũ IBP liên hệ tư vấn. Bạn cũng có thể gọi <a href="tel:0983537155">0983 537 155</a>.</p>
                @if($topic !== '')
                    <p>Chủ đề yêu cầu: <strong>{{ $topic }}</strong></p>
                @endif
                @if(session('contact_error'))
                    <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ session('contact_error') }}</div>
                @endif
                @if($errors->any())
                    <div class="form-alert" role="alert">{{ $errors->first() }}</div>
                @endif
                <form action="{{ route('contact', ['topic' => request('topic')]) }}" method="post" class="checkout-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ old('product_id', request('product_id')) }}">
                    <label>
                        Họ và tên
                        <input name="name" value="{{ old('name') }}" autocomplete="name" maxlength="150" required>
                    </label>
                    <div class="checkout-two-col">
                        <label>
                            Số điện thoại
                            <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="25" required>
                        </label>
                        <label>
                            Email (không bắt buộc)
                            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="190">
                        </label>
                    </div>
                    <label>
                        Danh mục quan tâm
                        <select name="product">
                            <option value="">Chưa chọn</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" @selected(old('product') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Nội dung
                        <textarea name="message" rows="4" maxlength="5000">{{ old('message') }}</textarea>
                    </label>
                    <button class="button-primary" type="submit">Gửi yêu cầu</button>
                </form>
            </section>
        @endif
    </div>
</main>
@endsection
