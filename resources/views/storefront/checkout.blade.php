@extends('layouts.storefront')

@section('title', 'Hóa đơn và thông tin giao hàng | IBP Technology')

@section('content')
<main class="shop-page checkout-page invoice-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <a href="{{ route('cart.index') }}">Giỏ hàng</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>Hóa đơn</span>
        </nav>
        <div class="section-heading"><div><span class="section-kicker">ĐƠN HÀNG CỦA BẠN</span><h1>Thông tin hóa đơn</h1></div></div>
        <div class="checkout-layout">
            <section class="detail-card checkout-form-card">
                <h2>Thông tin nhận hàng</h2>
                @if($demoMode)
                    <p>Đây là thao tác thử nghiệm. Không cần nhập thông tin cá nhân; hệ thống không tạo đơn thật và không thay đổi tồn kho.</p>
                @else
                    <p>Kiểm tra sản phẩm và nhập địa chỉ để IBP xác nhận đơn hàng.</p>
                @endif
                @if(session('checkout_error'))
                    <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ session('checkout_error') }}</div>
                @endif
                @if($errors->any())
                    <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ $errors->first() }}</div>
                @endif
                <form method="post" action="{{ route('cart.checkout') }}" class="checkout-form">
                    @csrf
                    @unless($demoMode)
                        <label>Họ và tên<input name="name" value="{{ $customer['name'] }}" autocomplete="name" maxlength="150" required></label>
                        <div class="checkout-two-col">
                            <label>Email<input type="email" name="email" value="{{ $customer['email'] }}" autocomplete="email" maxlength="190" required></label>
                            <label>Số điện thoại<input type="tel" name="phone" value="{{ old('phone', auth()->user()?->so_dien_thoai) }}" autocomplete="tel" maxlength="25" required></label>
                        </div>
                        <label>Địa chỉ nhận hàng<input name="address" value="{{ old('address') }}" maxlength="2000" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành" autocomplete="street-address" required></label>
                        <label>Ghi chú đơn hàng <span>(không bắt buộc)</span><textarea name="note" rows="3" maxlength="5000" placeholder="Hướng dẫn giao hàng hoặc lắp đặt">{{ old('note') }}</textarea></label>
                    @endunless
                    <button class="button-primary checkout-button" type="submit">{{ $demoMode ? 'Thử đặt hàng (không tạo đơn thật)' : 'Xác nhận đặt hàng' }} · {{ number_format($total, 0, ',', '.') }}₫ <i class="bi bi-arrow-right"></i></button>
                </form>
                @unless($demoMode)
                    <div class="checkout-privacy"><i class="bi bi-lock"></i>Thông tin của bạn được bảo mật và chỉ dùng để xử lý đơn hàng.</div>
                @endunless
            </section>
            <aside class="detail-card checkout-summary">
                <h2>Chi tiết hóa đơn</h2>
                @foreach($lines as $line)
                    <div class="checkout-product">
                        @php($image = trim((string) $line['product']->anh_chinh))
                        @if(str_starts_with($image, '/ibpvn.com/'))@php($image = substr($image, strlen('/ibpvn.com/')))@endif
                        <img src="{{ $image !== '' ? asset(ltrim($image, '/')) : asset('assets/images/sanpham.png') }}" alt="">
                        <span>{{ $line['product']->ten }}
                            @if($line['variant'])<small>Phiên bản: {{ $line['variant']->ten_phien_ban }}</small>@endif
                            <small>Số lượng: {{ $line['quantity'] }}</small>
                        </span>
                        <b>{{ number_format($line['unit_price'] * $line['quantity'], 0, ',', '.') }}₫</b>
                    </div>
                    @foreach($line['addons'] as $addon)
                        @php($addonPrice = (float) ($addon->gia_khuyen_mai ?: $addon->gia_goc))
                        <div class="checkout-product invoice-addon">
                            @php($addonImage = trim((string) $addon->image_url))
                            @if(str_starts_with($addonImage, '/ibpvn.com/'))@php($addonImage = substr($addonImage, strlen('/ibpvn.com/')))@endif
                            <img src="{{ $addonImage !== '' ? asset(ltrim($addonImage, '/')) : asset('assets/images/loiloc.png') }}" alt="">
                            <span>+ {{ $addon->ten }}<small>Số lượng: {{ $line['quantity'] }}</small></span>
                            <b>{{ number_format($addonPrice * $line['quantity'], 0, ',', '.') }}₫</b>
                        </div>
                    @endforeach
                @endforeach
                <div class="summary-row"><span>Tạm tính</span><b>{{ number_format($subtotal, 0, ',', '.') }}₫</b></div>
                <div class="summary-row"><span>Giao hàng</span><b>{{ $shipping > 0 ? number_format($shipping, 0, ',', '.') . '₫' : 'Miễn phí' }}</b></div>
                <div class="summary-row summary-total"><span>Tổng cộng</span><strong>{{ number_format($total, 0, ',', '.') }}₫</strong></div>
                <div class="secure-note"><i class="bi bi-shield-check"></i>Thanh toán an toàn và bảo mật</div>
            </aside>
        </div>
    </div>
</main>
@endsection
