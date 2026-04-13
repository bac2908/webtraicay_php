<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>@yield('title', 'Thế Giới Trái Cây - Trái cây Việt Nam loại 1 & nhập khẩu cao cấp')</title>

	<link rel="canonical" href="https://thegioitraicay.net/" />
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
											@if($category->children->isNotEmpty())
											<ul class="level1">
												@foreach($category->children as $child)
												<li class="level2">
													<a href="{{ route('categories.show', $child->slug) }}"><span>{{ $child->name }}</span></a>
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
				<li class="nav-item"><a class="nav-link" href="{{ url('/collections/mam-dia-ngu-qua') }}">Mâm dĩa ngũ quả</a></li>
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
		.btn-cart, .btn_view { background: #8bc34a; color: #fff; width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

		.sale-flash { position: absolute; top: 10px; left: 10px; background: #ff9800; color: #fff; padding: 2px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; z-index: 5; }

		/* Coupons */
		.coupon-item__inner { border: 1px dashed #8bc34a; display: flex; padding: 15px; border-radius: 8px; background: #f9fff0; margin-bottom: 15px; }

		/* Footer Styles */
		.footer { background: #fdfdfd; margin-top: 50px; padding-top: 50px; border-top: 1px solid #eee; }
		.footer-widget h3 { color: #333; font-size: 16px; font-weight: 700; margin-bottom: 20px; text-transform: uppercase; }
		.list-menu { list-style: none; padding: 0; }
		.list-menu li { margin-bottom: 10px; color: #666; font-size: 14px; }
		.list-menu li i { color: #8bc34a; margin-right: 10px; }
		.copyright { background: #8bc34a; color: #fff; padding: 15px 0; margin-top: 30px; }
	</style>

	@stack('styles')

	<footer class="footer">
		<div class="site-footer">
			<div class="container">
				<div class="footer-inner padding-top-50 padding-bottom-30">
					<div class="row">
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
							<div class="footer-widget">
							<h3 class="h3_footer text-uppercase">Liên hệ</h3>
							<ul class="list-menu">
								<li><i class="fa fa-map-marker"></i> 338 Hai Bà Trưng, P. Tân Định, Q.1, HCM</li>
									<li><i class="fa fa-phone"></i> 0909131418 - 0798531637</li>
									<li><i class="fa fa-envelope"></i> thegioitraicay.net@gmail.com</li>
								</ul>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
							<div class="footer-widget"><h3>Chính sách</h3>
								<ul class="list-menu">
									<li><a href="{{ route('page.shipping.payment') }}">Giao hàng và thanh toán</a></li>
									<li><a href="{{ route('page.return') }}">Chính sách đổi trả</a></li>
									<li><a href="{{ route('page.privacy') }}">Chính sách bảo mật</a></li>
									<li><a href="{{ route('page.privacy.info') }}">Bảo mật thông tin</a></li>
									<li><a href="{{ route('page.terms') }}">Điều khoản dịch vụ</a></li>
								</ul>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
							<div class="footer-widget"><h3>Hỗ trợ</h3>
								<ul class="list-menu">
									<li><a href="{{ route('page.frontpage') }}">Trang frontpage</a></li>
									<li><a href="{{ route('products.index') }}">Tìm kiếm sản phẩm</a></li>
									<li><a href="{{ route('page.faq') }}">Câu hỏi thường gặp</a></li>
									<li><a href="{{ route('page.corporate') }}">Khách hàng doanh nghiệp</a></li>
									<li><a href="{{ route('about') }}">Giới thiệu</a></li>
									<li><a href="{{ route('contact.page') }}">Liên hệ</a></li>
								</ul>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
							<div class="footer-widget">
								<h3>Kết nối với chúng tôi</h3>
								<div class="fb-page" data-href="https://www.facebook.com/thegioitraicay.net" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="copyright text-center py-3" style="background:#f5f5f5; border-top:1px solid #eee;">
			<p class="mb-0">© Bản quyền thuộc về Thế giới trái cây</p>
		</div>
	</footer>

<!-- Floating Contact Sidebar -->
	<div class="contact-float-wrapper">
		<div class="contact-float-item phone">
			<a href="tel:0909131418" title="Gọi điện">
				<i class="fa fa-phone"></i>
			</a>
		</div>
		<div class="contact-float-item zalo">
			<a href="https://zalo.me/0909131418" target="_blank" title="Zalo">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
					<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
				</svg>
			</a>
		</div>
		<div class="contact-float-item email">
			<a href="https://m.me/thegioitraicay.net" target="_blank" title="Messenger">
				<i class="fa fa-comment"></i>
			</a>
		</div>
		<div class="contact-float-item shop">
			<a href="{{ route('home') }}" title="Shop">
				<i class="fa fa-shopping-bag"></i>
			</a>
		</div>
		<div class="contact-float-item instagram">
			<a href="https://instagram.com/thegioitraicay.net" target="_blank" title="Instagram">
				<i class="fa fa-instagram"></i>
			</a>
		</div>
		<div class="contact-float-item tiktok">
			<a href="https://tiktok.com/@thegioitraicay.net" target="_blank" title="TikTok">
				<i class="fa fa-music"></i>
			</a>
		</div>
		<div class="contact-float-item location">
			<a href="{{ route('contact.page') }}" title="Liên hệ">
				<i class="fa fa-map-marker"></i>
			</a>
		</div>
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
	</script>
	@stack('scripts')
</body>
</html>

