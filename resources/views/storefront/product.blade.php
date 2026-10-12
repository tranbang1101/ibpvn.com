@extends('layouts.storefront')

@section('title', $product->ten . ' | IBP Technology')

@section('content')
@php
    $originalPrice = (float) $product->gia;
    $currentPrice = (float) ($product->gia_khuyen_mai ?: $originalPrice);
    $discount = $originalPrice > $currentPrice ? (int) round(($originalPrice - $currentPrice) / $originalPrice * 100) : 0;
    $assetPath = static function (?string $path): string {
        $path = trim((string) $path);
        if ($path === '') {
            return asset('assets/images/sanpham.png');
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (str_starts_with($path, '/ibpvn.com/')) {
            $path = substr($path, strlen('/ibpvn.com/'));
        }
        return asset(ltrim($path, '/'));
    };
    $promotions = [
        'LẮP Thêm LÕI LỌC NƯỚC ION KIỀM ALKALINE HYDROGEN - NHẬP KHẨU HÀN QUỐC chỉ 500.000Đ (Giá thị trường 950.000đ)',
        'Tặng thiết bị kiểm tra độ tinh khiết của nước TDS trị giá 150.000đ',
        'Tặng gói lắp đặt và phụ kiện trị giá 500.000đ',
        'Tặng 2.000.000đ đồng khi mua Lọc đầu nguồn và Heatpump A. O. Smith',
    ];
@endphp
<main class="shop-page product-page product-detail-redesign">
    <div class="shop-container product-detail-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="{{ route('home') }}">Trang chủ</a><i class="bi bi-chevron-right" aria-hidden="true"></i>
            <a href="{{ route('products.index', ['category' => array_search($product->danh_muc, config('store.categories'), true) ?: null]) }}">{{ $product->danh_muc ?: 'Sản phẩm' }}</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i><span>{{ $product->ten }}</span>
        </nav>
        <section class="pd-top-grid">
            <div class="pd-gallery">
                <div class="pd-main-visual">
                    <button type="button" class="pd-gallery-arrow is-prev" data-gallery-prev aria-label="Ảnh trước"><i class="bi bi-chevron-left"></i></button>
                    <img id="productMainImage" src="{{ $assetPath($imageUrls[0]) }}" alt="{{ $product->ten }}" data-main-image>
                    <button type="button" class="pd-gallery-arrow is-next" data-gallery-next aria-label="Ảnh tiếp theo"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="pd-gallery-bottom">
                    <div class="pd-thumbnails" data-gallery-thumbnails>
                        @foreach($imageUrls as $index => $url)
                            <button type="button" class="pd-thumb {{ $loop->first ? 'is-active' : '' }}" data-gallery-image="{{ $assetPath($url) }}" aria-label="Xem ảnh {{ $index + 1 }}">
                                <img src="{{ $assetPath($url) }}" alt="Ảnh sản phẩm {{ $index + 1 }}">
                            </button>
                        @endforeach
                    </div>
                    <a class="pd-spec-shortcut" href="#product-information" data-specs-shortcut>Xem thông số<br>kỹ thuật <i class="bi bi-arrow-down"></i></a>
                </div>
                <div class="pd-social">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(route('products.show', ['slug' => $product->slug])) }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ qua Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://zalo.me/0983537155" target="_blank" rel="noopener noreferrer" aria-label="Tư vấn qua Zalo" class="zalo-mark">Zalo</a>
                    <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="pd-summary">
                @php
                    $rating = max(0, min(5, (int) round((float) $product->rating)));
                @endphp
                <div class="pd-title-row"><div><h1>{{ $product->ten }}</h1></div><div class="pd-sold">Đã bán: <b>{{ number_format((int) $product->da_ban) }}</b><span class="pd-top-stars">★</span> ({{ number_format((float) $product->rating, 1) }})</div></div>
                <div class="pd-brand-rating"><span>Thương hiệu: <a href="#product-information">{{ $product->thuong_hieu ?: '—' }}</a></span><i></i><span class="pd-stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span><a href="#reviews">{{ (int) $product->so_danh_gia }} đánh giá</a></div>
                <div class="pd-price-line"><span>Giá:</span><strong data-unit-price="{{ (int) $currentPrice }}">{{ number_format($currentPrice, 0, ',', '.') }}₫</strong>@if($discount)<del>{{ number_format($originalPrice, 0, ',', '.') }}₫</del><em>-{{ $discount }}%</em><span class="pd-price-savings" data-price-savings>Tiết kiệm: {{ number_format($originalPrice - $currentPrice, 0, ',', '.') }}₫</span>@endif</div>
                <div class="pd-service-line"><span>Bảo hành: <b>{{ $product->detail?->bao_hanh ?: 'Đang cập nhật' }}</b></span><span>Thương hiệu/Nơi sản xuất: <b>{{ $origin }}</b></span><span>Giao hàng: <b>{{ $product->detail?->giao_hang_mien_phi ? 'Miễn phí' : number_format((float) ($product->detail?->phi_giao_hang ?? 0), 0, ',', '.') . '₫' }}</b></span></div>
                <div class="pd-product-info"><h2>THÔNG TIN SẢN PHẨM:</h2><p>{{ $product->detail?->mo_ta_chi_tiet ?: $product->mo_ta_ngan }}</p></div>
                @if($product->variants->isNotEmpty())
                    <div class="pd-variants"><b>Phiên bản</b><div class="pd-variant-list">
                        @foreach($product->variants as $index => $variant)
                            <button type="button" class="pd-variant {{ $loop->first ? 'is-selected' : '' }}" data-variant-id="{{ $variant->id }}" data-variant-image="{{ $assetPath($variant->image_url) }}" data-variant-price="{{ (int) ($variant->gia ?: $currentPrice) }}">
                                <span class="pd-variant-check"><i class="bi bi-check-lg"></i></span><img src="{{ $assetPath($variant->image_url) }}" alt=""><span>{{ $variant->ten_phien_ban }}</span>
                            </button>
                        @endforeach
                    </div></div>
                @endif
                <div class="pd-promotion"><h2><i class="bi bi-gift-fill"></i> ƯU ĐÃI KHI MUA HÀNG:</h2><ul>@foreach($promotions as $promotion)<li>{{ $promotion }}</li>@endforeach</ul></div>
                @if(session('cart_error'))
                    <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ session('cart_error') }}</div>
                @endif
                @if($errors->any())
                    <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ $errors->first() }}</div>
                @endif
                <form method="post" action="{{ route('cart.add') }}" class="product-buy-form product-detail-form" data-product-detail-form>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" value="{{ $product->variants->first()?->id ?? 0 }}" data-selected-variant>
                    <input type="hidden" name="selection_present" value="1">
                    <div data-selected-addons></div>
                    <div class="pd-buy-row">
                        <div class="quantity-control pd-quantity"><button type="button" data-quantity="minus" aria-label="Giảm số lượng">−</button><input name="quantity" value="1" min="1" max="99" type="number" aria-label="Số lượng" data-product-quantity><button type="button" data-quantity="plus" aria-label="Tăng số lượng">+</button></div>
                        <button name="add_to_cart" value="1" class="pd-add-button"><i class="bi bi-cart-plus"></i> Thêm vào giỏ</button>
                        <button name="buy_now" value="1" class="pd-buy-now">MUA NGAY</button>
                    </div>
                </form>
                <div class="pd-consult-links"><a href="https://zalo.me/0983537155"><i class="bi bi-chat-dots-fill"></i> Tư vấn qua Zalo</a><a href="https://m.me/ibptechnology"><i class="bi bi-messenger"></i> Tư vấn qua Messenger</a><a href="tel:0983537155"><i class="bi bi-headset"></i> Tư vấn qua Hotline</a></div>
                <p class="pd-hours">Gọi đặt mua: <a href="tel:0983537155">0983.537.155</a> (8:00 - 18:00)</p>
            </div>
        </section>
        <section class="pd-mid-grid">
            <div class="pd-main-column">
                <form method="post" action="{{ route('cart.add') }}" class="pd-bundle-form product-buy-form" data-product-detail-form>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" value="{{ $product->variants->first()?->id ?? 0 }}" data-selected-variant>
                    <input type="hidden" name="selection_present" value="1">
                    <input type="hidden" name="quantity" value="1" data-bundle-quantity>
                    <div data-selected-addons></div>
                    <section class="detail-card pd-bundle-card" aria-labelledby="bundle-heading">
                        <div class="pd-section-title"><h2 id="bundle-heading">MUA KÈM GIÁ GIẢM</h2><span>Chọn phụ kiện bạn cần</span></div>
                        <div class="pd-addon-carousel">
                            <button class="pd-addon-arrow is-prev" type="button" data-addon-prev aria-label="Phụ kiện trước"><i class="bi bi-chevron-left"></i></button>
                            <div class="pd-addon-track" data-addon-track>
                                @forelse($product->addons as $addon)
                                    @php
                                        $addonPrice = (float) ($addon->gia_khuyen_mai ?: $addon->gia_goc);
                                    @endphp
                                    <article class="pd-addon" data-addon-card data-addon-id="{{ $addon->id }}" data-addon-price="{{ (int) $addonPrice }}" data-addon-old-price="{{ (int) $addon->gia_goc }}">
                                        <img src="{{ $assetPath($addon->image_url ?: '/assets/images/loiloc.png') }}" alt="{{ $addon->ten }}"><b>{{ $addon->ten }}</b><button type="button" class="pd-addon-toggle" aria-pressed="false">CHỌN</button><strong>{{ number_format($addonPrice, 0, ',', '.') }}₫</strong>
                                    </article>
                                @empty
                                    <p>Chưa có sản phẩm mua kèm được cập nhật.</p>
                                @endforelse
                            </div>
                            <button class="pd-addon-arrow is-next" type="button" data-addon-next aria-label="Phụ kiện tiếp theo"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div class="pd-bundle-summary">
                            <div class="pd-addon-savings"><span>Mua kèm chỉ còn: <b data-addon-total>0₫</b></span><small>Tạm tính riêng: <b data-bundle-total>{{ number_format($currentPrice, 0, ',', '.') }}₫</b></small><small class="pd-saving" data-savings>Tiết kiệm {{ number_format($originalPrice - $currentPrice, 0, ',', '.') }}₫ · -{{ $discount }}%</small></div>
                            <div class="pd-bundle-actions"><button type="submit" name="buy_now" value="1" class="pd-bundle-buy">MUA NGAY <small>Giao hàng tận nơi</small></button><button type="submit" name="add_to_cart" value="1" class="pd-bundle-cart"><i class="bi bi-cart-plus"></i> Thêm vào giỏ</button></div>
                        </div>
                    </section>
                </form>
                <section class="detail-card pd-information-card" id="product-information">
                    <div class="pd-info-tabs" role="tablist"><button type="button" role="tab" aria-selected="false" data-info-tab="product">THÔNG TIN SẢN PHẨM</button><button type="button" class="is-active" role="tab" aria-selected="true" data-info-tab="specs">THÔNG SỐ KỸ THUẬT</button></div>
                    <div class="pd-info-panel" data-info-panel="product" hidden><h2>{{ $product->ten }}</h2><p>{{ $product->detail?->mo_ta_chi_tiet ?: $product->mo_ta_ngan }}</p>@if($product->detail?->thong_tin_uu_dai)<p>{{ $product->detail->thong_tin_uu_dai }}</p>@endif</div>
                    <div class="pd-info-panel is-visible" data-info-panel="specs" id="technical-specs"><div class="pd-spec-table">@foreach(array_slice($specifications, 0, 4, true) as $label => $value)<div><span>{{ $label }}</span><b>{{ $value }}</b></div>@endforeach</div><button type="button" class="pd-more-specs" data-open-specs>Xem chi tiết thông số kỹ thuật</button></div>
                </section>
                <dialog class="pd-spec-dialog" data-spec-dialog aria-labelledby="pd-spec-dialog-title">
                    <div class="pd-spec-dialog-header"><h2 id="pd-spec-dialog-title">THÔNG SỐ KỸ THUẬT</h2><button type="button" data-close-specs aria-label="Đóng thông số kỹ thuật"><i class="bi bi-x-lg"></i></button></div>
                    <div class="pd-spec-table">@foreach($specifications as $label => $value)<div><span>{{ $label }}</span><b>{{ $value }}</b></div>@endforeach</div>
                </dialog>
                <section class="pd-reviews" id="reviews">
                    <div class="pd-review-heading"><h2>Reviews - {{ $product->ten }}</h2></div>
                    <div class="pd-review-summary"><div class="pd-review-score"><strong>{{ number_format((float) $product->rating, 1) }}</strong><span>{{ str_repeat('★', (int) round((float) $product->rating)) }}{{ str_repeat('☆', 5 - (int) round((float) $product->rating)) }}</span><small>{{ (int) $product->so_danh_gia }} đánh giá</small></div>@unless(config('store.demo_mode'))<a href="{{ route('login') }}" class="pd-review-write">GỬI ĐÁNH GIÁ CỦA BẠN</a>@endunless</div>
                    <div class="pd-review-list" data-review-list>
                        @forelse($product->reviews as $review)
                            <article class="pd-review-item"><span class="pd-avatar"><i class="bi bi-person-fill"></i></span><div><h3>{{ $review->ten_hien_thi }} <small>Khách hàng đánh giá</small></h3><span class="pd-review-stars">{{ str_repeat('★', max(1, min(5, (int) $review->so_sao))) }}</span><p>{{ $review->noi_dung ?: 'Dịch vụ mua hàng nhanh chóng và hỗ trợ tốt.' }}</p><time>{{ $review->created_at?->format('d-m-Y H:i') }}</time></div></article>
                        @empty
                            <p>Chưa có đánh giá cho sản phẩm này.</p>
                        @endforelse
                    </div>
                    @if($product->reviews->count() > 3)
                        <nav class="pd-review-pagination" aria-label="Phân trang đánh giá">
                            <button type="button" data-review-page="first" aria-label="Trang đầu">«</button>
                            <button type="button" data-review-page="prev" aria-label="Trang trước">‹</button>
                            <span data-review-page-indicator aria-live="polite">1</span>
                            <button type="button" data-review-page="next" aria-label="Trang tiếp theo">›</button>
                            <button type="button" data-review-page="last" aria-label="Trang cuối">»</button>
                        </nav>
                    @endif
                </section>
            </div>
            <aside class="pd-sidebar">
                <section class="detail-card pd-why"><h2>VÌ SAO BẠN NÊN CHỌN IBP TECHNOLOGY</h2><ul><li><i class="bi bi-patch-check"></i> Sản phẩm chính hãng</li><li><i class="bi bi-tools"></i> Hỗ trợ kỹ thuật</li><li><i class="bi bi-shield-check"></i> Hỗ trợ bảo hành theo chính sách</li><li><i class="bi bi-truck"></i> Tư vấn giao hàng trên toàn quốc</li></ul></section>
                <section class="detail-card pd-faq-card">
                    <h2>CÂU HỎI THƯỜNG GẶP</h2>
                    <ul class="faq-list pd-faq-list">
                        @foreach($faqs as $index => $faq)
                            <li class="faq-item">
                                <button class="faq-question" type="button" aria-expanded="false" aria-controls="product-faq-{{ $index }}">
                                    <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                    <span>{{ data_get($faq, 'cau_hoi') }}</span>
                                </button>
                                <div class="faq-answer" id="product-faq-{{ $index }}" hidden>
                                    <p>{{ data_get($faq, 'cau_tra_loi') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
                <section class="pd-consult-card detail-card" aria-labelledby="pd-consult-heading">
                    <h2 id="pd-consult-heading">ĐĂNG KÝ TƯ VẤN</h2>
                    @if(session('contact_sent'))
                        <p class="pd-consult-feedback" role="status">Đã nhận đăng ký. IBP sẽ liên hệ tư vấn sản phẩm này sớm nhất.</p>
                    @elseif(session('contact_demo_notice'))
                        <p class="pd-consult-feedback" role="status">Đây là bản demo; thông tin tư vấn chưa được gửi hoặc lưu.</p>
                    @endif
                    @if(session('contact_error'))
                        <p class="pd-consult-feedback" role="alert">{{ session('contact_error') }}</p>
                    @endif
                    @if($errors->any())
                        <p class="pd-consult-feedback" role="alert">{{ $errors->first() }}</p>
                    @endif
                    <form method="post" action="{{ route('products.consultation', ['slug' => $product->slug]) }}">
                        @csrf
                        <input name="name" value="{{ old('name') }}" autocomplete="name" maxlength="150" placeholder="Họ và tên*" aria-label="Họ và tên" required>
                        <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="25" placeholder="Số điện thoại*" aria-label="Số điện thoại" required>
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="190" placeholder="Email*" aria-label="Email" required>
                        <textarea name="message" rows="3" maxlength="5000" placeholder="Nội dung" aria-label="Nội dung">{{ old('message') }}</textarea>
                        <button type="submit">ĐĂNG KÝ</button>
                    </form>
                </section>
                <section class="detail-card pd-viewed" aria-labelledby="pd-viewed-heading">
                    <h2 id="pd-viewed-heading">SẢN PHẨM ĐÃ XEM</h2>
                    @foreach($viewedProducts as $viewedProduct)
                        @php
                            $viewedImage = trim((string) $viewedProduct->anh_chinh);
                            if (str_starts_with($viewedImage, '/ibpvn.com/')) {
                                $viewedImage = substr($viewedImage, strlen('/ibpvn.com/'));
                            }
                        @endphp
                        <a href="{{ route('products.show', ['slug' => $viewedProduct->slug]) }}">
                            <img src="{{ $viewedImage !== '' ? $assetPath($viewedImage) : asset('assets/images/sanpham.png') }}" alt="">
                            <span>{{ $viewedProduct->ten }}<b>{{ number_format((float) ($viewedProduct->gia_khuyen_mai ?: $viewedProduct->gia), 0, ',', '.') }}₫</b></span>
                        </a>
                    @endforeach
                </section>
            </aside>
        </section>
        <section class="pd-similar" aria-labelledby="similar-products-title">
            <div class="pd-similar-heading"><h2 id="similar-products-title">Các sản phẩm tương tự</h2></div>
            <div class="pd-similar-carousel">
                <button type="button" class="pd-similar-arrow is-prev" data-similar-prev aria-label="Sản phẩm trước"><i class="bi bi-chevron-left"></i></button>
                <div class="pd-similar-grid" data-similar-track aria-label="Danh sách sản phẩm tương tự">
                    @foreach($similarProducts as $similarProduct)
                        @include('storefront.partials.product-card', ['product' => $similarProduct, 'cardClass' => 'pd-similar-card', 'showDescription' => true])
                    @endforeach
                </div>
                <button type="button" class="pd-similar-arrow is-next" data-similar-next aria-label="Sản phẩm tiếp theo"><i class="bi bi-chevron-right"></i></button>
            </div>
        </section>
    </div>
</main>
<script src="{{ asset('assets/js/product-detail.js?v=6') }}"></script>
@endsection
