@extends('layouts.frontend')

@section('title', 'Đặt Hàng Thành Công - Siêu Lò Nướng')

@section('content')
<div style="max-width: 650px; margin: 2rem auto; background: #ffffff; border-radius: 16px; border: 1px solid var(--card-border); padding: 3rem 2rem; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
    <div style="font-size: 4.5rem; margin-bottom: 1rem; animation: bounce 1s infinite alternate;">🎉</div>
    
    <h1 style="font-size: 1.8rem; font-weight: 800; color: #15803d; margin-bottom: 0.75rem;">
        ĐẶT HÀNG THÀNH CÔNG!
    </h1>
    
    <p style="color: #475569; font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">
        Cảm ơn quý khách đã tin tưởng và mua sắm tại <strong>Siêu Lò Nướng</strong>. Nhân viên giao hàng sẽ liên hệ xác nhận và tiến hành giao lò vi sóng đến tận nơi trong vòng <strong>2 giờ</strong>.
    </p>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; margin-bottom: 2rem; text-align: left; font-size: 0.9rem;">
        <div style="font-weight: 700; color: var(--dmx-blue); margin-bottom: 0.5rem;"><i class="fa-solid fa-headset"></i> THÔNG TIN ĐƠN VỊ BÁN HÀNG:</div>
        <p style="margin-bottom: 0.3rem;">Đơn vị: <strong>HỘ KINH DOANH SIÊU LÒ NƯỚNG</strong> (MST: 0109887766)</p>
        <p style="margin-bottom: 0.3rem;">Đại diện pháp luật: <strong>Lê Thiều Hưng</strong></p>
        <p style="margin-bottom: 0.3rem;">Hotline hỗ trợ CSKH: <strong style="color: var(--dmx-red);">0913 014 602</strong></p>
        <p>Địa chỉ Showroom: Số 96 đường Nguyễn Du, P. Cửa Nam, Hai Bà Trưng, Hà Nội</p>
    </div>

    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        @if(session('latest_order_id'))
            <a href="{{ route('orders.show', session('latest_order_id')) }}" class="btn-checkout" style="background: #16a34a; font-size: 0.95rem; text-decoration: none; color: #fff; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-receipt"></i> Xem chi tiết đơn hàng #{{ session('latest_order_id') }}
            </a>
        @else
            <a href="{{ route('profile.edit', ['tab' => 'orders']) }}" class="btn-checkout" style="background: #16a34a; font-size: 0.95rem; text-decoration: none; color: #fff; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-box-open"></i> Xem đơn hàng của tôi
            </a>
        @endif
        <a href="{{ route('home') }}" class="btn-checkout" style="background: var(--dmx-blue); font-size: 0.95rem; text-decoration: none; color: #fff; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-house"></i> Về trang chủ mua sắm
        </a>
    </div>
</div>
@endsection
