@extends('layouts.admin')
@section('title', 'Chỉnh sửa người dùng')

@section('content')
<div class="container" style="max-width: 760px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa fa-user-pen text-primary me-2"></i>Chỉnh Sửa Người Dùng</h2>
            <p class="text-muted mb-0">Cập nhật thông tin tài khoản và vai trò của #{{ $user->id }} - {{ $user->name }}.</p>
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
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên người dùng <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email đăng nhập <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Vai trò <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="user" @selected($user->role === 'customer' || $user->role === 'user')>Người dùng / Khách hàng</option>
                        <option value="admin" @selected($user->role === 'admin' || $user->is_admin)>Quản trị viên (Admin)</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-floppy-disk me-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light px-3">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
