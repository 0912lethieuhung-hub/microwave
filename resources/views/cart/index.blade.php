@extends('layouts.frontend')

@section('title', 'Giỏ Hàng Của Bạn - Siêu Lò Nướng')

@section('extra-css')
<style>
    .cart-container-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    .cart-table th {
        background: #f8fafc;
        padding: 0.85rem 1rem;
        font-size: 0.88rem;
        color: #475569;
        font-weight: 700;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
    }
    .cart-table td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .cart-product-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .cart-product-thumb {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #fff;
    }
    .cart-product-thumb img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    /* CART OPTIONS SECTION */
    .cart-options-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 1.5rem;
        margin-top: 1.75rem;
    }
    .cart-opt-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem;
    }
    .cart-opt-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* VOUCHER CHIPS */
    .voucher-chips-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.6rem;
    }
    .voucher-chip {
        background: #eff6ff;
        border: 1px dashed var(--dmx-blue);
        color: var(--dmx-blue);
        padding: 0.3rem 0.65rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .voucher-chip:hover {
        background: var(--dmx-blue);
        color: #ffffff;
    }

    /* SUMMARY BOX */
    .cart-summary-card {
        background: #f8fafc;
        border-radius: 10px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.92rem;
        color: #64748b;
        margin-bottom: 0.75rem;
    }
    .summary-line.total-line {
        border-top: 2px dashed #cbd5e1;
        padding-top: 1rem;
        margin-top: 1rem;
        font-size: 1.1rem;
        color: #0f172a;
        font-weight: 800;
    }
    .cart-total-price {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--dmx-red);
    }
    .btn-checkout {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #ffffff;
        width: 100%;
        padding: 0.9rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 1.05rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        transition: transform 0.2s;
        border: none;
        cursor: pointer;
        margin-top: 1rem;
    }
    .btn-checkout:hover {
        transform: translateY(-2px);
    }

    /* CROSS SELL SUGGESTIONS */
    .suggested-section {
        margin-top: 3rem;
        border-top: 1px solid var(--card-border);
        padding-top: 2rem;
    }
    .suggested-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }
    .suggested-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s;
    }
    .suggested-item:hover {
        border-color: var(--dmx-blue);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .suggested-item img {
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin: 0 auto 0.75rem;
    }
    .suggested-name {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.5rem;
        margin-bottom: 0.5rem;
    }
    .suggested-price {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--dmx-red);
        margin-bottom: 0.75rem;
    }
    .btn-add-suggested {
        width: 100%;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: var(--dmx-blue);
        padding: 0.4rem;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-add-suggested:hover {
        background: var(--dmx-blue);
        color: #ffffff;
        border-color: var(--dmx-blue);
    }

    @media (max-width: 900px) {
        .cart-options-grid {
            grid-template-columns: 1fr;
        }
        .suggested-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection

@section('content')
<div class="cart-container-card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--card-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--dmx-blue); margin: 0;">
            <i class="fa-solid fa-cart-shopping"></i> Giỏ Hàng Của Bạn
        </h1>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('home') }}" style="color: var(--dmx-blue); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm
            </a>
            @if($cart && $cart->cartItems->isNotEmpty())
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn làm trống toàn bộ giỏ hàng không?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                        <i class="fa-solid fa-trash-can"></i> Xóa tất cả
                    </button>
                </form>
            @endif
        </div>
    </div>
    @if(!$cart || $cart->cartItems->isEmpty())
        <div style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;">🛒</div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem;">Giỏ hàng của bạn đang trống</h3>
            <p style="color: #94a3b8; margin-bottom: 1.5rem; font-size: 0.9rem;">Hãy chọn những mẫu lò vi sóng chính hãng ưng ý nhất tại Siêu Lò Nướng nhé!</p>
            <a href="{{ route('home') }}" class="btn-checkout" style="background: var(--dmx-blue); max-width: 250px; margin: 0 auto;">
                Khám phá sản phẩm ngay
            </a>
        </div>
    @else
        <!-- CART ITEMS TABLE -->
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center;">Đơn giá</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Thành tiền</th>
                    <th style="text-align: center;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($cart->cartItems as $item)
                    @php 
                        $itemPrice = $item->price;
                        $itemTotal = $itemPrice * $item->quantity; 
                        $subtotal += $itemTotal; 
                        $itemMaxStock = $item->product ? $item->product->getColorStock($item->color) : 99;
                    @endphp
                    <tr>
                        <td>
                            <div class="cart-product-item">
                                <div class="cart-product-thumb">
                                    @if($item->product->image_url)
                                        <img src="{{ asset($item->product->image_url) }}" alt="{{ $item->product->name }}">
                                    @else
                                        <i class="fa-regular fa-image" style="color: #cbd5e1;"></i>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('product.show', $item->product->id) }}" style="font-weight: 700; color: var(--text-main); text-decoration: none; font-size: 0.95rem;">
                                        {{ $item->product->name }}
                                    </a>
                                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
                                        Phân loại: {{ $item->product->category->name ?? 'Lò vi sóng' }}
                                        @if($item->color)
                                            | Màu: <strong style="color: var(--dmx-blue);">{{ $item->color }}</strong>
                                            @if($item->product && $itemPrice != $item->product->price)
                                                <span class="badge bg-primary-subtle text-primary" style="font-size: 0.7rem;">Giá theo màu</span>
                                            @endif
                                        @endif
                                    </div>
                                    @if($itemMaxStock < 5 && $itemMaxStock > 0)
                                        <div style="font-size: 0.72rem; color: #d97706; font-weight: 600; margin-top: 0.15rem;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Kho chỉ còn {{ $itemMaxStock }} chiếc màu này
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 600; color: #334155;">
                            {{ number_format($itemPrice, 0, ',', '.') }}₫
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $itemMaxStock > 0 ? $itemMaxStock : 1 }}" style="width: 55px; text-align: center; padding: 0.3rem 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 700; font-size: 0.9rem;">
                                <button type="submit" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 0.3rem 0.55rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'" title="Cập nhật số lượng">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                            </form>
                        </td>
                        <td style="text-align: right; font-weight: 800; color: var(--dmx-red); font-size: 1rem;">
                            {{ number_format($itemTotal, 0, ',', '.') }}₫
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 1.1rem;" title="Xóa khỏi giỏ hàng">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $coupon = session('coupon');
            $discount = $coupon['discount'] ?? 0;
            $finalTotal = max(0, $subtotal - $discount);
        @endphp

        <!-- CART OPTIONS & SUMMARY LAYOUT -->
        <div class="cart-options-grid">
            <!-- LEFT: VOUCHER & ADD-ON SERVICES -->
            <div>
                <!-- VOUCHER CODE BOX -->
                <div class="cart-opt-card" style="margin-bottom: 1rem;">
                    <div class="cart-opt-title">
                        <i class="fa-solid fa-ticket" style="color: #ea580c;"></i> Mã Giảm Giá / Voucher Ưu Đãi
                    </div>

                    @if($coupon)
                        <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.9rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $coupon['name'] }}</strong>
                                <div style="font-size: 0.8rem; color: #15803d;">Mã: <code>{{ $coupon['code'] }}</code> (Tiết kiệm {{ number_format($coupon['discount'], 0, ',', '.') }}₫)</div>
                            </div>
                            <form action="{{ route('cart.coupon.remove') }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #dc2626; font-weight: 700; cursor: pointer; font-size: 0.85rem;">
                                    <i class="fa-solid fa-xmark"></i> Hủy mã
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.coupon.apply') }}" method="POST" style="display: flex; gap: 0.5rem;">
                            @csrf
                            <input type="text" name="coupon_code" id="couponInput" placeholder="Nhập mã giảm giá của bạn..." style="flex: 1; padding: 0.6rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; text-transform: uppercase; font-weight: 700;" required>
                            <button type="submit" style="background: var(--dmx-blue); color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 6px; font-weight: 700; font-size: 0.9rem; cursor: pointer;">
                                Áp dụng
                            </button>
                        </form>
                    @endif
                </div>

                <!-- ADD-ON SERVICES / COMMITMENTS -->
                <div class="cart-opt-card">
                    <div class="cart-opt-title">
                        <i class="fa-solid fa-shield-halved" style="color: #0284c7;"></i> Dịch Vụ & Cam Kết Kèm Theo
                    </div>
                    <div style="font-size: 0.85rem; color: #334155; display: grid; gap: 0.6rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" checked disabled style="accent-color: var(--dmx-blue);">
                            <span><i class="fa-solid fa-truck-fast" style="color: #16a34a;"></i> <strong>Giao siêu tốc 2 giờ</strong> miễn phí trong bán kính 10km.</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" checked disabled style="accent-color: var(--dmx-blue);">
                            <span><i class="fa-solid fa-wrench" style="color: #2563eb;"></i> <strong>Miễn phí công lắp đặt</strong> & hướng dẫn sử dụng tận nhà.</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" checked disabled style="accent-color: var(--dmx-blue);">
                            <span><i class="fa-solid fa-box-check" style="color: #ca8a04;"></i> <strong>Hàng mới nguyên seal 100%</strong> - Kiểm tra trước khi trả tiền.</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- RIGHT: ORDER SUMMARY -->
            <div>
                <div class="cart-summary-card">
                    <div class="cart-opt-title" style="margin-bottom: 1.25rem;">
                        <i class="fa-solid fa-receipt" style="color: var(--dmx-blue);"></i> Tóm Tắt Đơn Hàng
                    </div>

                    <div class="summary-line">
                        <span>Tạm tính ({{ $cart->cartItems->sum('quantity') }} sản phẩm):</span>
                        <strong style="color: #1e293b;">{{ number_format($subtotal, 0, ',', '.') }}₫</strong>
                    </div>

                    @if($discount > 0)
                        <div class="summary-line" style="color: #16a34a;">
                            <span>Giảm giá Voucher:</span>
                            <strong>-{{ number_format($discount, 0, ',', '.') }}₫</strong>
                        </div>
                    @endif

                    <div class="summary-line">
                        <span>Phí vận chuyển:</span>
                        <strong style="color: #16a34a;">Miễn phí (0₫)</strong>
                    </div>

                    <div class="summary-line total-line">
                        <span>Tổng tiền thanh toán:</span>
                        <div class="cart-total-price">{{ number_format($finalTotal, 0, ',', '.') }}₫</div>
                    </div>
                    <div style="font-size: 0.78rem; color: #94a3b8; text-align: right; margin-top: 0.2rem;">(Đã bao gồm thuế GTGT / VAT)</div>

                    <a href="{{ route('checkout.index') }}" class="btn-checkout">
                        <i class="fa-solid fa-lock"></i> Tiến Hành Đặt Hàng <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- SUGGESTED PRODUCTS (CROSS-SELL) -->
    @if(isset($suggestedProducts) && $suggestedProducts->isNotEmpty())
        <div class="suggested-section">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                🔥 Gợi Ý Mua Kèm Ưu Đãi Dành Cho Bạn
            </h3>
            <p style="color: #64748b; font-size: 0.88rem; margin-bottom: 1rem;">Các mẫu lò vi sóng bán chạy nhất có thể bạn cũng quan tâm</p>

            <div class="suggested-grid">
                @foreach($suggestedProducts as $sp)
                    <div class="suggested-item">
                        <a href="{{ route('product.show', $sp->id) }}">
                            @if($sp->image_url)
                                <img src="{{ asset($sp->image_url) }}" alt="{{ $sp->name }}">
                            @else
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center; color: #cbd5e1;"><i class="fa-regular fa-image fa-2x"></i></div>
                            @endif
                        </a>
                        <a href="{{ route('product.show', $sp->id) }}" class="suggested-name" title="{{ $sp->name }}">
                            {{ $sp->name }}
                        </a>
                        <div class="suggested-price">{{ number_format($sp->price, 0, ',', '.') }}₫</div>
                        <form action="{{ route('cart.add', $sp->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-add-suggested">
                                <i class="fa-solid fa-cart-plus"></i> + Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
