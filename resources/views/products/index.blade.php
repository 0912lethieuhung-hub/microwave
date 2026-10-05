@extends('layouts.frontend')

@section('title', 'Siêu Lò Nướng - Hệ Thống Lò Vi Sóng & Gia Dụng Số 1')

@section('extra-css')
<style>
    /* TOP HERO SECTION: CATEGORY SIDEBAR & HERO BANNER */
    .hero-layout-row {
        display: grid;
        grid-template-columns: 290px 1fr;
        gap: 1.5rem;
        align-items: stretch;
        margin-bottom: 2rem;
    }

    /* COMPACT SIDE CATEGORY MENU CARD */
    .category-side-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid var(--card-border);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .category-side-header {
        background: linear-gradient(135deg, #0071af 0%, #005a8c 100%);
        color: #ffffff;
        padding: 0.95rem 1.15rem;
        font-weight: 800;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        letter-spacing: 0.3px;
    }
    .category-side-header i {
        color: var(--dmx-yellow);
        font-size: 1.1rem;
    }

    /* ACCORDION CATEGORY LIST */
    .category-accordion-list {
        list-style: none;
        padding: 0.35rem 0;
        margin: 0;
        flex: 1;
    }
    .category-accordion-item {
        border-bottom: 1px solid #f8fafc;
    }
    .category-accordion-item:last-child {
        border-bottom: none;
    }

    .cat-head-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1.15rem;
        text-decoration: none;
        color: #1e293b;
        transition: all 0.2s ease;
        cursor: pointer;
        user-select: none;
    }
    .cat-head-row:hover {
        background: var(--dmx-blue-light);
        color: var(--dmx-blue);
        padding-left: 1.35rem;
    }
    .category-accordion-item.active .cat-head-row {
        background: #e0f2fe;
        color: var(--dmx-blue);
        font-weight: 700;
        border-left: 4px solid var(--dmx-blue);
    }
    .cat-left-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .cat-emoji {
        font-size: 1.15rem;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
    }
    .cat-name-text {
        font-size: 0.95rem;
        font-weight: 700;
    }

    /* HERO BANNER (ĐMX THEMED) */
    .hero-banner-dmx {
        background: linear-gradient(135deg, #005a8c 0%, #0071af 50%, #0288d1 100%);
        border-radius: 14px;
        padding: 2.25rem 2.5rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 20px rgba(0, 113, 175, 0.22);
        position: relative;
        overflow: hidden;
        height: 100%;
        box-sizing: border-box;
    }
    .hero-banner-dmx::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255, 212, 0, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-content h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.6rem;
        color: #ffffff;
        letter-spacing: -0.5px;
    }
    .hero-content h1 span {
        color: var(--dmx-yellow);
    }
    .hero-content p {
        font-size: 0.98rem;
        opacity: 0.95;
        max-width: 660px;
        line-height: 1.55;
        margin-bottom: 1.35rem;
    }
    .hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }
    .hero-badge-pill {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        padding: 0.4rem 0.9rem;
        border-radius: 50px;
        font-size: 0.84rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .hero-badge-pill i {
        color: var(--dmx-yellow);
    }
    .hero-illustration {
        font-size: 5.5rem;
        color: var(--dmx-yellow);
        filter: drop-shadow(0 4px 15px rgba(0,0,0,0.25));
        animation: float 4s ease-in-out infinite alternate;
    }
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        100% { transform: translateY(-8px) rotate(4deg); }
    }

    /* BREADCRUMB & TOOLBAR SECTION */
    .content-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 0.9rem 1.25rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.88rem;
        color: var(--text-muted);
    }
    .breadcrumb-nav a {
        color: var(--dmx-blue);
        text-decoration: none;
    }
    .breadcrumb-nav a:hover {
        text-decoration: underline;
    }
    .filter-summary-box {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .active-tag-chip {
        background: var(--dmx-blue-light);
        border: 1px solid #bae6fd;
        color: var(--dmx-blue);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
    }
    .active-tag-chip:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #dc2626;
    }

    /* SORT & BRAND FILTER BAR */
    .brand-filter-bar {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 0.9rem 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .filter-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .filter-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        min-width: 90px;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .filter-chips-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.45rem;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.85rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .filter-chip:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .filter-chip.active {
        background: var(--dmx-blue);
        border-color: var(--dmx-blue);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 113, 175, 0.25);
    }
    .sort-select-box {
        appearance: none;
        -webkit-appearance: none;
        background-color: #f8fafc;
        background-image: url("data:image/svg+xml;utf8,<svg fill='%230071af' height='18' viewBox='0 0 24 24' width='18' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.45rem 2.2rem 0.45rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .sort-select-box:focus {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 3px rgba(0, 113, 175, 0.15);
    }

    /* PAGINATION STYLING */
    .pagination-wrapper {
        margin-top: 2.5rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .pagination-wrapper nav {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .pagination-wrapper ul.pagination {
        display: flex;
        list-style: none;
        gap: 0.35rem;
        align-items: center;
        padding: 0;
        margin: 0;
    }
    .pagination-wrapper .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 0.65rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .pagination-wrapper .page-item .page-link:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: var(--dmx-blue);
    }
    .pagination-wrapper .page-item.active .page-link {
        background: var(--dmx-blue);
        border-color: var(--dmx-blue);
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0, 113, 175, 0.3);
    }
    .pagination-wrapper .page-item.disabled .page-link {
        color: #94a3b8;
        background: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* PRODUCT GRID */
    .product-grid-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }

    /* PRODUCT CARD (ĐMX STYLE) */
    .dmx-product-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        cursor: pointer;
    }
    .dmx-product-card:hover {
        border-color: var(--dmx-blue);
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
    }
    .dmx-product-card:hover .card-title {
        color: var(--dmx-blue);
    }
    .btn-wishlist-card {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid #e2e8f0;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 4;
        box-shadow: 0 2px 5px rgba(0,0,0,0.06);
    }
    .btn-wishlist-card:hover {
        color: var(--dmx-red);
        background: #ffffff;
        transform: scale(1.12);
    }
    .btn-wishlist-card.active {
        color: var(--dmx-red);
        background: #fee2e2;
        border-color: #fca5a5;
    }

    /* TOP BADGES */
    .card-top-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-bottom: 0.75rem;
        min-height: 24px;
    }
    .badge-deal {
        background: #fee2e2;
        color: var(--dmx-red);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.45rem;
        border-radius: 4px;
    }
    .badge-installment {
        background: #e0f2fe;
        color: var(--dmx-blue);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.45rem;
        border-radius: 4px;
    }

    /* PRODUCT IMAGE */
    .card-image-box {
        width: 100%;
        height: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 1rem;
        position: relative;
    }
    .card-image-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.4s ease;
    }
    .dmx-product-card:hover .card-image-box img {
        transform: scale(1.06);
    }

    /* PROMO LABEL */
    .card-promo-tag {
        background: #fef3c7;
        color: #92400e;
        font-size: 0.74rem;
        font-weight: 600;
        padding: 0.3rem 0.5rem;
        border-radius: 4px;
        margin-bottom: 0.65rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        line-height: 1.3;
    }

    /* TITLE */
    .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.4rem;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-decoration: none;
        transition: color 0.2s;
    }
    .card-title::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
    }
    .card-title:hover {
        color: var(--dmx-blue);
    }

    /* SPECS */
    .card-specs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-bottom: 0.85rem;
    }
    .card-spec-item {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
    }

    /* PRICE */
    .card-price-box {
        margin-top: auto;
        padding-top: 0.5rem;
    }
    .price-main {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--dmx-red);
        line-height: 1.2;
    }
    .price-original-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.2rem;
    }
    .price-original {
        font-size: 0.8rem;
        color: #94a3b8;
        text-decoration: line-through;
    }
    .price-discount-percent {
        font-size: 0.75rem;
        color: var(--dmx-red);
        font-weight: 700;
        background: #fee2e2;
        padding: 0.1rem 0.3rem;
        border-radius: 3px;
    }

    /* RATING & BUTTON */
    .card-rating-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.75rem;
        padding-top: 0.5rem;
    }
    .card-rating {
        font-size: 0.78rem;
        color: #f59e0b;
        font-weight: 600;
    }
    .card-rating span {
        color: var(--text-muted);
        font-weight: normal;
    }
    .btn-card-action {
        background: var(--dmx-blue-light);
        color: var(--dmx-blue);
        border: 1px solid #bae6fd;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
        z-index: 2;
    }
    .btn-card-action:hover {
        background: var(--dmx-blue);
        color: #ffffff;
    }

    /* EMPTY STATE */
    .empty-state {
        grid-column: 1 / -1;
        background: #ffffff;
        border-radius: 12px;
        padding: 4rem 2rem;
        text-align: center;
        border: 1px solid var(--card-border);
    }
    .empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .product-grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 992px) {
        .hero-layout-row {
            grid-template-columns: 1fr;
        }
        .product-grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .product-grid-container {
            grid-template-columns: 1fr;
        }
        .hero-banner-dmx {
            flex-direction: column;
            text-align: center;
            padding: 1.5rem;
        }
        .hero-badges {
            justify-content: center;
        }
        .hero-illustration {
            display: none;
        }
    }

    /* FLASH SALE BANNER */
    .flash-sale-banner {
        background: linear-gradient(135deg, #d70018 0%, #ea580c 50%, #f59e0b 100%);
        border-radius: 14px;
        padding: 1.15rem 1.75rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 18px rgba(215, 0, 24, 0.25);
        position: relative;
        overflow: hidden;
    }
    .flash-sale-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .flash-sale-left {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .flash-sale-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        font-size: 1.2rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .fs-pulse-icon {
        font-size: 1.4rem;
        animation: flashPulse 1s infinite alternate;
    }
    @keyframes flashPulse {
        0% { transform: scale(1); filter: drop-shadow(0 0 2px #fff); }
        100% { transform: scale(1.25); filter: drop-shadow(0 0 8px #ffd400); }
    }
    .fs-discount-badge {
        background: var(--dmx-yellow);
        color: #000;
        font-weight: 800;
        font-size: 0.8rem;
        padding: 0.2rem 0.55rem;
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }
    .fs-subtitle {
        font-size: 0.85rem;
        opacity: 0.95;
        margin: 0;
    }
    .flash-sale-center {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        background: rgba(0, 0, 0, 0.2);
        padding: 0.5rem 1.15rem;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .timer-label {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #ffedd5;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .fs-timer-boxes {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .timer-box {
        background: #1e293b;
        color: #ffffff;
        border-radius: 6px;
        padding: 0.25rem 0.55rem;
        min-width: 44px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .timer-box span {
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1;
        display: block;
        color: var(--dmx-yellow);
    }
    .timer-box small {
        font-size: 0.65rem;
        color: #94a3b8;
        text-transform: uppercase;
        display: block;
        margin-top: 2px;
    }
    .timer-colon {
        font-size: 1.2rem;
        font-weight: 800;
        color: #ffffff;
    }
    .flash-sale-right {
        flex-shrink: 0;
    }
    .btn-fs-action {
        background: #ffffff;
        color: var(--dmx-red);
        font-weight: 800;
        font-size: 0.88rem;
        padding: 0.6rem 1.25rem;
        border-radius: 50px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .btn-fs-action:hover {
        background: var(--dmx-yellow);
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    /* BRAND SHOWCASE BAR */
    .brand-showcase-bar {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 0.85rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        overflow-x: auto;
    }
    .brand-showcase-header {
        font-size: 0.82rem;
        font-weight: 800;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .brand-chips-list {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .brand-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .brand-chip:hover {
        border-color: var(--dmx-blue);
        background: var(--dmx-blue-light);
        color: var(--dmx-blue);
        transform: translateY(-1px);
    }
    .brand-chip.active {
        background: var(--dmx-blue);
        border-color: var(--dmx-blue);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 113, 175, 0.3);
    }

    @media (max-width: 992px) {
        .flash-sale-banner {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        .flash-sale-title {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')

<!-- KHU VỰC ĐỈNH TRANG: DANH MỤC GỌN GÀNG BÊN HÔNG & HERO BANNER BÊN PHẢI -->
<div class="hero-layout-row" id="categories">
    
    <!-- THANH BÊN HÔNG: CHỈ HIỂN THỊ DANH MỤC (BẤM VÀO SẼ MỞ RỘNG THÔNG TIN CẦN TÌM) -->
    <aside class="category-side-card">
        <div class="category-side-header">
            <i class="fa-solid fa-layer-group"></i>
            <span>DANH MỤC LÒ VI SÓNG</span>
        </div>

        <ul class="category-accordion-list">
            <!-- Tất cả sản phẩm -->
            <li class="category-accordion-item {{ !request('category') ? 'active' : '' }}">
                <a href="{{ route('home') }}" class="cat-head-row">
                    <div class="cat-left-info">
                        <div class="cat-emoji"><i class="fa-solid fa-house" style="color: var(--dmx-blue);"></i></div>
                        <span class="cat-name-text">Tất cả sản phẩm</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
                </a>
            </li>

            <!-- Lò vi sóng cơ -->
            <li class="category-accordion-item {{ request('category') == 'lo-vi-song-co' ? 'active' : '' }}">
                <a href="{{ route('home', ['category' => 'lo-vi-song-co']) }}" class="cat-head-row">
                    <div class="cat-left-info">
                        <div class="cat-emoji">🔘</div>
                        <span class="cat-name-text">Lò vi sóng cơ</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
                </a>
            </li>

            <!-- Lò vi sóng điện tử -->
            <li class="category-accordion-item {{ request('category') == 'lo-vi-song-dien-tu' ? 'active' : '' }}">
                <a href="{{ route('home', ['category' => 'lo-vi-song-dien-tu']) }}" class="cat-head-row">
                    <div class="cat-left-info">
                        <div class="cat-emoji">📟</div>
                        <span class="cat-name-text">Lò vi sóng điện tử</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
                </a>
            </li>

            <!-- Lò vi sóng có nướng -->
            <li class="category-accordion-item {{ request('category') == 'lo-vi-song-co-nuong' ? 'active' : '' }}">
                <a href="{{ route('home', ['category' => 'lo-vi-song-co-nuong']) }}" class="cat-head-row">
                    <div class="cat-left-info">
                        <div class="cat-emoji">🍗</div>
                        <span class="cat-name-text">Lò vi sóng có nướng</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
                </a>
            </li>

            <!-- Lò vi sóng âm tủ -->
            <li class="category-accordion-item {{ request('category') == 'lo-vi-song-am-tu' ? 'active' : '' }}">
                <a href="{{ route('home', ['category' => 'lo-vi-song-am-tu']) }}" class="cat-head-row">
                    <div class="cat-left-info">
                        <div class="cat-emoji">🔲</div>
                        <span class="cat-name-text">Lò vi sóng âm tủ</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
                </a>
            </li>

            <!-- Khuyến Mãi Lễ Tết -->
            <li class="category-accordion-item">
                <a href="{{ route('promotions') }}" class="cat-head-row" style="color: var(--dmx-red);">
                    <div class="cat-left-info">
                        <div class="cat-emoji">🔥</div>
                        <span class="cat-name-text">Khuyến Mãi Lễ Tết</span>
                    </div>
                    <span style="background: #fee2e2; color: var(--dmx-red); font-size: 0.72rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 4px;">-30%</span>
                </a>
            </li>

            <!-- Cẩm Nang & Mẹo Vặt -->
            <li class="category-accordion-item">
                <a href="{{ route('guides') }}" class="cat-head-row">
                    <div class="cat-left-info">
                        <div class="cat-emoji">📖</div>
                        <span class="cat-name-text">Cẩm nang & Mẹo vặt</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: #94a3b8;"></i>
                </a>
            </li>
        </ul>
    </aside>

    <!-- HERO BANNER (ĐIỆN MÁY XANH STYLE) -->
    <div class="hero-banner-dmx">
        <div class="hero-content">
            <h1>ĐẠI TIỆC GIA DỤNG <span>SIÊU LÒ NƯỚNG</span></h1>
            <p>Thương hiệu phân phối lò vi sóng hàng đầu Việt Nam. Cam kết hàng chính hãng 100%, bảo hành 12 tháng, đổi mới trong 365 ngày nếu lỗi nhà sản xuất.</p>
            <div class="hero-badges" id="cam-ket">
                <span class="hero-badge-pill"><i class="fa-solid fa-truck-fast"></i> Giao nhanh 2 Giờ</span>
                <span class="hero-badge-pill"><i class="fa-solid fa-rotate-left"></i> 1 Đổi 1 trong 365 Ngày</span>
                <span class="hero-badge-pill"><i class="fa-solid fa-credit-card"></i> Trả chậm 0% Trả trước 0đ</span>
                <span class="hero-badge-pill"><i class="fa-solid fa-gift"></i> Tặng thố thủy tinh cao cấp</span>
            </div>
        </div>
        <div class="hero-illustration">
            <i class="fa-solid fa-kitchen-set"></i>
        </div>
    </div>
</div>

<!-- KHU VỰC FLASH SALE ĐỒNG HỒ ĐẾM NGƯỢC -->
<div class="flash-sale-banner">
    <div class="flash-sale-left">
        <div class="flash-sale-title">
            <span class="fs-pulse-icon">⚡</span>
            <span>GIỜ VÀNG SĂN DEAL</span>
            <span class="fs-discount-badge">-40%</span>
        </div>
        <p class="fs-subtitle">Ưu đãi chớp nhoáng hàng ngày cho top lò vi sóng chính hãng được yêu thích</p>
    </div>

    <div class="flash-sale-center">
        <span class="timer-label"><i class="fa-regular fa-clock"></i> KẾT THÚC TRONG:</span>
        <div class="fs-timer-boxes">
            <div class="timer-box"><span id="fsHours">00</span><small>Giờ</small></div>
            <span class="timer-colon">:</span>
            <div class="timer-box"><span id="fsMinutes">00</span><small>Phút</small></div>
            <span class="timer-colon">:</span>
            <div class="timer-box"><span id="fsSeconds">00</span><small>Giây</small></div>
        </div>
    </div>

    <div class="flash-sale-right">
        <a href="{{ route('home', ['sort' => 'popular']) }}" class="btn-fs-action">
            <i class="fa-solid fa-fire"></i> Săn Deal Ngay
        </a>
    </div>
</div>

<!-- KHU VỰC THƯƠNG HIỆU CHÍNH HÃNG HÀNG ĐẦU -->
<div class="brand-showcase-bar">
    <div class="brand-showcase-header">
        <i class="fa-solid fa-award" style="color: var(--dmx-blue);"></i>
        <span>THƯƠNG HIỆU:</span>
    </div>
    <div class="brand-chips-list">
        <a href="{{ route('home', request()->except('brand', 'page')) }}" 
           class="brand-chip {{ !request('brand') ? 'active' : '' }}">
            Tất cả
        </a>
        @php
            $featuredBrands = ['Sharp', 'Panasonic', 'Electrolux', 'Toshiba', 'Roler', 'ProMaster'];
        @endphp
        @foreach($featuredBrands as $bName)
            @php
                $isActive = request('brand') === $bName;
                $url = $isActive 
                    ? route('home', request()->except('brand', 'page')) 
                    : route('home', array_merge(request()->except('page'), ['brand' => $bName]));
            @endphp
            <a href="{{ $url }}" class="brand-chip {{ $isActive ? 'active' : '' }}">
                <i class="fa-solid fa-tag" style="font-size: 0.75rem;"></i> {{ $bName }}
            </a>
        @endforeach
    </div>
</div>

<!-- KHU VỰC THANH ĐIỀU HƯỚNG & SẮP XẾP -->
<div class="content-header-bar">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <span>›</span>
        <span>Lò vi sóng & Thiết bị nướng</span>
        @if(request('category'))
            <span>›</span>
            <strong style="color: var(--dmx-blue);">{{ $categories->firstWhere('slug', request('category'))->name ?? request('category') }}</strong>
        @endif
        @if(request('search'))
            <span>›</span>
            <span>Tìm kiếm: "<strong>{{ request('search') }}</strong>"</span>
        @endif
    </div>

    <!-- Right: Sort Form & Total Count -->
    <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
        <span style="font-size: 0.88rem; font-weight: 700; color: #1e293b;">
            <i class="fa-solid fa-fire" style="color: var(--dmx-red);"></i> Hiển thị <strong>{{ $products->total() }}</strong> sản phẩm
        </span>

        <form method="GET" action="{{ route('home') }}" id="sortForm" style="display: flex; align-items: center; gap: 0.4rem; margin: 0;">
            @foreach(request()->except('sort', 'page') as $key => $val)
                @if(is_array($val))
                    @foreach($val as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endif
            @endforeach
            <label for="sortSelect" style="font-size: 0.85rem; font-weight: 600; color: #475569; white-space: nowrap;">
                <i class="fa-solid fa-arrow-down-short-wide" style="color: var(--dmx-blue);"></i> Sắp xếp:
            </label>
            <select id="sortSelect" name="sort" onchange="document.getElementById('sortForm').submit()" class="sort-select-box">
                <option value="default" {{ request('sort','default')=='default'?'selected':'' }}>Mới nhất</option>
                <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Giá: Thấp đến cao</option>
                <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Giá: Cao đến thấp</option>
                <option value="popular" {{ request('sort')=='popular'?'selected':'' }}>Xem nhiều nhất</option>
            </select>
        </form>
    </div>
</div>

<!-- KHU VỰC BỘ LỌC THƯƠNG HIỆU & MỨC GIÁ -->
<div class="brand-filter-bar">
    <!-- Hàng lọc thương hiệu -->
    <div class="filter-row">
        <div class="filter-label">
            <i class="fa-solid fa-tag" style="color: var(--dmx-blue);"></i> Hãng SX:
        </div>
        <div class="filter-chips-group">
            <a href="{{ route('home', request()->except(['brand', 'page'])) }}" 
               class="filter-chip {{ !request('brand') ? 'active' : '' }}">
                Tất cả hãng
            </a>
            @if(isset($brands) && $brands->count() > 0)
                @foreach($brands as $brand)
                    <a href="{{ route('home', array_merge(request()->except(['brand', 'page']), ['brand' => $brand])) }}" 
                       class="filter-chip {{ request('brand') == $brand ? 'active' : '' }}">
                        {{ $brand }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Hàng lọc khoảng giá -->
    <div class="filter-row" style="border-top: 1px dashed #e2e8f0; padding-top: 0.65rem;">
        <div class="filter-label">
            <i class="fa-solid fa-filter-circle-dollar" style="color: var(--dmx-orange);"></i> Mức giá:
        </div>
        <div class="filter-chips-group">
            <a href="{{ route('home', request()->except(['price_range', 'page'])) }}" 
               class="filter-chip {{ !request('price_range') ? 'active' : '' }}">
                Tất cả mức giá
            </a>
            <a href="{{ route('home', array_merge(request()->except(['price_range', 'page']), ['price_range' => 'under_2m'])) }}" 
               class="filter-chip {{ request('price_range') == 'under_2m' ? 'active' : '' }}">
                Dưới 2 triệu
            </a>
            <a href="{{ route('home', array_merge(request()->except(['price_range', 'page']), ['price_range' => '2m_5m'])) }}" 
               class="filter-chip {{ request('price_range') == '2m_5m' ? 'active' : '' }}">
                2 - 5 triệu
            </a>
            <a href="{{ route('home', array_merge(request()->except(['price_range', 'page']), ['price_range' => 'over_5m'])) }}" 
               class="filter-chip {{ request('price_range') == 'over_5m' ? 'active' : '' }}">
                Trên 5 triệu
            </a>
        </div>

        @if(request('category') || request('price_range') || request('brand') || request('search'))
            <div style="margin-left: auto;">
                <a href="{{ route('home') }}" style="font-size: 0.8rem; color: var(--dmx-red); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">
                    <i class="fa-solid fa-rotate-left"></i> Xóa tất cả bộ lọc
                </a>
            </div>
        @endif
    </div>

    @if(request('category') || request('price_range') || request('brand') || request('search'))
        <!-- Đang lọc theo chips -->
        <div class="filter-row" style="border-top: 1px solid #f1f5f9; padding-top: 0.6rem; align-items: center;">
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Đang lọc:</span>
            @if(request('category'))
                <a href="{{ route('home', request()->except('category')) }}" class="active-tag-chip" title="Bỏ chọn danh mục">
                    <span>Danh mục: {{ $categories->firstWhere('slug', request('category'))->name ?? request('category') }}</span>
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
            @if(request('brand'))
                <a href="{{ route('home', request()->except('brand')) }}" class="active-tag-chip" title="Bỏ lọc hãng">
                    <span>Hãng: {{ request('brand') }}</span>
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
            @if(request('price_range'))
                @php
                    $priceText = request('price_range') == 'under_2m' ? 'Dưới 2 triệu' : (request('price_range') == '2m_5m' ? '2 - 5 triệu' : 'Trên 5 triệu');
                @endphp
                <a href="{{ route('home', request()->except('price_range')) }}" class="active-tag-chip" title="Bỏ lọc giá">
                    <span>Giá: {{ $priceText }}</span>
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
            @if(request('search'))
                <a href="{{ route('home', request()->except('search')) }}" class="active-tag-chip" title="Bỏ từ khóa">
                    <span>Từ khóa: "{{ request('search') }}"</span>
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </div>
    @endif
</div>

<!-- LƯỚI HIỂN THỊ SẢN PHẨM (PRODUCT GRID 4 CỘT) -->
<div class="product-grid-container">
    @forelse ($products as $product)
        @php
            $displayOriginalPrice = $product->original_price ?: round($product->price * 1.18, -4);
            $discountPercent = $displayOriginalPrice > $product->price ? round((($displayOriginalPrice - $product->price) / $displayOriginalPrice) * 100) : 0;
            $calcReviewCount = (($product->id * 19 + 31) % 95) + 32;
            $calcRating = number_format(4.7 + (($product->id % 4) * 0.1), 1);
        @endphp
        <div class="dmx-product-card" id="product-card-{{ $product->id }}">
            @php
                $isFav = Auth::check() && in_array($product->id, $wishlistProductIds ?? []);
            @endphp
            <button type="button" 
                    class="btn-wishlist-card {{ $isFav ? 'active' : '' }}" 
                    onclick="event.stopPropagation(); toggleWishlist({{ $product->id }}, this)" 
                    title="{{ $isFav ? 'Bỏ thích' : 'Thêm vào yêu thích' }}">
                <i class="{{ $isFav ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
            </button>

            <!-- Badges -->
            <div class="card-top-badges">
                @if($product->stock <= 0)
                    <span class="badge-deal" style="background: #fee2e2; color: #dc2626;">Hết hàng</span>
                @elseif($product->stock <= 5)
                    <span class="badge-deal" style="background: #fef3c7; color: #b45309;">Sắp hết</span>
                @else
                    <span class="badge-deal">Mẫu mới</span>
                @endif
                <span class="badge-installment">Trả chậm 0%</span>
            </div>

            <!-- Image -->
            <div class="card-image-box">
                @if($product->image_url)
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" loading="lazy">
                @else
                    <div style="color: #94a3b8; font-size: 0.85rem;"><i class="fa-regular fa-image"></i> Đang cập nhật ảnh</div>
                @endif
            </div>

            <!-- Promo Tag -->
            <div class="card-promo-tag">
                <i class="fa-solid fa-gift" style="color: #d97706;"></i>
                <span>Tặng bộ thố thủy tinh cao cấp</span>
            </div>

            <!-- Title -->
            <a href="{{ route('product.show', $product->id) }}" class="card-title" title="{{ $product->name }}">
                {{ $product->name }}
            </a>

            <!-- Specs tag -->
            <div class="card-specs">
                @if($product->brand)
                    <span class="card-spec-item" style="color: var(--dmx-blue); font-weight: 700;">{{ $product->brand }}</span>
                @endif
                @if($product->capacity)
                    <span class="card-spec-item">{{ $product->capacity }}</span>
                @endif
                @if($product->power)
                    <span class="card-spec-item">{{ $product->power }}</span>
                @endif
                <span class="card-spec-item">{{ $product->category->name ?? 'Lò vi sóng' }}</span>
            </div>

            <!-- Price -->
            <div class="card-price-box">
                <div class="price-main">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                @if($discountPercent > 0)
                    <div class="price-original-wrapper">
                        <span class="price-original">{{ number_format($displayOriginalPrice, 0, ',', '.') }}₫</span>
                        <span class="price-discount-percent">-{{ $discountPercent }}%</span>
                    </div>
                @endif
            </div>

            <!-- Rating & CTA -->
            <div class="card-rating-footer">
                <div class="card-rating">
                    <i class="fa-solid fa-star"></i> {{ $calcRating }} <span>({{ $calcReviewCount }})</span>
                </div>
                <a href="{{ route('product.show', $product->id) }}" class="btn-card-action">
                    Xem chi tiết
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">Không tìm thấy sản phẩm phù hợp</h3>
            <p style="color: #64748b; margin-bottom: 1.5rem;">Hãy thử xóa bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
            <a href="{{ route('home') }}" class="btn-card-action" style="padding: 0.6rem 1.2rem;">Xem tất cả sản phẩm</a>
        </div>
    @endforelse
</div>

<!-- PHÂN TRANG (PAGINATION) -->
@if($products->hasPages())
    <div class="pagination-wrapper">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCountdown() {
        const now = new Date();
        const endOfDay = new Date();
        endOfDay.setHours(23, 59, 59, 999);

        let diff = Math.floor((endOfDay - now) / 1000);
        if (diff <= 0) diff = 0;

        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;

        const hEl = document.getElementById('fsHours');
        const mEl = document.getElementById('fsMinutes');
        const sEl = document.getElementById('fsSeconds');

        if (hEl) hEl.textContent = String(hours).padStart(2, '0');
        if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
        if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
@endsection


