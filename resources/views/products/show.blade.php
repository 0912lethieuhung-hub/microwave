@extends('layouts.frontend')

@section('title', $product->name . ' - Siêu Lò Nướng Chính Hãng')

@php
    $cleanDesc = Str::limit(strip_tags($product->description ?? 'Mua ' . $product->name . ' chính hãng tại Siêu Lò Nướng. Giá tốt nhất, bảo hành 12 tháng, đổi mới 365 ngày, giao nhanh 2 giờ.'), 160);
    $productImgUrl = $product->image_url ? asset($product->image_url) : asset('images/logo.png');
    $productCanonical = route('product.show', $product->id);
    $schemaRating = $product->reviews_count > 0 ? $product->average_rating : '5.0';
    $schemaReviewCount = $product->reviews_count > 0 ? $product->reviews_count : 1;
@endphp

@section('meta_description', $cleanDesc)

@section('seo')
    <!-- OpenGraph / Facebook -->
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="Siêu Lò Nướng">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ $cleanDesc }}">
    <meta property="og:image" content="{{ $productImgUrl }}">
    <meta property="og:url" content="{{ $productCanonical }}">
    <meta property="product:price:amount" content="{{ $product->price }}">
    <meta property="product:price:currency" content="VND">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product->name }}">
    <meta name="twitter:description" content="{{ $cleanDesc }}">
    <meta name="twitter:image" content="{{ $productImgUrl }}">

    @php
        $schemaData = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => [$productImgUrl],
            'description' => $cleanDesc,
            'sku' => 'SLN-' . $product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand ?? 'Siêu Lò Nướng',
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => $productCanonical,
                'priceCurrency' => 'VND',
                'price' => (string) $product->price,
                'priceValidUntil' => \Carbon\Carbon::now()->addMonths(3)->format('Y-m-d'),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'Siêu Lò Nướng',
                ],
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $schemaRating,
                'reviewCount' => (string) $schemaReviewCount,
            ],
        ];
    @endphp
    <!-- Google Rich Results / Schema.org JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('extra-css')
<style>
    /* BREADCRUMB */
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
    }
    .breadcrumb-nav a {
        color: var(--dmx-blue);
        text-decoration: none;
    }

    /* PRODUCT DETAIL CONTAINER */
    .detail-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .detail-header {
        border-bottom: 1px solid var(--card-border);
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
    .detail-header h1 {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.3;
        margin-bottom: 0.5rem;
    }
    .detail-header-meta {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        font-size: 0.88rem;
        color: var(--text-muted);
    }
    .detail-stars {
        color: #f59e0b;
        font-weight: 700;
    }

    .detail-main-layout {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 2.5rem;
    }

    /* LEFT: IMAGE & POLICIES */
    .detail-image-box {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 380px;
        background: #ffffff;
        margin-bottom: 1.25rem;
    }
    .detail-image-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s;
    }
    .detail-image-box:hover img {
        transform: scale(1.05);
    }

    /* THUMBNAIL GALLERY */
    .detail-thumbnails-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
        overflow-x: auto;
        padding-bottom: 0.35rem;
    }
    .thumb-item {
        width: 68px;
        height: 68px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .thumb-item:hover, .thumb-item.active {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 2px rgba(0, 113, 175, 0.2);
    }
    .thumb-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .detail-policies-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    .policy-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        font-size: 0.82rem;
        color: #334155;
    }
    .policy-item i {
        font-size: 1.25rem;
        color: var(--dmx-blue);
    }

    /* RIGHT: PRICING & ACTIONS */
    .price-promo-box {
        background: #f8fafc;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--dmx-red);
    }
    .current-price {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dmx-red);
        line-height: 1;
    }
    .old-price-box {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-top: 0.4rem;
        font-size: 0.9rem;
    }
    .old-price {
        color: #9ca3af;
        text-decoration: line-through;
    }
    .discount-badge {
        background: #fee2e2;
        color: var(--dmx-red);
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        font-size: 0.8rem;
    }

    /* PROMO GIFTS BOX */
    .promo-gifts-card {
        border: 1px solid #fed7aa;
        background: #fffbeb;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .promo-gifts-card h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #b45309;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .promo-gift-list {
        list-style: none;
        font-size: 0.85rem;
        color: #78350f;
    }
    .promo-gift-list li {
        margin-bottom: 0.4rem;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .promo-gift-list li i {
        color: #d97706;
        margin-top: 0.2rem;
    }

    /* BUY BUTTONS */
    .btn-buy-now {
        display: block;
        width: 100%;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 0.9rem 1.5rem;
        font-size: 1.1rem;
        font-weight: 800;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        margin-bottom: 0.75rem;
    }
    .btn-buy-now:hover {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        transform: translateY(-2px);
    }
    .btn-buy-now span {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        opacity: 0.9;
        margin-top: 0.1rem;
    }

    .btn-add-cart {
        display: block;
        width: 100%;
        background: #ffffff;
        color: var(--dmx-blue);
        border: 2px solid var(--dmx-blue);
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-add-cart:hover {
        background: var(--dmx-blue-light);
    }

    /* SPECS & DESC TABLE */
    .specs-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        font-size: 0.88rem;
    }
    .specs-table tr:nth-child(odd) {
        background: #f8fafc;
    }
    .specs-table td {
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .specs-table td:first-child {
        font-weight: 600;
        color: #475569;
        width: 35%;
    }

    /* COLOR PICKER */
    .variant-section {
        margin-bottom: 1.25rem;
    }
    .variant-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .color-pill-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .color-pill-btn {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.45rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .color-pill-btn:hover {
        border-color: var(--dmx-blue);
        color: var(--dmx-blue);
    }
    .color-pill-btn.active {
        border-color: var(--dmx-blue);
        background: #e0f2fe;
        color: var(--dmx-blue);
        box-shadow: 0 0 0 1px var(--dmx-blue);
    }

    /* QUANTITY SELECTOR */
    .quantity-wrapper {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 1.25rem;
    }
    .qty-control-box {
        display: inline-flex;
        align-items: center;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
    }
    .btn-qty {
        width: 36px;
        height: 36px;
        background: #f8fafc;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        color: #334155;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-qty:hover {
        background: #e2e8f0;
        color: var(--dmx-blue);
    }
    .input-qty {
        width: 48px;
        height: 36px;
        border: none;
        text-align: center;
        font-weight: 700;
        font-size: 0.95rem;
        color: #1e293b;
        outline: none;
    }
    .input-qty::-webkit-outer-spin-button,
    .input-qty::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* RELATED PRODUCTS */
    .related-products-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        padding: 1.75rem;
        margin-top: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
    .related-products-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .related-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }
    @media (max-width: 1024px) {
        .related-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 600px) {
        .related-grid {
            grid-template-columns: 1fr;
        }
    }
    .related-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        transition: all 0.25s ease;
    }
    .related-card:hover {
        border-color: var(--dmx-blue);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .related-img-box {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
    }
    .related-img-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .related-brand {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--dmx-blue);
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    .related-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .related-price {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--dmx-red);
        margin-top: auto;
    }

    /* WISHLIST BUTTON IN DETAIL */
    .btn-detail-wishlist {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 50px;
        padding: 0.55rem 1.1rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-detail-wishlist:hover {
        border-color: var(--dmx-red);
        color: var(--dmx-red);
        background: #fee2e2;
    }
    .btn-detail-wishlist.active {
        background: #fee2e2;
        border-color: #fca5a5;
        color: var(--dmx-red);
    }

    /* REVIEWS SECTION */
    .reviews-section-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        padding: 2rem;
        margin-top: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
    .reviews-header {
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 1rem;
    }
    .reviews-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .rating-summary-container {
        display: grid;
        grid-template-columns: 200px 1fr 240px;
        gap: 2rem;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 900px) {
        .rating-summary-container {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
    }
    .rating-big-score {
        text-align: center;
    }
    .big-score-num {
        font-size: 3.2rem;
        font-weight: 800;
        color: var(--dmx-red);
        line-height: 1;
    }
    .big-score-stars {
        color: var(--dmx-yellow);
        font-size: 1.1rem;
        margin: 0.4rem 0;
    }
    .big-score-total {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 500;
    }
    .rating-bars {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }
    .rating-bar-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
    }
    .bar-star-label {
        min-width: 40px;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .bar-track {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 50px;
        overflow: hidden;
    }
    .bar-fill {
        height: 100%;
        background: var(--dmx-yellow);
        border-radius: 50px;
        transition: width 0.5s ease;
    }
    .bar-percent {
        min-width: 35px;
        text-align: right;
        font-size: 0.78rem;
        color: #64748b;
    }
    .rating-cta-box {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-left: 1px dashed #cbd5e1;
        padding-left: 1.5rem;
    }
    @media (max-width: 900px) {
        .rating-cta-box {
            border-left: none;
            padding-left: 0;
            border-top: 1px dashed #cbd5e1;
            padding-top: 1.25rem;
        }
    }
    .btn-write-review {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--dmx-blue);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 0.65rem 1.25rem;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-write-review:hover {
        background: var(--dmx-blue-dark);
    }
    .review-form-box {
        background: #ffffff;
        border: 1.5px solid #bfdbfe;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 113, 175, 0.05);
    }
    .star-rating-picker {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 1.6rem;
        cursor: pointer;
    }
    .star-pick {
        color: #cbd5e1;
        transition: transform 0.15s, color 0.15s;
    }
    .star-pick:hover, .star-pick.active {
        color: var(--dmx-yellow);
    }
    .star-pick:hover {
        transform: scale(1.15);
    }
    .review-textarea {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.92rem;
        font-family: inherit;
        outline: none;
        resize: vertical;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }
    .review-textarea:focus {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 3px rgba(0, 113, 175, 0.12);
    }
    .btn-submit-review {
        background: var(--dmx-red);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 0.7rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }
    .btn-submit-review:hover {
        background: #b91c1c;
    }
    .reviews-list-container {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .review-item-card {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 1.25rem;
    }
    .review-item-card:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .review-user-row {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 0.65rem;
    }
    .review-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--dmx-blue);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .review-user-name {
        font-weight: 700;
        font-size: 0.92rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .badge-verified-buyer {
        background: #dcfce7;
        color: #166534;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .review-meta {
        font-size: 0.78rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.15rem;
    }
    .review-stars-display i {
        font-size: 0.8rem;
    }
    .review-content-text {
        font-size: 0.92rem;
        color: #334155;
        line-height: 1.6;
        padding-left: 48px;
    }
    @media (max-width: 600px) {
        .review-content-text {
            padding-left: 0;
        }
    }
    .no-reviews-state {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #64748b;
    }
    .no-reviews-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 850px) {
        .detail-main-layout {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumb-nav">
    <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span>›</span>
    <a href="{{ route('home') }}">Lò vi sóng</a>
    @if($product->category)
        <span>›</span>
        <a href="{{ route('home', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
    @endif
    <span>›</span>
    <span>{{ $product->name }}</span>
</div>

<div class="detail-card">
    <div class="detail-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
        <div>
            <h1>{{ $product->name }}</h1>
            <div class="detail-header-meta">
                <a href="#danh-gia" class="detail-stars" style="text-decoration: none;" title="Xem các đánh giá">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star" style="{{ $i <= round($product->average_rating) ? 'color: var(--dmx-yellow);' : 'color: #cbd5e1;' }}"></i>
                    @endfor
                    <span>{{ $product->average_rating }} ({{ $product->reviews_count }} đánh giá)</span>
                </a>
                <span>|</span>
                <span><i class="fa-solid fa-eye" style="color: var(--dmx-blue);"></i> {{ number_format($product->views) }} Lượt xem</span>
                <span>|</span>
                @if($product->stock > 5)
                    <span style="color: #16a34a; font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Còn hàng ({{ $product->stock }} sản phẩm có sẵn)</span>
                @elseif($product->stock > 0)
                    <span style="color: #d97706; font-weight: 700;"><i class="fa-solid fa-triangle-exclamation"></i> Sắp hết hàng (Chỉ còn {{ $product->stock }} chiếc)</span>
                @else
                    <span style="color: #dc2626; font-weight: 700;"><i class="fa-solid fa-circle-xmark"></i> Tạm hết hàng</span>
                @endif
            </div>
        </div>
        <div>
            @php $isFav = Auth::check() && $product->isWishlistedBy(); @endphp
            <button type="button" 
                    class="btn-detail-wishlist {{ $isFav ? 'active' : '' }}" 
                    onclick="toggleWishlist({{ $product->id }}, this)">
                <i class="{{ $isFav ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                <span>{{ $isFav ? 'Đã yêu thích' : 'Yêu thích' }}</span>
            </button>
        </div>
    </div>

    <div class="detail-main-layout">
        <!-- LEFT: IMAGE & POLICIES -->
        <div>
            <div class="detail-image-box">
                @if($product->image_url)
                    <img id="mainProductImg" src="{{ asset($product->image_url) }}" alt="{{ $product->name }}">
                @else
                    <div style="color: #94a3b8; font-size: 1rem;"><i class="fa-regular fa-image"></i> Chưa có hình ảnh</div>
                @endif
            </div>

            <!-- Gallery Thumbnails -->
            @if($product->images->count() > 0)
                <div class="detail-thumbnails-row">
                    @if($product->image_url)
                        <div class="thumb-item active" onclick="switchProductImage('{{ asset($product->image_url) }}', this)">
                            <img src="{{ asset($product->image_url) }}" alt="Ảnh chính">
                        </div>
                    @endif
                    @foreach($product->images as $img)
                        <div class="thumb-item" onclick="switchProductImage('{{ asset($img->image_url) }}', this)">
                            <img src="{{ asset($img->image_url) }}" alt="Ảnh phụ">
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Policies -->
            <div class="detail-policies-grid">
                <div class="policy-item">
                    <i class="fa-solid fa-rotate-left"></i>
                    <div><strong>Hư gì đổi nấy 12 tháng</strong> tại hệ thống Siêu Lò Nướng.</div>
                </div>
                <div class="policy-item">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div><strong>Bảo hành chính hãng</strong> 1 năm tận nhà nhanh chóng.</div>
                </div>
                <div class="policy-item">
                    <i class="fa-solid fa-truck-fast"></i>
                    <div><strong>Giao hàng siêu tốc</strong> miễn phí bán kính 10km.</div>
                </div>
                <div class="policy-item">
                    <i class="fa-solid fa-box-open"></i>
                    <div><strong>Hàng nguyên seal 100%</strong> kiểm tra trước khi nhận.</div>
                </div>
            </div>
        </div>

        <!-- RIGHT: PRICING, GIFTS, ACTIONS -->
        <div>
            @php
                $colorVariants = $product->colors;
                $colorsData = [];
                if ($colorVariants->isNotEmpty()) {
                    foreach ($colorVariants as $cv) {
                        $colorsData[] = [
                            'color' => $cv->color,
                            'stock' => (int) $cv->stock,
                            'price' => (float) $cv->effective_price,
                        ];
                    }
                } elseif (!empty($product->color)) {
                    $rawColors = array_filter(array_map('trim', explode(',', $product->color)));
                    foreach ($rawColors as $rc) {
                        $colorsData[] = [
                            'color' => $rc,
                            'stock' => (int) $product->stock,
                            'price' => (float) $product->price,
                        ];
                    }
                }

                $defaultVariant = !empty($colorsData) ? $colorsData[0] : [
                    'color' => '',
                    'stock' => (int) $product->stock,
                    'price' => (float) $product->price,
                ];

                $initialPrice = $defaultVariant['price'];
                $initialStock = $defaultVariant['stock'];
                $defaultColor = $defaultVariant['color'];

                $displayOriginalPrice = $product->original_price ?: round($initialPrice * 1.18, -4);
                $discountPercent = $displayOriginalPrice > $initialPrice ? round((($displayOriginalPrice - $initialPrice) / $displayOriginalPrice) * 100) : 0;
            @endphp

            <!-- Price Box -->
            <div class="price-promo-box">
                <div class="current-price" id="displayProductPrice">{{ number_format($initialPrice, 0, ',', '.') }}₫</div>
                <div class="old-price-box">
                    <span>Giá gốc: <span class="old-price" id="displayProductOriginalPrice">{{ number_format($displayOriginalPrice, 0, ',', '.') }}₫</span></span>
                    @if($discountPercent > 0)
                        <span class="discount-badge" id="displayDiscountBadge">Tiết kiệm {{ $discountPercent }}%</span>
                    @endif
                    <span style="color: #15803d; font-weight: 600; font-size: 0.85rem;"><i class="fa-solid fa-bolt"></i> Trả chậm 0%</span>
                </div>
            </div>

            <!-- Promotional Gifts Box -->
            <div class="promo-gifts-card">
                <h3><i class="fa-solid fa-gift"></i> ƯU ĐÃI ĐẶC QUYỀN KHI MUA HÀNG</h3>
                <ul class="promo-gift-list">
                    <li><i class="fa-solid fa-circle-check"></i> <strong>Tặng ngay:</strong> Bộ 3 thố thủy tinh chịu nhiệt cao cấp Lock&Lock.</li>
                    <li><i class="fa-solid fa-circle-check"></i> <strong>Tặng voucher:</strong> Giảm thêm 100.000đ cho đơn hàng tiếp theo.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Miễn phí công lắp đặt và hướng dẫn sử dụng tại chỗ bởi kỹ thuật viên.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Hỗ trợ kỹ thuật 24/7 qua Hotline: <strong>0913 014 602</strong>.</li>
                </ul>
            </div>

            <!-- BỘ CHỌN MÀU SẮC (COLOR VARIANT) -->
            @if(count($colorsData) > 0)
            <div class="variant-section">
                <div class="variant-label">
                    <i class="fa-solid fa-palette text-primary"></i> Chọn màu sắc: 
                    <span id="selectedColorName" class="text-primary fw-bold">{{ $defaultColor }}</span>
                </div>
                <div class="color-pill-group">
                    @foreach($colorsData as $index => $c)
                        <button type="button" 
                                class="color-pill-btn {{ $index === 0 ? 'active' : '' }} {{ $c['stock'] <= 0 ? 'text-muted' : '' }}" 
                                data-color="{{ $c['color'] }}"
                                data-stock="{{ $c['stock'] }}"
                                data-price="{{ $c['price'] }}"
                                onclick="selectColor('{{ $c['color'] }}', this)">
                            <i class="fa-solid fa-circle-check {{ $index === 0 ? '' : 'd-none' }}" style="font-size: 0.85rem;"></i>
                            <span>{{ $c['color'] }}</span>
                            @if($c['stock'] <= 0)
                                <small class="text-danger" style="font-size: 0.72rem;">(Hết hàng)</small>
                            @elseif($c['price'] != $product->price)
                                <small class="text-danger fw-bold" style="font-size: 0.72rem;">({{ number_format($c['price'], 0, ',', '.') }}₫)</small>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- BỘ CHỌN SỐ LƯỢNG (QUANTITY SELECTOR) -->
            <div class="quantity-wrapper">
                <span class="variant-label mb-0"><i class="fa-solid fa-cubes-stacked text-primary"></i> Số lượng:</span>
                <div class="qty-control-box">
                    <button type="button" class="btn-qty" onclick="changeQty(-1)">-</button>
                    <input type="number" id="purchaseQuantity" value="1" min="1" max="{{ $initialStock > 0 ? $initialStock : 1 }}" class="input-qty" onchange="syncQty(this.value)">
                    <button type="button" class="btn-qty" onclick="changeQty(1)">+</button>
                </div>
                <span id="stockStatusDisplay" style="font-size: 0.85rem;">
                    @if($initialStock > 5)
                        <span class="text-success fw-bold"><i class="fa-solid fa-circle-check"></i> Còn <strong>{{ $initialStock }}</strong> sản phẩm trong kho</span>
                    @elseif($initialStock > 0)
                        <span class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Sắp hết (Chỉ còn <strong>{{ $initialStock }}</strong> chiếc)</span>
                    @else
                        <span class="text-danger fw-bold"><i class="fa-solid fa-circle-xmark"></i> Màu này tạm hết hàng</span>
                    @endif
                </span>
            </div>

            <!-- Action Buttons -->
            @auth
                <div id="buyActionButtonsGroup">
                    <form action="{{ route('buy.now', $product->id) }}" method="POST" style="margin-bottom: 0.75rem;">
                        @csrf
                        <input type="hidden" name="color" id="inputFormColorBuy" value="{{ $defaultColor }}">
                        <input type="hidden" name="quantity" id="inputFormQtyBuy" value="1">
                        <button type="submit" class="btn-buy-now" id="btnBuyNowSubmit" style="{{ $initialStock <= 0 ? 'display: none;' : '' }}">
                            MUA NGAY
                            <span>Xác nhận thông tin & Giao hàng tận nơi</span>
                        </button>
                    </form>

                    <form id="addToCartForm" action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="color" id="inputFormColorCart" value="{{ $defaultColor }}">
                        <input type="hidden" name="quantity" id="inputFormQtyCart" value="1">
                        <button type="submit" class="btn-add-cart" id="btnAddToCartSubmit" style="{{ $initialStock <= 0 ? 'display: none;' : '' }}">
                            <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                        </button>
                    </form>

                    <button type="button" class="btn-buy-now" id="btnOutOfStockNotice" style="background: #94a3b8; cursor: not-allowed; box-shadow: none; {{ $initialStock > 0 ? 'display: none;' : '' }}">
                        MÀU NÀY TẠM HẾT HÀNG
                        <span>Vui lòng chọn màu sắc khác còn hàng</span>
                    </button>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-buy-now">
                    ĐĂNG NHẬP ĐỂ MUA HÀNG
                    <span>Ưu đãi thêm cho thành viên đăng ký mới</span>
                </a>
            @endauth

            <!-- Hotline Support Contact -->
            <div style="margin-top: 1.25rem; font-size: 0.88rem; color: #475569; display: flex; align-items: center; justify-content: space-between; background: #f1f5f9; padding: 0.75rem 1rem; border-radius: 8px;">
                <span><i class="fa-solid fa-headset" style="color: var(--dmx-blue);"></i> Tư vấn kỹ thuật & lắp đặt: <strong>Hộ Kinh Doanh Siêu Lò Nướng</strong></span>
                <a href="tel:0913014602" style="color: var(--dmx-red); font-weight: 800; text-decoration: none; font-size: 1rem;">📞 0913 014 602</a>
            </div>
        </div>
    </div>

    <!-- Product Description & Specs -->
    <div style="margin-top: 3rem; border-top: 1px solid var(--card-border); padding-top: 2rem;">
        <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--dmx-blue); margin-bottom: 1rem;">
            <i class="fa-solid fa-circle-info"></i> Thông Tin Chi Tiết & Thông Số Kỹ Thuật
        </h2>
        <div style="line-height: 1.7; color: #334155; margin-bottom: 1.5rem; font-size: 0.95rem;">
            {{ $product->description }}
        </div>

        <table class="specs-table">
            <tbody>
                <tr>
                    <td>Tên sản phẩm</td>
                    <td><strong>{{ $product->name }}</strong></td>
                </tr>
                <tr>
                    <td>Thương hiệu (Brand)</td>
                    <td><strong>{{ $product->brand ?? 'Chính hãng' }}</strong></td>
                </tr>
                <tr>
                    <td>Phân loại danh mục</td>
                    <td>{{ $product->category->name ?? 'Lò vi sóng' }}</td>
                </tr>
                <tr>
                    <td>Dung tích</td>
                    <td><strong>{{ $product->capacity ?? '20 - 25 Lít' }}</strong></td>
                </tr>
                <tr>
                    <td>Công suất hoạt động</td>
                    <td><strong>{{ $product->power ?? '800W - 1000W' }}</strong></td>
                </tr>
                <tr>
                    <td>Màu sắc sản phẩm</td>
                    <td>{{ $product->color ?? 'Đen / Bạc' }}</td>
                </tr>
                <tr>
                    <td>Tình trạng kho hàng</td>
                    <td>
                        @if($product->stock > 5)
                            <span style="color: #16a34a; font-weight: 700;">
                                <i class="fa-solid fa-circle-check"></i> Còn hàng ({{ $product->stock }} sản phẩm)
                            </span>
                        @elseif($product->stock > 0)
                            <span style="color: #d97706; font-weight: 700;">
                                <i class="fa-solid fa-triangle-exclamation"></i> Sắp hết hàng (Còn {{ $product->stock }} cái)
                            </span>
                        @else
                            <span style="color: #dc2626; font-weight: 700;">
                                <i class="fa-solid fa-circle-xmark"></i> Tạm hết hàng
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Xuất xứ & Bảo hành</td>
                    <td>Hàng chính hãng 100% - Bảo hành 12 tháng tại nhà - 1 Đổi 1 trong 365 ngày</td>
                </tr>
                <tr>
                    <td>Địa chỉ phân phối</td>
                    <td>Showroom Siêu Lò Nướng: Số 96 đường Nguyễn Du, P. Cửa Nam, Hà Nội</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- KHU VỰC ĐÁNH GIÁ & BÌNH LUẬN SẢN PHẨM -->
<div class="reviews-section-card" id="danh-gia">
    <div class="reviews-header">
        <div class="reviews-title">
            <i class="fa-solid fa-comments" style="color: var(--dmx-blue);"></i>
            <span>Đánh Giá Từ Khách Hàng ({{ $product->reviews_count }})</span>
        </div>
    </div>

    <!-- Rating Summary Box -->
    <div class="rating-summary-container">
        <div class="rating-big-score">
            <div class="big-score-num">{{ $product->average_rating }}</div>
            <div class="big-score-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star" style="{{ $i <= round($product->average_rating) ? 'color: var(--dmx-yellow);' : 'color: #cbd5e1;' }}"></i>
                @endfor
            </div>
            <div class="big-score-total">{{ $product->reviews_count }} đánh giá thực tế</div>
        </div>

        <div class="rating-bars">
            @for($star = 5; $star >= 1; $star--)
                @php
                    $starCount = $product->reviews->where('rating', $star)->count();
                    $totalReal = $product->reviews->count();
                    $percent = $totalReal > 0 ? round(($starCount / $totalReal) * 100) : ($star >= 4 ? ($star == 5 ? 78 : 18) : 4);
                @endphp
                <div class="rating-bar-row">
                    <span class="bar-star-label">{{ $star }} <i class="fa-solid fa-star" style="color: var(--dmx-yellow); font-size: 0.75rem;"></i></span>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ $percent }}%;"></div>
                    </div>
                    <span class="bar-percent">{{ $percent }}%</span>
                </div>
            @endfor
        </div>

        <div class="rating-cta-box">
            <p style="font-size: 0.9rem; color: #475569; margin-bottom: 0.75rem; line-height: 1.4;">Bạn đã từng trải nghiệm chiếc lò vi sóng này?</p>
            @auth
                <a href="#reviewFormBox" class="btn-write-review">
                    <i class="fa-solid fa-pen-to-square"></i> Viết đánh giá
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-write-review">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập để đánh giá
                </a>
            @endauth
        </div>
    </div>

    <!-- Review Form -->
    @auth
        <div class="review-form-box" id="reviewFormBox">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">
                <i class="fa-solid fa-star-half-stroke" style="color: var(--dmx-yellow);"></i> Gửi nhận xét của bạn
            </h3>
            <form action="{{ route('product.review.store', $product->id) }}" method="POST" id="productReviewForm">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.88rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem;">
                        Bạn đánh giá sản phẩm bao nhiêu sao?:
                    </label>
                    <div class="star-rating-picker" id="starPicker">
                        <input type="hidden" name="rating" id="ratingInput" value="5" required>
                        <i class="fa-solid fa-star star-pick active" data-val="1"></i>
                        <i class="fa-solid fa-star star-pick active" data-val="2"></i>
                        <i class="fa-solid fa-star star-pick active" data-val="3"></i>
                        <i class="fa-solid fa-star star-pick active" data-val="4"></i>
                        <i class="fa-solid fa-star star-pick active" data-val="5"></i>
                        <span id="starRatingDesc" style="font-size: 0.85rem; font-weight: 700; color: #15803d; margin-left: 0.5rem;">Cực kỳ hài lòng (5 sao)</span>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="reviewComment" style="display: block; font-size: 0.88rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem;">
                        Nhận xét chi tiết (Độ nóng, tốc độ rã đông, độ bền, độ ồn...):
                    </label>
                    <textarea name="comment" id="reviewComment" rows="3" class="review-textarea" placeholder="Chia sẻ cảm nhận chi tiết của bạn về chiếc lò vi sóng này để giúp mọi người lựa chọn tốt hơn..." required minlength="5"></textarea>
                </div>

                <button type="submit" class="btn-submit-review">
                    <i class="fa-solid fa-paper-plane"></i> Gửi đánh giá ngay
                </button>
            </form>
        </div>
    @endauth

    <!-- List of Existing Reviews -->
    <div class="reviews-list-container" id="reviewsList">
        @forelse($product->reviews as $rev)
            <div class="review-item-card">
                <div class="review-user-row">
                    <div class="review-avatar">
                        {{ strtoupper(substr($rev->user->name ?? 'K', 0, 1)) }}
                    </div>
                    <div>
                        <div class="review-user-name">
                            {{ $rev->user->name ?? 'Khách hàng' }}
                            <span class="badge-verified-buyer"><i class="fa-solid fa-check"></i> Đã mua tại Siêu Lò Nướng</span>
                        </div>
                        <div class="review-meta">
                            <span class="review-stars-display">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star" style="{{ $i <= $rev->rating ? 'color: var(--dmx-yellow);' : 'color: #cbd5e1;' }}"></i>
                                @endfor
                            </span>
                            <span>•</span>
                            <span>{{ $rev->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                <div class="review-content-text">
                    {{ $rev->comment }}
                </div>
            </div>
        @empty
            <div class="no-reviews-state">
                <i class="fa-regular fa-comment-dots"></i>
                <p style="font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Chưa có đánh giá gửi từ website</p>
                <p style="font-size: 0.85rem; color: #94a3b8;">Hãy là người đầu tiên trải nghiệm và chia sẻ nhận xét của bạn về sản phẩm này nhé!</p>
            </div>
        @endforelse
    </div>
</div>

<!-- SẢN PHẨM TƯƠNG TỰ CÙNG PHÂN KHÚC -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<div class="related-products-section">
    <div class="related-products-title">
        <i class="fa-solid fa-layer-group" style="color: var(--dmx-blue);"></i>
        <span>Sản Phẩm Tương Tự Cùng Phân Khúc</span>
    </div>
    <div class="related-grid">
        @foreach($relatedProducts as $rp)
            @php
                $rpOrigPrice = $rp->original_price ?: round($rp->price * 1.18, -4);
                $rpDiscount = $rpOrigPrice > $rp->price ? round((($rpOrigPrice - $rp->price) / $rpOrigPrice) * 100) : 0;
            @endphp
            <a href="{{ route('product.show', $rp->id) }}" class="related-card">
                <div class="related-img-box">
                    @if($rp->image_url)
                        <img src="{{ asset($rp->image_url) }}" alt="{{ $rp->name }}" loading="lazy">
                    @else
                        <div style="color: #94a3b8; font-size: 0.8rem;"><i class="fa-regular fa-image"></i> Chưa có ảnh</div>
                    @endif
                </div>
                @if($rp->brand)
                    <div class="related-brand">{{ $rp->brand }}</div>
                @endif
                <div class="related-name" title="{{ $rp->name }}">{{ $rp->name }}</div>
                <div class="related-price">
                    {{ number_format($rp->price, 0, ',', '.') }}₫
                    @if($rpDiscount > 0)
                        <span style="font-size: 0.75rem; color: #94a3b8; text-decoration: line-through; font-weight: 500; margin-left: 0.35rem;">
                            {{ number_format($rpOrigPrice, 0, ',', '.') }}₫
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- SẢN PHẨM BẠN VỪA XEM GẦN ĐÂY -->
@if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
<div class="related-products-section" style="margin-top: 1.5rem;">
    <div class="related-products-title">
        <i class="fa-solid fa-clock-rotate-left" style="color: var(--dmx-orange);"></i>
        <span>Sản Phẩm Bạn Đã Xem Gần Đây</span>
    </div>
    <div class="related-grid">
        @foreach($recentlyViewed as $rv)
            <a href="{{ route('product.show', $rv->id) }}" class="related-card">
                <div class="related-img-box">
                    @if($rv->image_url)
                        <img src="{{ asset($rv->image_url) }}" alt="{{ $rv->name }}" loading="lazy">
                    @else
                        <div style="color: #94a3b8; font-size: 0.8rem;"><i class="fa-regular fa-image"></i> Chưa có ảnh</div>
                    @endif
                </div>
                @if($rv->brand)
                    <div class="related-brand">{{ $rv->brand }}</div>
                @endif
                <div class="related-name" title="{{ $rv->name }}">{{ $rv->name }}</div>
                <div class="related-price">
                    {{ number_format($rv->price, 0, ',', '.') }}₫
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    const colorVariantsMap = @json($colorsData);

    function formatVND(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + '₫';
    }

    function selectColor(colorName, el) {
        document.querySelectorAll('.color-pill-btn').forEach(btn => {
            btn.classList.remove('active');
            const icon = btn.querySelector('i');
            if (icon) icon.classList.add('d-none');
        });
        el.classList.add('active');
        const activeIcon = el.querySelector('i');
        if (activeIcon) activeIcon.classList.remove('d-none');

        document.getElementById('selectedColorName').innerText = colorName;
        const buyColor = document.getElementById('inputFormColorBuy');
        const cartColor = document.getElementById('inputFormColorCart');
        if (buyColor) buyColor.value = colorName;
        if (cartColor) cartColor.value = colorName;

        // Tìm thông tin biến thể màu sắc được chọn
        const variant = colorVariantsMap.find(c => c.color === colorName) || {
            stock: parseInt(el.getAttribute('data-stock')) || 0,
            price: parseFloat(el.getAttribute('data-price')) || {{ (float)$product->price }}
        };

        const currentPriceEl = document.getElementById('displayProductPrice');
        if (currentPriceEl) {
            currentPriceEl.innerText = formatVND(variant.price);
        }

        // Cập nhật giá gốc và phần trăm giảm giá nếu có
        const origPriceEl = document.getElementById('displayProductOriginalPrice');
        const badgeEl = document.getElementById('displayDiscountBadge');
        const baseOriginalPrice = {{ (float)($product->original_price ?: round($product->price * 1.18, -4)) }};
        if (origPriceEl) {
            origPriceEl.innerText = formatVND(baseOriginalPrice);
        }
        if (badgeEl) {
            if (baseOriginalPrice > variant.price) {
                const pct = Math.round(((baseOriginalPrice - variant.price) / baseOriginalPrice) * 100);
                badgeEl.innerText = `Tiết kiệm ${pct}%`;
                badgeEl.style.display = '';
            } else {
                badgeEl.style.display = 'none';
            }
        }

        // Cập nhật hiển thị số lượng tồn kho
        const stockDisplay = document.getElementById('stockStatusDisplay');
        const qtyInput = document.getElementById('purchaseQuantity');
        const btnBuy = document.getElementById('btnBuyNowSubmit');
        const btnCart = document.getElementById('btnAddToCartSubmit');
        const btnOOS = document.getElementById('btnOutOfStockNotice');

        if (variant.stock > 0) {
            if (stockDisplay) {
                if (variant.stock > 5) {
                    stockDisplay.innerHTML = `<span class="text-success fw-bold"><i class="fa-solid fa-circle-check"></i> Còn <strong>${variant.stock}</strong> sản phẩm trong kho</span>`;
                } else {
                    stockDisplay.innerHTML = `<span class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Sắp hết (Chỉ còn <strong>${variant.stock}</strong> chiếc)</span>`;
                }
            }
            if (qtyInput) {
                qtyInput.max = variant.stock;
                if (parseInt(qtyInput.value) > variant.stock) {
                    qtyInput.value = variant.stock;
                    syncQty(variant.stock);
                }
            }
            if (btnBuy) btnBuy.style.display = '';
            if (btnCart) btnCart.style.display = '';
            if (btnOOS) btnOOS.style.display = 'none';
        } else {
            if (stockDisplay) {
                stockDisplay.innerHTML = `<span class="text-danger fw-bold"><i class="fa-solid fa-circle-xmark"></i> Màu này tạm hết hàng</span>`;
            }
            if (qtyInput) {
                qtyInput.max = 1;
            }
            if (btnBuy) btnBuy.style.display = 'none';
            if (btnCart) btnCart.style.display = 'none';
            if (btnOOS) btnOOS.style.display = '';
        }
    }

    function changeQty(delta) {
        const input = document.getElementById('purchaseQuantity');
        if (!input) return;
        const max = parseInt(input.getAttribute('max')) || 999;
        const min = parseInt(input.getAttribute('min')) || 1;
        let current = parseInt(input.value) || 1;
        let next = current + delta;
        if (next < min) next = min;
        if (next > max) next = max;
        input.value = next;
        syncQty(next);
    }

    function syncQty(val) {
        let qty = parseInt(val) || 1;
        const input = document.getElementById('purchaseQuantity');
        if (input) {
            const max = parseInt(input.getAttribute('max')) || 999;
            const min = parseInt(input.getAttribute('min')) || 1;
            if (qty < min) qty = min;
            if (qty > max) qty = max;
            input.value = qty;
        }
        const buyQty = document.getElementById('inputFormQtyBuy');
        const cartQty = document.getElementById('inputFormQtyCart');
        if (buyQty) buyQty.value = qty;
        if (cartQty) cartQty.value = qty;
    }

    // AJAX Thêm Vào Giỏ Hàng
    document.addEventListener('DOMContentLoaded', function() {
        const cartForm = document.getElementById('addToCartForm');
        if (cartForm) {
            cartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = cartForm.querySelector('button[type="submit"]');
                const origHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang thêm...';

                const formData = new FormData(cartForm);
                fetch(cartForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json().then(data => ({ ok: res.ok, data: data })))
                .then(result => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origHtml;
                    if (result.ok && result.data.success) {
                        if (window.showToast) {
                            window.showToast(result.data.message, 'success', 'Giỏ hàng');
                        }
                        // Cập nhật số lượng giỏ hàng trên header
                        const cartBadge = document.getElementById('headerCartBadge');
                        if (cartBadge) {
                            cartBadge.innerText = result.data.cart_count;
                            cartBadge.style.display = result.data.cart_count > 0 ? '' : 'none';
                        }
                    } else {
                        if (window.showToast) {
                            window.showToast(result.data.message || 'Thêm giỏ hàng thất bại!', 'error', 'Thông báo');
                        }
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origHtml;
                    cartForm.submit();
                });
            });
        }

        // Star Rating Picker Interactive Logic
        const starPicker = document.getElementById('starPicker');
        if (starPicker) {
            const stars = starPicker.querySelectorAll('.star-pick');
            const ratingInput = document.getElementById('ratingInput');
            const desc = document.getElementById('starRatingDesc');
            const descMap = {
                1: 'Rất tệ (1 sao)',
                2: 'Chưa hài lòng (2 sao)',
                3: 'Bình thường (3 sao)',
                4: 'Hài lòng (4 sao)',
                5: 'Cực kỳ hài lòng (5 sao)'
            };

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const val = parseInt(this.getAttribute('data-val'));
                    ratingInput.value = val;
                    desc.innerText = descMap[val] || `${val} sao`;
                    stars.forEach(s => {
                        const sVal = parseInt(s.getAttribute('data-val'));
                        if (sVal <= val) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
            });
        }

        // AJAX Review Form Submit
        const reviewForm = document.getElementById('productReviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = reviewForm.querySelector('button[type="submit"]');
                const origHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';

                const formData = new FormData(reviewForm);
                fetch(reviewForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json().then(data => ({ ok: res.ok, data: data })))
                .then(result => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origHtml;
                    if (result.ok && result.data.success) {
                        if (window.showToast) {
                            showToast(result.data.message, 'success', 'Đánh giá');
                        }
                        reviewForm.reset();
                        // Prepend new review card to reviews list
                        const list = document.getElementById('reviewsList');
                        const noRev = list.querySelector('.no-reviews-state');
                        if (noRev) noRev.remove();

                        const r = result.data.review;
                        let starHtml = '';
                        for (let i = 1; i <= 5; i++) {
                            starHtml += `<i class="fa-solid fa-star" style="${i <= r.rating ? 'color: var(--dmx-yellow);' : 'color: #cbd5e1;'}"></i>`;
                        }

                        const card = document.createElement('div');
                        card.className = 'review-item-card';
                        card.innerHTML = `
                            <div class="review-user-row">
                                <div class="review-avatar">${r.user_name.charAt(0).toUpperCase()}</div>
                                <div>
                                    <div class="review-user-name">
                                        ${r.user_name}
                                        <span class="badge-verified-buyer"><i class="fa-solid fa-check"></i> Đã mua tại Siêu Lò Nướng</span>
                                    </div>
                                    <div class="review-meta">
                                        <span class="review-stars-display">${starHtml}</span>
                                        <span>•</span>
                                        <span>${r.created_at}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="review-content-text">${r.comment}</div>
                        `;
                        list.prepend(card);
                    } else {
                        if (window.showToast) {
                            showToast(result.data.message || 'Gửi đánh giá thất bại!', 'error', 'Lỗi');
                        }
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origHtml;
                    reviewForm.submit();
                });
            });
        }
    });

    // Chuyển đổi ảnh trong Album Gallery
    function switchProductImage(src, el) {
        const mainImg = document.getElementById('mainProductImg');
        if (!mainImg) return;
        mainImg.style.transition = 'opacity 0.2s ease';
        mainImg.style.opacity = '0.3';
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        }, 150);
        document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
        if (el) el.classList.add('active');
    }
</script>
@endsection

