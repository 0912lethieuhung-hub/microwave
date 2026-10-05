<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SHOP ADMIN - Quản trị hệ thống')</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #0071af;
            --primary-dark: #005a8c;
            --sidebar-width: 240px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-text: #94a3b8;
            --sidebar-active: #2563eb;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-size: 0.88rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* LAYOUT CHÍNH: SIDEBAR TRÁI + NỘI DUNG PHẢI */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* SIDEBAR TỐI MÀU */
        .admin-sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 1030;
            transition: all 0.25s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand i {
            font-size: 1.25rem;
            color: #38bdf8;
        }

        .sidebar-user {
            padding: 1.25rem 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .user-avatar-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #475569;
            color: #ffffff;
            font-size: 1.35rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.6rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
            object-fit: cover;
        }

        .user-title-name {
            color: #f8fafc;
            font-weight: 600;
            font-size: 0.92rem;
            line-height: 1.2;
        }

        .user-sub-title {
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* SIDEBAR NAV MENU */
        .sidebar-menu {
            list-style: none;
            padding: 0.85rem 0.6rem;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.62rem 0.9rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.86rem;
            border-radius: 6px;
            transition: all 0.15s ease;
        }

        .sidebar-link i {
            width: 20px;
            font-size: 0.95rem;
            text-align: center;
            opacity: 0.85;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .sidebar-link.active {
            color: #ffffff;
            background-color: var(--sidebar-hover);
            font-weight: 600;
            border-left: 3px solid #38bdf8;
        }

        .sidebar-link.active i {
            color: #38bdf8;
            opacity: 1;
        }

        /* FOOTER SIDEBAR: LOGOUT */
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .btn-sidebar-logout {
            width: 100%;
            padding: 0.5rem;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #cbd5e1;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-sidebar-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: #ef4444;
            color: #ef4444;
        }

        /* MAIN CONTENT AREA */
        .admin-main-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* TOPBAR HEADER */
        .admin-topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .admin-main-content {
            flex: 1 0 auto;
            padding: 1.5rem;
        }

        /* RESPONSIVE */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                left: -240px;
                top: 0;
                bottom: 0;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            }
            .admin-sidebar.show {
                left: 0;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1025;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @yield('extra-css')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Backdrop (Mobile) -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Sidebar bên trái -->
        <aside class="admin-sidebar" id="adminSidebar">
            <!-- Brand -->
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <i class="fa-solid fa-shapes"></i>
                <span>SHOP ADMIN</span>
            </a>

            <!-- User Info -->
            <div class="sidebar-user">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar_url }}" alt="" class="user-avatar-circle">
                @else
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                @endif
                <div class="user-title-name">{{ Auth::user()->name ?? 'Admin User' }}</div>
                <div class="user-sub-title">QUẢN LÝ BÁN HÀNG</div>
            </div>

            <!-- Navigation Menu (8 đầu mục chuẩn theo hình ảnh mẫu) -->
            <ul class="sidebar-menu">
                <!-- 1. Dashboard -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 2. Sản phẩm -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box-archive"></i>
                        <span>Sản phẩm</span>
                    </a>
                </li>

                <!-- 3. Danh mục -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Danh mục</span>
                    </a>
                </li>

                <!-- 4. Người dùng -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Người dùng</span>
                    </a>
                </li>

                <!-- 5. Đơn hàng -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Đơn hàng</span>
                    </a>
                </li>

                <!-- 6. Thống kê tài chính -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.finance.index') }}" class="sidebar-link {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Thống kê tài chính</span>
                    </a>
                </li>

                <!-- 7. Giao dịch thanh toán -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.finance.transactions') }}" class="sidebar-link {{ request()->routeIs('admin.finance.transactions') ? 'active' : '' }}">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Giao dịch thanh toán</span>
                    </a>
                </li>

                <!-- 8. Báo cáo (Biểu đồ) -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.reports.charts') }}" class="sidebar-link {{ request()->routeIs('admin.reports.charts') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Báo cáo</span>
                    </a>
                </li>

                <!-- 9. Khuyến Mãi (Vouchers) -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.vouchers.index') }}" class="sidebar-link {{ request()->routeIs('admin.vouchers*') ? 'active' : '' }}">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Khuyến mãi</span>
                    </a>
                </li>
            </ul>

            <!-- Footer: Đăng xuất -->
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-sidebar-logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="admin-main-wrapper">
            <!-- Topbar Header -->
            <header class="admin-topbar">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <span class="text-muted d-none d-sm-inline" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-shop me-1 text-primary"></i> Hộ Kinh Doanh Siêu Lò Nướng
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="font-size: 0.82rem;">
                        <i class="fa-solid fa-globe"></i> Xem Website
                    </a>

                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold text-dark" style="font-size: 0.85rem;">{{ Auth::user()->name }}</span>
                        <span class="badge bg-light text-secondary border" style="font-size: 0.72rem;">Quản trị viên</span>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="admin-main-content">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 py-2 px-3 mb-3" role="alert" style="font-size: 0.88rem;">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ $message }}
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($error = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 py-2 px-3 mb-3" role="alert" style="font-size: 0.88rem;">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $error }}
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($info = Session::get('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 py-2 px-3 mb-3" role="alert" style="font-size: 0.88rem;">
                        <i class="fa-solid fa-circle-info me-2"></i> {{ $info }}
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Admin Footer -->
            <footer class="bg-white border-top text-center py-3 text-muted small mt-auto">
                &copy; {{ date('Y') }} <strong>SHOP ADMIN</strong> - Quản lý bán hàng & Vận hành logistics GHN.
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (toggleBtn) {
            toggleBtn.onclick = () => {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            };
        }
        if (backdrop) {
            backdrop.onclick = () => {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            };
        }
    </script>

    <!-- Admin Livechat Popup Styles & Markup (Giữ nguyên tính năng đã fix) -->
    <style>
        #admin-chat-box {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1050;
        }
        #admin-chat-box #chat-toggle {
            border-radius: 50px;
            padding: 10px 22px;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
            transition: all 0.2s ease;
        }
        #admin-chat-box #chat-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.35);
        }
        #admin-chat-box #chat-popup {
            width: 520px;
            max-width: calc(100vw - 40px);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.15);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
            background: #ffffff;
        }
        #admin-chat-box .user-item {
            padding: 9px 12px;
            cursor: pointer;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.85rem;
            color: #495057;
            transition: background 0.15s;
        }
        #admin-chat-box .user-item:hover {
            background: #e9ecef;
        }
        #admin-chat-box .user-item.active {
            background: #0071af;
            color: #ffffff;
            font-weight: 600;
        }
        #admin-chat-box .user-item.active .text-muted {
            color: rgba(255, 255, 255, 0.75) !important;
        }
        #admin-chat-box .msg-wrap {
            display: flex;
            margin-bottom: 10px;
        }
        #admin-chat-box .msg-wrap.mine {
            justify-content: flex-end;
        }
        #admin-chat-box .msg-wrap.theirs {
            justify-content: flex-start;
        }
        #admin-chat-box .msg-bubble {
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.88rem;
            line-height: 1.45;
            word-break: break-word;
        }
        #admin-chat-box .msg-wrap.mine .msg-bubble {
            background: #0071af;
            color: #ffffff;
            border-bottom-right-radius: 3px;
        }
        #admin-chat-box .msg-wrap.theirs .msg-bubble {
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 3px;
        }
        #admin-chat-box .msg-label {
            font-size: 0.7rem;
            margin-bottom: 3px;
            color: #64748b;
        }
        #admin-chat-box .msg-wrap.mine .msg-label { text-align: right; }
        #admin-chat-box .msg-wrap.theirs .msg-label { text-align: left; }
        #admin-chat-box .msg-time {
            font-size: 0.68rem;
            color: #94a3b8;
            margin-top: 3px;
        }
        #admin-chat-box .msg-wrap.mine .msg-time { text-align: right; }
        #admin-chat-box .msg-wrap.theirs .msg-time { text-align: left; }
    </style>

    <div id="admin-chat-box">
        <button id="chat-toggle" class="btn btn-dark shadow">💬 Chat Khách hàng</button>
        <div id="chat-popup" class="card shadow-lg" style="display:none;">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <strong>Hỗ trợ trực tuyến</strong>
                <button id="chat-close" class="btn btn-sm btn-light">X</button>
            </div>
            <div class="d-flex" style="height: 380px; overflow: hidden;">
                <div id="user-list" style="width: 175px; border-right: 1px solid #dee2e6; overflow-y: auto; background: #f8f9fa;">
                    <div class="p-2 text-center text-muted"><small>Đang tải danh sách...</small></div>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                    <div id="chat-messages" style="flex: 1; overflow-y: auto; padding: 12px; background: #ffffff;">
                        <div class="text-center mt-5 text-muted">Chọn một khách hàng để xem tin nhắn</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Nhập câu trả lời...">
                    <div class="input-group-append ms-1">
                        <button id="send-btn" class="btn btn-success btn-sm">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    let currentUserId = null;
    const chatPopup = document.getElementById("chat-popup");
    const chatMessages = document.getElementById("chat-messages");
    const chatInput = document.getElementById("chat-input");

    // Mở/Đóng popup
    document.getElementById("chat-toggle").onclick = () => {
        chatPopup.style.display = "block";
        loadUsers();
    };
    document.getElementById("chat-close").onclick = () => {
        chatPopup.style.display = "none";
    };

    // 1. Load danh sách User
    function loadUsers() {
        fetch("{{ route('admin.chat.users') }}")
            .then(res => res.json())
            .then(users => {
                let html = "";
                if (!users || users.length === 0) {
                    document.getElementById("user-list").innerHTML = '<div class="p-2 text-center text-muted"><small>Chưa có hội thoại</small></div>';
                    return;
                }

                users.forEach(user => {
                    let activeClass = (currentUserId == user.id) ? 'active' : '';
                    html += `
                    <div class="user-item ${activeClass}" data-user-id="${user.id}" onclick="selectUser(${user.id}, this)">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-truncate" style="max-width: 100px;">${user.name}</strong>
                            ${user.last_time ? `<span class="user-time text-muted" style="font-size: 0.68rem;">${user.last_time}</span>` : ''}
                        </div>
                        ${user.last_message ? `<div class="user-last-msg text-truncate text-muted mt-1" style="font-size: 0.74rem;">${user.last_message}</div>` : ''}
                    </div>`;
                });
                document.getElementById("user-list").innerHTML = html;

                if (!currentUserId && users.length > 0) {
                    const firstItem = document.querySelector('.user-item');
                    if (firstItem) {
                        selectUser(users[0].id, firstItem);
                    }
                }
            })
            .catch(err => console.error("Lỗi tải users:", err));
    }

    // 2. Chọn User
    function selectUser(userId, element) {
        currentUserId = userId;
        document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        loadMessages();
    }

    // 3. Load tin nhắn
    function loadMessages() {
        if (!currentUserId) return;
        fetch(`/admin/chat/messages/${currentUserId}`)
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(messages => {
                if (!Array.isArray(messages)) return;

                if (messages.length === 0) {
                    chatMessages.innerHTML = '<div class="text-center text-muted mt-4" style="font-size:.85rem">Chưa có tin nhắn nào</div>';
                    return;
                }

                let html = '';
                messages.forEach(msg => {
                    const mine = msg.is_mine === true;
                    const label = mine ? 'Bạn (Admin)' : (msg.sender ? msg.sender.name : 'Khách hàng');
                    const time = msg.time_formatted || '';
                    html += `
                        <div class="msg-wrap ${mine ? 'mine' : 'theirs'}">
                            <div style="max-width: 80%;">
                                <div class="msg-label">${label}</div>
                                <div class="msg-bubble">${msg.content}</div>
                                ${time ? `<div class="msg-time">${time}</div>` : ''}
                            </div>
                        </div>
                    `;
                });

                chatMessages.innerHTML = html;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(err => console.error('Lỗi tải tin nhắn:', err));
    }

    // 4. Gửi tin nhắn
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message || !currentUserId) return;

        const sendBtn = document.getElementById('send-btn');
        sendBtn.disabled = true;
        chatInput.disabled = true;

        fetch("{{ route('admin.chat.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message,
                user_id: currentUserId
            })
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(() => {
            chatInput.value = '';
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.focus();
            loadMessages();
        })
        .catch(err => {
            console.error('Lỗi gửi tin:', err);
            chatInput.disabled = false;
            sendBtn.disabled = false;
        });
    }

    document.getElementById("send-btn").onclick = sendMessage;
    chatInput.onkeypress = (e) => { if(e.key === 'Enter') sendMessage(); };

    // Polling
    setInterval(() => {
        if (chatPopup && chatPopup.style.display === 'block' && currentUserId) {
            loadMessages();
        }
    }, 3000);

    setInterval(() => {
        if (chatPopup && chatPopup.style.display === 'block') {
            loadUsers();
        }
    }, 8000);
    </script>

    @yield('scripts')
</body>
</html>
