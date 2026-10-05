@extends('layouts.frontend')

@section('title', 'Cẩm Nang & Hướng Dẫn Sử Dụng Lò Vi Sóng - Siêu Lò Nướng')

@section('extra-css')
<style>
    .guide-hero {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        border-radius: 16px;
        padding: 2.5rem;
        color: #ffffff;
        text-align: center;
        margin-bottom: 2rem;
    }
    .guide-hero h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: var(--dmx-yellow);
    }
    .guide-hero p {
        font-size: 1rem;
        opacity: 0.95;
        max-width: 680px;
        margin: 0 auto;
    }

    .guide-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .guide-content-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        padding: 2rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-bottom: 1.5rem;
    }
    .guide-content-card h2 {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--dmx-blue);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .guide-step-item {
        background: #f8fafc;
        border-left: 4px solid var(--dmx-blue);
        padding: 1rem 1.25rem;
        border-radius: 0 8px 8px 0;
        margin-bottom: 1rem;
    }
    .guide-step-item h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.4rem;
    }
    .guide-step-item p {
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.6;
    }

    .sidebar-widget {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        padding: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-bottom: 1.5rem;
    }
    .sidebar-widget h3 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--dmx-red);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .danger-tag {
        background: #fee2e2;
        color: #991b1b;
        padding: 0.6rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (max-width: 850px) {
        .guide-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero -->
<div class="guide-hero">
    <h1>📖 CẨM NANG SỬ DỤNG LÒ VI SÓNG CHUẨN CHUYÊN GIA</h1>
    <p>Tổng hợp kinh nghiệm sử dụng an toàn, mẹo nấu ăn ngon và các bước vệ sinh giúp lò vi sóng luôn bền bỉ, tiết kiệm điện năng như mới mua tại Siêu Lò Nướng.</p>
</div>

<div class="guide-grid">
    <!-- Main Guides Content -->
    <div>
        <!-- Section 1 -->
        <div class="guide-content-card">
            <h2><i class="fa-solid fa-shield-halved"></i> 1. Quy Tắc An Toàn Cho Người Mới Dùng</h2>
            
            <div class="guide-step-item">
                <h3>⚡ Nguồn điện & Ổ cắm riêng biệt</h3>
                <p>Lò vi sóng có công suất từ 800W - 1500W. Luôn cắm vào ổ điện riêng, tránh cắm chung với tủ lạnh, nồi cơm điện hoặc ấm siêu tốc để chống quá tải chập cháy.</p>
            </div>

            <div class="guide-step-item">
                <h3>🍽️ Chọn vật dụng an toàn (Microwave Safe)</h3>
                <p>Chỉ dùng bát đĩa thủy tinh chịu nhiệt (Borosilicate), gốm sứ không viền kim loại, hoặc nhựa có dán nhãn "An toàn với lò vi sóng".</p>
            </div>

            <div class="guide-step-item">
                <h3>🚫 Tuyệt đối không bật lò khi bên trong rỗng</h3>
                <p>Nếu không có thức ăn để hấp thụ sóng viba, sóng sẽ phản xạ ngược lại đèn phát Magnetron gây hỏng lò ngay lập tức.</p>
            </div>
        </div>

        <!-- Section 2 -->
        <div class="guide-content-card">
            <h2><i class="fa-solid fa-fire-burner"></i> 2. Hướng Dẫn Sử Dụng Các Chức Năng Nâng Cao</h2>
            
            <div class="guide-step-item">
                <h3>🥩 Rã đông thực phẩm đúng cách (Defrost)</h3>
                <p>Chọn chế độ rã đông theo trọng lượng. Với thịt nguyên khối, nên dừng giữa chừng sau 2-3 phút để lật mặt, giúp thực phẩm rã đông đều mà không bị chín bên ngoài.</p>
            </div>

            <div class="guide-step-item">
                <h3>🍗 Tận dụng chế độ Nướng (Grill)</h3>
                <p>Với các dòng lò có nướng (như Electrolux EMG23K38GB), sử dụng vỉ nướng chuyên dụng để nhiệt đối lưu đều 2 mặt, thích hợp làm giòn bánh mì, nướng thịt xiên, xúc xích.</p>
            </div>

            <div class="guide-step-item">
                <h3>💧 Bí quyết hâm nóng cơm, bánh không bị khô cứng</h3>
                <p>Đặt một chén nước nhỏ cạnh đĩa thức ăn hoặc phủ khăn giấy ẩm lên bề mặt trước khi bấm quay 1 - 2 phút. Hơi nước sẽ giữ độ ẩm mềm dẻo tuyệt đối.</p>
            </div>
        </div>

        <!-- Section 3 -->
        <div class="guide-content-card">
            <h2><i class="fa-solid fa-soap"></i> 3. Mẹo Vệ Sinh Lò Siêu Nhanh Bằng Quả Chanh</h2>
            <p style="color: #475569; font-size: 0.92rem; line-height: 1.6; margin-bottom: 1rem;">
                Không cần chất tẩy rửa hóa học độc hại! Bạn chỉ cần làm theo 3 bước:
            </p>
            <ol style="padding-left: 1.5rem; color: #334155; line-height: 1.8; font-size: 0.92rem;">
                <li>Cắt đôi 1 quả chanh tươi, vắt vào bát nước sạch rồi thả luôn vỏ chanh vào bát.</li>
                <li>Đặt bát vào lò và bật công suất cao nhất quay trong <strong>3 đến 5 phút</strong>.</li>
                <li>Chờ 2 phút cho hơi nước chanh lan tỏa làm mềm các vết dầu mỡ cứng đầu, sau đó chỉ cần dùng khăn mềm lau nhẹ là sạch bóng thơm ngát.</li>
            </ol>
        </div>
    </div>

    <!-- Sidebar Info Widget -->
    <div>
        <div class="sidebar-widget">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> VẬT CẤM CHO VÀO LÒ</h3>
            <div class="danger-tag"><i class="fa-solid fa-xmark"></i> Bát đĩa kim loại, giấy bạc (Tia lửa điện)</div>
            <div class="danger-tag"><i class="fa-solid fa-xmark"></i> Trứng nguyên vỏ (Nổ vỡ nguy hiểm)</div>
            <div class="danger-tag"><i class="fa-solid fa-xmark"></i> Hộp xốp, túi nilon mỏng (Chảy nhựa độc)</div>
            <div class="danger-tag"><i class="fa-solid fa-xmark"></i> Bình giữ nhiệt kim loại</div>
        </div>

        <div class="sidebar-widget" style="background: var(--dmx-blue-light); border-color: #bae6fd;">
            <h3 style="color: var(--dmx-blue);"><i class="fa-solid fa-phone-volume"></i> CẦN TRỢ GIÚP KỸ THUẬT?</h3>
            <p style="font-size: 0.88rem; color: #334155; margin-bottom: 1rem;">Kỹ thuật viên tại Siêu Lò Nướng luôn sẵn sàng giải đáp mọi thắc mắc của bạn.</p>
            <a href="tel:0913014602" style="display: block; background: var(--dmx-blue); color: #fff; text-align: center; padding: 0.75rem; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 1.1rem;">
                📞 0913 014 602
            </a>
            <div style="text-align: center; font-size: 0.8rem; color: #64748b; margin-top: 0.5rem;">Bộ phận Kỹ thuật - Hộ Kinh Doanh Siêu Lò Nướng</div>
        </div>
    </div>
</div>
@endsection
