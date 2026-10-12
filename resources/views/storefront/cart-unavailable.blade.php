@extends('layouts.storefront')

@section('title', 'Giỏ hàng tạm gián đoạn | IBP Technology')

@section('content')
<main class="shop-page">
    <div class="shop-container">
        <section class="detail-card catalog-empty" role="alert">
            <h1>Chưa thể tải giỏ hàng</h1>
            <p>Cơ sở dữ liệu chưa phản hồi. Giỏ hàng trong phiên của bạn vẫn được lưu; vui lòng thử lại sau.</p>
            <a class="button-primary" href="{{ route('products.index') }}">Tiếp tục mua sắm</a>
        </section>
    </div>
</main>
@endsection
