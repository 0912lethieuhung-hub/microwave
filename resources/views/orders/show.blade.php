@extends('layouts.frontend')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->id . ' - Siêu Lò Nướng')

@section('extra-css')
<style>
    .order-detail-container {
        max-width: 950px;
        margin: 0 auto;
    }

    /* BREADCRUMB */
    .order-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.88rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }
    .order-breadcrumb a {
        color: var(--dmx-blue);
        text-decoration: none;
        font-weight: 500;
    }
    .order-breadcrumb a:hover {
        text-decoration: underline;
    }

    /* CARD STYLING */
    .od-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .od-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        background: #fafafa;
    }
    .od-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--dmx-blue);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .od-date {
        font-size: 0.85rem;
        color: #64748b;
    }

    /* STATUS BADGES */
    .od-badge {
        font-size: 0.85rem;
        font-weight: 700;
        padding: 0.4rem 0.9rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .od-badge.pending {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .od-badge.processing {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .od-badge.completed {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .od-badge.cancelled {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    /* TIMELINE TRACKER */
    .od-tracker {
        padding: 1.75rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }
    .tracker-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 0 1.5rem;
    }
    .tracker-steps::before {
        content: '';
        position: absolute;
        top: 18px;
        left: 0;
        right: 0;
        height: 4px;
        background: #e2e8f0;
        z-index: 1;
    }
    .tracker-step {
        position: relative;
        z-index: 2;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100px;
    }
    .tracker-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        border: 3px solid #ffffff;
        box-shadow: 0 0 0 2px #e2e8f0;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
    }
    .tracker-step.done .tracker-icon {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 0 0 2px #10b981;
    }
    .tracker-step.current .tracker-icon {
        background: var(--dmx-blue);
        color: #ffffff;
        box-shadow: 0 0 0 2px var(--dmx-blue);
        animation: pulseBlue 1.5s infinite;
    }
    @keyframes pulseBlue {
        0% { box-shadow: 0 0 0 0 rgba(0, 113, 175, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(0, 113, 175, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 113, 175, 0); }
    }
    .tracker-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
    }
    .tracker-step.done .tracker-label, .tracker-step.current .tracker-label {
        color: #0f172a;
    }

    /* INFO GRID */
    .od-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        padding: 1.5rem;
    }
    .info-block h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }
    .info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        font-size: 0.88rem;
        line-height: 1.6;
        color: #1e293b;
    }

    /* PRODUCT TABLE */
    .od-table {
        width: 100%;
        border-collapse: collapse;
    }
    .od-table th {
        background: #f8fafc;
        padding: 0.85rem 1.25rem;
        text-align: left;
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }
    .od-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        vertical-align: middle;
    }
    .od-table tr:last-child td {
        border-bottom: none;
    }
    .prod-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .prod-img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        flex-shrink: 0;
    }
    .prod-name {
        font-weight: 700;
        color: #1e293b;
        text-decoration: none;
        display: block;
        line-height: 1.35;
    }
    .prod-name:hover {
        color: var(--dmx-blue);
    }

    /* SUMMARY FOOTER */
    .od-summary-footer {
        padding: 1.5rem;
        background: #fafafa;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
    }
    .od-summary-box {
        width: 320px;
        max-width: 100%;
    }
    .od-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }
    .od-summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--dmx-red);
        border-top: 2px solid #e2e8f0;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }

    /* ACTIONS BAR */
    .od-actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }

    /* PRINT STYLES */
    @media print {
        header, footer, .top-promo-strip, .order-breadcrumb, .od-actions-bar, .site-footer {
            display: none !important;
        }
        body, .main-wrapper {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .od-card {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }
    }

    @media (max-width: 768px) {
        .od-info-grid {
            grid-template-columns: 1fr;
        }
        .tracker-steps {
            margin: 0;
        }
        .tracker-step {
            width: auto;
        }
        .tracker-label {
            font-size: 0.72rem;
        }
    }
</style>
@endsection

@section('content')
<div class="order-detail-container">
    <!-- Breadcrumb -->
    <div class="order-breadcrumb">
        <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <span>/</span>
        <a href="{{ route('profile.edit', ['tab' => 'orders']) }}"><i class="fa-solid fa-box-open"></i> Đơn hàng của tôi</a>
        <span>/</span>
        <span style="color: #1e293b; font-weight: 600;">Chi tiết đơn hàng #{{ $order->id }}</span>
    </div>

    <!-- MAIN ORDER CARD -->
    <div class="od-card">
        <!-- Header -->
        <div class="od-header">
            <div>
                <h1 class="od-title">
                    <i class="fa-solid fa-receipt"></i> Đơn Hàng #{{ $order->id }}
                </h1>
                <div class="od-date">
                    <i class="fa-regular fa-calendar-days me-1"></i> Ngày đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
            <div>
                @if($order->status === 'pending')
                    <span class="od-badge pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>
                @elseif($order->status === 'processing')
                    <span class="od-badge processing"><i class="fa-solid fa-truck-fast"></i> Đang giao hàng</span>
                @elseif($order->status === 'completed')
                    <span class="od-badge completed"><i class="fa-solid fa-circle-check"></i> Đã hoàn tất</span>
                @else
                    <span class="od-badge cancelled"><i class="fa-solid fa-circle-xmark"></i> Đã hủy</span>
                @endif
            </div>
        </div>

        <!-- Order Timeline / Tracking Tracker -->
        @if($order->status !== 'cancelled')
            <div class="od-tracker">
                <div class="tracker-steps">
                    <!-- Step 1 -->
                    <div class="tracker-step done">
                        <div class="tracker-icon"><i class="fa-solid fa-check"></i></div>
                        <span class="tracker-label">Đặt hàng</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="tracker-step {{ in_array($order->status, ['processing', 'completed']) ? 'done' : 'current' }}">
                        <div class="tracker-icon"><i class="fa-solid fa-box-archive"></i></div>
                        <span class="tracker-label">Xác nhận đơn</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="tracker-step {{ $order->status === 'completed' ? 'done' : ($order->status === 'processing' ? 'current' : '') }}">
                        <div class="tracker-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <span class="tracker-label">Đang giao</span>
                    </div>

                    <!-- Step 4 -->
                    <div class="tracker-step {{ $order->status === 'completed' ? 'done' : '' }}">
                        <div class="tracker-icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
                        <span class="tracker-label">Hoàn tất</span>
                    </div>
                </div>
            </div>
        @else
            <div style="background: #fef2f2; border-bottom: 1px solid #fecaca; padding: 1rem 1.5rem; color: #991b1b; display: flex; align-items: center; gap: 0.6rem; font-size: 0.9rem;">
                <i class="fa-solid fa-circle-exclamation fs-5"></i>
                <div>
                    <strong>Đơn hàng này đã bị hủy.</strong> Nếu quý khách có thắc mắc, vui lòng liên hệ tổng đài hỗ trợ <strong>0913 014 602</strong>.
                </div>
            </div>
        @endif

        <!-- Customer & Shipping Information -->
        <div class="od-info-grid">
            <div class="info-block">
                <h4><i class="fa-solid fa-user-check" style="color: var(--dmx-blue);"></i> Người Nhận Hàng</h4>
                <div class="info-box">
                    <div><strong>Họ tên:</strong> {{ $order->user->name ?? 'Quý khách' }}</div>
                    <div><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</div>
                    <div><strong>Số điện thoại:</strong> {{ $order->phone }}</div>
                </div>
            </div>

            <div class="info-block">
                <h4><i class="fa-solid fa-location-dot" style="color: var(--dmx-red);"></i> Địa Chỉ & Giao Nhận GHN</h4>
                <div class="info-box">
                    <div><strong>Địa chỉ nhận:</strong> {{ $order->shipping_address }}</div>
                    <div><strong>Đơn vị vận chuyển:</strong> Giao Hàng Nhanh (GHN Express)</div>
                    @if($order->ghn_order_code)
                        <div style="margin-top: 0.35rem;">
                            <strong>Mã vận đơn GHN:</strong> 
                            <span style="background: #e0f2fe; color: #0369a1; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; font-family: monospace; border: 1px solid #bae6fd;">
                                {{ $order->ghn_order_code }}
                            </span>
                            <a href="{{ config('services.ghn.portal_url', 'https://5sao.ghn.dev') }}/?order_code={{ $order->ghn_order_code }}" target="_blank" style="color: var(--dmx-blue); font-size: 0.8rem; margin-left: 0.4rem; text-decoration: underline;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Tra cứu hành trình
                            </a>
                        </div>
                    @else
                        <div><strong>Mã vận đơn:</strong> <span style="color: #64748b;">Đang chuẩn bị kiện hàng</span></div>
                    @endif
                    <div style="margin-top: 0.35rem;">
                        <strong>Trạng thái vận chuyển:</strong>
                        <span class="badge {{ $order->shipping_badge['badge_class'] ?? 'bg-secondary text-white' }} px-2 py-1" style="font-size: 0.82rem;">
                            <i class="fa-solid {{ $order->shipping_badge['icon'] ?? 'fa-box' }} me-1"></i>
                            {{ $order->shipping_badge['label'] ?? 'Chờ bàn giao' }}
                        </span>
                        <div class="small text-muted mt-1" style="font-size: 0.8rem;">
                            {{ $order->shipping_badge['desc'] ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div style="padding: 0 1.5rem 0.5rem;">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 0.75rem;">
                <i class="fa-solid fa-boxes-stacked" style="color: var(--dmx-blue);"></i> Danh Sách Sản Phẩm ({{ $order->orderItems->count() }})
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="od-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="text-align: center;">Màu sắc</th>
                        <th style="text-align: right;">Đơn giá</th>
                        <th style="text-align: center;">Số lượng</th>
                        <th style="text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <div class="prod-cell">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset($item->product->image) }}" class="prod-img" alt="">
                                    @else
                                        <div class="prod-img" style="display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                            <i class="fa-solid fa-image fa-lg"></i>
                                        </div>
                                    @endif
                                    <div>
                                        @if($item->product)
                                            <a href="{{ route('product.show', $item->product->id) }}" class="prod-name">
                                                {{ $item->product->name }}
                                            </a>
                                            <small style="color: #64748b;">Mã SP: #{{ $item->product->id }}</small>
                                        @else
                                            <span class="prod-name" style="color: #94a3b8;">Sản phẩm đã ngừng kinh doanh</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($item->color)
                                    <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">{{ $item->color }}</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 600;">
                                {{ number_format($item->price, 0, ',', '.') }}₫
                            </td>
                            <td style="text-align: center; font-weight: 700;">
                                x{{ $item->quantity }}
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #0f172a;">
                                {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Footer -->
        <div class="od-summary-footer">
            <div class="od-summary-box">
                @php
                    $ghnFee = $order->ghn_total_fee ?? 0;
                    $itemsTotal = max(0, $order->total_price - $ghnFee);
                @endphp
                <div class="od-summary-row">
                    <span>Tiền hàng:</span>
                    <span style="font-weight: 600; color: #1e293b;">{{ number_format($itemsTotal, 0, ',', '.') }}₫</span>
                </div>
                <div class="od-summary-row">
                    <span>Cước vận chuyển (GHN):</span>
                    @if($ghnFee > 0)
                        <span style="color: #d97706; font-weight: 700;">{{ number_format($ghnFee, 0, ',', '.') }}₫</span>
                    @else
                        <span style="color: #16a34a; font-weight: 700;">MIỄN PHÍ</span>
                    @endif
                </div>
                <div class="od-summary-total">
                    <span>Tổng thanh toán:</span>
                    <span>{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="od-actions-bar">
        <a href="{{ route('profile.edit', ['tab' => 'orders']) }}" class="btn-card-action" style="background: #ffffff; color: #475569; border: 1px solid #cbd5e1; text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách đơn hàng
        </a>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button type="button" onclick="window.print()" class="btn-card-action" style="background: #f8fafc; color: #1e293b; border: 1px solid #cbd5e1; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-print"></i> In Hóa Đơn
            </button>

            {{-- Nút Thanh toán lại (MoMo) --}}
            @if($order->can_pay_again)
                <a href="{{ route('orders.momo.pay', $order->id) }}"
                   class="btn-card-action"
                   style="background: linear-gradient(135deg, #ae2070, #d64a8b); color: #fff; text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; border: none;">
                    <i class="fa-solid fa-wallet"></i> Thanh toán lại qua MoMo
                </a>
            @endif

            @if($order->status === 'pending')
                <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }} này không?')">
                    @csrf
                    <button type="submit" class="btn-danger-action" style="padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; background: #dc2626; color: #fff; border: none; cursor: pointer;">
                        <i class="fa-solid fa-ban"></i> Hủy Đơn Hàng
                    </button>
                </form>
            @endif
        </div>

    </div>
</div>
@endsection
