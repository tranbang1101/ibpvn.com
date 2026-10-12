@extends('layouts.storefront')

@section('title', $demoOrder ? 'Đặt hàng thử nghiệm | IBP Technology' : ($orderWasPlaced ? 'Đặt hàng thành công | IBP Technology' : 'Không có đơn hàng mới | IBP Technology'))

@section('content')
<main class="shop-page order-success-page">
    <section class="detail-card order-success-card">
        <span class="success-check"><i class="bi bi-{{ $orderWasPlaced ? 'check2' : 'info-circle' }}"></i></span>
        <span class="section-kicker">IBP TECHNOLOGY</span>
        @if($orderWasPlaced)
            <h1>{{ $demoOrder ? 'Đặt hàng thử nghiệm hoàn tất' : 'Cảm ơn bạn đã đặt hàng!' }}</h1>
            <p>{{ $demoOrder ? 'Đây chỉ là bản mô phỏng: không có đơn hàng nào được lưu, không gửi yêu cầu tới IBP và tồn kho không thay đổi.' : 'Đơn hàng của bạn đã được ghi nhận. Đội ngũ IBP sẽ liên hệ xác nhận và sắp xếp giao hàng sớm nhất.' }}</p>
            <div class="order-code"><span>{{ $demoOrder ? 'Mã tham khảo demo' : 'Mã đơn hàng' }}</span><strong>{{ $orderCode }}</strong></div>
            <div class="order-code"><span>Tổng thanh toán</span><strong>{{ number_format($orderTotal, 0, ',', '.') }}₫</strong></div>
            @if($demoOrder)
                <a class="button-primary" href="{{ route('products.index') }}">Tiếp tục xem sản phẩm <i class="bi bi-arrow-right"></i></a>
            @else
                <a class="button-primary" href="{{ route('orders.track') }}">Kiểm tra đơn hàng <i class="bi bi-arrow-right"></i></a>
            @endif
        @else
            <h1>Không có đơn hàng mới</h1>
            <p>Trang xác nhận chỉ hiển thị sau khi đặt hàng thành công.</p>
            <a class="button-primary" href="{{ route('products.index') }}">Xem sản phẩm <i class="bi bi-arrow-right"></i></a>
        @endif
        <div class="success-support">Cần hỗ trợ? Gọi <b>0983 537 155</b></div>
    </section>
</main>
@endsection
