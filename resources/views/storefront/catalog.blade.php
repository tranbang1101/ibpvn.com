@extends('layouts.storefront')

@section('title', ($categoryName ?: 'Sản phẩm') . ' | IBP Technology')

@section('content')
<main class="shop-page catalog-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>{{ $categoryName ?: 'Sản phẩm' }}</span>
        </nav>
        <header class="catalog-heading">
            <div>
                <span class="section-kicker">IBP TECHNOLOGY</span>
                <h1>{{ $categoryName ?: 'Danh sách sản phẩm' }}</h1>
                <p>{{ $keyword !== '' ? 'Kết quả tìm kiếm cho “' . $keyword . '”' : 'Chọn giải pháp phù hợp cho nhu cầu sử dụng của bạn.' }}</p>
            </div>
            <form class="catalog-sort" method="get" action="{{ route('products.index') }}">
                @if($categoryKey !== '')<input type="hidden" name="category" value="{{ $categoryKey }}">@endif
                @if($keyword !== '')<input type="hidden" name="keyword" value="{{ $keyword }}">@endif
                <label for="catalog-sort">Sắp xếp</label>
                <select id="catalog-sort" name="sort" onchange="this.form.submit()">
                    @foreach($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </header>
        <nav class="catalog-categories" aria-label="Danh mục sản phẩm">
            <a class="{{ $categoryKey === '' ? 'is-active' : '' }}" href="{{ route('products.index') }}">Tất cả</a>
            @foreach($categories as $key => $label)
                <a class="{{ $categoryKey === $key ? 'is-active' : '' }}" href="{{ route('products.index', ['category' => $key]) }}">{{ $label }}</a>
            @endforeach
        </nav>
        @if($loadError)
            <div class="catalog-empty" role="alert">
                <i class="bi bi-database-exclamation" aria-hidden="true"></i>
                <h2>Chưa thể hiển thị sản phẩm</h2>
                <p>Chưa tải được danh sách sản phẩm. Vui lòng kiểm tra kết nối cơ sở dữ liệu.</p>
            </div>
        @elseif($products->isEmpty())
            <div class="catalog-empty" role="status">
                <i class="bi bi-box-seam" aria-hidden="true"></i>
                <h2>Chưa có sản phẩm trong danh mục này</h2>
                <p>Thử chọn danh mục khác hoặc tìm với từ khóa khác.</p>
                <a class="button-primary" href="{{ route('products.index') }}">Xem tất cả sản phẩm</a>
            </div>
        @else
            <p class="catalog-result-count">Hiển thị {{ $products->firstItem() }}–{{ $products->lastItem() }} trong {{ $products->total() }} sản phẩm</p>
            <div class="catalog-grid">
                @foreach($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product, 'cardClass' => 'catalog-product-card'])
                @endforeach
            </div>
            @if($products->hasPages())
                <nav class="catalog-pagination" aria-label="Phân trang sản phẩm">
                    @if($products->onFirstPage())
                        <span aria-disabled="true">Trước</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" rel="prev">Trước</a>
                    @endif
                    <span>Trang {{ $products->currentPage() }} / {{ $products->lastPage() }}</span>
                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" rel="next">Tiếp</a>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</main>
@endsection
