@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <h2>Order Details #{{ $order->id }}</h2>
        <a class="btn btn-secondary" href="{{ route('admin.orders.index') }}"> Back to Orders</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light fw-bold"><i class="fa fa-user me-2 text-primary"></i>Thông tin khách hàng</div>
            <div class="card-body">
                <p class="mb-2"><strong>Họ và tên:</strong> {{ $order->user->name ?? 'Khách vãng lai' }}</p>
                <p class="mb-2"><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                <p class="mb-2"><strong>Số điện thoại:</strong> <a href="tel:{{ $order->phone }}" class="text-decoration-none fw-bold">{{ $order->phone }}</a></p>
                <p class="mb-2"><strong>Địa chỉ nhận hàng:</strong> {{ $order->shipping_address }}</p>
                <p class="mb-0"><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-primary">
            <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fa fa-truck-fast me-2"></i>Vận Chuyển Giao Hàng Nhanh (GHN)</span>
                @if($order->ghn_order_code)
                    <form action="{{ route('admin.orders.sync-ghn', $order->id) }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm fw-bold text-primary shadow-sm" title="Lấy trạng thái thực tế mới nhất từ GHN">
                            <i class="fa fa-rotate me-1"></i> Đồng bộ GHN
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="text-muted small d-block">MÃ VẬN ĐƠN GHN</span>
                        @if($order->ghn_order_code)
                            <span class="badge bg-dark text-white fs-6 font-monospace px-3 py-2">
                                <i class="fa fa-barcode me-1 text-warning"></i> {{ $order->ghn_order_code }}
                            </span>
                        @else
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="text-muted fst-italic">Chưa tạo vận đơn</span>
                                @if($order->status !== 'cancelled')
                                    <form action="{{ route('admin.orders.create-ghn', $order->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm shadow-sm fw-bold">
                                            <i class="fa fa-truck-fast me-1"></i> Tạo vận đơn GHN ngay
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                    @if($order->ghn_order_code)
                        <a href="{{ config('services.ghn.portal_url', 'https://5sao.ghn.dev') }}/?order_code={{ $order->ghn_order_code }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-external-link me-1"></i> Tra cứu trên portal GHN (5sao)
                        </a>
                    @endif
                </div>

                <div class="mb-3 p-3 rounded bg-light border">
                    <span class="text-muted small d-block mb-1">TRẠNG THÁI VẬN CHUYỂN THỰC TẾ (TỪ GHN)</span>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge {{ $order->shipping_badge['badge_class'] ?? 'bg-secondary text-white' }} fs-6 py-2 px-3 shadow-sm">
                            <i class="fa {{ $order->shipping_badge['icon'] ?? 'fa-box' }} me-1"></i> 
                            {{ $order->shipping_badge['label'] ?? 'Chờ bàn giao' }}
                        </span>
                        @if(!empty($ghnDetail['status']))
                            <span class="badge bg-white text-secondary border font-monospace small">GHN: {{ $ghnDetail['status'] }}</span>
                        @endif
                    </div>
                    <small class="text-muted d-block mt-1">
                        <i class="fa fa-circle-info text-primary me-1"></i>{{ $order->shipping_badge['desc'] ?? '' }}
                    </small>
                </div>

                <div class="row g-2 small mb-3">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white">
                            <span class="text-muted d-block">Cước phí GHN:</span>
                            <strong class="text-danger fs-6">{{ number_format($order->ghn_total_fee ?? 0, 0, ',', '.') }}₫</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white">
                            <span class="text-muted d-block">Dự kiến giao hàng:</span>
                            @if(!empty($ghnDetail['leadtime']))
                                <strong class="text-success">{{ \Carbon\Carbon::parse($ghnDetail['leadtime'])->format('d/m/Y H:i') }}</strong>
                            @else
                                <span class="text-muted">Đang cập nhật</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 mb-0 small border-info d-flex align-items-start gap-2">
                    <i class="fa fa-lightbulb text-info mt-1"></i>
                    <div>
                        <strong>Giải thích cơ chế GHN:</strong> Khi shop tạo đơn, trạng thái ban đầu luôn là <em>"Chờ lấy hàng / Chờ bàn giao"</em>. Trạng thái chỉ chuyển sang <em>"Đang lấy hàng"</em> hoặc <em>"Đang giao"</em> khi bưu tá GHN đến lấy hàng hoặc quét mã nhận kiện tại bưu cục.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold"><i class="fa fa-traffic-light me-2"></i>Trạng thái đơn hàng nội bộ (Shop)</span>
            <small class="text-muted ms-2">(Quản lý quy trình chuẩn bị hàng và xử lý thanh toán của shop)</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div>
                @if($order->status == 'pending')
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="fa fa-clock me-1"></i> Chờ duyệt (Pending)</span>
                @elseif($order->status == 'processing')
                    <span class="badge bg-primary text-white px-3 py-2 fs-6"><i class="fa fa-box-open me-1"></i> Đang chuẩn bị hàng (Processing)</span>
                @elseif($order->status == 'completed')
                    <span class="badge bg-success text-white px-3 py-2 fs-6"><i class="fa fa-check-circle me-1"></i> Đã hoàn tất (Completed)</span>
                @else
                    <span class="badge bg-danger text-white px-3 py-2 fs-6"><i class="fa fa-times-circle me-1"></i> Đã hủy (Cancelled)</span>
                @endif
            </div>
            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-flex gap-2 align-items-center flex-wrap">
                @csrf
                <select name="status" class="form-select form-select-sm" style="min-width: 170px;">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Chờ duyệt</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>📦 Đang xử lý / Đóng gói</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>✅ Đã hoàn tất</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' || $order->status == 'canceled' ? 'selected' : '' }}>❌ Đã hủy đơn (Hủy GHN)</option>
                </select>
                @if(!$order->ghn_order_code && $order->status !== 'cancelled')
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input" type="checkbox" name="create_ghn" id="create_ghn" value="1" checked>
                        <label class="form-check-label small fw-semibold text-primary" for="create_ghn">
                            <i class="fa fa-truck-fast me-1"></i>Tự động đẩy đơn sang GHN
                        </label>
                    </div>
                @endif
                <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Cập nhật</button>
            </form>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-light fw-bold"><i class="fa fa-boxes-stacked me-2"></i>Chi tiết sản phẩm đặt mua</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th class="text-center">Màu sắc</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                    <tr>
                        <td>
                            @if($item->product && $item->product->image)
                                <img src="{{ asset($item->product->image) }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;" alt="">
                            @else
                                <div class="rounded border bg-light text-center text-muted" style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fa fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $item->product->name ?? 'Sản phẩm đã xóa khỏi hệ thống' }}</div>
                            <small class="text-muted">Mã SP: #{{ $item->product_id }}</small>
                        </td>
                        <td class="text-center">
                            @if($item->color)
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $item->color }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                        <td class="text-center fw-bold">x{{ $item->quantity }}</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    @php
                        $adminGhnFee = $order->ghn_total_fee ?? 0;
                        $adminSubtotal = max(0, $order->total_price - $adminGhnFee);
                    @endphp
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Tiền hàng:</td>
                        <td class="text-end fw-bold">{{ number_format($adminSubtotal, 0, ',', '.') }}₫</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold text-muted">Cước vận chuyển (GHN):</td>
                        <td class="text-end fw-bold text-warning">{{ number_format($adminGhnFee, 0, ',', '.') }}₫</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold fs-6">Tổng tiền thanh toán:</td>
                        <td class="text-end fw-bold text-danger fs-5">{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Lịch sử giao dịch thanh toán (Payment Transactions) -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-credit-card me-2 text-primary"></i>Lịch sử giao dịch thanh toán</span>
        <span class="badge bg-secondary">{{ $order->paymentTransactions->count() }} giao dịch</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Cổng thanh toán</th>
                        <th>Mã GD (Trans ID)</th>
                        <th>Mã đơn MoMo</th>
                        <th class="text-end">Số tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->paymentTransactions as $tx)
                    <tr>
                        <td>
                            @if($tx->gateway === 'momo')
                                <span class="badge" style="background-color: #a50064; color: #fff;">
                                    <i class="fa-solid fa-wallet me-1"></i> MoMo
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fa-solid fa-money-bill me-1"></i> COD
                                </span>
                            @endif
                        </td>
                        <td class="font-monospace fw-semibold">
                            {{ $tx->transaction_id ?? '—' }}
                        </td>
                        <td class="font-monospace text-muted small">
                            {{ $tx->gateway_order_id ?? '—' }}
                        </td>
                        <td class="text-end fw-bold text-danger">
                            {{ number_format($tx->amount, 0, ',', '.') }}₫
                        </td>
                        <td class="text-center">
                            @php
                                $badgeClass = match($tx->status) {
                                    'paid' => 'bg-success text-white',
                                    'pending' => 'bg-warning text-dark',
                                    'failed' => 'bg-danger text-white',
                                    'cancelled' => 'bg-secondary text-white',
                                    default => 'bg-secondary text-white',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} px-2 py-1">
                                {{ strtoupper($tx->status) }}
                            </span>
                        </td>
                        <td>
                            {{ $tx->created_at ? $tx->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Chưa có bản ghi giao dịch thanh toán nào được tạo cho đơn hàng này.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

