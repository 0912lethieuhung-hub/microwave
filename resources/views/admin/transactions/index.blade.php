@extends('layouts.admin')

@section('title', 'Giao dịch thanh toán')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 text-muted small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Quản trị</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giao dịch thanh toán</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-credit-card text-primary me-2"></i>Giao Dịch Thanh Toán</h2>
        <p class="text-muted mb-0">Theo dõi nhật ký các giao dịch trực tuyến qua MoMo, tiền mặt COD và đối soát.</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Tổng giao dịch</span>
                    <h4 class="fw-bold my-1 text-dark">{{ number_format($totalTransactions) }}</h4>
                    <small class="text-muted">Tất cả lượt giao dịch</small>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 text-center" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-receipt fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Thành công</span>
                    <h4 class="fw-bold my-1 text-success">{{ number_format($paidCount) }}</h4>
                    <small class="text-muted">Giao dịch đã thu tiền</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 text-center" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-circle-check fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Doanh thu giao dịch</span>
                    <h4 class="fw-bold my-1 text-primary">{{ number_format($totalPaidAmount, 0, ',', '.') }} đ</h4>
                    <small class="text-muted">Tổng tiền thực nhận</small>
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 text-center" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-wallet fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Thất bại / Hủy</span>
                    <h4 class="fw-bold my-1 text-danger">{{ number_format($failedCount) }}</h4>
                    <small class="text-muted">Không hoàn tất</small>
                </div>
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 text-center" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-circle-xmark fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Mã giao dịch MoMo, mã đơn #, tên khách..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="gateway" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả cổng thanh toán --</option>
                    <option value="momo" @selected(request('gateway') === 'momo')>Ví điện tử MoMo</option>
                    <option value="cod" @selected(request('gateway') === 'cod')>COD (Tiền mặt)</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="paid" @selected(request('status') === 'paid')>Đã thanh toán (Paid)</option>
                    <option value="pending" @selected(request('status') === 'pending')>Chờ thanh toán (Pending)</option>
                    <option value="failed" @selected(request('status') === 'failed')>Thất bại (Failed)</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy (Cancelled)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Lọc</button>
                @if(request()->hasAny(['search', 'gateway', 'status']))
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc"><i class="fa fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Đơn hàng</th>
                        <th>Cổng TT</th>
                        <th>Mã GD (Trans ID)</th>
                        <th>Khách hàng</th>
                        <th class="text-end">Số tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td class="text-muted fw-bold">#{{ $tx->id }}</td>
                        <td>
                            @if($tx->order)
                                <a href="{{ route('admin.orders.show', $tx->order->id) }}" class="fw-bold text-decoration-none">
                                    DH{{ str_pad((string) $tx->order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                            @else
                                <span class="text-muted">#{{ $tx->order_id }}</span>
                            @endif
                        </td>
                        <td>
                            @if($tx->gateway === 'momo')
                                <span class="badge" style="background-color: #a50064; color: #ffffff;">
                                    <i class="fa-solid fa-wallet me-1"></i> MoMo
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fa-solid fa-money-bill-wave me-1"></i> COD
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($tx->transaction_id)
                                <span class="font-monospace fw-semibold text-dark">{{ $tx->transaction_id }}</span>
                            @elseif($tx->gateway_order_id)
                                <span class="font-monospace text-muted small">{{ $tx->gateway_order_id }}</span>
                            @else
                                <span class="text-muted fst-italic">—</span>
                            @endif
                        </td>
                        <td>
                            @if($tx->order && $tx->order->user)
                                <div class="fw-semibold">{{ $tx->order->user->name }}</div>
                                <small class="text-muted">{{ $tx->order->phone ?? $tx->order->user->email }}</small>
                            @else
                                <span class="text-muted">Khách vãng lai</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-danger">
                            {{ number_format($tx->amount, 0, ',', '.') }} đ
                        </td>
                        <td class="text-center">
                            @php
                                $statusBadge = match($tx->status) {
                                    'paid' => ['bg-success text-white', 'fa-circle-check', 'ĐÃ THANH TOÁN'],
                                    'pending' => ['bg-warning text-dark', 'fa-clock', 'CHỜ THANH TOÁN'],
                                    'failed' => ['bg-danger text-white', 'fa-circle-xmark', 'THẤT BẠI'],
                                    'cancelled' => ['bg-secondary text-white', 'fa-ban', 'ĐÃ HỦY'],
                                    default => ['bg-secondary text-white', 'fa-question', strtoupper($tx->status)],
                                };
                            @endphp
                            <span class="badge {{ $statusBadge[0] }} px-2 py-1">
                                <i class="fa-solid {{ $statusBadge[1] }} me-1"></i> {{ $statusBadge[2] }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $tx->created_at ? $tx->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') : '—' }}</div>
                            <small class="text-muted">{{ $tx->created_at ? $tx->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') : '' }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fa-solid fa-credit-card fa-3x mb-3 text-secondary d-block"></i>
                            Chưa có giao dịch thanh toán nào phù hợp.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-center">
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
@endsection
