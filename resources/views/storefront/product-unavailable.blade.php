@extends('layouts.storefront')

@section('title', $loadError ? 'Sản phẩm đang tạm gián đoạn | IBP Technology' : 'Không tìm thấy sản phẩm | IBP Technology')

@section('content')
<main class="shop-page">
    <div class="shop-container">
        <section class="detail-card catalog-empty" role="{{ $loadError ? 'alert' : 'status' }}">
            <h1>{{ $loadError ? 'Chưa thể tải sản phẩm' : 'Không tìm thấy sản phẩm' }}</h1>
            <p>{{ $loadError ? 'Chưa tải được thông tin sản phẩm. Vui lòng kiểm tra kết nối cơ sở dữ liệu.' : 'Sản phẩm không tồn tại hoặc hiện không được kinh doanh.' }}</p>
            <a class="button-primary" href="{{ route('products.index') }}">Quay lại danh sách sản phẩm</a>
        </section>
    </div>
</main>
@endsection
