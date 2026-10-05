@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<!-- Breadcrumb -->
<div class="mb-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 text-muted" style="font-size: 0.82rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Quản trị</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Đơn hàng</li>
        </ol>
    </nav>
</div>

<!-- Header & Top Action Controls (Các ô đầu mục) -->
<div class="card shadow-sm border-0 mb-3 bg-white">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Title & Pagination Counter -->
            <div class="d-flex align-items-center gap-3">
                <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.35rem;">Đơn hàng</h3>
                
                @if($orders->total() > 0)
                    <div class="d-flex align-items-center gap-1 text-muted border rounded px-2 py-1 bg-light" style="font-size: 0.8rem;">
                        <a href="{{ $orders->previousPageUrl() ?: '#' }}" class="text-decoration-none text-muted {{ $orders->onFirstPage() ? 'opacity-50 pe-none' : '' }}" title="Trang trước">
                            <i class="fa-solid fa-chevron-left" style="font-size: 0.72rem;"></i>
                        </a>
                        <span class="px-1 fw-semibold">{{ $orders->firstItem() }}-{{ $orders->lastItem() }}/{{ $orders->total() }}</span>
                        <a href="{{ $orders->nextPageUrl() ?: '#' }}" class="text-decoration-none text-muted {{ !$orders->hasMorePages() ? 'opacity-50 pe-none' : '' }}" title="Trang sau">
                            <i class="fa-solid fa-chevron-right" style="font-size: 0.72rem;"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Top Controls Form -->
            <form action="{{ route('admin.orders.index') }}" method="GET" id="quickFilterForm" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1 justify-content-end">
                @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                @endif
                @if(request('gateway'))
                    <input type="hidden" name="gateway" value="{{ request('gateway') }}">
                @endif
                @if(request('date_from'))
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                @endif
                @if(request('date_to'))
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                @endif
                @if(request('shipping_status'))
                    <input type="hidden" name="shipping_status" value="{{ request('shipping_status') }}">
                @endif

                <!-- Hiển thị per_page -->
                <div style="min-width: 125px;">
                    <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('quickFilterForm').submit()">
                        <option value="15" @selected(request('per_page') == 15)>Hiển thị 15</option>
                        <option value="25" @selected(!request('per_page') || request('per_page') == 25)>Hiển thị 25</option>
                        <option value="50" @selected(request('per_page') == 50)>Hiển thị 50</option>
                        <option value="100" @selected(request('per_page') == 100)>Hiển thị 100</option>
                    </select>
                </div>

                <!-- Lọc trạng thái thanh toán -->
                <div style="min-width: 155px;">
                    <select name="payment_status" class="form-select form-select-sm" onchange="document.getElementById('quickFilterForm').submit()">
                        <option value="">Tất cả thanh toán</option>
                        @foreach($paymentLabels as $val => $lbl)
                            <option value="{{ $val }}" @selected(request('payment_status') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div class="input-group input-group-sm" style="max-width: 320px;">
                    <input type="text" name="search" class="form-control" placeholder="Mã đơn, khách hàng, SĐT, sản phẩm..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    </button>
                </div>

                <!-- Refresh / Reset -->
                <a href="{{ route('admin.orders.index', request()->only('tab')) }}" class="btn btn-sm btn-outline-secondary" title="Làm mới bộ lọc">
                    <i class="fa-solid fa-rotate"></i>
                </a>

                <!-- Nút Xuất trang này (Export CSV/Excel) -->
                <a href="{{ route('admin.orders.index', array_merge(request()->all(), ['export' => 1])) }}" class="btn btn-sm btn-success fw-semibold shadow-sm d-inline-flex align-items-center gap-1" style="background-color: #198754; border-color: #198754;">
                    <i class="fa-solid fa-file-excel"></i> Xuất trang này
                </a>
            </form>
        </div>

        <!-- Toggle Bộ lọc nâng cao (Accordion) -->
        <div class="mt-2 pt-2 border-top">
            <button class="btn btn-link text-decoration-none text-muted p-0 d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="{{ request()->hasAny(['gateway', 'date_from', 'date_to', 'shipping_status']) ? 'true' : 'false' }}" style="font-size: 0.83rem;">
                <i class="fa-solid fa-caret-down text-secondary"></i>
                <span class="fw-semibold">Bộ lọc nâng cao</span>
                @if(request()->hasAny(['gateway', 'date_from', 'date_to', 'shipping_status']))
                    <span class="badge bg-primary ms-1" style="font-size: 0.65rem;">Đang lọc</span>
                @endif
            </button>

            <div class="collapse {{ request()->hasAny(['gateway', 'date_from', 'date_to', 'shipping_status']) ? 'show' : '' }} mt-3" id="advancedFilters">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center bg-light p-3 rounded-2 border">
                    @if(request('tab'))
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('payment_status'))
                        <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
                    @endif
                    @if(request('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-muted small mb-1 fw-semibold">Cổng thanh toán</label>
                        <select name="gateway" class="form-select form-select-sm">
                            <option value="">-- Tất cả cổng --</option>
                            <option value="cod" @selected(request('gateway') === 'cod')>COD (Tiền mặt)</option>
                            <option value="momo" @selected(request('gateway') === 'momo')>Ví MoMo</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-muted small mb-1 fw-semibold">Trạng thái vận chuyển GHN</label>
                        <select name="shipping_status" class="form-select form-select-sm">
                            <option value="">-- Tất cả trạng thái VC --</option>
                            @foreach($shippingLabels as $val => $lbl)
                                <option value="{{ $val }}" @selected(request('shipping_status') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-muted small mb-1 fw-semibold">Từ ngày</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-muted small mb-1 fw-semibold">Đến ngày</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2 col-sm-12 d-flex align-items-end gap-1 mt-auto">
                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Áp dụng</button>
                        <a href="{{ route('admin.orders.index', request()->only(['tab', 'search', 'per_page'])) }}" class="btn btn-sm btn-outline-secondary" title="Đặt lại bộ lọc nâng cao">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 8 Status Filter Tabs (Chuẩn theo hình ảnh mẫu) -->
<div class="card shadow-sm border-0 mb-3 bg-white">
    <div class="card-body p-2">
        <ul class="nav nav-pills flex-wrap gap-1" style="font-size: 0.84rem;">
            @foreach($tabs as $key => $tab)
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('tab', 'page'), ['tab' => $key])) }}" 
                       class="nav-link {{ $activeTab === $key ? 'active' : '' }} py-2 px-3 fw-semibold text-uppercase"
                       style="{{ $activeTab === $key ? 'background-color: #0d6efd;' : 'color: #495057;' }}">
                        {{ $tab['label'] }}
                        <span class="badge {{ $activeTab === $key ? 'bg-white text-primary' : 'bg-light text-secondary border' }} ms-1">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- Dòng thông tin phụ: Tổng số đơn và cập nhật GHN -->
<div class="d-flex justify-content-between align-items-center mb-2 px-1 text-muted" style="font-size: 0.82rem;">
    <div>
        <span class="fw-semibold text-dark">{{ $orders->total() }}</span> đơn hàng trong danh sách
    </div>
    <div class="d-flex align-items-center gap-2">
        <span>Trạng thái vận chuyển được cập nhật từ GHN</span>
        <form action="{{ route('admin.orders.sync-all-ghn') }}" method="POST" class="d-inline m-0">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-decoration-none text-primary" title="Đồng bộ ngay">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </form>
    </div>
</div>

<!-- FORM CẬP NHẬT TRẠNG THÁI HÀNG LOẠT (TỰ ĐỘNG GẮN VỚI CHECKBOX QUA FORM ATTRIBUTE) -->
<div id="bulkActionBar" class="card shadow-sm border-primary mb-3 bg-white" style="display: none; border-left: 4px solid #0d6efd !important;">
    <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background-color: #f0f7ff;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-3 py-2 fs-6 shadow-sm">
                <i class="fa-solid fa-check-double me-1"></i> Đã chọn <span id="selectedCountText" class="fw-bold">0</span> đơn hàng
            </span>
            <span class="text-dark small fw-semibold d-none d-md-inline">Chọn trạng thái để cập nhật cho các đơn đã tick chọn:</span>
        </div>

        <form action="{{ route('admin.orders.bulk-status') }}" method="POST" id="bulkStatusForm" class="d-flex align-items-center gap-2 m-0 flex-wrap" onsubmit="return confirmBulkAction()">
            @csrf
            <select name="status" class="form-select form-select-sm border-primary" required style="min-width: 210px; font-weight: 500;">
                <option value="">-- Đổi trạng thái sang --</option>
                <option value="pending">🟡 Chờ xử lý (Pending)</option>
                <option value="processing">🔵 Đang chuẩn bị (Processing)</option>
                <option value="completed">🟢 Hoàn tất đơn (Completed)</option>
                <option value="cancelled">🔴 Hủy đơn hàng (Cancelled)</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm fw-semibold shadow-sm text-nowrap px-3">
                <i class="fa-solid fa-arrows-rotate me-1"></i> Áp dụng thay đổi
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm text-nowrap" id="btnDeselectAll">
                <i class="fa-solid fa-xmark me-1"></i> Bỏ chọn
            </button>
        </form>
    </div>
</div>

<!-- Orders Table (ĐÚNG CHUẨN 10 CỘT NHƯ ẢNH MẪU ĐẦU TIÊN CỦA BÀI LAB) -->
<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.86rem;">
                <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: none; border-bottom: 2px solid #e9ecef;">
                    <tr>
                        <th style="width: 35px;" class="text-center">
                            <input type="checkbox" class="form-check-input" id="checkAllOrders" title="Chọn tất cả">
                        </th>
                        <th style="min-width: 135px;">Mã đơn hàng</th>
                        <th style="min-width: 110px;">Ngày tạo đơn</th>
                        <th style="min-width: 190px;">Sản phẩm</th>
                        <th class="text-end" style="min-width: 100px;">Tổng tiền</th>
                        <th class="text-end" style="min-width: 95px;">COD cần thu</th>
                        <th style="min-width: 130px;">Tên khách hàng</th>
                        <th style="min-width: 110px;">Mã vận đơn</th>
                        <th style="min-width: 140px;">Trạng thái giao hàng</th>
                        <th class="text-center" style="min-width: 80px;">Đơn vị VC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                    @php
                        // Trạng thái hủy đơn / hủy vận đơn
                        $isCancelled = in_array($order->status, ['cancelled', 'canceled'], true) 
                                       || $order->shipping_status === 'cancelled'
                                       || $order->payment_status === 'cancelled';

                        // Trạng thái thanh toán thất bại
                        $isPaymentFailed = ! $isCancelled && $order->payment_status === 'failed';

                        // Trạng thái đã thanh toán hợp lệ (không áp dụng nếu đơn đã hủy hoặc thanh toán thất bại)
                        $isPaid = ! $isCancelled && ! $isPaymentFailed && (
                            in_array($order->payment_status, ['paid', 'completed'], true) 
                            || in_array($order->status, ['paid', 'cod_paid', 'paid_momo', 'completed'], true)
                        );
                        
                        $paymentState = match(true) {
                            $order->payment_status === 'refund_pending' || $order->payment_status === 'refunded' => [
                                'class' => 'bg-info text-white', 
                                'text' => 'CHỜ HOÀN TIỀN'
                            ],
                            $isCancelled => [
                                'class' => 'bg-secondary text-white', 
                                'text' => 'ĐÃ HỦY'
                            ],
                            $isPaymentFailed => [
                                'class' => 'bg-danger text-white', 
                                'text' => 'THANH TOÁN THẤT BẠI'
                            ],
                            $isPaid => [
                                'class' => 'bg-success text-white', 
                                'text' => 'ĐÃ THANH TOÁN'
                            ],
                            $order->gateway === 'momo' || $order->payment_method === 'momo' => [
                                'class' => 'bg-warning text-dark', 
                                'text' => 'ĐANG CHỜ MOMO'
                            ],
                            default => [
                                'class' => 'bg-warning text-dark', 
                                'text' => 'CHỜ THANH TOÁN'
                            ],
                        };

                        // Tiền COD cần thu: Chỉ áp dụng cho đơn COD chưa thanh toán, chưa hoàn tất và chưa bị hủy
                        $isOnlinePayment = ($order->gateway === 'momo' || $order->payment_method === 'momo');
                        $isDeliveredOrCompleted = ($order->status === 'completed' || $order->shipping_status === 'delivered');
                        
                        $codAmount = ($isPaid || $isCancelled || $isOnlinePayment || $isDeliveredOrCompleted) ? 0 : $order->total_price;

                        // Dot màu trạng thái vận chuyển
                        $shippingDot = match($order->shipping_status) {
                            'ready_to_pick' => 'text-warning',
                            'picking', 'picked', 'storing', 'transporting', 'sorting', 'delivering' => 'text-primary',
                            'delivered' => 'text-success',
                            'return', 'returning', 'returned' => 'text-danger',
                            'cancelled' => 'text-secondary',
                            default => 'text-muted',
                        };

                        $isDelivering = in_array($order->shipping_status, ['delivering', 'picked', 'storing', 'transporting', 'sorting']);
                    @endphp
                    <tr>
                        <!-- 1. Checkbox gắn trực tiếp với form bulkStatusForm -->
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input order-checkbox" name="order_ids[]" value="{{ $order->id }}" form="bulkStatusForm">
                        </td>

                        <!-- 2. Mã đơn hàng & Badge thanh toán kèm Dropdown đổi trạng thái nhanh -->
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-primary text-decoration-none font-monospace" style="font-size: 0.9rem;" title="Bấm để xem chi tiết đơn hàng #{{ $order->id }}">
                                    DH{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>

                                <!-- Menu đổi trạng thái nhanh gọn gàng ngay cạnh mã đơn -->
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-link p-0 text-muted border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Đổi trạng thái đơn này">
                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.78rem;"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow border" style="font-size: 0.82rem; min-width: 170px;">
                                        <li class="dropdown-header small text-muted">Đổi trạng thái #{{ $order->id }}</li>
                                        <li>
                                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="dropdown-item py-1 {{ $order->status === 'pending' ? 'active disabled' : '' }}">
                                                    🟡 Chờ xử lý
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="dropdown-item py-1 {{ $order->status === 'processing' ? 'active disabled' : '' }}">
                                                    🔵 Đang chuẩn bị
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            @if($isPaymentFailed)
                                                <button type="button" class="dropdown-item py-1 text-muted disabled" title="Không thể hoàn tất đơn khi thanh toán trực tuyến bị THẤT BẠI!">
                                                    ⚠️ Không thể hoàn tất (Thanh toán lỗi)
                                                </button>
                                            @else
                                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="dropdown-item py-1 text-success fw-semibold {{ $order->status === 'completed' ? 'active disabled' : '' }}">
                                                        🟢 Hoàn tất đơn
                                                    </button>
                                                </form>
                                            @endif
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            @if($isDelivering)
                                                <button type="button" class="dropdown-item py-1 text-muted disabled" title="Đơn hàng đang giao GHN, KHÔNG thể hủy!">
                                                    🚫 Không thể hủy (Đang giao)
                                                </button>
                                            @else
                                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng #{{ $order->id }}? Số lượng tồn kho sẽ được tự động hoàn lại.')">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="dropdown-item py-1 text-danger {{ $order->status === 'cancelled' ? 'disabled' : '' }}">
                                                        🔴 Hủy đơn hàng
                                                    </button>
                                                </form>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-1">
                                <span class="badge {{ $paymentState['class'] }}" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2px; padding: 3px 6px;">
                                    {{ $paymentState['text'] }}
                                </span>
                            </div>
                        </td>

                        <!-- 3. Ngày tạo đơn -->
                        <td>
                            <div class="text-dark">{{ $order->created_at ? $order->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') : '-' }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $order->created_at ? $order->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') : '' }}</small>
                        </td>

                        <!-- 4. Sản phẩm -->
                        <td>
                            @if($order->items && $order->items->count() > 0)
                                @foreach($order->items as $item)
                                    <div class="text-truncate text-dark" style="max-width: 210px;" title="{{ $item->product->name ?? 'Sản phẩm' }}">
                                        {{ $item->product->name ?? 'Sản phẩm #' . $item->product_id }}
                                        <span class="text-muted fw-semibold">× {{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted fst-italic">Không có sản phẩm</span>
                            @endif
                        </td>

                        <!-- 5. Tổng tiền -->
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($order->total_price, 0, ',', '.') }}
                            <small class="text-muted fw-normal">đ</small>
                        </td>

                        <!-- 6. COD cần thu -->
                        <td class="text-end fw-semibold text-dark">
                            {{ number_format($codAmount, 0, ',', '.') }}
                            <small class="text-muted fw-normal">đ</small>
                        </td>

                        <!-- 7. Tên khách hàng & SĐT -->
                        <td>
                            <div class="fw-bold text-dark">{{ $order->user->name ?? ($order->name ?? 'Khách vãng lai') }}</div>
                            <div class="text-muted small" style="font-size: 0.78rem;">
                                {{ $order->phone ?? ($order->user->phone ?? '—') }}
                            </div>
                        </td>

                        <!-- 8. Mã vận đơn -->
                        <td>
                            @if($order->ghn_order_code)
                                <a href="{{ config('services.ghn.portal_url', 'https://5sao.ghn.dev') }}/?order_code={{ $order->ghn_order_code }}" target="_blank" class="fw-bold text-primary font-monospace text-decoration-none" title="Tra cứu trên GHN">
                                    {{ $order->ghn_order_code }}
                                </a>
                            @else
                                <span class="text-muted small fst-italic">Chưa có vận đơn</span>
                            @endif
                        </td>

                        <!-- 9. Trạng thái giao hàng -->
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle {{ $shippingDot }}" style="font-size: 0.55rem;"></i>
                                <span class="text-dark">{{ $order->shipping_badge['label'] ?? 'Chờ tạo vận đơn' }}</span>
                            </div>
                        </td>

                        <!-- 10. Đơn vị vận chuyển (Cột cuối cùng khớp chuẩn 100% ảnh mẫu) -->
                        <td class="text-center text-secondary fw-semibold">
                            {{ $order->ghn_order_code ? 'GHN' : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary d-block"></i>
                            Không tìm thấy đơn hàng nào phù hợp với điều kiện tìm kiếm.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table Footer: Pagination -->
    <div class="card-footer bg-white py-3 border-top">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted small">
                Hiển thị {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} trong {{ $orders->total() }} đơn hàng
            </div>
            <div>
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAllOrders');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const bulkBar = document.getElementById('bulkActionBar');
    const countText = document.getElementById('selectedCountText');
    const deselectBtn = document.getElementById('btnDeselectAll');

    function updateBulkBar() {
        const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkBar.style.display = 'block';
            countText.textContent = count;
        } else {
            bulkBar.style.display = 'none';
        }

        if (checkAll) {
            checkAll.checked = (count === checkboxes.length && checkboxes.length > 0);
            checkAll.indeterminate = (count > 0 && count < checkboxes.length);
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
            updateBulkBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    if (deselectBtn) {
        deselectBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            if (checkAll) {
                checkAll.checked = false;
                checkAll.indeterminate = false;
            }
            updateBulkBar();
        });
    }
});

function confirmBulkAction() {
    const form = document.getElementById('bulkStatusForm');
    const select = form.querySelector('select[name="status"]');
    const count = document.getElementById('selectedCountText').textContent;
    
    if (!select.value) {
        alert('Vui lòng chọn trạng thái cần chuyển đổi!');
        select.focus();
        return false;
    }

    const label = select.options[select.selectedIndex].text;
    return confirm(`Bạn có chắc chắn muốn chuyển ${count} đơn hàng đã chọn sang trạng thái: "${label}"?`);
}
</script>
@endsection
