@extends('layouts.admin')
@section('title', 'Báo cáo doanh thu')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa fa-chart-line text-primary me-2"></i>Báo Cáo Doanh Thu</h2>
            <p class="text-muted mb-0">Doanh thu tính theo ngày tạo đơn, chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng.</p>
        </div>
    </div>

    <ul class="nav nav-pills my-3" aria-label="Báo cáo">
        <li class="nav-item">
            <a class="nav-link active fw-semibold" aria-current="page" href="{{ route('admin.reports.index') }}">
                <i class="fa fa-table me-1"></i> Bảng số liệu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold" href="{{ route('admin.reports.charts') }}">
                <i class="fa fa-chart-pie me-1"></i> Biểu đồ
            </a>
        </li>
    </ul>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0">
                <span class="text-muted fw-semibold" style="font-size: 0.85rem;">TỔNG SỐ ĐƠN HÀNG</span>
                <h3 class="mb-0 fw-bold mt-2">{{ number_format($totalOrders) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0">
                <span class="text-muted fw-semibold" style="font-size: 0.85rem;">TỔNG SỐ KHÁCH HÀNG</span>
                <h3 class="mb-0 fw-bold mt-2">{{ number_format($totalCustomers) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0">
                <span class="text-muted fw-semibold" style="font-size: 0.85rem;">TỔNG DOANH THU (GỒM PHÍ VC)</span>
                <h3 class="mb-0 text-success fw-bold mt-2">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <strong class="fs-6">Doanh thu theo danh mục</strong>
            <div class="small text-muted">Tính theo giá sản phẩm khi đặt hàng, không gồm phí vận chuyển.</div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Danh mục</th>
                        <th class="text-end">Số lượng bán</th>
                        <th class="text-end">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryRevenue as $revenue)
                    <tr>
                        <td>{{ $revenue->category_name ?? ('Danh mục #'.$revenue->category_id) }}</td>
                        <td class="text-end">{{ number_format($revenue->total_qty) }}</td>
                        <td class="text-end fw-semibold text-primary">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach([
        ['Doanh thu theo ngày', 'Ngày', 'date', $revenueByDate, 'd/m/Y'],
        ['Doanh thu theo tháng', 'Tháng', 'month', $revenueByMonth, 'm/Y'],
        ['Doanh thu theo năm', 'Năm', 'year', $revenueByYear, null],
    ] as [$title, $label, $field, $rows, $format])
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 font-weight-bold fw-bold fs-6">{{ $title }}</div>
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ $label }}</th>
                        <th class="text-end">Số đơn đã thanh toán</th>
                        <th class="text-end">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $revenue)
                    <tr>
                        <td>{{ $format ? \Carbon\Carbon::parse($revenue->{$field}.($field === 'month' ? '-01' : ''))->format($format) : $revenue->{$field} }}</td>
                        <td class="text-end">{{ number_format($revenue->order_count) }}</td>
                        <td class="text-end fw-semibold text-success">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection
