@extends('layouts.admin')
@section('title', 'Biểu đồ báo cáo doanh thu')
@section('content')
<style>
    .chart-wrap { min-height: 360px; position: relative; }
    .chart-wrap canvas { width: 100% !important; height: 360px !important; }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa fa-chart-pie text-primary me-2"></i>Biểu Đồ Báo Cáo Doanh Thu</h2>
            <p class="text-muted mb-0">Chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng. Doanh thu tính theo ngày tạo đơn; số liệu theo danh mục không gồm phí vận chuyển.</p>
        </div>
    </div>

    <ul class="nav nav-pills my-3" aria-label="Báo cáo">
        <li class="nav-item">
            <a class="nav-link fw-semibold" href="{{ route('admin.reports.index') }}">
                <i class="fa fa-table me-1"></i> Bảng số liệu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-semibold" aria-current="page" href="{{ route('admin.reports.charts') }}">
                <i class="fa fa-chart-pie me-1"></i> Biểu đồ
            </a>
        </li>
    </ul>

    <div id="report-chart-error" class="alert alert-warning d-none" role="alert">
        Không tải được thư viện biểu đồ. Bạn có thể xem số liệu tại trang <a href="{{ route('admin.reports.index') }}">Bảng số liệu</a>.
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 fw-bold">Doanh thu theo danh mục</div>
                <div class="card-body chart-wrap"><canvas id="categoryRevenueChart"></canvas></div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 fw-bold">Doanh thu theo ngày (30 ngày gần nhất)</div>
                <div class="card-body chart-wrap"><canvas id="revenueByDateChart"></canvas></div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 fw-bold">Doanh thu theo tháng (12 tháng gần nhất)</div>
                <div class="card-body chart-wrap"><canvas id="revenueByMonthChart"></canvas></div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 fw-bold">Doanh thu theo năm</div>
                <div class="card-body chart-wrap"><canvas id="revenueByYearChart"></canvas></div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold">Doanh thu theo phương thức thanh toán</div>
                <div class="card-body chart-wrap" style="max-height: 400px; display: flex; justify-content: center;">
                    <div style="width: 360px; max-width: 100%;">
                        <canvas id="revenueByPaymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="report-chart-data" hidden data-chart-data="{{ json_encode([
    'catLabels' => $catLabels ?? [],
    'catRevenue' => $catRevenue ?? [],
    'revDateLabels' => $revDateLabels ?? [],
    'revDateData' => $revDateData ?? [],
    'revMonthLabels' => $revMonthLabels ?? [],
    'revMonthData' => $revMonthData ?? [],
    'revYearLabels' => $revYearLabels ?? [],
    'revYearData' => $revYearData ?? [],
    'paymentMethodLabels' => $paymentMethodLabels ?? [],
    'paymentMethodRevenue' => $paymentMethodRevenue ?? [],
]) }}"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') {
        document.getElementById('report-chart-error').classList.remove('d-none');
        return;
    }

    const reportData = JSON.parse(document.getElementById('report-chart-data').dataset.chartData);
    const catLabels = reportData.catLabels;
    const catRevenue = reportData.catRevenue.map(Number);
    const revDateLabels = reportData.revDateLabels;
    const revDateData = reportData.revDateData.map(Number);
    const revMonthLabels = reportData.revMonthLabels;
    const revMonthData = reportData.revMonthData.map(Number);
    const revYearLabels = reportData.revYearLabels;
    const revYearData = reportData.revYearData.map(Number);
    const payLabels = reportData.paymentMethodLabels;
    const payRevenue = reportData.paymentMethodRevenue.map(Number);

    const mk = (el, type, labels, data, label, bgColor) => new Chart(el, {
        type,
        data: {
            labels,
            datasets: [{
                label,
                data,
                fill: type === 'line',
                tension: 0.3,
                backgroundColor: bgColor || (type === 'line' ? 'rgba(0, 113, 175, 0.15)' : '#0071af'),
                borderColor: '#0071af',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return (context.dataset.label || '') + ': ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' đ';
                        }
                    }
                }
            }
        }
    });

    const catEl = document.getElementById('categoryRevenueChart');
    if (catEl) mk(catEl, 'bar', catLabels, catRevenue, 'Doanh thu (VNĐ)', '#3b82f6');

    const dateEl = document.getElementById('revenueByDateChart');
    if (dateEl) mk(dateEl, 'line', revDateLabels, revDateData, 'Doanh thu (VNĐ)', 'rgba(59, 130, 246, 0.15)');

    const monthEl = document.getElementById('revenueByMonthChart');
    if (monthEl) mk(monthEl, 'bar', revMonthLabels, revMonthData, 'Doanh thu (VNĐ)', '#10b981');

    const yearEl = document.getElementById('revenueByYearChart');
    if (yearEl) mk(yearEl, 'bar', revYearLabels, revYearData, 'Doanh thu (VNĐ)', '#f59e0b');

    const payEl = document.getElementById('revenueByPaymentMethodChart');
    if (payEl) {
        new Chart(payEl, {
            type: 'pie',
            data: {
                labels: payLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: payRevenue,
                    backgroundColor: ['#a21caf', '#0284c7']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return (context.label || '') + ': ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' đ';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
