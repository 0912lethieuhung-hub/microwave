@extends('layouts.frontend')

@section('title', 'Danh Sách Yêu Thích - Siêu Lò Nướng')

@section('extra-css')
<style>
    .wishlist-header {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }
    .wishlist-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }
    @media (max-width: 1200px) {
        .wishlist-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 900px) {
        .wishlist-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .wishlist-grid { grid-template-columns: 1fr; }
    }
    .wishlist-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        position: relative;
        transition: all 0.25s ease;
        cursor: pointer;
    }
    .wishlist-card:hover {
        border-color: var(--dmx-blue);
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
    }
    .wishlist-card:hover .wishlist-product-name {
        color: var(--dmx-blue);
    }
    .btn-remove-wishlist {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 5;
    }
    .btn-remove-wishlist:hover {
        background: #dc2626;
        color: #ffffff;
        transform: scale(1.1);
    }
    .wishlist-img-box {
        width: 100%;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .wishlist-img-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .wishlist-product-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-decoration: none;
    }
    .wishlist-product-name::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
    }
    .wishlist-product-name:hover {
        color: var(--dmx-blue);
    }
    .wishlist-price {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--dmx-red);
        margin-bottom: 0.75rem;
    }
    .wishlist-actions {
        margin-top: auto;
        display: flex;
        gap: 0.5rem;
    }
    .btn-wishlist-view {
        flex: 1;
        background: var(--dmx-blue);
        color: #ffffff;
        text-align: center;
        padding: 0.55rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
        transition: background 0.2s;
        position: relative;
        z-index: 2;
    }
    .btn-wishlist-view:hover {
        background: var(--dmx-blue-dark);
    }
    .empty-wishlist {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        padding: 4rem 2rem;
        text-align: center;
    }
    .empty-wishlist i {
        font-size: 4rem;
        color: #fca5a5;
        margin-bottom: 1.25rem;
    }
</style>
@endsection

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumb-nav" style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; color: #64748b;">
    <a href="{{ route('home') }}" style="color: var(--dmx-blue); text-decoration: none;"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span>›</span>
    <span>Danh sách yêu thích</span>
</div>

<div class="wishlist-header">
    <div class="wishlist-title">
        <i class="fa-solid fa-heart" style="color: var(--dmx-red);"></i>
        <span>Sản Phẩm Yêu Thích Của Bạn</span>
        <span style="font-size: 0.9rem; font-weight: 600; color: #64748b; margin-left: 0.5rem;">({{ $wishlists->total() }} sản phẩm)</span>
    </div>
    <a href="{{ route('home') }}" style="color: var(--dmx-blue); font-weight: 600; font-size: 0.88rem; text-decoration: none;">
        <i class="fa-solid fa-plus"></i> Tiếp tục chọn thêm
    </a>
</div>

@if($wishlists->count() > 0)
    <div class="wishlist-grid">
        @foreach($wishlists as $item)
            @php $product = $item->product; @endphp
            @if($product)
                <div class="wishlist-card" id="wishlist-item-{{ $item->id }}">
                    <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="form-toggle-wishlist">
                        @csrf
                        <button type="submit" class="btn-remove-wishlist" title="Xóa khỏi yêu thích">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </form>

                    <div class="wishlist-img-box">
                        @if($product->image_url)
                            <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div style="color: #94a3b8; font-size: 0.85rem;"><i class="fa-regular fa-image"></i> Đang cập nhật ảnh</div>
                        @endif
                    </div>

                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--dmx-blue); text-transform: uppercase; margin-bottom: 0.25rem;">
                        {{ $product->brand ?? 'Chính hãng' }}
                    </div>

                    <a href="{{ route('product.show', $product->id) }}" class="wishlist-product-name" title="{{ $product->name }}">
                        {{ $product->name }}
                    </a>

                    <div class="wishlist-price">
                        {{ number_format($product->price, 0, ',', '.') }}₫
                    </div>

                    <div class="wishlist-actions">
                        <a href="{{ route('product.show', $product->id) }}" class="btn-wishlist-view">
                            Xem & Mua ngay
                        </a>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($wishlists->hasPages())
        <div class="pagination-wrapper" style="margin-top: 2.5rem; display: flex; justify-content: center;">
            {{ $wishlists->links('pagination::bootstrap-5') }}
        </div>
    @endif
@else
    <div class="empty-wishlist">
        <i class="fa-regular fa-heart"></i>
        <h3 style="font-size: 1.3rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Danh sách yêu thích trống</h3>
        <p style="color: #64748b; margin-bottom: 1.5rem; max-width: 480px; margin-left: auto; margin-right: auto;">
            Bạn chưa lưu sản phẩm lò vi sóng nào vào danh sách yêu thích. Hãy bấm vào biểu tượng trái tim trên các sản phẩm bạn thích để lưu lại xem sau nhé!
        </p>
        <a href="{{ route('home') }}" class="btn-wishlist-view" style="display: inline-block; padding: 0.75rem 2rem; border-radius: 50px;">
            Khám phá lò vi sóng ngay
        </a>
    </div>
@endif

@endsection
