@extends('layouts.admin')

@section('title', 'Thống kê tài chính')

@section('content')
<!-- Breadcrumb -->
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 text-muted" style="font-size: 0.82rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Quản trị</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Thống kê tài chính</li>
        </ol>
    </nav>
</div>

<!-- Tiêu đề & Nút chuyển Tab -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.35rem;">Thống kê tài chính</h3>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">Tổng hợp giá trị thanh toán theo trạng thái và phương thức.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.finance.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-medium">
            <i class="fa-solid fa-chart-pie me-1"></i> Thống kê chỉ số
        </a>
        <a href="{{ route('admin.finance.transactions') }}" class="btn btn-outline-secondary btn-sm px-3 fw-medium bg-white">
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
        <form action="{{ route('admin.finance.index') }}" method="GET" id="financeFilterForm">
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
            </div>

            <!-- Nút bấm lọc -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2 border-top">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fa-solid fa-filter me-1"></i> Áp dụng bộ lọc
                </button>
                <a href="{{ route('admin.finance.index') }}" class="btn btn-light btn-sm px-3 text-muted border">
                    <i class="fa-solid fa-rotate-left me-1"></i> Xóa bộ lọc
                </a>
            </div>
        </form>

        <div class="mt-3 pt-2 text-muted" style="font-size: 0.78rem;">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i> Có <strong>{{ number_format((int)($summary->order_count ?? 0)) }}</strong> đơn phù hợp. Số tiền bao gồm phí vận chuyển; thống kê theo ngày tạo đơn toàn bộ kết quả lọc.
        </div>
    </div>
</div>

<!-- Lưới 8 Thẻ Thống Kê Chỉ Số -->
<div class="row g-3 mb-4">
    <!-- 1. Tổng giá trị đơn hàng -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #0284c7 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Tổng giá trị đơn hàng</span>
                    <span class="badge bg-light text-primary border"><i class="fa-solid fa-sack-dollar"></i></span>
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ number_format((float)($summary->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($summary->order_count ?? 0)) }} đơn, bao gồm đơn đã hủy
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Chờ thanh toán (pending) -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #f59e0b !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Chờ thanh toán</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="fa-solid fa-clock"></i></span>
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ number_format((float)($statusTotals['pending']->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($statusTotals['pending']->order_count ?? 0)) }} đơn
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Đang chờ MoMo (initiated) -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #d946ef !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Đang chờ MoMo</span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fa-solid fa-mobile-screen-button"></i></span>
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ number_format((float)($statusTotals['initiated']->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($statusTotals['initiated']->order_count ?? 0)) }} đơn
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Đã thanh toán (paid) -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Đã thanh toán</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-circle-check"></i></span>
                </div>
                <div class="fw-bold text-success fs-5 mb-1">
                    {{ number_format((float)($statusTotals['paid']->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($statusTotals['paid']->order_count ?? 0)) }} đơn
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Thanh toán thất bại (failed) -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #ef4444 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Thanh toán thất bại</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fa-solid fa-circle-xmark"></i></span>
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ number_format((float)($statusTotals['failed']->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($statusTotals['failed']->order_count ?? 0)) }} đơn
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Đã hủy (cancelled) -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #64748b !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Đã hủy</span>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="fa-solid fa-ban"></i></span>
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ number_format((float)($statusTotals['cancelled']->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($statusTotals['cancelled']->order_count ?? 0)) }} đơn
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Chờ hoàn tiền (refund_pending) -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #06b6d4 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Chờ hoàn tiền</span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fa-solid fa-arrow-rotate-left"></i></span>
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ number_format((float)($statusTotals['refund_pending']->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($statusTotals['refund_pending']->order_count ?? 0)) }} đơn
                </div>
            </div>
        </div>
    </div>

    <!-- 8. Đã hoàn tiền (refunded) -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 4px solid #8b5cf6 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.8rem;">Đã hoàn tiền</span>
                    <span class="badge bg-light text-dark border"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ number_format((float)($statusTotals['refunded']->total_amount ?? 0), 0, ',', '.') }} đ
                </div>
                <div class="text-muted" style="font-size: 0.76rem;">
                    {{ number_format((int)($statusTotals['refunded']->order_count ?? 0)) }} đơn
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bảng Thống Kê Theo Phương Thức -->
<div class="card shadow-sm border-0 bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
            <i class="fa-solid fa-wallet text-primary me-2"></i> Thống kê theo phương thức
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.86rem;">
            <thead class="table-light text-muted">
                <tr>
                    <th class="ps-3 py-2">Phương thức</th>
                    <th class="text-center py-2">Số đơn</th>
                    <th class="text-end py-2">Tổng giá trị</th>
                    <th class="text-end pe-3 py-2">Đã thanh toán</th>
                </tr>
            </thead>
            <tbody>
                @foreach($methods as $key => $label)
                    @php
                        $methodData = $methodTotals[$key] ?? null;
                        $count = $methodData->order_count ?? 0;
                        $total = (float)($methodData->total_amount ?? 0);
                        $paid = (float)($methodData->paid_amount ?? 0);
                    @endphp
                    <tr>
                        <td class="ps-3 fw-medium">
                            @if($key === 'cod')
                                <span class="badge bg-secondary-subtle text-secondary border me-1"><i class="fa-solid fa-truck"></i></span>
                            @elseif($key === 'momo')
                                <span class="badge bg-danger-subtle text-danger border me-1"><i class="fa-solid fa-qrcode"></i></span>
                            @else
                                <span class="badge bg-light text-muted border me-1"><i class="fa-solid fa-circle-question"></i></span>
                            @endif
                            {{ $label }}
                        </td>
                        <td class="text-center fw-semibold">{{ number_format($count) }}</td>
                        <td class="text-end fw-semibold text-dark">{{ number_format($total, 0, ',', '.') }} đ</td>
                        <td class="text-end pe-3 fw-bold text-success">{{ number_format($paid, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
