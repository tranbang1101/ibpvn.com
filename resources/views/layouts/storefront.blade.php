<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IBP Technology')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/fancybox.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12.2.0/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css?v=11') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/shop.css?v=11') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-responsive.css?v=17') }}">
    @if(config('store.demo_mode'))
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css?v=1') }}">
    @endif
</head>
<body>
@if(config('store.demo_mode'))
    <div class="demo-mode-banner" role="status">
        <strong>BẢN DEMO CÔNG KHAI</strong>
        <span>Đơn hàng chỉ mô phỏng; đăng nhập, tra cứu đơn và gửi yêu cầu tư vấn đã tắt. Không nhập thông tin cá nhân.</span>
    </div>
@endif
<header class="site-header">
    <div class="header-banner">
        <a href="{{ route('products.index') }}" class="header-banner-link">
            <img src="{{ asset('assets/images/header_promotion.png') }}" alt="Đặt hàng Online - Nhận ngay ưu đãi" class="header-banner-image">
        </a>
    </div>
    <div class="header-top">
        <div class="header-top-inner">
            <div class="header-top-message">{{ config('store.demo_mode') ? 'Trải nghiệm bản demo của IBP Technology' : 'Đăng nhập để theo dõi đơn hàng và nhận hỗ trợ từ IBP' }}</div>
            <div class="header-top-links">
                <a href="mailto:info@ibpvn.com?subject=Dealer%20application" class="header-top-link"><span>Tuyển đại lý</span></a>
                <a href="{{ route('home') }}#about" class="header-top-link"><span>Giới thiệu</span></a>
                <a href="{{ route('contact') }}" class="header-top-link"><span>Liên hệ</span></a>
            </div>
        </div>
    </div>
    <div class="header-main">
        <div class="header-main-inner">
            <div class="mobile-header-tools">
                <button class="mobile-menu-toggle" type="button" aria-label="Mở danh mục sản phẩm" aria-controls="mobile-category-panel" aria-expanded="false">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <a class="mobile-shop-link" href="{{ route('products.index') }}"><i class="bi bi-shop" aria-hidden="true"></i><span>Xem Shop</span></a>
            </div>
            <a href="{{ route('home') }}" class="header-logo"><img src="{{ asset('assets/images/logo_IBP.png') }}" alt="IBP Technology" class="header-logo-image"></a>
            <form action="{{ route('products.index') }}" method="get" class="header-search">
                <input type="text" name="keyword" class="header-search-input" placeholder="Tìm sản phẩm..." value="{{ request('keyword') }}" autocomplete="off">
                <button type="submit" class="header-search-button" aria-label="Tìm kiếm"><i class="bi bi-search"></i></button>
            </form>
            <button class="mobile-search-toggle" type="button" aria-label="Tìm kiếm" aria-expanded="false"><i class="bi bi-search" aria-hidden="true"></i></button>
            <div class="header-actions">
                @unless(config('store.demo_mode'))
                    <a href="{{ route('orders.track') }}" class="header-action header-order-check"><i class="bi bi-file-earmark-text header-action-icon"></i><span class="header-action-text">Kiểm tra đơn hàng</span></a>
                    @auth
                        <div class="header-action header-account">
                            <i class="bi bi-person-circle header-action-icon"></i>
                            <span class="header-action-text">{{ auth()->user()->ho_ten }}</span>
                            <form action="{{ route('logout') }}" method="post">@csrf<button type="submit" class="header-logout">(Thoát)</button></form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="header-action header-account"><i class="bi bi-person-circle header-action-icon"></i><span class="header-action-text">Đăng nhập/Đăng ký</span></a>
                    @endauth
                @endunless
                <a href="{{ route('cart.index') }}" class="header-action header-cart">
                    <span class="header-cart-icon-wrap"><i class="bi bi-cart3 header-action-icon"></i><span class="cart-count">{{ array_sum(session('cart', [])) }}</span></span>
                    <span class="header-action-text">Giỏ hàng</span>
                </a>
            </div>
        </div>
    </div>
    <nav class="mobile-category-panel" id="mobile-category-panel" aria-label="Danh mục sản phẩm" hidden>
        @foreach(config('store.categories') as $key => $name)
            <a href="{{ route('products.index', ['category' => $key]) }}">{{ $name }} <i class="bi bi-chevron-right"></i></a>
        @endforeach
    </nav>
    <nav class="header-navigation">
        <div class="header-navigation-inner">
            <a href="{{ route('home') }}#about" class="navigation-item"><span>Giới thiệu</span></a>
            @foreach(config('store.categories') as $key => $name)
                @if($key !== 'phu-kien')
                    <span class="navigation-divider"></span>
                    <a href="{{ route('products.index', ['category' => $key]) }}" class="navigation-item"><span>{{ $name }}</span></a>
                @endif
            @endforeach
            <span class="navigation-divider"></span>
            <a href="{{ route('contact') }}" class="navigation-item navigation-service"><span>Dịch vụ</span><i class="bi bi-chevron-down navigation-service-icon"></i></a>
            <span class="navigation-divider"></span>
            <a href="{{ route('products.index', ['category' => 'phu-kien']) }}" class="navigation-item"><span>Phụ kiện</span></a>
            <span class="navigation-divider"></span>
            <a href="{{ route('news.index') }}" class="navigation-item"><span>Cẩm nang</span></a>
            <span class="navigation-divider"></span>
            <a href="{{ route('contact') }}" class="navigation-item"><span>Liên hệ</span></a>
        </div>
    </nav>
</header>

@yield('content')

<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-container">
            <div class="footer-col footer-about">
                <div class="footer-brand"><img src="{{ asset('assets/images/logo_IBP.png') }}" alt="IBP Logo"><span class="footer-company-name">IBP TECHNOLOGY Co., Ltd.</span></div>
                <p class="footer-desc">IBP Technology cung cấp các sản phẩm và giải pháp chăm sóc nguồn nước, cùng dịch vụ tư vấn và hỗ trợ khách hàng.</p>
                <div class="footer-contact">
                    <h4 class="footer-heading">Liên hệ</h4>
                    <ul class="footer-contact-list">
                        <li><strong>Hotline:</strong> +84 983 537 155</li>
                        <li><strong>Email:</strong> info@ibpvn.com</li>
                        <li><strong>Địa chỉ:</strong> B54 Park Riverside, 101 Bưng Ông Thoàn, Phú Hữu, TP. Thủ Đức, TP. Hồ Chí Minh</li>
                        <li><strong>Văn phòng:</strong> 198 đường N7, KĐT Đông Tăng Long, phường Trường Thạnh, TP. Thủ Đức</li>
                    </ul>
                </div>
            </div>
            <div class="footer-col footer-links">
                <div class="footer-block">
                    <h4 class="footer-heading blue">Sản phẩm</h4>
                    <ul class="footer-menu">
                        @foreach(config('store.categories') as $key => $name)
                            <li><a href="{{ route('products.index', ['category' => $key]) }}">{{ $name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="footer-block">
                    <h4 class="footer-heading blue">Chính sách</h4>
                    <ul class="footer-menu">
                        @foreach(['warranty' => 'bảo hành', 'insurance' => 'bảo hiểm', 'refund' => 'hoàn tiền', 'promotion' => 'khuyến mãi'] as $topic => $name)
                            <li><a href="{{ route('contact', ['topic' => $topic]) }}">Hỏi về chính sách {{ $name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="footer-col footer-social">
                <div class="footer-block"><h4 class="footer-heading blue">Fanpage</h4><div class="footer-fanpage"><img src="{{ asset('assets/images/fanpage.png') }}" alt="Fanpage"></div></div>
                <div class="footer-block">
                    <h4 class="footer-heading blue">Bản đồ</h4>
                    <p class="footer-map-desc">Vị trí văn phòng IBP Technology tại Thủ Đức, TP. Hồ Chí Minh.</p>
                    <div class="footer-map"><iframe src="https://www.google.com/maps?q=198+%C4%91%C6%B0%E1%BB%9Dng+N7,+K%C4%90T+%C4%90%C3%B4ng+T%C4%83ng+Long,+Tr%C6%B0%E1%BB%9Dng+Th%E1%BA%A1nh,+Th%E1%BB%A7+%C4%90%E1%BB%A9c,+H%E1%BB%93+Ch%C3%AD+Minh&output=embed" width="400" height="171" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom"><div class="footer-bottom-inner">© {{ date('Y') }} IBP Technology Co., Ltd.</div></div>
</footer>
<script src="{{ asset('assets/js/fancybox.umd.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@12.2.0/swiper-bundle.min.js"></script>
<script src="{{ asset('assets/js/main.js?v=10') }}"></script>
<script src="{{ asset('assets/js/cart.js?v=2') }}"></script>
<script src="{{ asset('assets/js/home-products.js?v=4') }}"></script>
<script src="{{ asset('assets/js/iconify-icon.min.js') }}"></script>
</body>
</html>
