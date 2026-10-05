@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-weight-bold">Quản Lý Kho Voucher / Mã Giảm Giá</h2>
        <p class="text-muted mb-0">Tạo và kiểm soát các mã khuyến mãi trên toàn hệ thống</p>
    </div>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
        + Thêm Mã Giảm Giá Mới
    </a>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p class="mb-0">{{ $message }}</p>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Mã Voucher</th>
                    <th>Tên Chương Trình</th>
                    <th>Mức Giảm</th>
                    <th>Đơn Tối Thiểu</th>
                    <th class="text-center">Lượt Dùng</th>
                    <th>Hạn Dùng</th>
                    <th class="text-center">Trạng Thái</th>
                    <th class="text-center">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $index => $v)
                    <tr>
                        <td>{{ $vouchers->firstItem() + $index }}</td>
                        <td>
                            <strong class="text-primary font-monospace fs-6">{{ $v->code }}</strong>
                        </td>
                        <td>{{ $v->name }}</td>
                        <td>
                            @if($v->discount_type === 'percent')
                                <span class="badge bg-danger fs-6">{{ $v->discount_value }}%</span>
                                @if($v->max_discount)
                                    <small class="text-muted d-block">Tối đa {{ number_format($v->max_discount, 0, ',', '.') }}₫</small>
                                @endif
                            @else
                                <span class="badge bg-success fs-6">-{{ number_format($v->discount_value, 0, ',', '.') }}₫</span>
                            @endif
                        </td>
                        <td>{{ number_format($v->min_order_amount, 0, ',', '.') }}₫</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $v->used_count }} / {{ $v->usage_limit ?? '∞' }}</span>
                        </td>
                        <td>
                            @if($v->expires_at)
                                @if(\Carbon\Carbon::now()->startOfDay()->gt($v->expires_at))
                                    <span class="text-danger"><small>Đã hết hạn ({{ $v->expires_at->format('d/m/Y') }})</small></span>
                                @else
                                    <span class="text-success"><small>{{ $v->expires_at->format('d/m/Y') }}</small></span>
                                @endif
                            @else
                                <span class="text-muted"><small>Vô thời hạn</small></span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.vouchers.toggle', $v->id) }}" method="POST" class="d-inline">
                                @csrf
                                @if($v->is_active)
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Bấm để tạm khóa">
                                        ✓ Đang bật
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Bấm để kích hoạt">
                                        ✕ Tạm khóa
                                    </button>
                                @endif
                            </form>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.vouchers.edit', $v->id) }}" class="btn btn-sm btn-warning">
                                Sửa
                            </a>
                            <form action="{{ route('admin.vouchers.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã voucher này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Chưa có mã giảm giá nào trong hệ thống.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $vouchers->links() }}
</div>
@endsection
