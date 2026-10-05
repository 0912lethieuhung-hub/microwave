<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Siêu Lò Nướng - Hệ Thống Lò Vi Sóng & Gia Dụng Chính Hãng')</title>
    <meta name="description" content="@yield('meta_description', 'Siêu Lò Nướng - Hệ thống bán lẻ lò vi sóng cơ, điện tử, có nướng, âm tủ chính hãng Sharp, Panasonic, Electrolux giá rẻ nhất, giao nhanh 2h, trả góp 0%, 1 đổi 1 365 ngày.')">
    @yield('seo')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --dmx-blue: #0071af;
            --dmx-blue-dark: #005a8c;
            --dmx-blue-light: #e8f4fc;
            --dmx-yellow: #ffd400;
            --dmx-yellow-hover: #f0c500;
            --dmx-red: #d70018;
            --dmx-orange: #f97316;
            --text-main: #212529;
            --text-muted: #6c757d;
            --bg-body: #f3f4f6;
            --card-border: #e5e7eb;
            --container-max: 1420px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Be Vietnam Pro', 'Outfit', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* TOP PROMO STRIP */
        .top-promo-strip {
            background: #005a8c;
            color: #ffffff;
            font-size: 0.8rem;
            padding: 0.35rem 0;
            text-align: center;
            font-weight: 500;
        }
        .top-promo-strip a {
            color: var(--dmx-yellow);
            text-decoration: none;
            font-weight: 700;
            margin-left: 0.5rem;
        }

        /* MAIN HEADER */
        .site-header {
            background-color: var(--dmx-blue);
            color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .header-container {
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        /* LOGO */
        .logo-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #ffffff;
            flex-shrink: 0;
        }
        .logo-icon {
            width: 42px;
            height: 42px;
            background: var(--dmx-yellow);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dmx-blue);
            font-size: 1.4rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        .logo-box:hover .logo-icon {
            transform: rotate(15deg) scale(1.08);
        }
        .logo-text {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.1;
            color: #ffffff;
            text-transform: uppercase;
        }
        .logo-text span {
            color: var(--dmx-yellow);
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* CATEGORY BUTTON */
        .btn-category-toggle {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 0.55rem 0.9rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .btn-category-toggle:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* SEARCH BAR */
        .search-wrapper {
            flex: 1;
            position: relative;
            max-width: 480px;
        }
        .search-form {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .search-input {
            flex: 1;
            border: none;
            padding: 0.65rem 1.2rem;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            color: #333;
        }
        .search-btn {
            background: transparent;
            border: none;
            padding: 0 1.1rem;
            color: var(--dmx-blue);
            font-size: 1.1rem;
            cursor: pointer;
            transition: color 0.2s;
        }
        .search-btn:hover {
            color: var(--dmx-red);
        }

        /* HEADER ACTIONS */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        .header-action-item {
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.45rem 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .header-action-item:hover {
            background: rgba(255, 255, 255, 0.18);
        }
        .header-action-item i {
            font-size: 1.2rem;
            color: var(--dmx-yellow);
        }

        .cart-pill {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 0.45rem 1rem;
            position: relative;
        }
        .cart-badge {
            background: var(--dmx-yellow);
            color: #000;
            font-weight: 800;
            font-size: 0.75rem;
            padding: 0.1rem 0.45rem;
            border-radius: 50px;
            margin-left: 0.25rem;
        }

        /* USER DROPDOWN */
        .user-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }
        .user-dropdown-trigger {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 0.35rem 0.85rem 0.35rem 0.45rem;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            font-family: inherit;
            outline: none;
            user-select: none;
        }
        .user-dropdown-trigger:hover, .user-dropdown-wrapper.open .user-dropdown-trigger {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
        }
        .user-header-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid var(--dmx-yellow);
            pointer-events: none;
        }
        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            padding-top: 6px;
            display: none;
            z-index: 99999;
        }
        .user-dropdown-inner {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18), 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid var(--card-border);
            min-width: 230px;
            padding: 0.5rem 0;
            overflow: hidden;
            animation: dropFade 0.18s ease;
        }
        .user-dropdown-wrapper:hover .user-dropdown-menu,
        .user-dropdown-wrapper.open .user-dropdown-menu {
            display: block;
        }
        @keyframes dropFade {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .user-dropdown-header {
            padding: 0.75rem 1.1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.78rem;
            color: #64748b;
            background: #f8fafc;
        }
        .user-dropdown-header strong {
            color: #1e293b;
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.15rem;
        }
        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 1.1rem;
            color: #334155;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.15s;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            cursor: pointer;
            box-sizing: border-box;
        }
        .user-dropdown-item:hover {
            background: var(--dmx-blue-light);
            color: var(--dmx-blue);
        }
        .user-dropdown-item.danger {
            color: #dc2626;
        }
        .user-dropdown-item.danger:hover {
            background: #fee2e2;
        }
        .user-dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 0.92rem;
        }

        /* LEFT CATEGORY DRAWER */
        .category-drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 999998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
            backdrop-filter: blur(2px);
        }
        .category-drawer-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .category-drawer {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 330px;
            max-width: 85vw;
            background: #ffffff;
            z-index: 999999;
            box-shadow: 6px 0 25px rgba(0, 0, 0, 0.25);
            transform: translateX(-100%);
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .category-drawer.active {
            transform: translateX(0);
        }
        .drawer-header {
            background: linear-gradient(135deg, #0071af 0%, #005a8c 100%);
            color: #ffffff;
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .drawer-header h3 {
            font-size: 1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }
        .drawer-close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #ffffff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .drawer-close-btn:hover {
            background: rgba(255, 255, 255, 0.35);
        }
        .drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
        }
        .drawer-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1.25rem;
            color: #1e293b;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 700;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }
        .drawer-item:hover {
            background: var(--dmx-blue-light);
            color: var(--dmx-blue);
            padding-left: 1.5rem;
        }
        .drawer-item.active-drawer-item {
            background: #e0f2fe;
            color: var(--dmx-blue);
            font-weight: 700;
            border-left: 4px solid var(--dmx-blue);
        }
        .drawer-footer {
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 1rem 1.25rem;
        }

        /* SUB NAVIGATION BAR */
        .sub-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .sub-nav-container {
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            overflow-x: auto;
            white-space: nowrap;
            gap: 0.25rem;
            scrollbar-width: none;
        }
        .sub-nav-container::-webkit-scrollbar {
            display: none;
        }
        .sub-nav-link {
            padding: 0.75rem 0.95rem;
            color: #4b5563;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        .sub-nav-link:hover, .sub-nav-link.active {
            color: var(--dmx-blue);
            border-bottom-color: var(--dmx-blue);
            background: var(--dmx-blue-light);
        }
        .sub-nav-link.highlight {
            color: var(--dmx-red);
        }
        .sub-nav-link.highlight:hover {
            color: #b91c1c;
            border-bottom-color: var(--dmx-red);
        }

        /* MAIN CONTENT AREA */
        .main-wrapper {
            max-width: var(--container-max);
            width: 100%;
            margin: 1.5rem auto 3rem;
            padding: 0 1rem;
            flex: 1;
        }

        /* FOOTER */
        .site-footer {
            background: #ffffff;
            border-top: 3px solid var(--dmx-blue);
            padding: 3rem 1rem 1.5rem;
            margin-top: auto;
            color: #4b5563;
            font-size: 0.88rem;
        }
        .footer-container {
            max-width: var(--container-max);
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
        }
        .footer-col h4 {
            color: var(--dmx-blue);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .footer-col ul {
            list-style: none;
        }
        .footer-col ul li {
            margin-bottom: 0.5rem;
        }
        .footer-col ul li a {
            color: #4b5563;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-col ul li a:hover {
            color: var(--dmx-blue);
        }
        .hotline-box {
            background: var(--dmx-blue-light);
            border-left: 4px solid var(--dmx-blue);
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-top: 0.75rem;
        }
        .hotline-number {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--dmx-red);
        }
        .footer-bottom {
            max-width: var(--container-max);
            margin: 0 auto;
            padding-top: 1.5rem;
            border-top: 1px solid var(--card-border);
            text-align: center;
            color: #9ca3af;
            font-size: 0.8rem;
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .footer-container {
                grid-template-columns: 1fr 1fr;
            }
            .header-container {
                flex-wrap: wrap;
            }
            .search-wrapper {
                order: 3;
                max-width: 100%;
                width: 100%;
            }
        }
        @media (max-width: 640px) {
            .footer-container {
                grid-template-columns: 1fr;
            }
            .header-actions .contact-pill {
                display: none;
            }
        }

        /* TOAST NOTIFICATION SYSTEM */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 400px;
            width: calc(100% - 32px);
            pointer-events: none;
        }
        .custom-toast {
            pointer-events: auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
            border-left: 5px solid #10b981;
            position: relative;
            overflow: hidden;
            animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.3s ease;
        }
        .custom-toast.hide {
            animation: toastSlideOut 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .custom-toast.toast-success {
            border-left-color: #10b981;
        }
        .custom-toast.toast-success .toast-icon {
            color: #10b981;
            background: #dcfce7;
        }
        .custom-toast.toast-error {
            border-left-color: #ef4444;
        }
        .custom-toast.toast-error .toast-icon {
            color: #ef4444;
            background: #fee2e2;
        }
        .custom-toast.toast-info {
            border-left-color: var(--dmx-blue);
        }
        .custom-toast.toast-info .toast-icon {
            color: var(--dmx-blue);
            background: var(--dmx-blue-light);
        }
        .toast-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .toast-body {
            flex: 1;
            min-width: 0;
        }
        .toast-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.15rem;
        }
        .toast-message {
            font-size: 0.84rem;
            color: #475569;
            line-height: 1.4;
        }
        .toast-close {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.3rem;
            line-height: 1;
            cursor: pointer;
            padding: 0;
            margin-left: 0.5rem;
            transition: color 0.15s;
        }
        .toast-close:hover {
            color: #334155;
        }
        @keyframes toastSlideIn {
            from {
                transform: translateX(120%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes toastSlideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(120%);
                opacity: 0;
            }
        }

        @yield('extra-css')
    </style>
</head>
<body>
    <!-- Top Promo Announcement -->
    <div class="top-promo-strip">
        <span>⚡ LỄ HỘI LÒ VI SÓNG - GIẢM ĐẾN 30% - 1 ĐỔI 1 TRONG 365 NGÀY - TRẢ GÓP 0% ⚡</span>
        <a href="{{ route('promotions') }}">XEM KHUYẾN MÃI NGAY →</a>
    </div>

    <!-- Main Header (Điện Máy XANH Style) -->
    <header class="site-header">
        <div class="header-container">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="logo-box">
                <div class="logo-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div class="logo-text">
                    Siêu Lò Nướng
                    <span>Hàng gia dụng chính hãng</span>
                </div>
            </a>

            <!-- Category Toggle Button -->
            <button type="button" class="btn-category-toggle" onclick="toggleCategoryDrawer()" title="Mở danh mục lò vi sóng">
                <i class="fa-solid fa-bars"></i>
                <span>Danh mục</span>
            </button>

            <!-- Search Bar -->
            <div class="search-wrapper">
                <form action="{{ route('home') }}" method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" value="{{ request('search') }}" placeholder="Bạn tìm gì... (VD: Sharp, Inverter, có nướng,...)">
                    <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Location / Hotline Manager Pill -->
                <a href="{{ route('contact') }}" class="header-action-item contact-pill" title="Hà Nội - Showroom Nguyễn Du">
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <div style="font-size: 0.72rem; opacity: 0.9;">Hà Nội</div>
                        <div style="font-weight: 700; font-size: 0.8rem;">0913 014 602</div>
                    </div>
                </a>

                <!-- Wishlist Button -->
                @auth
                    @php
                        $wishlistCount = \App\Models\Wishlist::where('user_id', Auth::id())->count();
                    @endphp
                    <a href="{{ route('wishlist.index') }}" class="header-action-item cart-pill" title="Sản phẩm yêu thích">
                        <i class="fa-solid fa-heart" style="color: var(--dmx-yellow);"></i>
                        <span>Yêu thích</span>
                        <span class="cart-badge wishlist-badge" id="headerWishlistBadge" style="{{ $wishlistCount > 0 ? '' : 'display: none;' }}">{{ $wishlistCount }}</span>
                    </a>
                @endauth

                <!-- Cart Button -->
                <a href="{{ route('cart.index') }}" class="header-action-item cart-pill">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Giỏ hàng</span>
                    @php
                        $cartCount = 0;
                        if(Auth::check()) {
                            $userCart = \App\Models\Cart::where('user_id', Auth::id())->first();
                            if($userCart) {
                                $cartCount = (int) $userCart->cartItems()->sum('quantity');
                            }
                        }
                    @endphp
                    <span class="cart-badge" id="headerCartBadge" style="{{ $cartCount > 0 ? '' : 'display: none;' }}">{{ $cartCount }}</span>
                </a>

                <!-- User Account Dropdown -->
                @auth
                    <div class="user-dropdown-wrapper" id="userDropdownWrapper">
                        <button type="button" class="user-dropdown-trigger" id="userDropdownBtn" onclick="toggleUserDropdown(event)">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-header-avatar">
                            <span>{{ Str::limit(Auth::user()->name, 12) }}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.72rem; opacity: 0.85; margin-left: 0.15rem;"></i>
                        </button>
                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <div class="user-dropdown-inner">
                                <div class="user-dropdown-header">
                                    <strong>{{ Auth::user()->name }}</strong>
                                    <span>{{ Auth::user()->email }}</span>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                    <i class="fa-solid fa-id-card" style="color: var(--dmx-blue);"></i>
                                    <span>Hồ sơ cá nhân</span>
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="user-dropdown-item">
                                    <i class="fa-solid fa-heart" style="color: var(--dmx-red);"></i>
                                    <span>Sản phẩm yêu thích</span>
                                </a>
                                <a href="{{ route('profile.edit', ['tab' => 'orders']) }}" class="user-dropdown-item">
                                    <i class="fa-solid fa-box-open" style="color: #f97316;"></i>
                                    <span>Đơn hàng của tôi</span>
                                </a>
                                <a href="{{ route('profile.edit', ['tab' => 'password']) }}" class="user-dropdown-item">
                                    <i class="fa-solid fa-key" style="color: #64748b;"></i>
                                    <span>Đổi mật khẩu</span>
                                </a>
                                @if(Auth::user()->is_admin || Auth::user()->role === 'admin')
                                    <div style="border-top: 1px dashed #e2e8f0; margin: 0.25rem 0;"></div>
                                    <a href="{{ route('admin.dashboard') }}" class="user-dropdown-item" style="color: #b45309; font-weight: 600;">
                                        <i class="fa-solid fa-shield-halved" style="color: #b45309;"></i>
                                        <span>Trang quản trị Admin</span>
                                    </a>
                                @endif
                                <div style="border-top: 1px solid #f1f5f9; margin: 0.25rem 0;"></div>
                                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="user-dropdown-item danger">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span>Đăng xuất</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="header-action-item" style="background: rgba(255,255,255,0.15); border-radius: 50px;">
                        <i class="fa-solid fa-circle-user"></i>
                        <span>Đăng nhập</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Sub Navigation Menu (Thanh Menu Chính Đầy Đủ Chức Năng) -->
    <nav class="sub-nav">
        <div class="sub-nav-container">
            <a href="{{ route('home') }}" class="sub-nav-link {{ !request('category') && !request()->routeIs('promotions', 'guides', 'contact', 'profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a>
            <a href="{{ route('promotions') }}" class="sub-nav-link highlight {{ request()->routeIs('promotions') ? 'active' : '' }}">
                <i class="fa-solid fa-fire-flame-curved"></i> Khuyến Mãi Lễ Tết
            </a>
            <a href="{{ route('home') }}#cam-ket" class="sub-nav-link">
                <i class="fa-solid fa-truck-fast" style="color: #f97316;"></i> Giao Nhanh 2 Giờ
            </a>
            <a href="{{ route('contact') }}" class="sub-nav-link">
                <i class="fa-solid fa-shield-halved" style="color: #16a34a;"></i> Bảo Hành 12 Tháng & 1 Đổi 1
            </a>
            <a href="{{ route('guides') }}" class="sub-nav-link {{ request()->routeIs('guides') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open-reader"></i> Cẩm Nang Sử Dụng
            </a>
            <a href="{{ route('contact') }}" class="sub-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                <i class="fa-solid fa-phone-volume"></i> Showroom & Liên Hệ
            </a>
            <a href="{{ route('user.orders') }}" class="sub-nav-link">
                <i class="fa-solid fa-boxes-packing" style="color: var(--dmx-blue);"></i> Tra Cứu Đơn Hàng
            </a>
            @auth
                <a href="{{ route('profile.edit') }}" class="sub-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear"></i> Hồ Sơ Của Tôi
                </a>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="sub-nav-link" style="color: #b45309; font-weight: 700; background: #fef3c7;">
                        <i class="fa-solid fa-gauge-high"></i> Admin Panel
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    <!-- CATEGORY DRAWER (THANH BÊN TRƯỢT TRÁI TOÀN DIỆN) -->
    <div class="category-drawer-overlay" id="categoryDrawerOverlay" onclick="closeCategoryDrawer()"></div>
    <aside class="category-drawer" id="categoryDrawer">
        <div class="drawer-header">
            <h3><i class="fa-solid fa-layer-group" style="color: var(--dmx-yellow);"></i> DANH MỤC SẢN PHẨM</h3>
            <button type="button" class="drawer-close-btn" onclick="closeCategoryDrawer()" title="Đóng">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="drawer-body">
            <a href="{{ route('home') }}" class="drawer-item {{ !request('category') && !request()->routeIs('promotions') && !request()->routeIs('guides') ? 'active-drawer-item' : '' }}">
                <span style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fa-solid fa-house" style="color: var(--dmx-blue); width: 22px; text-align: center;"></i>
                    <span>Tất cả sản phẩm</span>
                </span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
            </a>

            <!-- Lò vi sóng cơ -->
            <a href="{{ route('home', ['category' => 'lo-vi-song-co']) }}" class="drawer-item {{ request('category') == 'lo-vi-song-co' ? 'active-drawer-item' : '' }}">
                <span style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="width: 22px; text-align: center; display: inline-block;">🔘</span>
                    <span>Lò vi sóng cơ</span>
                </span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
            </a>

            <!-- Lò vi sóng điện tử -->
            <a href="{{ route('home', ['category' => 'lo-vi-song-dien-tu']) }}" class="drawer-item {{ request('category') == 'lo-vi-song-dien-tu' ? 'active-drawer-item' : '' }}">
                <span style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="width: 22px; text-align: center; display: inline-block;">📟</span>
                    <span>Lò vi sóng điện tử</span>
                </span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
            </a>

            <!-- Lò vi sóng có nướng -->
            <a href="{{ route('home', ['category' => 'lo-vi-song-co-nuong']) }}" class="drawer-item {{ request('category') == 'lo-vi-song-co-nuong' ? 'active-drawer-item' : '' }}">
                <span style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="width: 22px; text-align: center; display: inline-block;">🍗</span>
                    <span>Lò vi sóng có nướng</span>
                </span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
            </a>

            <!-- Lò vi sóng âm tủ -->
            <a href="{{ route('home', ['category' => 'lo-vi-song-am-tu']) }}" class="drawer-item {{ request('category') == 'lo-vi-song-am-tu' ? 'active-drawer-item' : '' }}">
                <span style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="width: 22px; text-align: center; display: inline-block;">🔲</span>
                    <span>Lò vi sóng âm tủ</span>
                </span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
            </a>

            <a href="{{ route('promotions') }}" class="drawer-item {{ request()->routeIs('promotions') ? 'active-drawer-item' : '' }}" style="color: var(--dmx-red);">
                <span style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="width: 22px; text-align: center; display: inline-block;">🔥</span>
                    <span>Khuyến Mãi Lễ Tết</span>
                </span>
                <span style="background: #fee2e2; color: var(--dmx-red); font-size: 0.72rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 4px;">-30%</span>
            </a>

            <a href="{{ route('guides') }}" class="drawer-item {{ request()->routeIs('guides') ? 'active-drawer-item' : '' }}">
                <span style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="width: 22px; text-align: center; display: inline-block;">📖</span>
                    <span>Cẩm Nang Sử Dụng</span>
                </span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
            </a>
        </div>
        <div class="drawer-footer">
            <div style="font-size: 0.78rem; color: #475569; font-weight: 600;">TỔNG ĐÀI TƯ VẤN & BÁN HÀNG:</div>
            <div style="font-size: 1.15rem; font-weight: 800; color: var(--dmx-red); margin-top: 0.2rem;">0913 014 602</div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Showroom: 96 Nguyễn Du, Hà Nội</div>
        </div>
    </aside>

    <!-- Main Dynamic Content -->
    <main class="main-wrapper">
        {{-- Thông báo success được hiển thị qua Toast JS ở cuối trang, không dùng banner inline để tránh hiện 2 lần --}}

        @if (session('error') && !request()->routeIs('login', 'register', 'verification.notice'))
            <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.6rem; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.1rem; color: #dc2626;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer (Điện Máy XANH Style) -->
    <footer class="site-footer">
        <div class="footer-container">
            <!-- Col 1: Store info -->
            <div class="footer-col">
                <h4>Hộ Kinh Doanh Siêu Lò Nướng</h4>
                <p style="margin-bottom: 0.75rem; line-height: 1.6;">Hệ thống bán lẻ lò vi sóng và thiết bị gia dụng nhà bếp chính hãng: Sharp, Panasonic, Electrolux, Toshiba.</p>
                <div class="hotline-box">
                    <div style="font-size: 0.8rem; font-weight: 600; color: #1e3a8a;">TỔNG ĐÀI TƯ VẤN & BÁN HÀNG:</div>
                    <div class="hotline-number">0913 014 602</div>
                    <div style="font-size: 0.78rem; color: #475569;">Đại diện HKD: <strong>Lê Thiều Hưng</strong></div>
                </div>
            </div>

            <!-- Col 2: Customer Service -->
            <div class="footer-col">
                <h4>Hỗ Trợ Khách Hàng</h4>
                <ul>
                    <li><a href="{{ route('guides') }}">Hướng dẫn mua hàng online</a></li>
                    <li><a href="{{ route('guides') }}">Hướng dẫn sử dụng lò an toàn</a></li>
                    <li><a href="{{ route('promotions') }}">Chính sách khuyến mãi lễ tết</a></li>
                    <li><a href="{{ route('contact') }}">Chính sách đổi trả 365 ngày</a></li>
                    <li><a href="{{ route('contact') }}">Bảo hành chính hãng 12 tháng</a></li>
                </ul>
            </div>

            <!-- Col 3: Product Categories -->
            <div class="footer-col">
                <h4>Danh Mục Sản Phẩm</h4>
                <ul>
                    <li><a href="{{ route('home', ['category' => 'lo-vi-song-co']) }}">Lò vi sóng cơ giá rẻ</a></li>
                    <li><a href="{{ route('home', ['category' => 'lo-vi-song-dien-tu']) }}">Lò vi sóng điện tử Inverter</a></li>
                    <li><a href="{{ route('home', ['category' => 'lo-vi-song-co-nuong']) }}">Lò vi sóng có chức năng nướng</a></li>
                    <li><a href="{{ route('home', ['category' => 'lo-vi-song-am-tu']) }}">Lò vi sóng âm tủ cao cấp</a></li>
                </ul>
            </div>

            <!-- Col 4: Showroom & Contact -->
            <div class="footer-col">
                <h4>Showroom Trưng Bày</h4>
                <p style="margin-bottom: 0.5rem;"><i class="fa-solid fa-location-dot" style="color: var(--dmx-red);"></i> <strong>Địa chỉ:</strong> Số 96 đường Nguyễn Du, phường Cửa Nam, TP. Hà Nội</p>
                <p style="margin-bottom: 0.5rem;"><i class="fa-solid fa-envelope" style="color: var(--dmx-blue);"></i> <strong>Email:</strong> 0912lethieuhung@gmail.com</p>
                <p style="margin-bottom: 0.5rem;"><i class="fa-solid fa-clock" style="color: #16a34a;"></i> <strong>Mở cửa:</strong> 8:00 - 21:30 (Cả CN & Lễ Tết)</p>
                <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <span style="background: #e2e8f0; padding: 0.35rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">GIAO NHANH 2H</span>
                    <span style="background: #e2e8f0; padding: 0.35rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">TRẢ GÓP 0%</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p style="margin-bottom: 0.35rem;">© 2026 <strong>HỘ KINH DOANH SIÊU LÒ NƯỚNG</strong>. Giấy chứng nhận ĐKKD số: <strong>01E8039281</strong> do UBND Quận Hai Bà Trưng, TP. Hà Nội cấp.</p>
            <p style="margin-bottom: 0.35rem; font-size: 0.82rem; color: #94a3b8;">Mã số thuế: <strong>0109887766</strong> | Đại diện pháp luật: <strong>Lê Thiều Hưng</strong> | Hotline CSKH: <strong>0913 014 602</strong></p>
            <p style="font-size: 0.78rem; color: #64748b; margin-bottom: 0;">Trụ sở: Số 96 đường Nguyễn Du, P. Cửa Nam, Q. Hai Bà Trưng, Hà Nội. Tuân thủ theo Nghị định 52/2013/NĐ-CP và Nghị định 85/2021/NĐ-CP về Thương mại điện tử.</p>
        </div>
    </footer>

    <script>
        function toggleUserDropdown(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const wrapper = document.getElementById('userDropdownWrapper');
            if (wrapper) {
                wrapper.classList.toggle('open');
            }
        }

        function toggleCategoryDrawer() {
            const drawer = document.getElementById('categoryDrawer');
            const overlay = document.getElementById('categoryDrawerOverlay');
            if (drawer && overlay) {
                drawer.classList.toggle('active');
                overlay.classList.toggle('active');
            }
        }

        function closeCategoryDrawer() {
            const drawer = document.getElementById('categoryDrawer');
            const overlay = document.getElementById('categoryDrawerOverlay');
            if (drawer && overlay) {
                drawer.classList.remove('active');
                overlay.classList.remove('active');
            }
        }

        // Close dropdown & drawer on outside click or ESC key
        document.addEventListener('click', function(event) {
            const wrapper = document.getElementById('userDropdownWrapper');
            if (wrapper && !wrapper.contains(event.target)) {
                wrapper.classList.remove('open');
            }
        });

        // Toast Notification System
        window.showToast = function(message, type = 'success', title = '') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `custom-toast toast-${type}`;

            let icon = 'fa-circle-check';
            let defaultTitle = 'Thành công';
            if (type === 'error') {
                icon = 'fa-circle-xmark';
                defaultTitle = 'Thông báo';
            } else if (type === 'info') {
                icon = 'fa-circle-info';
                defaultTitle = 'Thông tin';
            }

            toast.innerHTML = `
                <div class="toast-icon"><i class="fa-solid ${icon}"></i></div>
                <div class="toast-body">
                    <div class="toast-title">${title || defaultTitle}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button type="button" class="toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 300);">&times;</button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.add('hide');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3500);
        };

        // Wishlist Toggle AJAX Helper
        window.toggleWishlist = function(productId, btnElement) {
            @guest
                window.location.href = "{{ route('login') }}";
                return;
            @endguest

            if (!productId) return;

            const csrfToken = '{{ csrf_token() }}';
            fetch(`/wishlist/toggle/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (window.showToast) {
                        showToast(data.message, data.in_wishlist ? 'success' : 'info', 'Yêu thích');
                    }
                    if (btnElement) {
                        if (data.in_wishlist) {
                            btnElement.classList.add('active');
                            const icon = btnElement.querySelector('i');
                            if (icon) {
                                icon.classList.remove('fa-regular');
                                icon.classList.add('fa-solid');
                            }
                        } else {
                            btnElement.classList.remove('active');
                            const icon = btnElement.querySelector('i');
                            if (icon) {
                                icon.classList.remove('fa-solid');
                                icon.classList.add('fa-regular');
                            }
                        }
                    }
                    // Update header badge
                    const badge = document.getElementById('headerWishlistBadge');
                    if (badge) {
                        badge.innerText = data.count;
                        badge.style.display = data.count > 0 ? '' : 'none';
                    }
                }
            })
            .catch(err => console.error('Wishlist error:', err));
        };

        // Auto trigger toasts from Laravel session flash
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast("{{ addslashes(session('success')) }}", 'success', 'Thành công');
            });
        @endif
        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast("{{ addslashes(session('error')) }}", 'error', 'Thông báo lỗi');
            });
        @endif
        @if(session('info'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast("{{ addslashes(session('info')) }}", 'info', 'Thông tin');
            });
        @endif
    </script>

    <!-- TOAST CONTAINER -->
    <div id="toastContainer" class="toast-container" aria-live="polite" aria-atomic="true"></div>

    <!-- USER LIVECHAT POPUP (Theo tài liệu Livechat) -->
    <style>
        #chat-box {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 9999;
        }
        #chat-box #chat-toggle {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: #0071af;
            color: #ffffff;
            border: none;
            font-size: 0.88rem;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(0, 113, 175, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }
        #chat-box #chat-toggle:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(0, 113, 175, 0.55);
        }
        #chat-box #chat-popup {
            width: 340px;
            max-width: calc(100vw - 40px);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
            display: flex;
            flex-direction: column;
        }
        #chat-box .card-header {
            background: #0071af;
            color: #ffffff;
            padding: 12px 16px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #chat-box #chat-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #ffffff;
            padding: 3px 9px;
            border-radius: 4px;
            font-weight: 700;
            cursor: pointer;
            line-height: 1;
        }
        #chat-box #chat-close:hover {
            background: rgba(255,255,255,0.35);
        }
        #chat-box #chat-messages {
            height: 280px;
            overflow-y: auto;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: #f8fafc;
        }
        #chat-box .message-row {
            padding: 7px 12px;
            border-radius: 10px;
            font-size: 0.88rem;
            max-width: 85%;
            word-break: break-word;
            line-height: 1.4;
        }
        #chat-box .user-msg {
            align-self: flex-end;
            background: #0071af;
            color: #ffffff;
            border-bottom-right-radius: 2px;
        }
        #chat-box .admin-msg {
            align-self: flex-start;
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 2px;
        }
        #chat-box .card-footer {
            padding: 10px 12px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
        #chat-box .input-group {
            display: flex;
            gap: 6px;
        }
        #chat-box #chat-input {
            flex: 1;
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.88rem;
            outline: none;
        }
        #chat-box #chat-input:focus {
            border-color: #0071af;
        }
        #chat-box #send-btn {
            background: #16a34a;
            color: #ffffff;
            border: none;
            padding: 7px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }
        #chat-box #send-btn:hover {
            background: #15803d;
        }
    </style>

    @auth
    <div id="chat-box">
        <button id="chat-toggle" class="btn btn-primary rounded-circle shadow">💬 Chat</button>
        <div id="chat-popup" class="card shadow-lg" style="display:none; border-radius: 10px;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Hỗ trợ khách hàng</span>
                <button id="chat-close" class="btn btn-sm btn-light">X</button>
            </div>
            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
            <div style="background: #fff3cd; color: #856404; font-size: 0.76rem; padding: 6px 10px; border-bottom: 1px solid #ffeeba; line-height: 1.35;">
                ⚠️ <strong>Bạn đang đăng nhập tài khoản Admin:</strong> Để thử nghiệm chat Khách hàng ↔ Admin, vui lòng mở <strong>Tab ẩn danh (Ctrl + Shift + N)</strong> đăng nhập tài khoản Khách hàng.
            </div>
            @endif
            <div id="chat-messages" class="card-body">
                <small class="text-muted">Đang tải lịch sử...</small>
            </div>
            <div class="card-footer bg-white">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off">
                    <div class="input-group-append">
                        <button id="send-btn" class="btn btn-success">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("chat-toggle");
        const chatPopup = document.getElementById("chat-popup");
        const closeBtn = document.getElementById("chat-close");
        const sendBtn = document.getElementById("send-btn");
        const input = document.getElementById("chat-input");
        const chatBox = document.getElementById("chat-messages");

        if (!toggleBtn) return; // Nếu khách chưa đăng nhập thì không chạy script chat

        // --- MỞ / ĐÓNG CHAT ---
        toggleBtn.onclick = () => {
            chatPopup.style.display = "block";
            toggleBtn.style.display = "none";
            loadMessages();
        };

        closeBtn.onclick = () => {
            chatPopup.style.display = "none";
            toggleBtn.style.display = "block";
        };

        // --- LOAD TIN NHẮN ---
        function loadMessages() {
            fetch("{{ route('user.chat.messages') }}")
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    if (!messages || messages.length === 0) {
                        html = "<div class='text-center text-muted' style='margin:auto;'><small>Bắt đầu cuộc trò chuyện với Admin</small></div>";
                    } else {
                        messages.forEach(msg => {
                            const isMe = msg.sender_id == "{{ Auth::id() }}";
                            const time = msg.time_formatted || '';
                            html += `
                            <div class="message-row ${isMe ? 'user-msg' : 'admin-msg'}">
                                <div><strong>${isMe ? 'Bạn' : 'Admin'}:</strong> ${msg.content}</div>
                                ${time ? `<div style="font-size: 0.68rem; opacity: 0.8; margin-top: 2px; text-align: ${isMe ? 'right' : 'left'};">${time}</div>` : ''}
                            </div>
                            `;
                        });
                    }
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight; // Tự động cuộn xuống cuối
                })
                .catch(err => console.error("Lỗi tải tin nhắn:", err));
        }

        // --- GỬI TIN NHẮN ---
        function sendMessage() {
            let message = input.value.trim();
            if (message === "") return;

            // Vô hiệu hóa input/button khi đang gửi để tránh gửi lặp
            input.disabled = true;
            sendBtn.disabled = true;

            fetch("{{ route('user.chat.send') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                input.value = "";
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
                loadMessages(); // Cập nhật lại khung chat ngay lập tức
            })
            .catch(err => {
                console.error("Lỗi gửi tin:", err);
                input.disabled = false;
                sendBtn.disabled = false;
            });
        }

        // Sự kiện Click nút Gửi
        sendBtn.onclick = sendMessage;

        // Sự kiện nhấn phím Enter
        input.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                sendMessage();
            }
        });

        // --- AUTO REFRESH (3 giây/lần) ---
        setInterval(() => {
            if (chatPopup.style.display === "block") {
                loadMessages();
            }
        }, 3000);
    });
    </script>
    @endauth

    @yield('scripts')
</body>
</html>
