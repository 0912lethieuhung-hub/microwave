@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold fs-5">
                + Thêm Mã Giảm Giá Mới
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

                <form action="{{ route('admin.vouchers.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="code" class="form-label font-weight-bold">Mã Voucher <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control text-uppercase" placeholder="VD: GIAM50K, SIEULONUONG" value="{{ old('code') }}" required>
                            <small class="text-muted">Mã khách sẽ nhập tại giỏ hàng (viết liền không dấu).</small>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label font-weight-bold">Tên Chương Trình <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="VD: Khuyến mãi mừng khai trương" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="discount_type" class="form-label font-weight-bold">Loại Giảm Giá <span class="text-danger">*</span></label>
                            <select name="discount_type" id="discount_type" class="form-select" required>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định (₫)</option>
                                <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="discount_value" class="form-label font-weight-bold">Mức Giảm <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" id="discount_value" class="form-control" placeholder="VD: 50000 hoặc 10" value="{{ old('discount_value') }}" min="0" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_order_amount" class="form-label font-weight-bold">Đơn Hàng Tối Thiểu (₫)</label>
                            <input type="number" name="min_order_amount" id="min_order_amount" class="form-control" placeholder="0 nếu không yêu cầu" value="{{ old('min_order_amount', 0) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="max_discount" class="form-label font-weight-bold">Giảm Tối Đa (₫) <small class="text-muted">(Nếu giảm %)</small></label>
                            <input type="number" name="max_discount" id="max_discount" class="form-control" placeholder="Để trống nếu không giới hạn" value="{{ old('max_discount') }}" min="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="usage_limit" class="form-label font-weight-bold">Giới Hạn Lượt Dùng</label>
                            <input type="number" name="usage_limit" id="usage_limit" class="form-control" placeholder="Để trống nếu không giới hạn" value="{{ old('usage_limit') }}" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="expires_at" class="form-label font-weight-bold">Hạn Sử Dụng</label>
                            <input type="date" name="expires_at" id="expires_at" class="form-control" value="{{ old('expires_at') }}">
                            <small class="text-muted">Để trống nếu muốn áp dụng vĩnh viễn.</small>
                        </div>
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold" for="is_active">Kích hoạt mã ngay sau khi tạo</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-primary">Lưu Mã Giảm Giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
