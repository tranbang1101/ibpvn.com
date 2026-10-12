@php
    $productUrl = route('products.show', ['slug' => $product->slug]);
    $originalPrice = (float) $product->gia;
    $currentPrice = (float) ($product->gia_khuyen_mai ?: $originalPrice);
    $discount = $originalPrice > $currentPrice
        ? (int) round(($originalPrice - $currentPrice) / $originalPrice * 100)
        : 0;
    $image = trim((string) $product->anh_chinh);
    if (str_starts_with($image, '/ibpvn.com/')) {
        $image = substr($image, strlen('/ibpvn.com/'));
    } else {
        $image = ltrim($image, '/');
    }
    if ($image === '') {
        $image = 'assets/images/sanpham.png';
    }
@endphp
<article class="product-card {{ $cardClass ?? '' }}">
    <a class="product-card-image" href="{{ $productUrl }}">
        <img src="{{ asset($image) }}" alt="{{ $product->ten }}" loading="lazy">
        @if($discount > 0)
            <span class="badge-hot">Ưu đãi</span>
            <span class="badge-discount" aria-label="Ưu đãi"></span>
        @endif
    </a>
    <div class="product-card-body">
        <a class="product-card-name" href="{{ $productUrl }}">{{ $product->ten }}</a>
        <div class="product-card-price">
            <span class="price-current">{{ number_format($currentPrice, 0, ',', '.') }}₫</span>
            @if($discount > 0)
                <del class="price-old">{{ number_format($originalPrice, 0, ',', '.') }}₫</del>
            @endif
        </div>
        @if(!empty($showDescription))
            <div class="product-card-desc">{{ $product->mo_ta_ngan }}</div>
        @endif
        <div class="product-card-meta">
            <span class="meta-item rating"><strong>{{ number_format((float) $product->rating, 1) }}</strong><i class="bi bi-star-fill"></i><span class="review-count">({{ (int) $product->so_danh_gia }})</span></span>
            <span class="meta-item">Đã bán {{ number_format((int) $product->da_ban) }}</span>
        </div>
        <div class="product-card-actions">
            <form method="post" action="{{ route('cart.add') }}" class="card-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn-add-cart">Thêm vào giỏ</button>
            </form>
            <a href="{{ $productUrl }}" class="btn-buy-now">Xem chi tiết</a>
        </div>
    </div>
</article>
