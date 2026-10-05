@extends('layouts.admin')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1"><i class="fa fa-chart-line text-primary me-2"></i>Bảng Điều Khiển & Thống Kê</h2>
        <p class="text-muted mb-0">Theo dõi doanh thu, đơn hàng, khách hàng và hiệu suất bán hàng thời gian thực.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <span class="badge bg-light text-dark border p-2 px-3 shadow-sm">
            <i class="fa fa-calendar-alt text-primary me-1"></i> Hôm nay: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </span>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: #fff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tổng Doanh Thu</span>
                        <h4 class="fw-bold my-1 text-white">{{ number_format($totalRevenue, 0, ',', '.') }}₫</h4>
                        <small class="text-white-50"><i class="fa fa-calendar-days me-1"></i>Tháng này: {{ number_format($thisMonthRevenue, 0, ',', '.') }}₫</small>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 text-center" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-wallet fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%); color: #fff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tổng Đơn Hàng</span>
                        <h4 class="fw-bold my-1 text-white">{{ number_format($totalOrders) }}</h4>
                        <small class="text-white-50"><i class="fa fa-shopping-cart me-1"></i>Đã phát sinh trên web</small>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 text-center" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-receipt fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #212529;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-dark fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.8;">Đơn Chờ Xử Lý</span>
                        <h4 class="fw-bold my-1 text-dark">{{ number_format($pendingOrders) }}</h4>
                        <small class="text-dark opacity-75"><i class="fa fa-clock me-1"></i>Cần kiểm tra duyệt đơn</small>
                    </div>
                    <div class="rounded-circle bg-dark bg-opacity-10 p-3 text-center" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-hourglass-half fa-lg text-dark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%); color: #fff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Khách Hàng / Lượt Xem</span>
                        <h4 class="fw-bold my-1 text-white">{{ number_format($totalCustomers) }} / {{ number_format($totalVisits) }}</h4>
                        <small class="text-white-50"><i class="fa fa-user-plus me-1"></i>+{{ number_format($newCustomersThisMonth) }} khách mới tháng này</small>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 text-center" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-chart-pie fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Revenue Trend -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0"><i class="fa fa-chart-area text-primary me-2"></i>Doanh Thu {{ $chartDays }} Ngày Gần Nhất</h5>
                    <small class="text-muted">Đơn vị: Việt Nam Đồng (VND)</small>
                </div>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.dashboard', ['days' => 7]) }}" class="btn {{ $chartDays == 7 ? 'btn-primary' : 'btn-outline-secondary' }}">
                        7 ngày
                    </a>
                    <a href="{{ route('admin.dashboard', ['days' => 30]) }}" class="btn {{ $chartDays == 30 ? 'btn-primary' : 'btn-outline-secondary' }}">
                        30 ngày
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div style="position: relative; height: 310px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Doughnut -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa fa-pie-chart text-success me-2"></i>Trạng Thái Đơn Hàng</h5>
            </div>
            <div class="card-body pt-0 d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; height: 230px; width: 230px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <div class="mt-3 w-100">
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span><i class="fa fa-circle text-success me-2"></i>Hoàn thành</span>
                        <strong class="text-success">{{ $completedOrders }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span><i class="fa fa-circle text-primary me-2"></i>Đang xử lý</span>
                        <strong class="text-primary">{{ $processingOrders }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span><i class="fa fa-circle text-warning me-2"></i>Chờ duyệt</span>
                        <strong class="text-warning">{{ $pendingOrders }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span><i class="fa fa-circle text-danger me-2"></i>Đã hủy</span>
                        <strong class="text-danger">{{ $cancelledOrders }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Tables: Low Stock & Top Selling -->
<div class="row g-4 mb-4">
    <!-- Low Stock Alert -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-danger">
                    <i class="fa fa-exclamation-triangle me-2"></i>Cảnh Báo Tồn Kho Thấp (≤ 5)
                </h5>
                <span class="badge bg-danger-subtle text-danger border px-2">{{ count($lowStockProducts) }} sản phẩm</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Tồn kho</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $low)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($low->image)
                                                <img src="{{ asset($low->image) }}" class="rounded" style="width: 42px; height: 42px; object-fit: cover;" alt="{{ $low->name }}">
                                            @else
                                                <div class="rounded bg-light border text-center text-muted" style="width: 42px; height: 42px; line-height: 42px;">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $low->name }}">{{ $low->name }}</div>
                                                <small class="text-muted">{{ $low->category->name ?? 'Chưa phân loại' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($low->stock <= 0)
                                            <span class="badge bg-danger text-white px-2 py-1">Hết hàng (0)</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1">Còn {{ $low->stock }} cái</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.products.edit', $low->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-edit me-1"></i>Nhập hàng
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-success py-4">
                                        <i class="fa fa-check-circle fa-2x mb-2 d-block"></i>
                                        Tất cả sản phẩm đều đang đủ tồn kho an toàn!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="fa fa-trophy text-warning me-2"></i>Top 5 Sản Phẩm Bán Chạy Nhất
                </h5>
                <span class="badge bg-success-subtle text-success border px-2">Theo đơn hoàn tất</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đã bán</th>
                                <th class="text-end">Doanh số</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSelling as $idx => $item)
                                <tr>
                                    <td><span class="badge {{ $idx === 0 ? 'bg-warning text-dark' : ($idx === 1 ? 'bg-secondary text-white' : 'bg-light text-dark border') }} rounded-pill">{{ $idx + 1 }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset($item->product->image) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="">
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-truncate" style="max-width: 180px;" title="{{ $item->product->name ?? 'Sản phẩm đã xóa' }}">
                                                    {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success px-2 py-1">{{ $item->total_sold }} cái</span>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($item->total_sales, 0, ',', '.') }}₫
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fa fa-shopping-basket fa-2x mb-2 d-block"></i>
                                        Chưa có dữ liệu sản phẩm đã bán.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Top Viewed & Recent Orders -->
<div class="row g-4 mb-4">
    <!-- Top Viewed -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">
                    <i class="fa fa-fire text-danger me-2"></i>Sản Phẩm Quan Tâm Nhất (Lượt Xem)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th class="text-center">Lượt xem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $prod)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('product.show', $prod->id) }}" target="_blank" class="text-decoration-none fw-semibold text-dark">
                                            {{ $prod->name }}
                                        </a>
                                        <div class="small text-muted">{{ $prod->category->name ?? '' }}</div>
                                    </td>
                                    <td class="text-danger fw-bold">{{ number_format($prod->price, 0, ',', '.') }}₫</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill px-3">{{ number_format($prod->views) }} <i class="fa fa-eye ms-1"></i></span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu lượt xem.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="fa fa-clock text-info me-2"></i>Đơn Hàng Mới Nhất
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none">
                                            #{{ $order->id }}
                                        </a>
                                        <div class="small text-muted">{{ $order->created_at->format('H:i d/m') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->user->name ?? $order->customer_name ?? 'Khách vãng lai' }}</div>
                                        <small class="text-muted">{{ $order->customer_phone ?? '' }}</small>
                                    </td>
                                    <td class="fw-bold text-primary">{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                                    <td class="text-center">
                                        @if($order->status === 'completed')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @elseif($order->status === 'processing')
                                            <span class="badge bg-primary">Đang giao</span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Chưa có đơn hàng nào phát sinh.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Revenue 7-day Line Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
    revGradient.addColorStop(0, 'rgba(13, 110, 253, 0.35)');
    revGradient.addColorStop(1, 'rgba(13, 110, 253, 0.02)');

    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueDates) !!},
            datasets: [{
                label: 'Doanh thu (₫)',
                data: {!! json_encode($revenueValues) !!},
                backgroundColor: revGradient,
                borderColor: '#0d6efd',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000).toFixed(1) + 'M ₫';
                            } else if (value >= 1000) {
                                return (value / 1000).toFixed(0) + 'k ₫';
                            }
                            return value + ' ₫';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 2. Order Status Doughnut Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hoàn thành', 'Đang xử lý', 'Chờ duyệt', 'Đã hủy'],
            datasets: [{
                data: [
                    {{ $completedOrders }},
                    {{ $processingOrders }},
                    {{ $pendingOrders }},
                    {{ $cancelledOrders }}
                ],
                backgroundColor: [
                    '#198754', // Green
                    '#0d6efd', // Blue
                    '#ffc107', // Yellow
                    '#dc3545'  // Red
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endsection
