@props(['product'])

@php
    $discount = 0;
    if ($product->price > 0 && $product->sale_price > 0 && $product->sale_price < $product->price) {
        $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
    }
	$imgUrl = $product->thumb_url;
@endphp

<div class="product-box">
	<div class="product-thumbnail flexbox-grid">
		<a href="{{ route('products.show', $product->slug) }}" title="{{ $product->name }}">
			<img src="{{ $imgUrl }}" data-lazyload="{{ $imgUrl }}" alt="{{ $product->name }}">
		</a>

		@if($discount > 0)
		<div class="sale-flash"><div class="before"></div>- {{ $discount }}% </div>
		@endif

		<div class="product-action hidden-md hidden-sm hidden-xs clearfix">
			<form action="{{ route('cart.add') }}" method="post" class="variants form-nut-grid margin-bottom-0" data-id="product-{{ $product->id }}">
				<div>
					@csrf
					<input type="hidden" name="product_id" value="{{ $product->id }}" />
					<input type="hidden" name="quantity" value="1" />
					<button class="btn-buy btn-cart btn btn-primary left-to add_to_cart" data-toggle="tooltip" title="Đặt hàng">
						<i class="fa fa-shopping-bag"></i>
					</button>
					<a href="{{ route('products.show', $product->slug) }}" data-toggle="tooltip" title="Xem nhanh" class="btn-gray btn_view btn right-to quick-view">
						<i class="fa fa-eye"></i></a>
				</div>
			</form>
		</div>
	</div>
	<div class="product-info a-center">
		<h3 class="product-name"><a href="{{ route('products.show', $product->slug) }}" title="{{ $product->name }}">{{ $product->name }}</a></h3>

		<div class="price-box clearfix">
			@if($product->sale_price > 0 && $product->sale_price < $product->price)
				<div class="special-price">
					<span class="price product-price">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
				</div>
				<div class="old-price">
					<span class="price product-price-old">{{ number_format($product->price, 0, ',', '.') }}₫</span>
				</div>
			@else
				<div class="special-price">
					<span class="price product-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
				</div>
			@endif
		</div>
	</div>
</div>
