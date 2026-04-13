@extends('layouts.app')

@section('title', 'Đặt hàng thành công - Thế Giới Trái Cây')

@section('content')
<section class="bread_crumb py-4">
    <div class="container">
        <ul class="breadcrumb">
            <li class="home">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span> <i class="fa fa-angle-right"></i> </span>
            </li>
            <li><strong>Đặt hàng thành công</strong></li>
        </ul>
    </div>
</section>

<section class="checkout-success-wrap">
    <div class="container">
        <div class="success-card">
            <div class="icon"><i class="fa fa-check-circle"></i></div>
            <h1>Cảm ơn bạn đã đặt hàng</h1>
            <p>Mã đơn hàng của bạn là <strong>{{ $order->code }}</strong>. Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.</p>

            <div class="order-meta">
                <p><span>Khách hàng:</span> {{ $order->customer_name }}</p>
                <p><span>Điện thoại:</span> {{ $order->customer_phone ?: 'Chưa cung cấp' }}</p>
                <p><span>Email:</span> {{ $order->customer_email ?: 'Chưa cung cấp' }}</p>
                <p><span>Địa chỉ:</span> {{ $order->shipping_address }}</p>
                <p><span>Tổng tiền:</span> {{ number_format($order->total) }}₫</p>
            </div>

            <div class="action-row">
                <a href="{{ route('products.index') }}" class="btn-go-shop">Tiếp tục mua sắm</a>
                <a href="{{ route('home') }}" class="btn-go-home">Về trang chủ</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .checkout-success-wrap {
        padding-bottom: 36px;
    }

    .success-card {
        max-width: 700px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 14px;
        text-align: center;
        padding: 28px 22px;
    }

    .success-card .icon {
        font-size: 56px;
        color: #7fbe3b;
        margin-bottom: 8px;
    }

    .success-card h1 {
        margin: 0 0 10px;
        color: #2f2f2f;
        font-size: 32px;
        font-weight: 700;
    }

    .success-card > p {
        color: #555;
        font-size: 15px;
        margin: 0 0 16px;
    }

    .order-meta {
        text-align: left;
        margin: 0 auto 16px;
        max-width: 520px;
        border: 1px dashed #e4e4e4;
        border-radius: 10px;
        padding: 14px;
        background: #fafafa;
    }

    .order-meta p {
        margin: 0 0 6px;
        color: #444;
    }

    .order-meta p:last-child {
        margin-bottom: 0;
    }

    .order-meta span {
        display: inline-block;
        min-width: 110px;
        color: #777;
    }

    .action-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-go-shop,
    .btn-go-home {
        height: 40px;
        padding: 0 18px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        font-weight: 600;
    }

    .btn-go-shop {
        background: #7fbe3b;
        color: #fff;
    }

    .btn-go-home {
        border: 1px solid #ddd;
        color: #555;
        background: #fff;
    }
</style>
@endpush
