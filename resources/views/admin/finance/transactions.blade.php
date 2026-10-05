@extends('layouts.admin')

@section('title', 'Giao dịch thanh toán')

@section('content')
<!-- Breadcrumb -->
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 text-muted" style="font-size: 0.82rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Quản trị</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Giao dịch thanh toán</li>
        </ol>
    </nav>
</div>

<!-- Tiêu đề & Nút chuyển Tab -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.35rem;">Giao dịch thanh toán</h3>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">Tra cứu thanh toán theo đơn hàng và cập nhật trạng thái COD.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-secondary btn-sm px-3 fw-medium bg-white">
            <i class="fa-solid fa-chart-pie me-1"></i> Thống kê chỉ số
        </a>
        <a href="{{ route('admin.finance.transactions') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-medium">
            <i class="fa-solid fa-credit-card me-1"></i> Giao dịch thanh toán
        </a>
    </div>
</div>

<!-- Thông báo Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 mb-3" role="alert" style="font-size: 0.85rem;">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 mb-3" role="alert" style="font-size: 0.85rem;">
        <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first() }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Bộ lọc tìm kiếm -->
<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('admin.finance.transactions') }}" method="GET" id="transactionFilterForm">
            <div class="row g-3">
                <!-- Dòng 1: Tìm đơn hàng, Từ ngày, Đến ngày -->
                <div class="col-12 col-md-4">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Tìm đơn hàng</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Mã đơn, tên hoặc số điện thoại" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Từ ngày tạo đơn</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Đến ngày tạo đơn</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                </div>

                <!-- Dòng 2: Số tiền tối thiểu, Số tiền tối đa, Phương thức, Trạng thái -->
                <div class="col-6 col-md-3">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Số tiền tối thiểu</label>
                    <input type="number" name="min_amount" class="form-control form-control-sm" placeholder="Không giới hạn" min="0" step="1000" value="{{ $filters['min_amount'] ?? '' }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Số tiền tối đa</label>
                    <input type="number" name="max_amount" class="form-control form-control-sm" placeholder="Không giới hạn" min="0" step="1000" value="{{ $filters['max_amount'] ?? '' }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Phương thức</label>
                    <select name="gateway" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        @foreach($methods as $key => $label)
                            <option value="{{ $key }}" @selected(($filters['gateway'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Trạng thái thanh toán</label>
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" @selected(($filters['payment_status'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dòng 3: Sắp xếp & Nút hành động -->
                <div class="col-12 col-md-4">
                    <label class="form-label text-muted fw-medium mb-1" style="font-size: 0.8rem;">Sắp xếp</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="newest" @selected(($filters['sort'] ?? '') === 'newest')>Mới nhất</option>
                        <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Cũ nhất</option>
                        <option value="amount_asc" @selected(($filters['sort'] ?? '') === 'amount_asc')>Số tiền tăng dần</option>
                        <option value="amount_desc" @selected(($filters['sort'] ?? '') === 'amount_desc')>Số tiền giảm dần</option>
                    </select>
                </div>
                <div class="col-12 col-md-8 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fa-solid fa-filter me-1"></i> Áp dụng bộ lọc
                    </button>
                    <a href="{{ route('admin.finance.transactions') }}" class="btn btn-light btn-sm px-3 text-muted border">
                        <i class="fa-solid fa-rotate-left me-1"></i> Xóa bộ lọc
                    </a>
                </div>
            </div>
        </form>

        <div class="mt-3 pt-2 text-muted" style="font-size: 0.78rem;">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i> Có <strong>{{ number_format($orders->total()) }}</strong> đơn phù hợp bộ lọc. Số tiền bao gồm phí vận chuyển; ngày lọc là ngày tạo đơn.
        </div>
    </div>
</div>

<!-- Khối Danh Sách Giao Dịch -->
<div class="card shadow-sm border-0 bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h5 class="fw-bold mb-1 text-dark" style="font-size: 1rem;">
                <i class="fa-solid fa-list-check text-primary me-2"></i> Danh sách giao dịch ({{ number_format($orders->total()) }} đơn)
            </h5>
            <small class="text-muted" style="font-size: 0.78rem;">
                COD: xác nhận thu tiền hoặc thất bại; đơn đã thu tiền có thể chuyển sang chờ hoàn tiền rồi xác nhận đã hoàn tiền.
            </small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-light text-muted">
                <tr>
                    <th class="ps-3 py-2" style="min-width: 140px;">Đơn hàng</th>
                    <th class="py-2" style="min-width: 160px;">Khách hàng</th>
                    <th class="py-2 text-center" style="min-width: 110px;">Phương thức</th>
                    <th class="py-2 text-end" style="min-width: 130px;">Số tiền</th>
                    <th class="py-2 text-center" style="min-width: 160px;">Thanh toán</th>
                    <th class="pe-3 py-2 text-center" style="min-width: 220px;">Cập nhật COD</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $isCod = ($order->gateway === 'cod') || in_array($order->status, ['cod_ordered', 'cod_paid'], true) || ($order->payment_method ?? null) === 'cod';
                        $allowedTransitions = $codTransitions[$order->payment_status] ?? [];
                    @endphp
                    <tr>
                        <!-- 1. Đơn hàng -->
                        <td class="ps-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-primary text-decoration-none" title="Xem chi tiết đơn">
                                #{{ $order->id }}
                            </a>
                            <span class="text-muted d-block" style="font-size: 0.74rem;">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                            </span>
                        </td>

                        <!-- 2. Khách hàng -->
                        <td>
                            <div class="fw-semibold text-dark">{{ $order->name ?? 'Khách vãng lai' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-phone me-1"></i>{{ $order->phone ?? '—' }}
                            </div>
                        </td>

                        <!-- 3. Phương thức -->
                        <td class="text-center">
                            @if($order->gateway === 'cod')
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                    <i class="fa-solid fa-truck me-1"></i> COD
                                </span>
                            @elseif($order->gateway === 'momo')
                                <span class="badge bg-danger-subtle text-danger border px-2 py-1">
                                    <i class="fa-solid fa-qrcode me-1"></i> MoMo
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1">
                                    Chưa xác định
                                </span>
                            @endif
                        </td>

                        <!-- 4. Số tiền -->
                        <td class="text-end">
                            <span class="fw-bold text-dark" style="font-size: 0.92rem;">
                                {{ number_format((float)$order->total_price, 0, ',', '.') }} đ
                            </span>
                        </td>

                        <!-- 5. Trạng thái thanh toán -->
                        <td class="text-center">
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="fa-solid fa-circle-check me-1"></i> Đã thanh toán
                                </span>
                                @if($order->paid_at)
                                    <span class="d-block text-muted mt-1" style="font-size: 0.72rem;">
                                        {{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            @elseif($order->payment_status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="fa-solid fa-clock me-1"></i> Chờ thanh toán
                                </span>
                            @elseif($order->payment_status === 'initiated')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="fa-solid fa-mobile-screen-button me-1"></i> Đang chờ MoMo
                                </span>
                            @elseif($order->payment_status === 'failed')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> Thất bại
                                </span>
                            @elseif($order->payment_status === 'cancelled')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                    <i class="fa-solid fa-ban me-1"></i> Đã hủy
                                </span>
                            @elseif($order->payment_status === 'refund_pending')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="fa-solid fa-arrow-rotate-left me-1"></i> Chờ hoàn tiền
                                </span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fa-solid fa-hand-holding-dollar me-1"></i> Đã hoàn tiền
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1">
                                    {{ $statuses[$order->payment_status] ?? $order->payment_status }}
                                </span>
                            @endif
                        </td>

                        <!-- 6. Cập nhật COD -->
                        <td class="pe-3 text-center">
                            @if($isCod)
                                @if(count($allowedTransitions) > 1)
                                    <form action="{{ route('admin.finance.update-status', $order->id) }}" method="POST" class="d-inline-flex align-items-center justify-content-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="current_payment_status" value="{{ $order->payment_status }}">
                                        <input type="hidden" name="current_order_status" value="{{ $order->status }}">
                                        <input type="hidden" name="current_payment_id" value="{{ $order->payment_id ?? 0 }}">
                                        
                                        <select name="payment_status" class="form-select form-select-sm" style="min-width: 145px; font-size: 0.8rem; height: 31px;">
                                            @foreach($allowedTransitions as $trans)
                                                <option value="{{ $trans }}" @selected($trans === $order->payment_status)>
                                                    {{ $statuses[$trans] ?? $trans }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary px-2" style="height: 31px;" title="Cập nhật trạng thái COD" onclick="return confirm('Cập nhật trạng thái thanh toán đơn COD #{{ $order->id }}?')">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted" style="font-size: 0.78rem;">Không thể chuyển</span>
                                @endif
                            @else
                                <span class="text-muted" style="font-size: 0.78rem;">Tự động qua cổng</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                            Không có đơn hàng phù hợp với bộ lọc
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    @if($orders->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
