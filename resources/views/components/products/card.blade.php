@props(['product'])

@php
    $discount = 0;
	$isSalePrice = $product->price > 0 && $product->sale_price > 0 && $product->sale_price < $product->price;
	$isContactPrice = !$isSalePrice && (int) $product->price <= 0;
	$hasGearDetail = (bool) ($product->has_gear_detail ?? false);

	if ($isSalePrice) {
        $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
    }
	$imgUrl = $product->primary_image_url;
	$productShowUrl = route('products.show', $product->slug);
@endphp

<div class="product-box">
	<div class="product-thumbnail flexbox-grid">
		<a href="{{ $productShowUrl }}" title="{{ $product->name }}">
			<img src="{{ $imgUrl }}" data-lazyload="{{ $imgUrl }}" alt="{{ $product->name }}">
		</a>

		@if($discount > 0)
		<div class="sale-flash"><div class="before"></div>- {{ $discount }}% </div>
		@endif

		<div class="product-action hidden-md hidden-sm hidden-xs clearfix">
			@if(!$isContactPrice && !$hasGearDetail)
				<form action="{{ route('cart.add') }}" method="post" class="variants form-nut-grid margin-bottom-0" data-id="product-{{ $product->id }}">
					<div>
						@csrf
						<input type="hidden" name="product_id" value="{{ $product->id }}" />
						<input type="hidden" name="quantity" value="1" />
						<button type="submit" class="btn-buy btn-cart btn btn-primary left-to add_to_cart" data-toggle="tooltip" title="Đặt hàng">
							<i class="fa fa-shopping-bag"></i>
						</button>
						<a href="{{ $productShowUrl }}" title="Xem nhanh" class="btn-gray product-detail-link btn right-to">
							<i class="fa fa-eye"></i>
						</a>
					</div>
				</form>
			@else
				<div class="variants form-nut-grid margin-bottom-0" data-id="product-{{ $product->id }}">
					<div>
						@if(!$isContactPrice && $hasGearDetail)
							<a href="{{ $productShowUrl }}" class="btn-cart btn btn-primary left-to" title="Chọn sản phẩm">
								<i class="fa fa-gear"></i>
							</a>
						@endif

						<a href="{{ $productShowUrl }}" title="Xem nhanh" class="btn-gray product-detail-link btn right-to">
							<i class="fa fa-eye"></i>
						</a>
					</div>
				</div>
			@endif
		</div>
	</div>
	<div class="product-info a-center">
		<h3 class="product-name"><a href="{{ $productShowUrl }}" title="{{ $product->name }}">{{ $product->name }}</a></h3>

		<div class="price-box clearfix">
			@if($isContactPrice)
				<div class="special-price clearfix">
					<span class="price product-price">Liên hệ</span>
				</div>
			@elseif($isSalePrice)
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
