<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>@yield('title', 'Thế Giới Trái Cây - Trái cây Việt Nam loại 1 & nhập khẩu cao cấp')</title>
	<meta name="description" content="@yield('meta_description', 'The Gioi Trai Cay - Trai cay Viet Nam loai 1 va nhap khau chat luong cao.')">

	<link rel="canonical" href="@yield('canonical', url()->current())" />
	<meta name="robots" content="index,follow" />
	<meta name="revisit-after" content="1 day" />
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="HandheldFriendly" content="true">
	<link rel="icon" href="//theme.hstatic.net/200000157781/1001036201/14/favicon.png?v=1061" type="image/x-icon" />

	<!-- Fonts & Icons -->
	<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i&amp;subset=vietnamese" rel="stylesheet">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

	<!-- Toàn bộ CSS chính thức từ thegioitraicay.net -->
	<link href='//theme.hstatic.net/200000157781/1001036201/14/plugin.scss.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/base.scss.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/style.scss.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/module.scss.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/responsive.scss.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/bootstrap-theme.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/style-theme.scss.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/responsive-update.scss.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	<link href='//theme.hstatic.net/200000157781/1001036201/14/hrv-style.css?v=1061' rel='stylesheet' type='text/css'  media='all'  />
	@stack('head_meta')

	@yield('styles')
</head>
<body>
	@php
		$headerCartCount = collect(session('cart', []))->sum(function ($item) {
			return (int) ($item['quantity'] ?? 0);
		});
	@endphp
	<header class="header">
	<div class="topbar-mobile hidden-lg hidden-md text-center text-md-left">
		<div class="container">
			<i class="fa fa-mobile" style=" font-size: 20px; display: inline-block; position: relative; transform: translateY(2px); "></i> Hotline:
			<span>
				<a href="callto:0909131418"> 0909131418</a>
			</span>
		</div>
	</div>
	<div class="topbar hidden-sm hidden-xs">
	 	<div class="container">
		<div class="d-flex justify-content-between align-items-center">
			<div class="topbar-left">
				<span class="margin-right-20">
					<i class="fa fa-mobile" style="font-size: 16px;"></i> Hotline: <b>0909131418 - 0798531637</b>
				</span>
				<span>
					<i class="fa fa-map-marker"></i> Địa chỉ: 338 Hai Bà Trưng, Phường Tân Định, Quận 1, Tp Hồ Chí Minh
				</span>
				</div>
				<div class="topbar-right">
				<a href="#"><i class="fa fa-user"></i> Đăng nhập</a>
				<span class="margin-left-5 margin-right-5">hoặc</span>
				<a href="#">Đăng ký</a>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="header-content clearfix">
			<div class="row align-items-center">
				{{-- Logo --}}
				<div class="col-xs-12 col-md-3">
					<div class="logo">
						<a href="{{ route('home') }}" class="logo-wrapper">
							<img src="//theme.hstatic.net/200000157781/1001036201/14/logo.png?v=1061" alt="logo Thế Giới Trái Cây">
						</a>
					</div>
				</div>

				{{-- Policy and Cart --}}
				<div class="col-xs-12 col-md-9 hidden-xs">
					<div class="header-right d-flex align-items-center justify-content-between">
						<div class="policy d-flex justify-content-around flex-1">
							<div class="item-policy d-flex align-items-center">
								<a href="#"><img src="//theme.hstatic.net/200000157781/1001036201/14/policy1.png?v=1061" alt=""></a>
								<div class="info">
									<a href="#">Miễn phí vận chuyển</a>
									<p>Đơn hàng từ 500k</p>
								</div>
							</div>
							<div class="item-policy d-flex align-items-center">
								<a href="#"><img src="//theme.hstatic.net/200000157781/1001036201/14/policy2.png?v=1061" alt=""></a>
								<div class="info">
								<a href="#">Hỗ trợ 24/7</a>
									<p>Hotline: 0909131418</p>
								</div>
							</div>
							<div class="item-policy d-flex align-items-center">
								<a href="#"><img src="//theme.hstatic.net/200000157781/1001036201/14/policy3.png?v=1061" alt=""></a>
								<div class="info">
								<a href="#">Giờ làm việc</a>
									<p>T2 - CN (7:00-19:00)</p>
								</div>
							</div>
						</div>

						<div class="top-cart-contain">
							<div class="mini-cart">
								<div class="heading-cart">
									<a href="{{ route('cart') }}" class="d-flex align-items-center">
										<div class="icon relative" style="background: #ff9800; color: #fff; padding: 8px 15px; border-radius: 20px; display: flex; align-items: center;">
											<i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>
										<span class="label" style="font-weight: bold;">Giỏ hàng ({{ $headerCartCount }})</span>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

		<div class="menu-bar hidden-md hidden-lg">
			<img src='//theme.hstatic.net/200000157781/1001036201/14/menu-bar.png?v=1061' alt='menu bar'  />
		</div>
		<div class="icon-cart-mobile hidden-md hidden-lg f-left absolute" data-href="{{ route('cart') }}" onclick="window.location.href=this.getAttribute('data-href');">
			<div class="icon relative">
				<i class="fa fa-shopping-bag"></i>
				<span class="cartCount count_item_pr">{{ $headerCartCount }}</span>
			</div>
		</div>
	</div>
	<nav>
		<div class="container">
			<div class="hidden-sm hidden-xs d-flex align-items-center justify-content-between">
				<ul class="nav nav-left">
					<li class="nav-item {{ request()->is('/') ? 'active' : '' }}"><a class="nav-link" href="{{ route('home') }}">Trang chủ</a></li>
					<li class="nav-item {{ request()->is('collections/all') ? 'active' : '' }} has-mega">
						<a href="{{ route('products.index') }}" class="nav-link">Sản phẩm <i class="fa fa-angle-right" data-toggle="dropdown"></i></a>
						<div class="mega-content">
							<div class="level0-wrapper2">
								<div class="nav-block nav-block-center">
									<ul class="level0">
										@foreach(($megaCategories ?? collect()) as $category)
										<li class="level1 parent item">
											<h2 class="h4"><a href="{{ route('categories.show', $category->slug) }}"><span>{{ $category->name }}</span></a></h2>
											@php
												$megaItems = collect($category->mega_items ?? []);
											@endphp
											@if($megaItems->isNotEmpty())
											<ul class="level1">
												@foreach($megaItems as $menuItem)
												<li class="level2">
														<a href="{{ $menuItem['url'] }}"><span>{{ $menuItem['label'] }}</span></a>
												</li>
												@endforeach
											</ul>
											@endif
										</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					</li>
				<li class="nav-item {{ request()->is('collections/mam-dia-ngu-qua') ? 'active' : '' }}"><a class="nav-link" href="{{ url('/collections/mam-dia-ngu-qua') }}">Mâm dĩa ngũ quả</a></li>
				<li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}"><a class="nav-link" href="{{ route('about') }}">Giới thiệu</a></li>
				<li class="nav-item {{ request()->routeIs('contact.page') ? 'active' : '' }}"><a class="nav-link" href="{{ route('contact.page') }}">Liên hệ</a></li>
				</ul>

				<div class="menu-search">
					<div class="header_search search_form">
						<form class="input-group search-bar search_form" action="{{ route('search') }}" method="get" role="search">
							<input type="search" name="q" value="" placeholder="Tìm sản phẩm" class="input-group-field search-text auto-search" autocomplete="off">
							<span class="input-group-btn">
								<button class="btn">
									<i class="fa fa-search"></i>
								</button>
							</span>
						</form>
					</div>
				</div>
			</div>
		</div>
	</nav>
</header>

	<main>
		@yield('content')
	</main>

	<style>
		/* Sticky Sidebar - Contact/Social Icons */
		.contact-float-wrapper {
			position: fixed !important;
			right: 0;
			top: 50%;
			transform: translateY(-50%);
			z-index: 9999;
			display: flex;
			flex-direction: column;
			gap: 0;
		}

		.contact-float-item {
			width: 48px;
			height: 48px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 50%;
			cursor: pointer;
			transition: all 0.3s ease;
			box-shadow: 0 2px 8px rgba(0,0,0,0.15);
		}

		.contact-float-item a {
			width: 100%;
			height: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff !important;
			text-decoration: none !important;
			font-size: 20px;
			border-radius: 50%;
		}

		.contact-float-item a:hover {
			transform: scale(1.1);
		}

		.contact-float-item.phone { background: #ef4444; }
		.contact-float-item.phone:hover { background: #dc2626; }

		.contact-float-item.zalo { background: #0084ff; }
		.contact-float-item.zalo:hover { background: #0073e6; }

		.contact-float-item.email { background: #ec4899; }
		.contact-float-item.email:hover { background: #be185d; }

		.contact-float-item.shop { background: #ea580c; }
		.contact-float-item.shop:hover { background: #c2410c; }

		.contact-float-item.instagram { background: #e1306c; }
		.contact-float-item.instagram:hover { background: #c13584; }

		.contact-float-item.tiktok { background: #000; }
		.contact-float-item.tiktok:hover { background: #333; }

		.contact-float-item.location { background: #f59e0b; }
		.contact-float-item.location:hover { background: #d97706; }

		@media (max-width: 768px) {
			.contact-float-wrapper {
				right: 10px;
				gap: 8px;
			}
			.contact-float-item {
				width: 44px;
				height: 44px;
			}
		}

		.fixed-sidebar,
		.sidebar-contact,
		[class*="contact-sidebar"],
		.contact-float {
			position: fixed !important;
			right: 0;
			top: 50%;
			transform: translateY(-50%);
			z-index: 9999;
		}

		/* Flexbox Utils */
		.d-flex { display: flex !important; }
		.align-items-center { align-items: center !important; }
		.justify-content-between { justify-content: space-between !important; }

		/* Topbar */
		.topbar { background: #8bc34a !important; color: #fff !important; padding: 10px 0; font-size: 13px; }
		.topbar a { color: #fff !important; text-decoration: none; font-weight: bold; }
		.topbar i { margin-right: 5px; }

		/* Header */
		.header-content { padding: 20px 0; background: #fff; }
		.item-policy { padding: 0 15px; }
		.item-policy .info a { font-weight: bold; color: #333; font-size: 14px; }
		.item-policy .info p { font-size: 12px; color: #666; margin: 0; }
		.icon.relative { background: #ff9800 !important; color: #fff !important; padding: 10px 20px; border-radius: 25px; transition: 0.3s; }
		.icon.relative:hover { background: #e68a00 !important; }

		/* Nav - Sticky fallback using fixed when scrolled */
		nav {
			background: #8bc34a !important;
			border-top: 1px solid rgba(255,255,255,0.1) !important;
			position: relative !important;
			z-index: 1000 !important;
			width: 100% !important;
			margin: 0 !important;
			padding: 0 !important;
			box-sizing: border-box !important;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
			transition: position 0.3s ease !important;
		}
		nav.fixed-nav {
			position: fixed !important;
			top: 0 !important;
			left: 0 !important;
			right: 0 !important;
		}
		nav > .container {
			max-width: 100%;
			margin: 0 auto;
		}
		nav .nav-left > .nav-item { display: inline-block; }
		nav .nav-left > .nav-item > .nav-link { color: #fff !important; font-weight: bold; text-transform: none; font-size: 14px; padding: 15px 20px !important; display: block; }
		nav .nav-left > .nav-item.active > .nav-link { background: #ff9800 !important; }
		nav .nav-left > .nav-item:hover > .nav-link { background: rgba(0,0,0,0.05); }

		nav .nav-left > .nav-item.has-mega {
			position: relative;
		}

		nav .nav-left > .nav-item.has-mega .mega-content {
			left: 0 !important;
			right: auto !important;
			width: min(1120px, calc(100vw - 32px)) !important;
			max-width: calc(100vw - 32px) !important;
			padding: 14px 18px 18px !important;
			border: 1px solid #ececec;
			background: #fff;
			box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
			z-index: 1002;
		}

		nav .nav-left > .nav-item.has-mega .level0-wrapper2,
		nav .nav-left > .nav-item.has-mega .nav-block,
		nav .nav-left > .nav-item.has-mega .nav-block.nav-block-center {
			width: 100% !important;
			max-width: 100% !important;
		}

		nav .nav-left > .nav-item.has-mega .level0 {
			display: grid;
			grid-template-columns: repeat(4, minmax(180px, 1fr));
			gap: 24px;
			margin: 0;
			padding: 0;
			list-style: none;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item {
			margin: 0;
			padding: 0;
			list-style: none;
			float: none !important;
			width: auto !important;
			min-width: 0;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > h2 {
			margin: 0 0 10px;
			font-size: 16px;
			line-height: 1.2;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > h2 a {
			color: #1f1f1f !important;
			text-decoration: none !important;
			font-weight: 700;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > h2 a span {
			display: block;
			white-space: nowrap;
			overflow: visible;
			text-overflow: clip;
			max-width: none;
			word-break: keep-all;
			overflow-wrap: normal;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > ul.level1 {
			margin: 0;
			padding: 0;
			list-style: none;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > ul.level1 > li.level2 {
			margin: 0 0 10px;
			padding: 0;
			float: none !important;
			width: auto !important;
			min-width: 0;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > ul.level1 > li.level2:last-child {
			margin-bottom: 0;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > ul.level1 > li.level2 a {
			color: #333 !important;
			font-size: 14px;
			line-height: 1.25;
			font-weight: 500;
			text-decoration: none !important;
			white-space: nowrap;
			overflow: visible;
			text-overflow: clip;
			display: block;
			max-width: none;
			word-break: keep-all;
			overflow-wrap: normal;
		}

		nav .nav-left > .nav-item.has-mega .level1.parent.item > ul.level1 > li.level2 a:hover {
			color: #7fbe3b !important;
		}

		@media (max-width: 1399px) {
			nav .nav-left > .nav-item.has-mega .level1.parent.item > h2 {
				font-size: 15px;
			}

			nav .nav-left > .nav-item.has-mega .level1.parent.item > ul.level1 > li.level2 a {
				font-size: 13px;
			}
		}

		/* Search */
		.menu-search { padding: 8px 0; }
		.header_search form { background: #fff; border-radius: 25px; height: 34px; padding: 0 15px; width: 250px; display: flex; align-items: center; }
		.header_search input { border: none !important; width: 100%; font-size: 13px; outline: none !important; height: 100%; }
		.header_search .btn { color: #333 !important; font-size: 16px; padding: 0; background: none; }

		/* Grid Failsafe */
		.row { margin-left: -15px; margin-right: -15px; display: block; }
		.row:before, .row:after { content: " "; display: table; }
		.row:after { clear: both; }
		[class*="col-"] { position: relative; min-height: 1px; padding-left: 15px; padding-right: 15px; float: left; box-sizing: border-box; }
		.col-md-3 { width: 25%; }
		.col-md-9 { width: 75%; }
		.col-md-4 { width: 33.33333333%; }
		.col-md-8 { width: 66.66666667%; }
		.col-md-12 { width: 100%; }

		@media (max-width: 991px) {
			[class*="col-md-"] { width: 100% !important; float: none !important; }
		}

		/* Product UI matching */
		.product-box { border: 1px solid #eee; transition: 0.3s; padding: 10px; background: #fff; border-radius: 10px; margin-bottom: 20px; position: relative; overflow: hidden; }
		.product-box:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-color: #8bc34a; }
		.product-thumbnail img { width: 100%; height: auto; object-fit: cover; }
		.product-name a { color: #333 !important; font-size: 14px; font-weight: 700; text-decoration: none; display: block; margin-top: 10px; height: 40px; overflow: hidden; }

		.product-action { display: none; position: absolute; bottom: 20px; left: 0; right: 0; text-align: center; }
		.product-box:hover .product-action { display: block; }
		.btn-cart, .btn_view, .product-detail-link { background: #8bc34a; color: #fff; width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

		.sale-flash { position: absolute; top: 10px; left: 10px; background: #ff9800; color: #fff; padding: 2px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; z-index: 5; }

		/* Coupons */
		.coupon-item__inner { border: 1px dashed #8bc34a; display: flex; padding: 15px; border-radius: 8px; background: #f9fff0; margin-bottom: 15px; }

		/* Footer Replica (thegioitraicay.net style) */
		.section_brand.section,
		.section.section-brand {
			display: none !important;
		}

		.tgc-brand-strip {
			background: #fff;
			padding: 22px 0 18px;
			border-top: 1px solid #efefef;
		}

		.tgc-brand-grid {
			display: grid;
			grid-template-columns: repeat(6, minmax(90px, 1fr));
			gap: 24px;
			align-items: center;
		}

		.tgc-brand-item {
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 72px;
		}

		.tgc-brand-item img {
			max-width: 100%;
			max-height: 68px;
			object-fit: contain;
			filter: saturate(0.95);
		}

		.footer {
			background: #679a24;
			color: #fff;
			margin-top: 0;
			padding-top: 0;
			border-top: 0;
		}

		.footer .site-footer {
			background: #679a24;
		}

		.footer .footer-inner {
			padding: 36px 0 28px;
		}

		.footer .footer-widget {
			margin-bottom: 18px;
		}

		.footer .footer-widget h3 {
			color: #fff;
			font-size: 16px;
			font-weight: 700;
			line-height: 1.45;
			margin: 0 0 10px;
			text-transform: uppercase;
		}

		.footer .list-menu {
			list-style: none;
			padding: 0;
			margin: 0;
		}

		.footer .list-menu li {
			margin-bottom: 0;
			padding: 3px 0;
			color: #fff;
			font-size: 15px;
			line-height: 1.7;
		}

		.footer .list-menu li a {
			color: #fff !important;
			text-decoration: none;
		}

		.footer .list-menu li a:hover {
			opacity: 0.85;
		}

		.footer .footer-contact-list li {
			display: flex;
			align-items: flex-start;
			gap: 8px;
		}

		.footer .footer-contact-list li i {
			color: #ff9f0e;
			font-size: 16px;
			margin-top: 6px;
			min-width: 20px;
		}

		.footer .footer-contact-list .line-break {
			display: block;
			margin-left: 0;
		}

		.footer .menu-dot li {
			padding-left: 16px;
			position: relative;
		}

		.footer .menu-dot li::before {
			content: '';
			position: absolute;
			left: 0;
			top: 12px;
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: #fff;
		}

		.footer .footer-connect {
			min-height: 118px;
			display: flex;
			align-items: center;
			gap: 16px;
		}

		.footer .footer-connect .divider {
			width: 1px;
			height: 68px;
			background: rgba(255, 255, 255, 0.55);
		}

		.footer .footer-connect .connect-label {
			font-size: 16px;
			font-style: italic;
			font-weight: 500;
			color: #fff;
			text-decoration: none;
		}

		.footer .footer-connect .connect-label:hover {
			opacity: 0.85;
		}

		.footer .copyright {
			background: rgba(0, 0, 0, 0.2);
			color: #fff;
			padding: 10px 0;
			margin-top: 0;
			position: relative;
		}

		.footer .copyright .copyright-text {
			font-size: 15px;
			font-weight: 600;
		}

		.footer .copyright .list-menu-footer {
			list-style: none;
			margin: 0;
			padding: 0;
			display: flex;
			justify-content: flex-end;
			gap: 0;
		}

		.footer .copyright .list-menu-footer li {
			display: inline-block;
			padding: 0 10px;
		}

		.footer .copyright .list-menu-footer li a {
			color: #fff !important;
			font-size: 15px;
			font-weight: 400;
			text-decoration: none;
		}

		.footer .back-to-top {
			position: fixed;
			right: 14px;
			bottom: 16px;
			width: 54px;
			height: 46px;
			background: #7bb337;
			color: #fff;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-size: 20px;
			cursor: pointer;
			border-radius: 2px;
			z-index: 9998;
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
			opacity: 0;
			visibility: hidden;
			pointer-events: none;
			transform: translateY(10px);
			transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s ease;
		}

		.footer .back-to-top.is-visible {
			opacity: 1;
			visibility: visible;
			pointer-events: auto;
			transform: translateY(0);
		}

		.tgc-side-icons {
			position: fixed;
			right: 14px;
			top: 50%;
			transform: translateY(-50%);
			z-index: 9999;
			display: flex;
			flex-direction: column;
			gap: 14px;
			opacity: 0;
			visibility: hidden;
			pointer-events: none;
			transform: translateY(calc(-50% + 12px));
			transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s ease;
		}

		.tgc-side-icons.is-visible {
			opacity: 1;
			visibility: visible;
			pointer-events: auto;
			transform: translateY(-50%);
		}

		.tgc-side-icons .icon-item {
			width: 54px;
			height: 54px;
			border-radius: 50%;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			color: #fff !important;
			text-decoration: none !important;
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
			font-size: 23px;
		}

		.tgc-side-icons .icon-item.phone { background: #ea2f2f; }
		.tgc-side-icons .icon-item.zalo { background: #2f83ff; font-size: 15px; font-weight: 700; letter-spacing: 0.2px; }
		.tgc-side-icons .icon-item.youtube { background: #ff1b1b; font-size: 20px; }
		.tgc-side-icons .icon-item.instagram {
			background: linear-gradient(145deg, #f9ce34, #ee2a7b, #6228d7);
		}
		.tgc-side-icons .icon-item.tiktok { background: #000; }
		.tgc-side-icons .icon-item.location { background: #f2ae16; }
		.tgc-side-icons .icon-item.messenger { background: #22a8ff; }

		@media (max-width: 1399px) {
			.footer .footer-inner { padding: 34px 0 26px; }
		}

		@media (max-width: 1199px) {
			.tgc-brand-grid {
				grid-template-columns: repeat(4, minmax(90px, 1fr));
				gap: 18px;
			}

			.footer .footer-widget h3 { font-size: 15px; margin-bottom: 8px; }
			.footer .list-menu li { font-size: 14px; }
			.footer .footer-connect .connect-label { font-size: 15px; }
		}

		@media (max-width: 991px) {
			.tgc-brand-strip {
				padding: 18px 0 16px;
			}

			.tgc-brand-grid {
				grid-template-columns: repeat(3, minmax(80px, 1fr));
				gap: 14px;
			}

			.footer .footer-inner {
				padding: 28px 0 22px;
			}

			.footer .footer-widget h3 { font-size: 15px; }
			.footer .list-menu li { font-size: 14px; }
			.footer .footer-connect { min-height: 84px; gap: 14px; }
			.footer .footer-connect .divider { height: 58px; }
			.footer .footer-connect .connect-label { font-size: 15px; }
			.footer .copyright .list-menu-footer {
				justify-content: flex-start;
				gap: 16px;
				flex-wrap: wrap;
				margin-top: 8px;
			}

			.footer .back-to-top {
				display: none;
			}
		}

		@media (max-width: 767px) {
			.tgc-side-icons { display: none; }
			.footer .footer-widget { margin-bottom: 18px; }
			.footer .list-menu li { font-size: 14px; }
			.footer .footer-contact-list .line-break { margin-left: 0; }
			.footer .footer-contact-list li {
				align-items: flex-start;
			}
		}
	</style>

	@stack('styles')

	<section class="tgc-brand-strip">
		<div class="container">
			<div class="tgc-brand-grid">
				<div class="tgc-brand-item"><img src="//theme.hstatic.net/200000157781/1001036201/14/brand1.png?v=1064" alt="Envy"></div>
				<div class="tgc-brand-item"><img src="//theme.hstatic.net/200000157781/1001036201/14/brand2.png?v=1064" alt="Koala Cherries"></div>
				<div class="tgc-brand-item"><img src="//theme.hstatic.net/200000157781/1001036201/14/brand3.png?v=1064" alt="Sun World"></div>
				<div class="tgc-brand-item"><img src="//theme.hstatic.net/200000157781/1001036201/14/brand4.png?v=1064" alt="Pretty Lady"></div>
				<div class="tgc-brand-item"><img src="//theme.hstatic.net/200000157781/1001036201/14/brand5.png?v=1064" alt="Zespri"></div>
				<div class="tgc-brand-item"><img src="//theme.hstatic.net/200000157781/1001036201/14/brand6.png?v=1064" alt="Sunkist"></div>
			</div>
		</div>
	</section>

	<footer class="footer">
		<div class="site-footer">
			<div class="container">
				<div class="footer-inner">
					<div class="row">
						<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
							<div class="footer-widget">
								<h3>Liên hệ</h3>
								<ul class="list-menu footer-contact-list">
									<li><i class="fa fa-map-marker"></i><span>338 Hai Bà Trưng, Phường Tân định, Quận 1, Tp Hồ Chí Minh</span></li>
									<li><i class="fa fa-phone"></i><span>0909131418 - 0798531637<span class="line-break">Thứ 2 - Chủ nhật: 7:00 - 21:00</span></span></li>
									<li><i class="fa fa-envelope"></i><span>hong3ly@gmail.com</span></li>
								</ul>
								<ul class="list-menu">
									<li>Hộ kinh doanh Võ Trần Cương</li>
									<li>Đại diện Pháp luật: Võ Trần Cương</li>
									<li>Mã số thuế: 8125704849</li>
									<li>Giấy ĐKKD số :41A8037733</li>
									<li>Ngày cấp:13/04/2015</li>
								</ul>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
							<div class="footer-widget">
								<h3>Danh mục</h3>
								<ul class="list-menu menu-dot">
									<li><a href="{{ route('home') }}">Trang chủ</a></li>
									<li><a href="{{ route('products.index') }}">Sản phẩm</a></li>
									<li><a href="{{ route('categories.show', 'mam-dia-ngu-qua') }}">Mâm dĩa ngũ quả</a></li>
									<li><a href="{{ route('about') }}">Giới thiệu</a></li>
									<li><a href="{{ route('contact.page') }}">Liên hệ</a></li>
								</ul>
								<div class="wrap-bct" style="margin-top:14px;">
									<a href="http://online.gov.vn/Home/WebDetails/94759" target="_blank" rel="noopener noreferrer">
										<img src="//theme.hstatic.net/200000157781/1001036201/14/logo_bct.png?v=1064" alt="Bộ Công Thương" style="max-width: 220px; width: 100%;">
									</a>
								</div>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
							<div class="footer-widget">
								<h3>Hỗ trợ khách hàng</h3>
								<ul class="list-menu menu-dot">
									<li><a href="{{ route('about') }}">Giới thiệu</a></li>
									<li><a href="{{ route('search') }}">Tìm kiếm</a></li>
									<li><a href="{{ route('page.faq') }}">Câu hỏi thường gặp</a></li>
									<li><a href="{{ route('page.shipping.payment') }}">Chính sách giao hàng và thanh toán</a></li>
									<li><a href="{{ route('page.corporate') }}">Khách hàng doanh nghiệp</a></li>
									<li><a href="{{ route('page.return') }}">Chính sách đổi trả</a></li>
									<li><a href="{{ route('page.privacy.info') }}">Chính sách bảo mật thông tin</a></li>
								</ul>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
							<div class="footer-widget">
								<h3>Kết nối với thế giới trái cây</h3>
								<div class="footer-connect">
									<div class="divider"></div>
									<a class="connect-label" href="https://www.facebook.com/thegioitraicay.net" target="_blank" rel="noopener noreferrer">Facebook</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="copyright">
			<div class="container" style="position: relative;">
				<div class="row">
					<div class="col-xs-12 col-md-6">
						<div class="copyright-text">© Bản quyền thuộc về Thế giới trái cây</div>
					</div>
					<div class="col-xs-12 col-md-6 hidden-xs">
						<ul class="list-menu-footer">
							<li><a href="{{ route('home') }}">Trang chủ</a></li>
							<li><a href="{{ route('products.index') }}">Sản phẩm</a></li>
							<li><a href="{{ route('categories.show', 'mam-dia-ngu-qua') }}">Mâm dĩa ngũ quả</a></li>
							<li><a href="{{ route('about') }}">Giới thiệu</a></li>
							<li><a href="{{ route('contact.page') }}">Liên hệ</a></li>
						</ul>
					</div>
				</div>
				<div class="back-to-top" id="backToTop" title="Lên đầu trang">
					<i class="fa fa-angle-up"></i>
				</div>
			</div>
		</div>
	</footer>

	<div class="tgc-side-icons hidden-xs hidden-sm">
		<a class="icon-item phone" href="tel:0909131418" aria-label="phone"><i class="fa fa-phone"></i></a>
		<a class="icon-item zalo" href="https://zalo.me/0909131418" target="_blank" rel="noopener noreferrer" aria-label="zalo">Zalo</a>
		<a class="icon-item youtube" href="https://www.youtube.com/channel/UCbP8WRHFrvv06knZzKyf1Ew" target="_blank" rel="noopener noreferrer" aria-label="youtube"><i class="fa fa-youtube"></i></a>
		<a class="icon-item instagram" href="https://www.instagram.com/thegioitraicay/" target="_blank" rel="noopener noreferrer" aria-label="instagram"><i class="fa fa-instagram"></i></a>
		<a class="icon-item tiktok" href="https://www.tiktok.com/@thegioitraicay.net" target="_blank" rel="noopener noreferrer" aria-label="tiktok"><i class="fa fa-music"></i></a>
		<a class="icon-item location" href="{{ route('contact.page') }}" aria-label="location"><i class="fa fa-map-marker"></i></a>
		<a class="icon-item messenger" href="https://www.facebook.com/messages/t/270232287830" target="_blank" rel="noopener noreferrer" aria-label="messenger"><i class="fa fa-comment"></i></a>
	</div>

	<script src='//theme.hstatic.net/200000157781/1001036201/14/jquery-2.2.3.min.js?v=1061'></script>
	<script src='//theme.hstatic.net/200000157781/1001036201/14/plugin.js?v=1061'></script>
	<script src='//theme.hstatic.net/200000157781/1001036201/14/main.js?v=1061'></script>

	<script>
		// Sticky Nav on Scroll
		(function() {
			const nav = document.querySelector('nav');
			if (!nav) return;

			const header = document.querySelector('.header');
			const headerHeight = header ? header.offsetHeight : 0;

			window.addEventListener('scroll', function() {
				const scrollTop = window.scrollY || window.pageYOffset;

				if (scrollTop >= headerHeight) {
					nav.classList.add('fixed-nav');
					// Add padding to prevent content jump
					if (!document.body.style.paddingTop) {
						document.body.style.paddingTop = nav.offsetHeight + 'px';
					}
				} else {
					nav.classList.remove('fixed-nav');
					document.body.style.paddingTop = '0';
				}
			}, false);
		})();

		// Fallback lazyload: ensure images with data-lazyload are rendered.
		(function() {
			function hydrateLazyImages() {
				document.querySelectorAll('img[data-lazyload]').forEach(function (img) {
					const lazySrc = img.getAttribute('data-lazyload');
					if (!lazySrc) {
						return;
					}

					if (img.getAttribute('src') !== lazySrc) {
						img.setAttribute('src', lazySrc);
					}
				});
			}

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', hydrateLazyImages);
			} else {
				hydrateLazyImages();
			}
		})();

		// Footer back-to-top
		(function() {
			var backToTop = document.getElementById('backToTop');
			if (!backToTop) {
				return;
			}

			backToTop.addEventListener('click', function() {
				window.scrollTo({ top: 0, behavior: 'smooth' });
			});
		})();

		// Show floating quick-actions only after scrolling down.
		(function() {
			var sideIcons = document.querySelector('.tgc-side-icons');
			var backToTop = document.getElementById('backToTop');

			if (!sideIcons && !backToTop) {
				return;
			}

			var scrollThreshold = 140;

			function toggleFloatingActions() {
				var currentScroll = window.pageYOffset || document.documentElement.scrollTop || 0;
				var isVisible = currentScroll > scrollThreshold;

				if (sideIcons) {
					sideIcons.classList.toggle('is-visible', isVisible);
				}

				if (backToTop) {
					backToTop.classList.toggle('is-visible', isVisible);
				}
			}

			toggleFloatingActions();
			window.addEventListener('scroll', toggleFloatingActions, { passive: true });
		})();
	</script>
	@stack('scripts')
</body>
</html>

