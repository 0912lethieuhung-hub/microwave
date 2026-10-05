@extends('layouts.frontend')

@section('title', 'Hồ Sơ Cá Nhân & Quản Lý Tài Khoản - Siêu Lò Nướng')

@section('extra-css')
<style>
    /* BREADCRUMB */
    .profile-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.88rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }
    .profile-breadcrumb a {
        color: var(--dmx-blue);
        text-decoration: none;
        font-weight: 500;
    }
    .profile-breadcrumb a:hover {
        text-decoration: underline;
    }

    /* PROFILE CONTAINER GRID */
    .profile-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 1.75rem;
        align-items: start;
    }

    /* SIDEBAR CARD */
    .profile-sidebar {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 1.5rem;
        position: sticky;
        top: 90px;
    }

    .user-card-header {
        text-align: center;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1.25rem;
    }

    .avatar-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 1rem;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--dmx-blue-light);
        box-shadow: 0 4px 10px rgba(0, 113, 175, 0.15);
    }

    .avatar-badge-online {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 16px;
        height: 16px;
        background: #10b981;
        border: 2px solid #ffffff;
        border-radius: 50%;
    }

    .user-name-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .user-email-text {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 0.6rem;
        word-break: break-all;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .role-badge.admin {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .role-badge.customer {
        background: var(--dmx-blue-light);
        color: var(--dmx-blue);
        border: 1px solid #bae6fd;
    }

    /* STATS WIDGET */
    .profile-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .stat-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
    }
    .stat-number {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--dmx-blue);
    }
    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.15rem;
    }

    /* NAV TABS (SIDEBAR) */
    .profile-menu {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .profile-menu-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        color: #475569;
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .profile-menu-item:hover {
        background: var(--dmx-blue-light);
        color: var(--dmx-blue);
    }
    .profile-menu-item.active {
        background: var(--dmx-blue);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 113, 175, 0.25);
    }
    .profile-menu-item i {
        font-size: 1.05rem;
        width: 20px;
        text-align: center;
    }

    /* MAIN CONTENT AREA */
    .profile-content-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 2rem;
    }

    .tab-pane {
        display: none;
        animation: fadeIn 0.25s ease-in-out;
    }
    .tab-pane.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .section-header {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .section-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--dmx-blue);
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .section-subtitle {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.2rem;
    }

    /* FORM STYLES */
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.45rem;
    }
    .form-label span.req {
        color: var(--dmx-red);
    }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.7rem 0.95rem;
        font-family: inherit;
        font-size: 0.92rem;
        outline: none;
        transition: all 0.2s;
        background: #ffffff;
        color: #1e293b;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 3px rgba(0, 113, 175, 0.15);
    }
    .form-input[disabled] {
        background: #f1f5f9;
        cursor: not-allowed;
        color: #64748b;
    }

    .form-error {
        color: var(--dmx-red);
        font-size: 0.8rem;
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* AVATAR UPLOAD CONTROLLER */
    .avatar-upload-box {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .avatar-preview-box {
        position: relative;
        width: 80px;
        height: 80px;
        flex-shrink: 0;
    }
    .avatar-preview-box img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--dmx-blue);
    }
    .btn-upload-avatar {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 0.55rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
    }
    .btn-upload-avatar:hover {
        background: var(--dmx-blue-light);
        border-color: var(--dmx-blue);
        color: var(--dmx-blue);
    }

    /* BUTTONS */
    .btn-primary-action {
        background: linear-gradient(135deg, #0071af 0%, #005a8c 100%);
        color: #ffffff;
        border: none;
        padding: 0.8rem 1.6rem;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0, 113, 175, 0.25);
    }
    .btn-primary-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 113, 175, 0.35);
    }

    /* ORDERS TAB STYLING */
    .order-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 10px;
        margin-bottom: 1.25rem;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }
    .order-card:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    }
    .order-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.9rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .order-code {
        font-weight: 800;
        color: var(--dmx-blue);
        font-size: 0.95rem;
    }
    .order-date {
        font-size: 0.82rem;
        color: #64748b;
    }
    .order-status-badge {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.3rem 0.7rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .order-status-badge.pending {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .order-status-badge.completed {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .order-status-badge.cancelled {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .order-status-badge.shipping {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .order-body {
        padding: 1.25rem;
    }
    .order-item-detail {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .order-item-detail:last-child {
        border-bottom: none;
    }
    .order-item-thumb {
        width: 55px;
        height: 55px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .order-item-meta {
        flex: 1;
    }
    .order-item-name {
        font-size: 0.92rem;
        font-weight: 600;
        color: #1e293b;
        text-decoration: none;
        display: block;
        line-height: 1.3;
    }
    .order-item-name:hover {
        color: var(--dmx-blue);
    }
    .order-item-qty {
        font-size: 0.82rem;
        color: #64748b;
        margin-top: 0.2rem;
    }
    .order-item-price {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
        text-align: right;
    }

    .order-card-footer {
        background: #fafafa;
        border-top: 1px solid #e2e8f0;
        padding: 0.9rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .order-shipping-info {
        font-size: 0.82rem;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .order-total-amount {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--dmx-red);
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 3.5rem 1rem;
    }
    .empty-state-icon {
        font-size: 3.5rem;
        color: #94a3b8;
        margin-bottom: 1rem;
    }
    .empty-state h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.5rem;
    }
    .empty-state p {
        font-size: 0.88rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    /* DANGER ZONE */
    .danger-zone-card {
        border: 1px solid #fecaca;
        background: #fff5f5;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 2rem;
    }
    .danger-title {
        color: #991b1b;
        font-weight: 800;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .danger-desc {
        font-size: 0.85rem;
        color: #7f1d1d;
        line-height: 1.5;
        margin-bottom: 1rem;
    }
    .btn-danger-action {
        background: #dc2626;
        color: #ffffff;
        border: none;
        padding: 0.65rem 1.2rem;
        border-radius: 6px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-danger-action:hover {
        background: #b91c1c;
    }

    /* MODAL */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
    }
    .modal-box {
        background: #ffffff;
        border-radius: 12px;
        max-width: 480px;
        width: 100%;
        padding: 1.75rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        animation: scaleUp 0.2s ease;
    }
    @keyframes scaleUp {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
        .profile-sidebar {
            position: static;
        }
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="profile-breadcrumb">
    <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span>/</span>
    <span style="color: #1e293b; font-weight: 600;">Hồ sơ cá nhân & Quản lý tài khoản</span>
</div>

<div class="profile-layout">
    <!-- LEFT SIDEBAR -->
    <aside class="profile-sidebar">
        <div class="user-card-header">
            <div class="avatar-wrapper">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-img" id="sidebarAvatarPreview">
                <span class="avatar-badge-online" title="Đang hoạt động"></span>
            </div>
            <h2 class="user-name-title">{{ $user->name }}</h2>
            <div class="user-email-text">{{ $user->email }}</div>
            <div>
                <span class="role-badge {{ $user->is_admin ? 'admin' : 'customer' }}">
                    <i class="fa-solid {{ $user->is_admin ? 'fa-shield-halved' : 'fa-gem' }}"></i>
                    {{ $user->role_name }}
                </span>
            </div>
        </div>

        <!-- Quick Stats Widget -->
        <div class="profile-stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $totalOrders }}</div>
                <div class="stat-label">Tổng đơn hàng</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" style="font-size: 0.95rem; color: var(--dmx-red);">{{ number_format($totalSpent, 0, ',', '.') }}₫</div>
                <div class="stat-label">Tổng chi tiêu</div>
            </div>
        </div>

        <!-- Nav Tab List -->
        <ul class="profile-menu">
            <li>
                <a href="#info" class="profile-menu-item active" onclick="switchProfileTab('info', event)">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Thông tin tài khoản</span>
                </a>
            </li>
            <li>
                <a href="#orders" class="profile-menu-item" onclick="switchProfileTab('orders', event)">
                    <i class="fa-solid fa-box-open"></i>
                    <span>Lịch sử đơn hàng ({{ $totalOrders }})</span>
                </a>
            </li>
            <li>
                <a href="#password" class="profile-menu-item" onclick="switchProfileTab('password', event)">
                    <i class="fa-solid fa-key"></i>
                    <span>Đổi mật khẩu</span>
                </a>
            </li>
            <li>
                <a href="#settings" class="profile-menu-item" onclick="switchProfileTab('settings', event)">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Cài đặt & Bảo mật</span>
                </a>
            </li>
            @if($user->is_admin)
            <li style="margin-top: 0.5rem; border-top: 1px dashed #e2e8f0; padding-top: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" class="profile-menu-item" style="color: #b45309; background: #fef3c7;">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Vào Admin Panel</span>
                </a>
            </li>
            @endif
            <li style="margin-top: 0.25rem;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="profile-menu-item" style="width: 100%; border: none; background: none; color: #dc2626;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Đăng xuất tài khoản</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <!-- RIGHT CONTENT AREA -->
    <main class="profile-content-card">

        <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
        <div id="tab-info" class="tab-pane active">
            <div class="section-header">
                <div>
                    <h1 class="section-title"><i class="fa-solid fa-id-card"></i> Thông Tin Cá Nhân</h1>
                    <p class="section-subtitle">Quản lý và điều chỉnh thông tin hồ sơ của bạn trên hệ thống Siêu Lò Nướng</p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: #64748b;">
                        <i class="fa-solid fa-calendar-check"></i> Thành viên từ: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Mới' }}
                    </span>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Avatar Upload Section -->
                <div class="avatar-upload-box">
                    <div class="avatar-preview-box">
                        <img src="{{ $user->avatar_url }}" alt="Preview" id="formAvatarPreview">
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.92rem; color: #1e293b; margin-bottom: 0.2rem;">Ảnh đại diện</div>
                        <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">Hỗ trợ JPG, PNG, WEBP hoặc GIF (Dưới 2MB).</div>
                        <label for="avatar_input" class="btn-upload-avatar">
                            <i class="fa-solid fa-camera"></i> Chọn ảnh mới
                        </label>
                        <input type="file" name="avatar" id="avatar_input" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
                        @error('avatar')
                            <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Họ và tên <span class="req">*</span></label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required placeholder="Ví dụ: Nguyễn Văn A">
                        @error('name')
                            <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Địa chỉ Email <span class="req">*</span></label>
                        <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required placeholder="user@gmail.com">
                        @error('email')
                            <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="phone">Số điện thoại liên hệ</label>
                        <input type="tel" name="phone" id="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="Ví dụ: 0988 123 456">
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Dùng để liên lạc xác nhận khi giao hàng.</div>
                        @error('phone')
                            <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Giới tính</label>
                        <select name="gender" id="gender" class="form-select">
                            <option value="">-- Chọn giới tính --</option>
                            <option value="nam" {{ old('gender', $user->gender) == 'nam' ? 'selected' : '' }}>Nam</option>
                            <option value="nu" {{ old('gender', $user->gender) == 'nu' ? 'selected' : '' }}>Nữ</option>
                            <option value="khac" {{ old('gender', $user->gender) == 'khac' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('gender')
                            <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="birthday">Ngày sinh</label>
                        <input type="date" name="birthday" id="birthday" class="form-input" value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}">
                        @error('birthday')
                            <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Vai trò tài khoản</label>
                        <input type="text" class="form-input" value="{{ $user->role_name }}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Địa chỉ nhận hàng mặc định</label>
                    <textarea name="address" id="address" rows="2" class="form-textarea" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố...">{{ old('address', $user->address) }}</textarea>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Hệ thống sẽ tự động điền địa chỉ này khi bạn đặt mua lò vi sóng.</div>
                    @error('address')
                        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="bio">Ghi chú cá nhân / Yêu cầu riêng</label>
                    <textarea name="bio" id="bio" rows="2" class="form-textarea" placeholder="Ghi chú thêm về sở thích hoặc yêu cầu khi nhận hàng...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center;">
                    <button type="submit" class="btn-primary-action">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi Thông Tin
                    </button>
                    @if (session('status') === 'profile-updated' || session('success'))
                        <span style="color: #16a34a; font-weight: 600; font-size: 0.9rem;">
                            <i class="fa-solid fa-circle-check"></i> Đã lưu thay đổi!
                        </span>
                    @endif
                </div>
            </form>
        </div>

        <!-- TAB 2: LỊCH SỬ ĐƠN HÀNG -->
        <div id="tab-orders" class="tab-pane">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Lịch Sử Đơn Hàng</h2>
                    <p class="section-subtitle">Theo dõi trạng thái và chi tiết các đơn hàng lò vi sóng bạn đã mua</p>
                </div>
                <div>
                    <a href="{{ route('home') }}" class="btn-upload-avatar" style="text-decoration: none;">
                        <i class="fa-solid fa-plus"></i> Đặt mua thêm lò mới
                    </a>
                </div>
            </div>

            @if($orders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3>Bạn chưa có đơn hàng nào</h3>
                    <p>Hãy khám phá các mẫu lò vi sóng cơ, điện tử, có nướng cao cấp tại Siêu Lò Nướng ngay hôm nay!</p>
                    <a href="{{ route('home') }}" class="btn-primary-action" style="text-decoration: none;">
                        <i class="fa-solid fa-cart-shopping"></i> Mua sắm ngay
                    </a>
                </div>
            @else
                @foreach($orders as $order)
                    <div class="order-card">
                        <div class="order-card-header">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <a href="{{ route('orders.show', $order->id) }}" class="order-code" style="text-decoration: none;">
                                    #OD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                                <span class="order-date"><i class="fa-regular fa-clock"></i> {{ $order->created_at->format('H:i - d/m/Y') }}</span>
                            </div>
                            <div>
                                @php
                                    $statusClass = 'pending';
                                    $statusText = 'Chờ xác nhận';
                                    $statusIcon = 'fa-hourglass-half';
                                    if($order->status === 'completed') {
                                        $statusClass = 'completed';
                                        $statusText = 'Đã giao thành công';
                                        $statusIcon = 'fa-circle-check';
                                    } elseif($order->status === 'cancelled') {
                                        $statusClass = 'cancelled';
                                        $statusText = 'Đã hủy đơn';
                                        $statusIcon = 'fa-circle-xmark';
                                    } elseif($order->status === 'processing' || $order->status === 'shipping') {
                                        $statusClass = 'shipping';
                                        $statusText = 'Đang giao hàng';
                                        $statusIcon = 'fa-truck-fast';
                                    }
                                @endphp
                                <span class="order-status-badge {{ $statusClass }}">
                                    <i class="fa-solid {{ $statusIcon }}"></i>
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>

                        <div class="order-body">
                            @foreach($order->orderItems as $item)
                                <div class="order-item-detail">
                                    <img src="{{ asset($item->product->image ?? 'images/microwave_sharp.jpg') }}" alt="{{ $item->product->name ?? 'Lò vi sóng' }}" class="order-item-thumb" onerror="this.src='{{ asset('images/microwave_sharp.jpg') }}'">
                                    <div class="order-item-meta">
                                        @if($item->product)
                                            <a href="{{ route('product.show', $item->product->id) }}" class="order-item-name">
                                                {{ $item->product->name }}
                                            </a>
                                        @else
                                            <span class="order-item-name">Sản phẩm lò vi sóng</span>
                                        @endif
                                        <div class="order-item-qty">
                                            @if($item->color)
                                                Màu: <strong>{{ $item->color }}</strong> | 
                                            @endif
                                            Số lượng: <strong>x{{ $item->quantity }}</strong> | Đơn giá: {{ number_format($item->price, 0, ',', '.') }}₫
                                        </div>
                                    </div>
                                    <div class="order-item-price">
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="order-card-footer">
                            <div class="order-shipping-info">
                                <div><i class="fa-solid fa-location-dot" style="color: var(--dmx-red);"></i> <strong>Địa chỉ nhận:</strong> {{ $order->shipping_address }} (SĐT: {{ $order->phone }})</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span style="font-size: 0.88rem; color: #64748b;">
                                        {{ $order->payment_method === 'momo' ? ($order->is_paid_online ? 'Đã thanh toán (MoMo):' : 'Cần thanh toán (MoMo):') : 'Tổng COD:' }}
                                    </span>
                                    <span class="order-total-amount">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                                </div>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn-upload-avatar" style="text-decoration: none; padding: 0.45rem 0.85rem; font-size: 0.82rem;">
                                    <i class="fa-solid fa-eye"></i> Chi tiết
                                </a>
                                @if($order->can_pay_again)
                                    <a href="{{ route('orders.momo.pay', $order->id) }}"
                                       style="display: inline-flex; align-items: center; gap: 0.35rem; background: linear-gradient(135deg, #ae2070, #d64a8b); color: #fff; text-decoration: none; padding: 0.45rem 0.85rem; border-radius: 6px; font-size: 0.82rem; font-weight: 700;">
                                        <i class="fa-solid fa-wallet"></i> Thanh toán MoMo
                                    </a>
                                @endif
                                @if($order->status === 'pending')
                                    <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }} không?')" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-danger-action" style="padding: 0.45rem 0.85rem; font-size: 0.82rem;">
                                            <i class="fa-solid fa-ban"></i> Hủy đơn
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- TAB 3: ĐỔI MẬT KHẨU -->
        <div id="tab-password" class="tab-pane">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><i class="fa-solid fa-lock"></i> Đổi Mật Khẩu Bảo Mật</h2>
                    <p class="section-subtitle">Đảm bảo tài khoản của bạn luôn an toàn với mật khẩu mạnh và duy nhất</p>
                </div>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST" style="max-width: 550px;">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">Mật khẩu hiện tại <span class="req">*</span></label>
                    <input type="password" name="current_password" id="current_password" class="form-input" required placeholder="Nhập mật khẩu bạn đang sử dụng">
                    @error('current_password')
                        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Mật khẩu mới <span class="req">*</span></label>
                    <input type="password" name="password" id="password" class="form-input" required placeholder="Tối thiểu 8 ký tự">
                    @error('password')
                        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Xác nhận mật khẩu mới <span class="req">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required placeholder="Nhập lại mật khẩu mới">
                    @error('password_confirmation')
                        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.85rem 1rem; border-radius: 8px; font-size: 0.82rem; color: #166534; margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-shield-halved"></i> <strong>Khuyến nghị an toàn:</strong> Sử dụng kết hợp chữ hoa, chữ thường, chữ số và ký tự đặc biệt để tài khoản được bảo vệ tốt nhất.
                </div>

                <button type="submit" class="btn-primary-action">
                    <i class="fa-solid fa-key"></i> Cập Nhật Mật Khẩu Mới
                </button>
            </form>
        </div>

        <!-- TAB 4: CÀI ĐẶT & BẢO MẬT -->
        <div id="tab-settings" class="tab-pane">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><i class="fa-solid fa-sliders"></i> Cài Đặt Tài Khoản</h2>
                    <p class="section-subtitle">Tùy chỉnh các thông báo hệ thống và quản lý trạng thái tài khoản</p>
                </div>
            </div>

            <!-- Notification Settings -->
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">
                    <i class="fa-solid fa-bell" style="color: var(--dmx-blue);"></i> Tùy chọn thông báo
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.9rem; color: #334155; cursor: pointer;">
                        <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: var(--dmx-blue);">
                        <span>Nhận thông báo ưu đãi và mã giảm giá lò vi sóng qua Email</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.9rem; color: #334155; cursor: pointer;">
                        <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: var(--dmx-blue);">
                        <span>Nhận tin nhắn SMS cập nhật tiến độ giao hàng và xác nhận đơn hàng COD</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.9rem; color: #334155; cursor: pointer;">
                        <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: var(--dmx-blue);">
                        <span>Nhắc nhở lịch bảo hành định kỳ 12 tháng từ kỹ thuật viên</span>
                    </label>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="danger-zone-card">
                <div class="danger-title">
                    <i class="fa-solid fa-triangle-exclamation"></i> Vùng Nguy Hiểm: Xóa Tài Khoản
                </div>
                <p class="danger-desc">
                    Khi bạn thực hiện xóa tài khoản, tất cả dữ liệu cá nhân, lịch sử đơn hàng và quyền lợi thành viên sẽ bị xóa vĩnh viễn khỏi hệ thống Siêu Lò Nướng và không thể hoàn tác.
                </p>
                <button type="button" class="btn-danger-action" onclick="openDeleteModal()">
                    <i class="fa-solid fa-trash-can"></i> Yêu Cầu Xóa Tài Khoản Vĩnh Viễn
                </button>
            </div>
        </div>

    </main>
</div>

<!-- Modal xác nhận xóa tài khoản -->
<div id="deleteAccountModal" class="modal-overlay">
    <div class="modal-box">
        <h3 style="font-size: 1.15rem; font-weight: 800; color: #991b1b; margin-bottom: 0.75rem;">
            <i class="fa-solid fa-triangle-exclamation"></i> Xác Nhận Xóa Tài Khoản
        </h3>
        <p style="font-size: 0.88rem; color: #475569; margin-bottom: 1.25rem; line-height: 1.5;">
            Bạn có chắc chắn muốn xóa tài khoản <strong>{{ $user->email }}</strong>? Vui lòng nhập mật khẩu hiện tại để xác nhận.
        </p>

        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="form-group">
                <label class="form-label" for="delete_password">Mật khẩu xác nhận <span class="req">*</span></label>
                <input type="password" name="password" id="delete_password" class="form-input" required placeholder="Nhập mật khẩu để xóa">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" onclick="closeDeleteModal()" style="background: #e2e8f0; border: none; padding: 0.65rem 1.1rem; border-radius: 6px; font-size: 0.88rem; font-weight: 600; cursor: pointer; color: #334155;">
                    Hủy bỏ
                </button>
                <button type="submit" class="btn-danger-action">
                    Xóa Tài Khoản
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab switching logic
    function switchProfileTab(tabName, event) {
        if (event) {
            event.preventDefault();
        }

        // Deactivate all menu items
        document.querySelectorAll('.profile-menu-item').forEach(item => {
            item.classList.remove('active');
        });

        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });

        // Activate matching menu item
        const matchingLink = document.querySelector(`.profile-menu-item[href="#${tabName}"]`);
        if (matchingLink) {
            matchingLink.classList.add('active');
        }

        // Show target tab
        const targetTab = document.getElementById(`tab-${tabName}`);
        if (targetTab) {
            targetTab.classList.add('active');
        }

        // Update URL hash without jumping
        if (history.pushState) {
            history.pushState(null, null, `#${tabName}`);
        } else {
            location.hash = `#${tabName}`;
        }
    }

    // Auto-detect tab from URL Hash or Query String
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const queryTab = urlParams.get('tab');
        const hashTab = window.location.hash.replace('#', '');
        
        const initialTab = queryTab || hashTab;
        if (initialTab && ['info', 'orders', 'password', 'settings'].includes(initialTab)) {
            switchProfileTab(initialTab);
        }
    });

    // Live avatar preview before submitting
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('formAvatarPreview').src = e.target.result;
                document.getElementById('sidebarAvatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    // Delete Modal controls
    function openDeleteModal() {
        document.getElementById('deleteAccountModal').style.display = 'flex';
    }
    function closeDeleteModal() {
        document.getElementById('deleteAccountModal').style.display = 'none';
    }
    window.onclick = function(event) {
        const modal = document.getElementById('deleteAccountModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection
