@extends('layouts.admin')
@section('title', 'Thêm người dùng mới')

@section('content')
<div class="container" style="max-width: 760px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa fa-user-plus text-primary me-2"></i>Thêm Người Dùng Mới</h2>
            <p class="text-muted mb-0">Tạo tài khoản thành viên hoặc cấp quyền quản trị viên.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên người dùng <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Nhập họ và tên..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email đăng nhập <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="example@domain.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự..." required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Vai trò <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Người dùng / Khách hàng</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa fa-check me-1"></i> Lưu người dùng
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light px-3">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
