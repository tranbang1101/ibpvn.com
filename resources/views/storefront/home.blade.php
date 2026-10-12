@extends('layouts.storefront')

@section('title', 'IBP Technology - Trang chủ')

@section('content')
<main>
    <section class="home-banner-section">
        <div class="home-banner-container">
            <aside class="product-category">
                <div class="product-category-header"><i class="bi bi-list"></i><span>DANH MỤC SẢN PHẨM</span></div>
                <ul class="product-category-list">
                    @foreach($categories as $key => $name)
                        <li class="product-category-item">
                            <a href="{{ route('products.index', ['category' => $key]) }}"><span class="category-dot"></span><span class="category-name">{{ $name }}</span><i class="bi bi-chevron-right"></i></a>
                        </li>
                    @endforeach
                </ul>
            </aside>
            <div class="main-banner-group">
                <div class="main-banner">
                    <div class="swiper main-banner-swiper">
                        <div class="swiper-wrapper"><div class="swiper-slide"><img src="{{ asset('assets/images/Banner.png') }}" alt="Giải pháp nước sạch IBP Technology" class="main-banner-image"></div></div>
                        <div class="swiper-pagination main-banner-pagination"></div>
                    </div>
                </div>
            </div>
            <div class="side-banners">
                <div class="side-banner"><div class="swiper side-banner-swiper" data-delay="4000"><div class="swiper-wrapper"><div class="swiper-slide"><img src="{{ asset('assets/images/banner2.png') }}" alt="Ưu đãi sản phẩm IBP"></div><div class="swiper-slide"><img src="{{ asset('assets/images/banner2-1.png') }}" alt="Sản phẩm IBP"></div></div></div></div>
                <div class="side-banner"><div class="swiper side-banner-swiper" data-delay="4000"><div class="swiper-wrapper"><div class="swiper-slide"><img src="{{ asset('assets/images/banner3.png') }}" alt="Ứng dụng sản phẩm IBP"></div><div class="swiper-slide"><img src="{{ asset('assets/images/ungdung.png') }}" alt="Ứng dụng sản phẩm"></div></div></div></div>
            </div>
        </div>
    </section>
    <div class="feature-bar" id="about">
        <div class="feature-bar-inner">
            @foreach([
                ['bi-truck', 'Giao hàng toàn quốc'],
                ['bi-award', 'Bảo hành lâu dài'],
                ['bi-tags', 'Ưu đãi thường xuyên'],
                ['bi-arrow-repeat', 'Đổi trả miễn phí'],
                ['bi-headset', 'Dịch vụ tư vấn 24/7'],
            ] as [$icon, $text])
                <div class="feature-item"><div class="feature-icon"><i class="bi {{ $icon }}"></i></div><span class="feature-text">{{ $text }}</span></div>
            @endforeach
        </div>
    </div>
    <section class="featured-category">
        <div class="featured-category-inner">
            <div class="featured-category-left"><h2 class="featured-category-title">Danh mục nổi bật</h2><a href="{{ route('products.index') }}" class="featured-category-btn">XEM NGAY <i class="bi bi-arrow-right-circle"></i></a></div>
            <div class="featured-category-list">
                @foreach($categories as $key => $name)
                    <a href="{{ route('products.index', ['category' => $key]) }}" class="featured-category-item">
                        <div class="featured-category-icon"><img src="{{ asset('assets/images/danhmuc.png') }}" alt=""></div>
                        <span class="featured-category-name">{{ $name }}</span>
                    </a>
                @endforeach
            </div>
            <div class="featured-category-scrollbar" aria-hidden="true"><span class="featured-category-scrollbar-thumb"></span></div>
        </div>
    </section>
    @php
        $discountedCategories = collect($categoryProducts)->mapWithKeys(function (array $category, string $key): array {
            $discounted = $category['products']->filter(fn ($product) => $product->gia_khuyen_mai > 0 && $product->gia_khuyen_mai < $product->gia);
            $products = $discounted->isNotEmpty() ? $discounted->values() : $category['products']->values();
            $source = $products->all();
            while ($products->count() < 6 && $source !== []) {
                $products->push($source[$products->count() % count($source)]);
            }
            return [$key => ['name' => $category['name'], 'products' => $products]];
        })->take(5)->all();
        $featuredCategories = collect($categoryProducts)->except('phu-kien')->all();
    @endphp
    @include('storefront.partials.product-carousel', ['title' => 'Sản Phẩm Ưu Đãi', 'categoryProducts' => $discountedCategories])
    <section class="dual-promo-banner">
        <div class="dual-promo-inner">
            <a href="{{ route('products.index') }}" class="dual-promo-item"><img src="{{ asset('assets/images/promotion1.png') }}" alt="Khám phá sản phẩm IBP" class="dual-promo-image"></a>
            <a href="{{ route('products.index') }}" class="dual-promo-item"><img src="{{ asset('assets/images/promotion2.png') }}" alt="Khám phá sản phẩm IBP" class="dual-promo-image"></a>
        </div>
    </section>
    @include('storefront.partials.product-carousel', ['title' => 'Sản Phẩm Nổi Bật', 'categoryProducts' => $featuredCategories])
    <section class="hero-banner"><div class="hero-banner-inner"><img src="{{ asset('assets/images/advertisement.png') }}" alt="Giải pháp chăm sóc nguồn nước" class="hero-banner-image"></div></section>
    <section class="review-faq-section">
        <div class="water-splash-top"></div><div class="water-bg-bottom"></div>
        <div class="review-faq-inner">
            <div class="review-faq-top">
                <div class="customer-reviews">
                    <div class="section-title-wrap"><h2 class="section-title">Khách hàng nói gì</h2><span class="product-promo-underline"></span></div>
                    <div class="reviews-grid">
                        @forelse($featuredReviews as $index => $review)
                            @php($rating = max(0, min(5, (float) $review->so_sao)))
                            <article class="review-card">
                                <div class="review-header">
                                    <img class="review-avatar" src="{{ asset('assets/images/' . ($index < 2 ? 'customer1.png' : 'customer-africa.png')) }}" alt="" loading="lazy">
                                    <div class="review-info">
                                        <h3 class="review-name">{{ $review->ten_hien_thi }}</h3>
                                        <span class="review-badge">Khách hàng</span>
                                        <div class="review-rating"><div class="stars" aria-label="{{ number_format($rating, 1) }} trên 5 sao">@for($star = 1; $star <= 5; $star++)<i class="bi {{ $star <= floor($rating) ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor</div><span class="rating-score">{{ number_format($rating, 1) }}</span></div>
                                    </div>
                                </div>
                                <p class="review-text">{{ $review->noi_dung ?: 'Đánh giá về ' . ($review->product?->ten ?? 'sản phẩm IBP') }}</p>
                            </article>
                        @empty
                            <p>{{ $loadError ? 'Chưa tải được đánh giá khách hàng.' : 'Chưa có đánh giá khách hàng được công khai.' }}</p>
                        @endforelse
                    </div>
                </div>
                <div class="faq-section">
                    <div class="section-title-wrap"><h2 class="section-title">Góc thắc mắc giải đáp</h2><span class="product-promo-underline"></span></div>
                    <ul class="faq-list">
                        @foreach([
                            ['Làm sao để chọn máy lọc nước phù hợp với gia đình?', 'Hãy cân nhắc số người sử dụng, nguồn nước đầu vào và vị trí lắp đặt. Liên hệ IBP để được tư vấn mẫu phù hợp.'],
                            ['Sản phẩm có được bảo hành chính hãng không?', 'Sản phẩm được áp dụng chính sách bảo hành của nhà sản xuất. Thời hạn bảo hành được ghi trên trang chi tiết sản phẩm.'],
                            ['Phí giao hàng được tính như thế nào?', 'Phí giao hàng hiển thị trong giỏ hàng theo thông tin của sản phẩm.'],
                            ['Tôi có thể nhờ tư vấn lắp đặt sản phẩm không?', 'Liên hệ IBP để trao đổi nhu cầu và được tư vấn phương án lắp đặt phù hợp.'],
                            ['Khi nào cần thay lõi lọc?', 'Chu kỳ thay lõi tùy theo loại lõi, nguồn nước và mức độ sử dụng.'],
                            ['Làm sao để kiểm tra đơn hàng của tôi?', 'Liên hệ IBP để được hỗ trợ tra cứu tình trạng đơn hàng.'],
                            ['Tôi cần hỗ trợ thêm thì liên hệ bằng cách nào?', 'Gọi hotline 0983 537 155 hoặc gửi yêu cầu qua trang Liên hệ.'],
                        ] as $index => [$question, $answer])
                            <li class="faq-item">
                                <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-{{ $index + 1 }}"><i class="bi bi-plus-circle" aria-hidden="true"></i><span>{{ $question }}</span></button>
                                <div class="faq-answer" id="faq-answer-{{ $index + 1 }}" hidden><p>{{ $answer }}</p></div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="contact-form-bottom">
                <div class="contact-cta"><h2 class="contact-heading">Liên hệ với chúng tôi<br>để có trải nghiệm mua hàng<br>tốt nhất</h2><div class="contact-buttons"><a href="{{ route('products.index') }}" class="btn-shop-now">Shop Now <i class="bi bi-arrow-right"></i></a><a href="{{ route('contact') }}" class="btn-contact-us">Contact Us <i class="bi bi-arrow-right"></i></a></div></div>
                <div class="quote-form"><h3 class="form-title">Đăng Ký Nhận Báo Giá</h3>
                    <form action="{{ route('contact') }}" method="post" class="form-body">
                        @csrf
                        <input type="text" name="name" placeholder="Tên quý khách" class="form-input" required>
                        <div class="form-row"><input type="tel" name="phone" placeholder="Số điện thoại" class="form-input" required><input type="email" name="email" placeholder="Email" class="form-input"></div>
                        <select name="product" class="form-select" required><option value="" disabled selected>Chọn sản phẩm</option>@foreach($categories as $key => $name)<option value="{{ $key }}">{{ $name }}</option>@endforeach</select>
                        <textarea name="message" placeholder="Ghi chú" class="form-textarea" rows="4"></textarea>
                        <button type="submit" class="btn-submit">ĐĂNG KÝ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="news-section">
        <div class="news-inner">
            <div class="news-title-wrap">
                <h2 class="news-title">Tin tức &amp; Sự kiện</h2>
                <span class="underline"></span>
            </div>
            <div class="news-grid" id="newsGrid">
                @foreach([['new1.png', 'Giải pháp nước sạch cho gia đình'], ['new2.png', 'Lựa chọn thiết bị lọc nước phù hợp'], ['new3.png', 'Hướng dẫn chăm sóc thiết bị lõi lọc'], ['new4.png', 'New Apartment Nice in the Best Canadian Cities']] as [$image, $title])
                    <article class="news-card">
                        <a href="{{ route('news.index') }}" class="news-card-image"><img src="{{ asset('assets/images/' . $image) }}" alt="{{ $title }}" loading="lazy"></a>
                        <div class="news-card-body">
                            <span class="news-date">Aug 19, 2025</span>
                            <h3 class="news-card-title">
                                <a href="{{ route('news.index') }}">{{ $title }}</a>
                            </h3>
                            <a href="{{ route('news.index') }}" class="news-read-more">Read More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    <section class="real-images-section">
        <div class="real-images-inner">
            <div class="real-images-title-wrap">
                <h2 class="real-images-title">Hình Ảnh Thực Tế</h2>
                <span class="underline"></span>
            </div>
            <div class="real-images-grid">@foreach(['thucte1.png', 'thucte2.png', 'thucte3.png', 'thucte4.png'] as $image)<div class="real-image-item"><img src="{{ asset('assets/images/' . $image) }}" alt="Hình ảnh thực tế tại IBP" loading="lazy"></div>@endforeach</div>
        </div>
    </section>
</main>
@endsection
