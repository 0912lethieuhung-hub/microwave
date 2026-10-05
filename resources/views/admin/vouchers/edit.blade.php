@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold fs-5">
                Chỉnh Sửa Mã Giảm Giá: {{ $voucher->code }}
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="code" class="form-label font-weight-bold">Mã Voucher <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control text-uppercase" value="{{ old('code', $voucher->code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label font-weight-bold">Tên Chương Trình <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $voucher->name) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="discount_type" class="form-label font-weight-bold">Loại Giảm Giá <span class="text-danger">*</span></label>
                            <select name="discount_type" id="discount_type" class="form-select" required>
                                <option value="fixed" {{ old('discount_type', $voucher->discount_type) == 'fixed' ? 'selected' : '' }}>Số tiền cố định (₫)</option>
                                <option value="percent" {{ old('discount_type', $voucher->discount_type) == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="discount_value" class="form-label font-weight-bold">Mức Giảm <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" id="discount_value" class="form-control" value="{{ old('discount_value', $voucher->discount_value) }}" min="0" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_order_amount" class="form-label font-weight-bold">Đơn Hàng Tối Thiểu (₫)</label>
                            <input type="number" name="min_order_amount" id="min_order_amount" class="form-control" value="{{ old('min_order_amount', $voucher->min_order_amount) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="max_discount" class="form-label font-weight-bold">Giảm Tối Đa (₫)</label>
                            <input type="number" name="max_discount" id="max_discount" class="form-control" value="{{ old('max_discount', $voucher->max_discount) }}" min="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="usage_limit" class="form-label font-weight-bold">Giới Hạn Lượt Dùng</label>
                            <input type="number" name="usage_limit" id="usage_limit" class="form-control" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="expires_at" class="form-label font-weight-bold">Hạn Sử Dụng</label>
                            <input type="date" name="expires_at" id="expires_at" class="form-control" value="{{ old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold" for="is_active">Kích hoạt mã hoạt động</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-primary">Cập Nhật Mã Giảm Giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
