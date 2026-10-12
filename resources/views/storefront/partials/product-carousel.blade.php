<section class="product-promo" data-product-section data-default-category="{{ array_key_first($categoryProducts) ?? '' }}">
    <div class="product-promo-inner">
        <div class="product-promo-header">
            <div class="product-promo-title-wrap">
                <h2 class="product-promo-title">{{ $title }}</h2>
                <span class="product-promo-underline"></span>
            </div>
            @if($categoryProducts)
                <div class="product-promo-tabs">
                    @foreach($categoryProducts as $key => $category)
                        @if(!$loop->first)<span class="promo-dot"></span>@endif
                        <button type="button" class="promo-tab {{ $loop->first ? 'is-active' : '' }}" data-category="{{ $key }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $category['name'] }}</button>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="product-slider-wrap">
            <div class="swiper product-swiper" @if(!$categoryProducts) hidden @endif>
                <div class="swiper-wrapper">
                    @foreach($categoryProducts as $key => $category)
                        @foreach($category['products'] as $product)
                            <div class="swiper-slide" data-category="{{ $key }}">
                                @include('storefront.partials.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <div class="product-empty-state" @if($categoryProducts) hidden @endif role="status">
                @if($loadError)
                    Chưa tải được danh sách sản phẩm. Vui lòng kiểm tra kết nối cơ sở dữ liệu.
                @else
                    Hiện chưa có sản phẩm trong danh mục này.
                @endif
            </div>
            <button class="product-prev" type="button" aria-label="Sản phẩm trước"><i class="bi bi-chevron-left"></i></button>
            <button class="product-next" type="button" aria-label="Sản phẩm tiếp theo"><i class="bi bi-chevron-right"></i></button>
        </div>
        <div class="product-promo-footer"><a class="btn-view-all" href="{{ route('products.index') }}">XEM TẤT CẢ <i class="bi bi-chevron-right"></i></a></div>
    </div>
</section>
