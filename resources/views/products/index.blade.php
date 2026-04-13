@extends('layouts.app')

@php
	$pageTitle = isset($category)
		? (isset($selectedTag) && $selectedTag ? ($category->name . ' / ' . $selectedTag) : $category->name)
		: 'Tất cả sản phẩm';
@endphp

@section('title', $pageTitle . ' - Thế Giới Trái Cây')

@section('content')
<!-- Breadcrumb -->
<section class="bread_crumb py-4">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<ul class="breadcrumb" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<li class="home">
						<a itemprop="url" href="{{ route('home') }}"><span itemprop="title">Trang chủ</span></a>
						<span> <i class="fa fa-angle-right"></i> </span>
					</li>
					@if(isset($category))
						<li>
							<a itemprop="url" href="{{ route('products.index') }}"><span itemprop="title">Sản phẩm</span></a>
							<span> <i class="fa fa-angle-right"></i> </span>
						</li>
						@if(!empty($selectedTag))
							<li>
								<a itemprop="url" href="{{ route('categories.show', $category->slug) }}"><span itemprop="title">{{ $category->name }}</span></a>
								<span> <i class="fa fa-angle-right"></i> </span>
							</li>
						@endif
						<li><strong><span itemprop="title">{{ $pageTitle }}</span></strong></li>
					@else
						<li><strong><span itemprop="title">Tất cả sản phẩm</span></strong></li>
					@endif
				</ul>
			</div>
		</div>
	</div>
</section>

<div class="container">
	<div class="row">
		<!-- Main Product Area -->
		<section class="main_container collection col-lg-9 col-lg-push-3">
			<div class="box-heading hidden relative">
				<h1 class="title-head margin-top-0">{{ $pageTitle }}</h1>
			</div>
			<div class="category-products products">
				@if(isset($category) && isset($childTags) && $childTags->isNotEmpty())
					<div class="collection-tag-filters">
						<a href="{{ route('categories.show', $category->slug) }}" class="collection-tag-btn {{ empty($selectedTag) ? 'is-active' : '' }}">Tất cả</a>
						@foreach($childTags as $tagItem)
							<a href="{{ route('categories.show.tag', ['slug' => $category->slug, 'tag' => $tagItem->name]) }}" class="collection-tag-btn {{ (!empty($selectedTag) && strcasecmp($selectedTag, $tagItem->name) === 0) ? 'is-active' : '' }}">{{ $tagItem->name }}</a>
						@endforeach
					</div>
				@endif

				<!-- Sorting Toolbar -->
				<div class="sortPagiBar">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 text-xs-left text-sm-right">
							<div class="bg-white clearfix">
								<!-- Sort By Dropdown -->
								<div id="sort-by">
									<label class="left hidden-xs">Sắp xếp: </label>
									<ul>
										<li>
											<span class="val">
												@switch(request('sort'))
													@case('alpha-asc')
														A → Z
														@break
													@case('alpha-desc')
														Z → A
														@break
													@case('price-asc')
														Giá tăng dần
														@break
													@case('price-desc')
														Giá giảm dần
														@break
													@default
														Mặc định
												@endswitch
											</span>
											<ul class="ul_2">
												<li><a href="{{ route('products.index') }}">Mặc định</a></li>
												<li><a href="{{ route('products.index', ['sort' => 'alpha-asc']) }}">A → Z</a></li>
												<li><a href="{{ route('products.index', ['sort' => 'alpha-desc']) }}">Z → A</a></li>
												<li><a href="{{ route('products.index', ['sort' => 'price-asc']) }}">Giá tăng dần</a></li>
												<li><a href="{{ route('products.index', ['sort' => 'price-desc']) }}">Giá giảm dần</a></li>
											</ul>
										</li>
									</ul>
								</div>

								<!-- View Mode Toggle -->
								<div class="view-mode f-left">
									<a href="javascript:;" data-view="grid" >
										<b class="btn button-view-mode view-mode-grid active ">
											<i class="fa fa-th" aria-hidden="true"></i>
										</b>
										<span>Lưới</span>
									</a>
									<a href="javascript:;" data-view="list">
										<b class="btn button-view-mode view-mode-list ">
											<i class="fa fa-th-list" aria-hidden="true"></i>
										</b>
										<span>Danh sách</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Product Grid -->
				<section class="products-view products-view-grid">
					<div class="row">
						@if($products->count() > 0)
							@foreach($products as $product)
								@php
									$thumbUrl = $product->thumb_url;
								@endphp
								<div class="col-xs-6 col-xss-6 col-sm-4 col-md-4 col-lg-4">
									<!-- Product Card -->
									<div class="product-box">
										<div class="product-thumbnail flexbox-grid">
											<a href="{{ route('products.show', $product->slug) }}" title="{{ $product->name }}">
												<img src="{{ $thumbUrl }}"
													 data-lazyload="{{ $thumbUrl }}"
													 alt="{{ $product->name }}">
											</a>

											<!-- Sales Flash Badge -->
											@if($product->sale_price && $product->sale_price < $product->price)
												@php
													$discount = round((($product->price - $product->sale_price) / $product->price) * 100);
												@endphp
												<div class="sale-flash"><div class="before"></div>- {{ $discount }}%</div>
											@endif

											<!-- Action Buttons -->
											<div class="product-action hidden-md hidden-sm hidden-xs clearfix">
												<form action="{{ route('cart.add') }}" method="post" class="variants form-nut-grid margin-bottom-0" enctype="multipart/form-data">
													<div>
														@csrf
														<input type="hidden" name="product_id" value="{{ $product->id }}" />
														<input type="hidden" name="quantity" value="1" />
														<button class="btn-buy btn-cart btn btn-primary left-to add_to_cart" data-toggle="tooltip" title="Đặt hàng">
															<i class="fa fa-shopping-bag"></i>
														</button>
														<a href="{{ route('products.show', $product->slug) }}" data-toggle="tooltip" title="Xem nhanh" class="btn-gray btn_view btn right-to quick-view">
															<i class="fa fa-eye"></i>
														</a>
													</div>
												</form>
											</div>
										</div>
										<div class="product-info a-center">
											<h3 class="product-name">
												<a href="{{ route('products.show', $product->slug) }}" title="{{ $product->name }}">
													{{ $product->name }}
												</a>
											</h3>

											<!-- Price Box -->
											<div class="price-box clearfix">
												@if($product->sale_price)
													<div class="special-price">
														<span class="price product-price">{{ number_format($product->sale_price) }}₫</span>
													</div>
													<div class="old-price">
														<span class="price product-price-old">{{ number_format($product->price) }}₫</span>
													</div>
												@else
													<div class="special-price">
														<span class="price product-price">{{ number_format($product->price) }}₫</span>
													</div>
												@endif
											</div>
										</div>
									</div>
								</div>
							@endforeach
						@else
							<div class="col-12 text-center py-5">
								<p class="text-muted lead">Chưa có sản phẩm nào trong danh mục này.</p>
							</div>
						@endif
					</div>

					<!-- Pagination -->
					@if($products->lastPage() > 1)
						@php
							$currentPage = $products->currentPage();
							$lastPage = $products->lastPage();
							$visiblePages = [];

							if ($lastPage <= 7) {
								$visiblePages = range(1, $lastPage);
							} elseif ($currentPage <= 3) {
								$visiblePages = [1, 2, 3, '...', $lastPage];
							} elseif ($currentPage >= $lastPage - 2) {
								$visiblePages = [1, '...', $lastPage - 2, $lastPage - 1, $lastPage];
							} else {
								$visiblePages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
							}
						@endphp

						<div class="text-center mt-5 mb-4">
							<nav class="product-pagination-nav" aria-label="Phan trang san pham">
								<ul class="product-pagination-list">
									@if(!$products->onFirstPage())
										<li>
											<a class="product-page-btn product-page-nav" href="{{ $products->previousPageUrl() }}" aria-label="Trang truoc">
												<i class="fa fa-angle-left" aria-hidden="true"></i>
											</a>
										</li>
									@endif

									@foreach($visiblePages as $page)
										@if($page === '...')
											<li>
												<span class="product-page-ellipsis">...</span>
											</li>
										@elseif($page == $currentPage)
											<li>
												<span class="product-page-btn is-active">{{ $page }}</span>
											</li>
										@else
											<li>
												<a class="product-page-btn" href="{{ $products->url($page) }}">{{ $page }}</a>
											</li>
										@endif
									@endforeach

									@if($products->hasMorePages())
										<li>
											<a class="product-page-btn product-page-nav" href="{{ $products->nextPageUrl() }}" aria-label="Trang sau">
												<i class="fa fa-angle-right" aria-hidden="true"></i>
											</a>
										</li>
									@endif
								</ul>
							</nav>
						</div>
					@endif
				</section>
			</div>
		</section>

<!-- Sidebar Navigation & Filters -->
		<aside class="dqdt-sidebar sidebar left left-content col-lg-3 col-lg-pull-9">
			<!-- Category Navigation -->
			<aside class="aside-item sidebar-category collection-category">
				<div class="aside-title">
					<h2 class="title-head margin-top-0"><span>Danh mục</span></h2>
				</div>
				<div class="aside-content">
					<div class="nav-category navbar-toggleable-md">
						<ul class="nav navbar-pills">
							<li class="nav-item">
								<i class="fa fa-caret-right"></i>
								<a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
							</li>

							<li class="nav-item okactive">
								<i class="fa fa-caret-right"></i>
								<a href="{{ route('products.index') }}" class="nav-link">Sản phẩm</a>
								<i class="fa fa-angle-down menu-arrow"></i>
							</li>

							<li class="nav-item">
								<i class="fa fa-caret-right"></i>
								<a class="nav-link" href="{{ url('/collections/mam-dia-ngu-qua') }}">Mâm dĩa ngũ quả</a>
							</li>

							<li class="nav-item">
								<i class="fa fa-caret-right"></i>
								<a class="nav-link" href="{{ route('about') }}">Giới thiệu</a>
							</li>

							<li class="nav-item">
								<i class="fa fa-caret-right"></i>
								<a class="nav-link" href="{{ route('contact.page') }}">Liên hệ</a>
							</li>
						</ul>
					</div>
				</div>
			</aside>

			<!-- Price Filter -->
			<div class="aside-filter">
				<div class="filter-container">
					<aside class="aside-item filter-price">
						<div class="aside-title">
							<h2 class="title-head margin-top-0"><span>Giá sản phẩm</span></h2>
						</div>
						<div class="aside-content filter-group">
							<ul>
								<li class="filter-item filter-item--check-box filter-item--green">
									<span>
										<label for="filter-duoi-100-000d">
											<input type="checkbox" id="filter-duoi-100-000d"
												   onclick="applyFilter(this, 'price', '0-100000')"
												   {{ request('price') == '0-100000' ? 'checked' : '' }}>
											<i class="fa"></i>
											Giá dưới 100.000₫
										</label>
									</span>
								</li>

								<li class="filter-item filter-item--check-box filter-item--green">
									<span>
										<label for="filter-100-200">
											<input type="checkbox" id="filter-100-200"
												   onclick="applyFilter(this, 'price', '100000-200000')"
												   {{ request('price') == '100000-200000' ? 'checked' : '' }}>
											<i class="fa"></i>
											100.000₫ - 200.000₫
										</label>
									</span>
								</li>

								<li class="filter-item filter-item--check-box filter-item--green">
									<span>
										<label for="filter-200-300">
											<input type="checkbox" id="filter-200-300"
												   onclick="applyFilter(this, 'price', '200000-300000')"
												   {{ request('price') == '200000-300000' ? 'checked' : '' }}>
											<i class="fa"></i>
											200.000₫ - 300.000₫
										</label>
									</span>
								</li>

								<li class="filter-item filter-item--check-box filter-item--green">
									<span>
										<label for="filter-300-500">
											<input type="checkbox" id="filter-300-500"
												   onclick="applyFilter(this, 'price', '300000-500000')"
												   {{ request('price') == '300000-500000' ? 'checked' : '' }}>
											<i class="fa"></i>
											300.000₫ - 500.000₫
										</label>
									</span>
								</li>

								<li class="filter-item filter-item--check-box filter-item--green">
									<span>
										<label for="filter-500-1000">
											<input type="checkbox" id="filter-500-1000"
												   onclick="applyFilter(this, 'price', '500000-1000000')"
												   {{ request('price') == '500000-1000000' ? 'checked' : '' }}>
											<i class="fa"></i>
											500.000₫ - 1.000.000₫
										</label>
									</span>
								</li>
								<li class="filter-item filter-item--check-box filter-item--green">
									<span>
										<label for="filter-tren1000">
											<input type="checkbox" id="filter-tren1000"
												   onclick="applyFilter(this, 'price', 'over-1000000')"
												   {{ request('price') == 'over-1000000' ? 'checked' : '' }}>
											<i class="fa"></i>
											Giá trên 1.000.000₫
										</label>
									</span>
								</li>
							</ul>
						</div>
					</aside>
				</div>
			</div>

			<!-- Featured Products -->
			<div class="aside-item aside-mini-list-product mb-5">
				<div>
					<div class="aside-title">
						<h2 class="title-head">
							<a href="{{ route('products.index') }}">Sản phẩm nổi bật</a>
						</h2>
					</div>
					<div class="aside-content related-product">
						<div class="product-mini-lists">
							<div class="products">
								@foreach($featuredProducts as $fp)
									@php
										$featuredThumbUrl = $fp->thumb_url;
									@endphp
									<div class="row row-noGutter">
										<div class="col-sm-12">
											<div class="product-mini-item clearfix on-sale">
												<div class="product-img relative">
													<a href="{{ route('products.show', $fp->slug) }}">
														<img src="{{ $featuredThumbUrl }}" alt="{{ $fp->name }}" style="width: 100%; height: 100%; object-fit: cover;">
													</a>
												</div>
												<div class="product-info">
													<h3>
														<a href="{{ route('products.show', $fp->slug) }}" title="{{ $fp->name }}" class="product-name">
															{{ \Illuminate\Support\Str::limit($fp->name, 40) }}
														</a>
													</h3>
													<div class="price-box">
														@if($fp->sale_price)
															<span class="price"><span class="price product-price">{{ number_format($fp->sale_price) }}₫</span></span>
															<span class="old-price"><del class="sale-price">{{ number_format($fp->price) }}₫</del></span>
														@else
															<span class="price"><span class="price product-price">{{ number_format($fp->price) }}₫</span></span>
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</aside>
	</div>
</div>

<script>
function applyFilter(checkbox, filterType, filterValue) {
	const url = new URL(window.location);

	if (checkbox.checked) {
		url.searchParams.set(filterType, filterValue);
	} else {
		url.searchParams.delete(filterType);
	}

	window.location.href = url.toString();
}
</script>

@endsection

@push('styles')
<style>
	.collection-category .nav-category .nav-item {
		display: flex !important;
		align-items: center !important;
		float: none !important;
		width: 100%;
		position: relative;
		padding: 12px 0 !important;
		border-bottom: 1px solid #efefef;
	}

	.collection-category .nav-category .nav-item:last-child {
		border-bottom: 0;
	}

	.collection-category .nav-category .nav-item > .fa {
		position: static !important;
		top: auto !important;
		left: auto !important;
		transform: none !important;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 18px;
		height: 18px;
		line-height: 1;
		margin: 0 10px 0 0 !important;
		color: #8b8b8b;
		min-width: 18px;
	}

	.collection-category .nav-category .nav-link {
		color: #333 !important;
		background: transparent !important;
		display: block !important;
		flex: 1;
		padding: 0 !important;
		margin: 0 !important;
		font-size: 14px !important;
		font-weight: 500 !important;
		line-height: 1.4 !important;
		white-space: normal;
	}

	.collection-category .nav-category .nav-item:hover > .nav-link {
		color: #7fbe3b !important;
	}

	.collection-category .nav-category .dropdown-menu {
		display: none;
		position: static;
		float: none;
		border: 0;
		box-shadow: none;
		padding: 8px 0 0 18px;
		margin: 0;
		width: 100%;
	}

	.collection-category .nav-category .menu-arrow {
		position: static !important;
		top: auto !important;
		right: auto !important;
		transform: none !important;
		margin-left: auto;
		color: #666;
		font-size: 12px;
	}

	.product-pagination-nav {
		display: flex;
		justify-content: center;
	}

	.collection-tag-filters {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		margin-bottom: 14px;
	}

	.collection-tag-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		height: 32px;
		padding: 0 12px;
		border-radius: 999px;
		border: 1px solid #dcdcdc;
		background: #fff;
		color: #444;
		text-decoration: none !important;
		font-size: 13px;
		transition: all .2s ease;
	}

	.collection-tag-btn:hover {
		border-color: #7fbe3b;
		color: #7fbe3b;
	}

	.collection-tag-btn.is-active {
		background: #7fbe3b;
		border-color: #7fbe3b;
		color: #fff;
	}

	.product-pagination-list {
		list-style: none;
		padding: 0;
		margin: 0;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.product-page-btn,
	.product-page-ellipsis {
		min-width: 42px;
		height: 42px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 0 12px;
		border: 1px solid #d9d9d9;
		border-radius: 12px;
		background: #fff;
		color: #333;
		font-size: 22px;
		line-height: 1;
		text-decoration: none !important;
		transition: all 0.2s ease;
	}

	.product-page-btn {
		font-size: 22px;
		font-weight: 400;
	}

	.product-page-btn:hover {
		border-color: #7fbe3b;
		color: #7fbe3b;
	}

	.product-page-btn.is-active {
		background: #7fbe3b;
		border-color: #7fbe3b;
		color: #fff;
	}

	.product-page-ellipsis {
		font-size: 24px;
		color: #444;
	}

	.product-page-nav {
		font-size: 24px;
	}

	@media (max-width: 576px) {
		.product-pagination-list {
			gap: 6px;
		}

		.product-page-btn,
		.product-page-ellipsis {
			min-width: 38px;
			height: 38px;
			padding: 0 10px;
			border-radius: 10px;
			font-size: 20px;
		}
	}
</style>
@endpush
