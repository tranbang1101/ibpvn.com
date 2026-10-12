@extends('layouts.storefront')

@section('title', 'Giỏ hàng | IBP Technology')

@section('content')
<main class="shop-page cart-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>Giỏ hàng</span>
        </nav>
        <div class="section-heading cart-heading">
            <div><span class="section-kicker">IBP TECHNOLOGY</span><h1>Giỏ hàng của bạn</h1></div>
            <span class="cart-item-count">{{ $itemCount }} sản phẩm</span>
        </div>
        @if(session('cart_added'))
            <div class="cart-notice" role="status"><i class="bi bi-check-circle-fill"></i>Sản phẩm đã được thêm vào giỏ hàng.</div>
        @endif
        @if(session('cart_notice'))
            <div class="cart-notice" role="status"><i class="bi bi-check-circle-fill"></i>{{ session('cart_notice') }}</div>
        @endif
        @if(session('cart_error'))
            <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ session('cart_error') }}</div>
        @endif
        @if($errors->any())
            <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ $errors->first() }}</div>
        @endif
        @if($lines === [])
            <section class="empty-cart detail-card">
                <div class="empty-cart-icon"><i class="bi bi-bag"></i></div>
                <h2>Giỏ hàng đang trống</h2>
                <p>Khám phá các giải pháp chăm sóc nguồn nước phù hợp cho gia đình bạn.</p>
                <a class="button-primary" href="{{ route('products.index') }}">Khám phá sản phẩm <i class="bi bi-arrow-right"></i></a>
            </section>
        @else
            <div class="cart-layout">
                <section class="cart-items-panel detail-card">
                    <div class="cart-table-head"><span>Sản phẩm</span><span>Đơn giá</span><span>Số lượng</span><span>Tạm tính</span></div>
                    @foreach($lines as $productId => $line)
                        @php($product = $line['product'])
                        <article class="cart-line">
                            <div class="cart-product">
                                <a href="{{ route('products.show', ['slug' => $product->slug]) }}">
                                    <img src="{{ $product->anh_chinh ? asset(ltrim(str_replace('/ibpvn.com/', '', $product->anh_chinh), '/')) : asset('assets/images/sanpham.png') }}" alt="{{ $product->ten }}">
                                </a>
                                <div>
                                    <a class="cart-product-name" href="{{ route('products.show', ['slug' => $product->slug]) }}">{{ $product->ten }}</a>
                                    <small>Chính hãng · Bảo hành theo nhà sản xuất</small>
                                    @if($line['variant'])<small>Phiên bản: {{ $line['variant']->ten_phien_ban }}</small>@endif
                                    @foreach($line['addons'] as $addon)
                                        <small>+ {{ $addon->ten }} · {{ number_format((float) ($addon->gia_khuyen_mai ?: $addon->gia_goc), 0, ',', '.') }}₫</small>
                                    @endforeach
                                    <form method="post" action="{{ route('cart.update') }}">
                                        @csrf
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="{{ $productId }}">
                                        <button class="remove-item" type="submit"><i class="bi bi-trash3"></i> Xóa sản phẩm</button>
                                    </form>
                                </div>
                            </div>
                            <strong class="cart-price">{{ number_format($line['unit_total'], 0, ',', '.') }}₫</strong>
                            <form method="post" action="{{ route('cart.update') }}" class="cart-quantity-form">
                                @csrf
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="{{ $productId }}">
                                <div class="quantity-control">
                                    <button type="button" data-cart-quantity="minus" aria-label="Giảm">−</button>
                                    <input type="number" name="quantity" min="1" max="99" value="{{ $line['quantity'] }}" aria-label="Số lượng">
                                    <button type="button" data-cart-quantity="plus" aria-label="Tăng">+</button>
                                </div>
                            </form>
                            <strong class="cart-line-total">{{ number_format($line['line_total'], 0, ',', '.') }}₫</strong>
                        </article>
                    @endforeach
                    <a class="continue-shopping" href="{{ route('products.index') }}"><i class="bi bi-arrow-left"></i> Tiếp tục mua sắm</a>
                </section>
                <aside class="cart-summary detail-card">
                    <h2>Tóm tắt đơn hàng</h2>
                    <div class="summary-row"><span>Tạm tính</span><b>{{ number_format($subtotal, 0, ',', '.') }}₫</b></div>
                    <div class="summary-row"><span>Giao hàng</span><b>{{ $shipping > 0 ? number_format($shipping, 0, ',', '.') . '₫' : 'Miễn phí' }}</b></div>
                    <div class="summary-row summary-total"><span>Tổng cộng</span><strong>{{ number_format($total, 0, ',', '.') }}₫</strong></div>
                    <small class="vat-note">Phí giao hàng áp dụng một lần theo mức cao nhất trong các sản phẩm không miễn phí.</small>
                    <a class="button-primary checkout-button" href="{{ route('cart.checkout') }}">Tiến hành đặt hàng <i class="bi bi-arrow-right"></i></a>
                    <div class="secure-note"><i class="bi bi-shield-check"></i> Thanh toán an toàn và bảo mật</div>
                    <div class="cart-support"><i class="bi bi-headset"></i><span>Cần hỗ trợ?<b> <a href="tel:0983537155">0983537155</a></b></span></div>
                </aside>
            </div>
        @endif
    </div>
</main>
@endsection
