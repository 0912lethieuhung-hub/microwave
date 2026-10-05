@extends('layouts.frontend')

@section('title', 'Xác Thực Email Tài Khoản - Siêu Lò Nướng')

@section('extra-css')
<style>
    .verify-card {
        max-width: 520px;
        margin: 3rem auto;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .verify-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 1.5rem;
        background: #eff6ff;
        color: var(--dmx-blue, #0071af);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }
    .verify-card h2 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.75rem;
    }
    .verify-card p {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    .alert-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 0.85rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }
    .btn-verify-resend {
        width: 100%;
        background: var(--dmx-blue, #0071af);
        color: #ffffff;
        border: none;
        padding: 0.85rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-verify-resend:hover {
        background: #005a8c;
        transform: translateY(-1px);
    }
    .btn-verify-logout {
        background: transparent;
        border: none;
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
        margin-top: 1rem;
        display: inline-block;
    }
    .btn-verify-logout:hover {
        color: #ef4444;
    }
</style>
@endsection

@section('content')
<div class="verify-card">
    <div class="verify-icon">
        <i class="fas fa-envelope-open-text"></i>
    </div>
    
    <h2>Xác Thực Email Của Bạn</h2>
    
    <p>
        Cảm ơn bạn đã đăng ký tài khoản! Một liên kết xác thực đã được gửi tới địa chỉ email của bạn: 
        <strong>{{ Auth::user()->email ?? '' }}</strong>.<br>
        Vui lòng kiểm tra hộp thư đến (hoặc thư mục Spam) và nhấn vào liên kết để kích hoạt tài khoản.
    </p>

    @if (session('error'))
        <div class="alert-box" style="background: #fef2f2; border-color: #fecaca; color: #991b1b; margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert-box" style="background: #fffbeb; border-color: #fde68a; color: #92400e; margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i> {{ session('warning') }}
        </div>
    @endif

    @if (session('message') || session('status') == 'verification-link-sent' || session('success'))
        <div class="alert-box">
            <i class="fas fa-check-circle"></i> {{ session('message') ?? session('success') ?? 'Liên kết xác thực mới đã được gửi đến email của bạn thành công!' }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-verify-resend">
            <i class="fas fa-paper-plane"></i> Gửi lại email xác thực
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn-verify-logout">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất tài khoản
        </button>
    </form>
</div>
@endsection
