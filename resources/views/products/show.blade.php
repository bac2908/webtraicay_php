@extends('layouts.app')

@section('title', $product->name . ' - Thế Giới Trái Cây')

@section('content')
@php
    $galleryImages = collect([$product->thumb_url])
        ->merge(
            $product->images->map(function ($img) {
                $url = trim((string) $img->url);
                if ($url === '') {
                    return null;
                }

                if (\Illuminate\Support\Str::startsWith($url, ['http://', 'https://', '//'])) {
                    return $url;
                }

                return asset(ltrim($url, '/'));
            })
        )
        ->filter()
        ->unique()
        ->values();

    if ($galleryImages->isEmpty()) {
        $galleryImages = collect(['//theme.hstatic.net/200000157781/1001036201/14/no-image.jpg?v=1064']);
    }

    $thumbUrl = $galleryImages->first();
    $displayPrice = $product->sale_price && $product->sale_price < $product->price
        ? $product->sale_price
        : $product->price;

    $productDescription = trim((string) ($product->description ?? ''));
@endphp

<section class="bread_crumb py-4">
    <div class="container">
        <ul class="breadcrumb">
            <li class="home">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span> <i class="fa fa-angle-right"></i> </span>
            </li>
            <li>
                <a href="{{ route('products.index') }}">Tất cả sản phẩm</a>
                <span> <i class="fa fa-angle-right"></i> </span>
            </li>
            <li><strong>{{ $product->name }}</strong></li>
        </ul>
    </div>
</section>

<section class="product-detail-wrap">
    <div class="container">
        <div class="product-detail-card">
            <div class="row">
                <div class="col-md-6">
                    <div class="product-gallery">
                        <div class="product-image-box">
                            <img id="product-main-image" src="{{ $thumbUrl }}" alt="{{ $product->name }}">
                        </div>
                        @if($galleryImages->count() > 1)
                            <div class="product-thumb-list">
                                @foreach($galleryImages as $image)
                                    <button type="button" class="product-thumb-btn {{ $loop->first ? 'is-active' : '' }}" data-image="{{ $image }}">
                                        <img src="{{ $image }}" alt="{{ $product->name }} {{ $loop->iteration }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="product-meta">
                        <h1 class="product-title">{{ $product->name }}</h1>
                        <p class="product-sub">Đơn vị: {{ $product->unit ?? 'Sản phẩm' }}</p>
                        <p class="product-sub">Tình trạng: {{ $product->stock > 0 ? 'Còn hàng' : 'Tạm hết hàng' }}</p>

                        <div class="product-price-row">
                            <span class="product-price-main">{{ number_format($displayPrice) }}₫</span>
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="product-price-old">{{ number_format($product->price) }}₫</span>
                            @endif
                        </div>

                        @if(!empty($product->short_desc))
                            <p class="product-short-desc">{{ $product->short_desc }}</p>
                        @endif

                        <form action="{{ route('cart.add') }}" method="post" class="buy-form-wrap">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="buy-qty-row">
                                <label for="qty-input">Số lượng</label>
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" data-action="minus">-</button>
                                    <input id="qty-input" type="number" min="1" name="quantity" value="1">
                                    <button type="button" class="qty-btn" data-action="plus">+</button>
                                </div>
                            </div>

                            <div class="buy-action-row">
                                <button type="submit" class="btn-add-cart">Thêm vào giỏ hàng</button>
                                <button type="submit" name="checkout_redirect" value="1" class="btn-buy-now">Mua ngay</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="detail-tabs-wrap">
                <div class="detail-tabs-head">
                    <button class="detail-tab-btn is-active" type="button" data-tab="description">Mô tả sản phẩm</button>
                    <button class="detail-tab-btn" type="button" data-tab="reviews">Đánh giá</button>
                </div>

                <div class="detail-tab-content is-active" id="tab-description">
                    @if($productDescription !== '')
                        {!! nl2br(e($productDescription)) !!}
                    @elseif(!empty($product->short_desc))
                        <p>{{ $product->short_desc }}</p>
                    @else
                        <p>Sản phẩm đang được cập nhật mô tả chi tiết. Vui lòng liên hệ để được tư vấn nhanh.</p>
                    @endif
                </div>

                <div class="detail-tab-content" id="tab-reviews">
                    <div class="review-list">
                        @foreach($reviewSamples as $review)
                            <article class="review-item">
                                <div class="review-head">
                                    <strong>{{ $review['name'] }}</strong>
                                    <span>{{ $review['created_at'] }}</span>
                                </div>
                                <div class="review-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star {{ $i <= $review['rating'] ? 'is-on' : '' }}"></i>
                                    @endfor
                                </div>
                                <p>{{ $review['content'] }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="review-form-wrap">
                        <h4>Gửi đánh giá của bạn</h4>
                        <form action="javascript:void(0)">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="Họ và tên">
                                </div>
                                <div class="col-sm-6">
                                    <input type="email" class="form-control" placeholder="Email">
                                </div>
                            </div>
                            <textarea class="form-control" rows="4" placeholder="Nội dung đánh giá"></textarea>
                            <button type="button" class="btn-review-submit">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
<section class="related-products-wrap">
    <div class="container">
        <div class="section-title a-center title_line">
            <h2><span>Sản phẩm liên quan</span></h2>
        </div>

        <div class="row">
            @foreach($relatedProducts as $related)
                <div class="col-xs-6 col-sm-4 col-md-3">
                    <x-products.card :product="$related" />
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
    .product-detail-wrap {
        margin-block-end: 26px;
    }

    .product-detail-card {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 12px;
        padding: 22px;
    }

    .product-image-box {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 12px;
        background: #fff;
    }

    .product-image-box img {
        inline-size: 100%;
        block-size: auto;
        border-radius: 8px;
        display: block;
    }

    .product-thumb-list {
        margin-block-start: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .product-thumb-btn {
        border: 1px solid #e6e6e6;
        border-radius: 8px;
        padding: 4px;
        background: #fff;
        inline-size: 70px;
        block-size: 70px;
        cursor: pointer;
    }

    .product-thumb-btn img {
        inline-size: 100%;
        block-size: 100%;
        object-fit: cover;
        border-radius: 6px;
    }

    .product-thumb-btn.is-active {
        border-color: #7fbe3b;
    }

    .product-meta {
        padding-inline-start: 8px;
    }

    .product-title {
        margin: 0 0 10px;
        font-size: 30px;
        font-weight: 700;
        color: #242424;
    }

    .product-sub {
        margin: 0 0 6px;
        font-size: 14px;
        color: #666;
    }

    .product-price-row {
        margin: 14px 0;
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    .product-price-main {
        color: #ff9800;
        font-size: 34px;
        line-height: 1;
        font-weight: 700;
    }

    .product-price-old {
        color: #9ca3af;
        text-decoration: line-through;
        font-size: 20px;
    }

    .product-short-desc {
        color: #4f4f4f;
        line-height: 1.8;
    }

    .buy-form-wrap {
        margin-block-start: 18px;
        border-block-start: 1px dashed #e5e5e5;
        padding-block-start: 16px;
    }

    .buy-qty-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-block-end: 14px;
    }

    .buy-qty-row label {
        margin: 0;
        font-weight: 600;
        color: #333;
    }

    .qty-control {
        display: inline-flex;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        block-size: 40px;
    }

    .qty-btn {
        inline-size: 40px;
        border: 0;
        background: #f8f8f8;
        font-size: 20px;
        color: #444;
    }

    .qty-control input {
        inline-size: 58px;
        border: 0;
        text-align: center;
        font-size: 15px;
    }

    .buy-action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-add-cart,
    .btn-buy-now {
        min-inline-size: 180px;
        block-size: 42px;
        border-radius: 999px;
        border: 0;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-add-cart {
        background: #7fbe3b;
    }

    .btn-buy-now {
        background: #f7941e;
    }

    .btn-add-cart:hover,
    .btn-buy-now:hover {
        color: #fff;
        opacity: .92;
    }

    .detail-tabs-wrap {
        margin-block-start: 20px;
        border-block-start: 1px solid #eee;
        padding-block-start: 16px;
    }

    .detail-tabs-head {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-block-end: 14px;
    }

    .detail-tab-btn {
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 999px;
        padding: 8px 14px;
        color: #555;
        font-size: 14px;
    }

    .detail-tab-btn.is-active {
        border-color: #7fbe3b;
        background: #7fbe3b;
        color: #fff;
    }

    .detail-tab-content {
        display: none;
        color: #4f4f4f;
        line-height: 1.8;
    }

    .detail-tab-content.is-active {
        display: block;
    }

    .review-list {
        display: grid;
        gap: 10px;
    }

    .review-item {
        border: 1px solid #ededed;
        border-radius: 10px;
        padding: 12px;
        background: #fcfcfc;
    }

    .review-head {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-block-end: 6px;
        color: #333;
    }

    .review-stars {
        margin-block-end: 6px;
    }

    .review-stars .fa {
        color: #d7d7d7;
    }

    .review-stars .fa.is-on {
        color: #f8b300;
    }

    .review-item p {
        margin: 0;
    }

    .review-form-wrap {
        margin-block-start: 14px;
        border-block-start: 1px dashed #e4e4e4;
        padding-block-start: 12px;
    }

    .review-form-wrap h4 {
        font-size: 18px;
        margin: 0 0 10px;
    }

    .review-form-wrap .form-control {
        margin-block-end: 10px;
        border-radius: 8px;
        min-block-size: 38px;
    }

    .btn-review-submit {
        block-size: 38px;
        border: 0;
        border-radius: 999px;
        background: #7fbe3b;
        color: #fff;
        padding: 0 16px;
    }

    .related-products-wrap {
        margin-block-end: 42px;
    }

    @media (max-inline-size: 991px) {
        .product-meta {
            padding-inline-start: 0;
            margin-block-start: 16px;
        }

        .product-title {
            font-size: 24px;
        }

        .product-price-main {
            font-size: 30px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        var mainImage = document.getElementById('product-main-image');
        var thumbButtons = document.querySelectorAll('.product-thumb-btn');
        var qtyInput = document.getElementById('qty-input');
        var qtyButtons = document.querySelectorAll('.qty-btn');
        var tabButtons = document.querySelectorAll('.detail-tab-btn');
        var tabContents = {
            description: document.getElementById('tab-description'),
            reviews: document.getElementById('tab-reviews')
        };

        thumbButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var img = btn.getAttribute('data-image');
                if (mainImage && img) {
                    mainImage.setAttribute('src', img);
                }

                thumbButtons.forEach(function (item) {
                    item.classList.remove('is-active');
                });
                btn.classList.add('is-active');
            });
        });

        qtyButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!qtyInput) {
                    return;
                }

                var current = parseInt(qtyInput.value || '1', 10);
                if (Number.isNaN(current) || current < 1) {
                    current = 1;
                }

                if (btn.getAttribute('data-action') === 'plus') {
                    qtyInput.value = current + 1;
                    return;
                }

                qtyInput.value = Math.max(1, current - 1);
            });
        });

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tab = btn.getAttribute('data-tab');
                if (!tab || !tabContents[tab]) {
                    return;
                }

                tabButtons.forEach(function (item) {
                    item.classList.remove('is-active');
                });
                btn.classList.add('is-active');

                Object.keys(tabContents).forEach(function (key) {
                    tabContents[key].classList.toggle('is-active', key === tab);
                });
            });
        });
    })();
</script>
@endpush
