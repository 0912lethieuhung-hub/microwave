@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa fa-users text-primary me-2"></i>Quản Lý Người Dùng & Khách Hàng</h2>
        <p class="text-muted mb-0">Danh sách tài khoản thành viên, phân quyền quản trị và lịch sử hoạt động.</p>
    </div>
    <div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success shadow-sm fw-semibold">
            <i class="fa fa-plus me-1"></i> Thêm người dùng
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Tổng Người Dùng</span>
                    <h4 class="fw-bold my-1 text-dark">{{ number_format($totalUsers) }}</h4>
                    <small class="text-muted"><i class="fa fa-user me-1"></i>Tài khoản đăng ký</small>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 text-center" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-users fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Quản Trị Viên</span>
                    <h4 class="fw-bold my-1 text-warning">{{ number_format($adminCount) }}</h4>
                    <small class="text-muted"><i class="fa fa-shield-halved me-1"></i>Toàn quyền hệ thống</small>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 text-center" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-user-shield fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Khách Hàng</span>
                    <h4 class="fw-bold my-1 text-success">{{ number_format($customerCount) }}</h4>
                    <small class="text-muted"><i class="fa fa-basket-shopping me-1"></i>Tài khoản mua hàng</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 text-center" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-user-tag fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Đã Mua Hàng</span>
                    <h4 class="fw-bold my-1 text-info">{{ number_format($customersWithOrders) }}</h4>
                    <small class="text-muted"><i class="fa fa-check-circle me-1"></i>Có phát sinh đơn</small>
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 text-center" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-cart-check fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search Bar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index', request()->except('role', 'page')) }}" 
                   class="btn btn-sm {{ !request('role') ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Tất cả ({{ $totalUsers }})
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'customer'])) }}" 
                   class="btn btn-sm {{ request('role') == 'customer' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fa fa-user me-1"></i> Khách hàng ({{ $customerCount }})
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'admin'])) }}" 
                   class="btn btn-sm {{ request('role') == 'admin' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">
                    <i class="fa fa-shield-halved me-1"></i> Quản trị viên ({{ $adminCount }})
                </a>
            </div>

            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2" style="max-width: 360px; width: 100%;">
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Tên, email, số điện thoại..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tìm</button>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index', request()->except('search', 'page')) }}" class="btn btn-outline-secondary" title="Xóa tìm kiếm"><i class="fa fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Thành viên</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th class="text-center">Vai trò</th>
                        <th class="text-center">Số đơn</th>
                        <th>Ngày tạo</th>
                        <th class="text-center" width="180px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $user->avatar_url }}" alt="" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->phone)
                                <a href="tel:{{ $user->phone }}" class="text-decoration-none text-dark">
                                    <i class="fa fa-phone text-muted me-1"></i>{{ $user->phone }}
                                </a>
                            @else
                                <span class="text-muted">Chưa cập nhật</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 220px;" title="{{ $user->address }}">
                                {{ $user->address ?? 'Chưa cập nhật' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($user->is_admin || $user->role === 'admin')
                                <span class="badge bg-warning text-dark px-2 py-1">
                                    <i class="fa fa-shield-halved me-1"></i> Quản trị viên
                                </span>
                            @else
                                <span class="badge bg-light text-primary border px-2 py-1">
                                    <i class="fa fa-user me-1"></i> Khách hàng
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $user->orders_count > 0 ? 'bg-success' : 'bg-secondary' }} rounded-pill px-2">
                                {{ $user->orders_count }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</div>
                            <small class="text-muted">{{ $user->created_at ? $user->created_at->format('H:i') : '' }}</small>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết hồ sơ">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa thông tin">
                                    <i class="fa fa-pen"></i>
                                </a>
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng {{ $user->name }}?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa người dùng">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.toggleRole', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi quyền hạn của người dùng {{ $user->name }}?')" class="d-inline">
                                        @csrf
                                        @if($user->is_admin || $user->role === 'admin')
                                            <button type="submit" class="btn btn-sm btn-outline-warning text-dark" title="Hạ quyền xuống Khách hàng">
                                                <i class="fa fa-user-minus"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Thăng quyền lên Quản trị viên">
                                                <i class="fa fa-user-shield"></i>
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fa fa-users-slash fa-3x mb-3 text-secondary d-block"></i>
                            Không tìm thấy người dùng nào phù hợp với điều kiện tìm kiếm.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
@endsection
