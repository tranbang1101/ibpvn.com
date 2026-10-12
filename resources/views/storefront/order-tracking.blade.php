@extends('layouts.storefront')

@section('title', 'Kiểm tra đơn hàng | IBP Technology')

@section('content')
@php
    $statuses = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao hàng',
        'completed' => 'Đã hoàn tất',
        'cancelled' => 'Đã hủy',
    ];
@endphp
<main class="shop-page order-lookup-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>Kiểm tra đơn hàng</span>
        </nav>
        <div class="section-heading"><div><span class="section-kicker">IBP TECHNOLOGY</span><h1>Kiểm tra đơn hàng</h1></div></div>
        <section class="detail-card checkout-form-card">
            <p>Nhập mã đơn hàng và số điện thoại đã cung cấp khi đặt hàng.</p>
            @if($error)
                <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ $error }}</div>
            @endif
            @if($errors->any())
                <div class="form-alert" role="alert"><i class="bi bi-exclamation-circle"></i>{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('orders.track.lookup') }}" class="checkout-form">
                @csrf
                <label>Mã đơn hàng<input name="order_code" value="{{ old('order_code', $orderCode) }}" maxlength="32" autocomplete="off" required></label>
                <label>Số điện thoại đặt hàng<input type="tel" name="phone" value="{{ old('phone', $phone) }}" maxlength="25" autocomplete="tel" required></label>
                <button class="button-primary" type="submit">Tra cứu đơn hàng</button>
            </form>
        </section>
        @if($order)
            <section class="detail-card checkout-summary" aria-labelledby="order-result-title">
                <h2 id="order-result-title">Đơn hàng {{ $order->ma_don }}</h2>
                <p>Trạng thái: <strong>{{ $statuses[$order->trang_thai] ?? 'Đang xử lý' }}</strong></p>
                <p>Người nhận: {{ $order->ten_nguoi_nhan }}</p>
                <p>Ngày đặt: {{ $order->created_at?->format('d-m-Y H:i') }}</p>
                <h3>Sản phẩm</h3>
                @foreach($order->items as $item)
                    <div class="summary-row"><span>{{ $item->ten_san_pham }} × {{ $item->so_luong }}</span><b>{{ number_format((float) $item->thanh_tien, 0, ',', '.') }}₫</b></div>
                @endforeach
                <div class="summary-row"><span>Tạm tính</span><b>{{ number_format((float) $order->tam_tinh, 0, ',', '.') }}₫</b></div>
                <div class="summary-row"><span>Giao hàng</span><b>{{ number_format((float) $order->phi_giao_hang, 0, ',', '.') }}₫</b></div>
                <div class="summary-row summary-total"><span>Tổng cộng</span><strong>{{ number_format((float) $order->tong_tien, 0, ',', '.') }}₫</strong></div>
            </section>
        @endif
    </div>
</main>
@endsection
