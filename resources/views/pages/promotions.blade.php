@extends('layouts.frontend')

@section('title', 'Chương Trình Khuyến Mãi Lễ Tết - Siêu Lò Nướng')

@section('extra-css')
<style>
    .promo-banner-hero {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 50%, #f97316 100%);
        border-radius: 16px;
        padding: 2.5rem;
        color: #ffffff;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 8px 24px rgba(220, 38, 38, 0.25);
    }
    .promo-banner-hero h1 {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: var(--dmx-yellow);
        text-transform: uppercase;
    }
    .promo-banner-hero p {
        font-size: 1.1rem;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
    }

    .promo-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .promo-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s;
    }
    .promo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .promo-card-header {
        padding: 1.5rem;
        color: #ffffff;
    }
    .promo-card-header.tet {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }
    .promo-card-header.national {
        background: linear-gradient(135deg, #b91c1c, #d97706);
    }
    .promo-card-header.xmas {
        background: linear-gradient(135deg, #15803d, #16a34a);
    }
    .promo-card-header h3 {
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 0.35rem;
    }
    .promo-card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .promo-card-body p {
        color: #475569;
        font-size: 0.92rem;
        line-height: 1.6;
        margin-bottom: 1.25rem;
        flex: 1;
    }

    .voucher-box {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
    }
    .voucher-code {
        font-weight: 800;
        color: var(--dmx-red);
        font-size: 1.1rem;
        letter-spacing: 1px;
    }
    .btn-apply-voucher {
        background: var(--dmx-blue);
        color: #fff;
        border: none;
        padding: 0.35rem 0.75rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    @media (max-width: 900px) {
        .promo-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Banner -->
<div class="promo-banner-hero">
    <h1>🧧 SĂN SALE ĐÓN LỄ TẾT 🧧</h1>
    <p>Đại tiệc giảm giá gia dụng nhà bếp lớn nhất năm tại <strong>Siêu Lò Nướng</strong>. Hàng loạt ưu đãi giảm sâu đến 30%, tặng quà cao cấp và miễn phí giao hàng toàn quốc.</p>
</div>

<!-- Promo Cards -->
<div class="promo-cards-grid">
    <!-- Card 1: Tết Nguyên Đán -->
    <div class="promo-card">
        <div class="promo-card-header tet">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🧧 🌸</div>
            <h3>Tết Giáp Thìn - Rước Lộc Về Nhà</h3>
            <span style="font-size: 0.8rem; opacity: 0.9;">Áp dụng từ 15 Tháng Chạp đến Mùng 10 Tết</span>
        </div>
        <div class="promo-card-body">
            <p>Giảm sốc đến <strong>30%</strong> cho toàn bộ dòng lò vi sóng điện tử cảm ứng và lò vi sóng Inverter. Tặng ngay bộ 3 thố thủy tinh cao cấp và lì xì 200.000đ trừ thẳng vào giá.</p>
            <div class="voucher-box">
                <div>
                    <div style="font-size: 0.72rem; color: #64748b;">MÃ GIẢM GIÁ:</div>
                    <div class="voucher-code">TETVANG30</div>
                </div>
                <a href="{{ route('home') }}" class="btn-apply-voucher">Mua Ngay</a>
            </div>
        </div>
    </div>

    <!-- Card 2: Quốc Khánh 2/9 -->
    <div class="promo-card">
        <div class="promo-card-header national">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">⭐ 🇻🇳</div>
            <h3>Mừng Đại Lễ Quốc Khánh 2/9</h3>
            <span style="font-size: 0.8rem; opacity: 0.9;">Áp dụng dịp lễ 2/9 hàng năm</span>
        </div>
        <div class="promo-card-body">
            <p>Đồng giá vận chuyển toàn quốc chỉ <strong>29.000đ</strong>. Tặng phiếu mua hàng trị giá 300.000đ khi mua kèm phụ kiện khay nướng chuyên dụng cho lò vi sóng có nướng.</p>
            <div class="voucher-box">
                <div>
                    <div style="font-size: 0.72rem; color: #64748b;">MÃ GIẢM GIÁ:</div>
                    <div class="voucher-code">QUOCKHANH29</div>
                </div>
                <a href="{{ route('home') }}" class="btn-apply-voucher">Mua Ngay</a>
            </div>
        </div>
    </div>

    <!-- Card 3: Giáng Sinh -->
    <div class="promo-card">
        <div class="promo-card-header xmas">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎄 ❄️</div>
            <h3>Giáng Sinh Ấm Áp - Bếp Nhà Lung Linh</h3>
            <span style="font-size: 0.8rem; opacity: 0.9;">Áp dụng tháng 12 mùa lễ hội</span>
        </div>
        <div class="promo-card-body">
            <p>Combo mùa đông: Mua lò vi sóng nướng được tặng ngay vỉ nướng đôi đa năng. Giảm thêm <strong>10%</strong> cho khách hàng thanh toán chuyển khoản hoặc thanh toán COD.</p>
            <div class="voucher-box">
                <div>
                    <div style="font-size: 0.72rem; color: #64748b;">MÃ GIẢM GIÁ:</div>
                    <div class="voucher-code">XMASNOEL10</div>
                </div>
                <a href="{{ route('home') }}" class="btn-apply-voucher">Mua Ngay</a>
            </div>
        </div>
    </div>
</div>

<!-- Special Promotion Policy -->
<div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--card-border); padding: 2rem; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
    <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--dmx-blue); margin-bottom: 1rem;">
        <i class="fa-solid fa-gift"></i> Cam Kết Khuyến Mãi Tại Siêu Lò Nướng
    </h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <div style="display: flex; gap: 1rem;">
            <i class="fa-solid fa-certificate" style="font-size: 2rem; color: var(--dmx-blue);"></i>
            <div>
                <strong style="color: #1e293b;">100% Chính Hãng</strong>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem;">Hoàn tiền 200% nếu phát hiện hàng không chuẩn chính hãng.</p>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            <i class="fa-solid fa-truck-ramp-box" style="font-size: 2rem; color: #16a34a;"></i>
            <div>
                <strong style="color: #1e293b;">Giao Nhanh 2 Giờ</strong>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem;">Khu vực nội thành Hà Nội nhận hàng ngay trong ngày.</p>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            <i class="fa-solid fa-screwdriver-wrench" style="font-size: 2rem; color: var(--dmx-orange);"></i>
            <div>
                <strong style="color: #1e293b;">Lắp Đặt Miễn Phí</strong>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem;">Kỹ thuật viên bàn giao và test máy tận tình tại nhà.</p>
            </div>
        </div>
    </div>
</div>
@endsection
