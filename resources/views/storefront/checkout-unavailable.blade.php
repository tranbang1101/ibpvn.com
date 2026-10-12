@extends('layouts.storefront')

@section('title', 'Thanh toán tạm gián đoạn | IBP Technology')

@section('content')
<main class="shop-page">
    <div class="shop-container">
        <section class="detail-card catalog-empty" role="alert">
            <h1>Chưa thể tải thông tin thanh toán</h1>
            <p>Cơ sở dữ liệu chưa phản hồi; giỏ hàng của bạn vẫn được giữ lại. Vui lòng thử lại sau.</p>
            <a class="button-primary" href="{{ route('cart.index') }}">Quay lại giỏ hàng</a>
        </section>
    </div>
</main>
@endsection
