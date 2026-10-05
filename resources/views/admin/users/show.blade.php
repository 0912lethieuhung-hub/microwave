@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa fa-user text-primary me-2"></i>Chi Tiết Hồ Sơ: {{ $user->name }}</h2>
        <p class="text-muted mb-0">Xem thông tin cá nhân, tổng quan chi tiêu và toàn bộ đơn hàng của thành viên.</p>
    </div>
    <div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- User Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body text-center p-4">
                <img src="{{ $user->avatar_url }}" alt="" class="rounded-circle border border-3 border-primary mb-3" style="width: 90px; height: 90px; object-fit: cover;">
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <div>
                    @if($user->is_admin || $user->role === 'admin')
                        <span class="badge bg-warning text-dark px-3 py-2"><i class="fa fa-shield-halved me-1"></i> Quản trị viên</span>
                    @else
                        <span class="badge bg-primary px-3 py-2"><i class="fa fa-user me-1"></i> Khách hàng</span>
                    @endif
                </div>

                <hr class="my-4">

                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block">Số điện thoại</small>
                        <span class="fw-semibold">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Địa chỉ giao hàng</small>
                        <span class="fw-semibold">{{ $user->address ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Giới tính / Ngày sinh</small>
                        <span class="fw-semibold">
                            {{ ucfirst($user->gender ?? 'Chưa rõ') }} 
                            @if($user->birthday)
                                ({{ \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') }})
                            @endif
                        </span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Ngày đăng ký</small>
                        <span class="fw-semibold">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Xác thực Email</small>
                        @if($user->email_verified_at)
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                <i class="fa fa-check-circle me-1"></i> Đã xác thực lúc {{ $user->email_verified_at->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1">
                                <i class="fa fa-clock me-1"></i> Chưa xác thực
                            </span>
                        @endif
                    </div>
                </div>

                @if($user->id !== Auth::id())
                    <div class="mt-4 pt-2 border-top">
                        <form action="{{ route('admin.users.toggleRole', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi quyền hạn của người dùng {{ $user->name }}?')">
                            @csrf
                            @if($user->is_admin || $user->role === 'admin')
                                <button type="submit" class="btn btn-outline-warning text-dark w-100">
                                    <i class="fa fa-user-minus me-1"></i> Chuyển về quyền Khách hàng
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="fa fa-user-shield me-1"></i> Cấp quyền Quản trị viên
                                </button>
                            @endif
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- User Spending & Stats -->
    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Tổng Đơn Đặt</span>
                    <h3 class="fw-bold text-primary my-1">{{ $user->orders->count() }}</h3>
                    <small class="text-muted">Đơn hàng</small>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Đơn Hoàn Thành</span>
                    <h3 class="fw-bold text-success my-1">{{ $user->orders->where('status', 'completed')->count() }}</h3>
                    <small class="text-muted">Đã giao thành công</small>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Tổng Tiền Chi Tiêu</span>
                    <h3 class="fw-bold text-danger my-1" style="font-size: 1.35rem;">{{ number_format($totalSpent, 0, ',', '.') }}₫</h3>
                    <small class="text-muted">Không tính đơn hủy</small>
                </div>
            </div>
        </div>

        <!-- Orders History Table -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa fa-boxes-packing text-primary me-2"></i>Lịch Sử Đơn Hàng Của Thành Viên</h5>
                <span class="badge bg-primary-subtle text-primary border px-2">{{ $user->orders->count() }} đơn</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Sản phẩm</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->orders as $order)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none">
                                        #{{ $order->id }}
                                    </a>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $order->orderItems->count() }} sản phẩm
                                        @if($order->orderItems->first() && $order->orderItems->first()->product)
                                            ({{ Str::limit($order->orderItems->first()->product->name, 25) }})
                                        @endif
                                    </small>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    {{ number_format($order->total_price, 0, ',', '.') }}₫
                                </td>
                                <td class="text-center">
                                    @if($order->status == 'pending') 
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @elseif($order->status == 'processing') 
                                        <span class="badge bg-primary">Đang giao</span>
                                    @elseif($order->status == 'completed') 
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @else 
                                        <span class="badge bg-danger">Đã hủy</span> 
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Thành viên này chưa đặt đơn hàng nào.
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
@endsection
