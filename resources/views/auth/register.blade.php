@extends('layouts.frontend')

@section('title', 'Đăng Ký Tài Khoản Mới - Siêu Lò Nướng')

@section('extra-css')
<style>
    .auth-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem;
    }
    .auth-card {
        max-width: 440px;
        width: 100%;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, 0.03);
        padding: 2.25rem 2rem;
    }
    .auth-card h1 {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--dmx-blue);
        text-align: center;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .auth-card p.auth-subtitle {
        text-align: center;
        color: #64748b;
        font-size: 0.88rem;
        margin-bottom: 1.5rem;
    }

    /* SOCIAL LOGIN BUTTONS */
    .social-btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-bottom: 1.35rem;
    }
    .btn-social {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .btn-social-google {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .btn-social-google:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .btn-social-facebook {
        background: #1877f2;
        color: #ffffff;
    }
    .btn-social-facebook:hover {
        background: #166fe5;
        box-shadow: 0 2px 8px rgba(24, 119, 242, 0.3);
    }

    /* DIVIDER */
    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.25rem 0;
        color: #94a3b8;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .auth-divider::before, .auth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }
    .auth-divider span {
        padding: 0 0.75rem;
    }

    /* FORM STYLES */
    .form-group {
        margin-bottom: 1.15rem;
    }
    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.35rem;
    }
    .form-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        font-family: inherit;
        font-size: 0.92rem;
        outline: none;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 3px rgba(0, 113, 175, 0.15);
    }
    .btn-auth-submit {
        width: 100%;
        background: linear-gradient(135deg, #0071af 0%, #005a8c 100%);
        color: #ffffff;
        border: none;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 113, 175, 0.25);
    }
    .btn-auth-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 175, 0.35);
    }
</style>
@endsection

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h1><i class="fa-solid fa-user-plus"></i> ĐĂNG KÝ TÀI KHOẢN</h1>
        <p class="auth-subtitle">Tạo tài khoản để nhận voucher 100.000đ và theo dõi đơn hàng</p>

        @if (session('success'))
            <div style="background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Social Register Buttons -->
        <div class="social-btn-group">
            <a href="{{ route('auth.google') }}" class="btn-social btn-social-google">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    <path fill="none" d="M0 0h48v48H0z"/>
                </svg>
                <span>Đăng ký nhanh với Google</span>
            </a>

            <a href="{{ route('auth.facebook') }}" class="btn-social btn-social-facebook">
                <i class="fa-brands fa-facebook-f" style="font-size: 1rem;"></i>
                <span>Đăng ký nhanh với Facebook</span>
            </a>
        </div>

        <div class="auth-divider">
            <span>Hoặc điền thông tin</span>
        </div>

        <!-- Normal Registration Form -->
        <form action="{{ url('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Họ và tên *</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Địa chỉ Email *</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="example@gmail.com" value="{{ old('email') }}" required>
                @error('email')
                    <span style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu (Tối thiểu 8 ký tự) *</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                @error('password')
                    <span style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Xác nhận lại mật khẩu *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-auth-submit">
                <i class="fa-solid fa-user-check"></i> ĐĂNG KÝ TÀI KHOẢN
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.86rem; color: #64748b;">
            Đã có tài khoản? <a href="{{ route('login') }}" style="color: var(--dmx-blue); font-weight: 700; text-decoration: none;">Đăng nhập tại đây →</a>
        </div>
    </div>
</div>
@endsection
