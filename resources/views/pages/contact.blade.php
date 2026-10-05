@extends('layouts.frontend')

@section('title', 'Liên Hệ Quản Lý & Showroom - Siêu Lò Nướng')

@section('extra-css')
<style>
    .contact-hero {
        background: linear-gradient(135deg, #0071af 0%, #0284c7 100%);
        border-radius: 16px;
        padding: 2.5rem;
        color: #ffffff;
        text-align: center;
        margin-bottom: 2rem;
    }
    .contact-hero h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: var(--dmx-yellow);
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 2rem;
    }

    .contact-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        padding: 2rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .contact-card h2 {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--dmx-blue);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-icon {
        width: 46px;
        height: 46px;
        background: var(--dmx-blue-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dmx-blue);
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .info-text strong {
        display: block;
        color: #1e293b;
        font-size: 1rem;
        margin-bottom: 0.2rem;
    }
    .info-text p {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0;
    }

    /* FORM */
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-size: 0.88rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    .form-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.65rem 0.9rem;
        font-family: inherit;
        font-size: 0.9rem;
        outline: none;
        transition: border 0.2s;
    }
    .form-control:focus {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 3px rgba(0, 113, 175, 0.15);
    }
    .btn-submit-contact {
        width: 100%;
        background: var(--dmx-blue);
        color: #fff;
        border: none;
        padding: 0.85rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit-contact:hover {
        background: var(--dmx-blue-dark);
    }

    @media (max-width: 850px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero -->
<div class="contact-hero">
    <h1>📞 TRUNG TÂM HỖ TRỢ & LIÊN HỆ QUẢN LÝ</h1>
    <p>Hệ thống Siêu Lò Nướng cam kết phục vụ tận tâm, tư vấn trung thực và bảo hành chu đáo nhất cho mọi khách hàng.</p>
</div>

<div class="contact-grid">
    <!-- Showroom & Manager Info -->
    <div class="contact-card">
        <h2><i class="fa-solid fa-address-card"></i> Thông Tin Hộ Kinh Doanh & Showroom</h2>

        <div class="info-row">
            <div class="info-icon">
                <i class="fa-solid fa-building"></i>
            </div>
            <div class="info-text">
                <strong>Tên cơ sở kinh doanh</strong>
                <p style="font-size: 1.1rem; font-weight: 700; color: var(--dmx-blue);">HỘ KINH DOANH SIÊU LÒ NƯỚNG</p>
                <p>Giấy phép ĐKKD: 01E8039281 | MST: 0109887766</p>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="info-text">
                <strong>Đại diện pháp luật</strong>
                <p style="font-size: 1.05rem; font-weight: 700; color: #1e293b;">Lê Thiều Hưng</p>
                <p>Chủ hộ kinh doanh & Quản lý điều hành bán lẻ</p>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <i class="fa-solid fa-phone"></i>
            </div>
            <div class="info-text">
                <strong>Điện thoại Hotline / Zalo</strong>
                <p><a href="tel:0913014602" style="font-size: 1.25rem; font-weight: 800; color: var(--dmx-red); text-decoration: none;">0913 014 602</a></p>
                <p>Hỗ trợ tư vấn trực tiếp 24/7 (Miễn phí cuộc gọi)</p>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div class="info-text">
                <strong>Địa chỉ Email</strong>
                <p><a href="mailto:0912lethieuhung@gmail.com" style="color: #2563eb; font-weight: 600; text-decoration: none;">0912lethieuhung@gmail.com</a></p>
                <p>Tiếp nhận yêu cầu báo giá dự án, khiếu nại dịch vụ</p>
            </div>
        </div>

        <div class="info-row">
            <div class="info-icon">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div class="info-text">
                <strong>Showroom & Trụ sở đăng ký</strong>
                <p style="color: #1e293b; font-weight: 600;">Số 96 đường Nguyễn Du, phường Cửa Nam, quận Hai Bà Trưng, TP. Hà Nội</p>
                <p>Kho hàng gửi GHN: 277 Quan Hoa, Cầu Giấy, Hà Nội | Giờ mở cửa: 08:00 - 21:30</p>
            </div>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-card">
        <h2><i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn Cho Chúng Tôi</h2>
        
        @if(session('success_contact'))
            <div style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem;">
                Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn và sẽ gọi lại trong 5 phút.
            </div>
        @endif

        <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Cảm ơn quý khách! Quản lý Lê Thiều Hưng sẽ liên hệ tư vấn qua số điện thoại này trong ít phút.');">
            @csrf
            <div class="form-group">
                <label for="c_name">Họ và tên *</label>
                <input type="text" id="c_name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
            </div>

            <div class="form-group">
                <label for="c_phone">Số điện thoại liên hệ *</label>
                <input type="tel" id="c_phone" class="form-control" placeholder="Ví dụ: 0988 123 456" required>
            </div>

            <div class="form-group">
                <label for="c_content">Nội dung cần hỗ trợ / Sản phẩm quan tâm</label>
                <textarea id="c_content" rows="4" class="form-control" placeholder="Quý khách cần tư vấn chọn mẫu lò vi sóng nào, địa chỉ giao hàng ở đâu?..."></textarea>
            </div>

            <button type="submit" class="btn-submit-contact">
                <i class="fa-solid fa-paper-plane"></i> GỬI YÊU CẦU TƯ VẤN NGAY
            </button>
        </form>
    </div>
</div>
@endsection
