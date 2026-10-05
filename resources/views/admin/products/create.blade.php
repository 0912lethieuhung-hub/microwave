@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <div>
            <h2>Thêm Sản Phẩm Mới (Create Product)</h2>
            <p class="text-muted mb-0">Điền đầy đủ thông tin chi tiết, màu sắc và thông số kỹ thuật cho sản phẩm.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('admin.products.index') }}">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay Lại
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <!-- Tên sản phẩm -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên sản phẩm: <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="VD: Lò vi sóng Sharp R-205VN(S)" value="{{ old('name') }}" required>
                </div>

                <!-- Thương hiệu -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Thương hiệu (Brand):</label>
                    <input type="text" name="brand" class="form-control" placeholder="VD: Sharp, Panasonic, Electrolux..." value="{{ old('brand') }}">
                </div>

                <!-- Danh mục -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Danh mục: <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Giá bán -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá bán hiện tại (VNĐ): <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" placeholder="VD: 1500000" value="{{ old('price') }}" required min="0">
                </div>

                <!-- Giá gốc -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá gốc niêm yết (VNĐ):</label>
                    <input type="number" name="original_price" class="form-control" placeholder="VD: 1900000 (Để hiện giảm giá)" value="{{ old('original_price') }}" min="0">
                </div>

                <!-- Số lượng tồn kho -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Số lượng tồn kho: <span class="text-danger">*</span></label>
                    <input type="number" name="stock" class="form-control" placeholder="VD: 20" value="{{ old('stock', 15) }}" required min="0">
                </div>

                <!-- Màu sắc tóm tắt -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Màu sắc tổng hợp:</label>
                    <input type="text" name="color" id="productColorSummary" class="form-control" placeholder="Tự động đồng bộ từ bảng màu bên dưới" value="{{ old('color') }}">
                    <small class="text-muted">Được tự động cập nhật từ các biến thể màu</small>
                </div>

                <!-- Dung tích -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Dung tích (Capacity):</label>
                    <input type="text" name="capacity" class="form-control" placeholder="VD: 20 Lít, 25 Lít..." value="{{ old('capacity') }}">
                </div>

                <!-- Công suất -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Công suất (Power):</label>
                    <input type="text" name="power" class="form-control" placeholder="VD: 800W, 1000W Nướng..." value="{{ old('power') }}">
                </div>

                <!-- QUẢN LÝ BIẾN THỂ MÀU SẮC, TỒN KHO & GIÁ THEO MÀU -->
                <div class="col-md-12 my-2">
                    <div class="card border-primary shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><i class="fa-solid fa-palette me-2"></i> Chi tiết phiên bản màu sắc (Tồn kho & Giá theo màu)</span>
                            <button type="button" class="btn btn-light btn-sm fw-bold" onclick="addColorRow()">
                                <i class="fa-solid fa-plus me-1"></i> Thêm màu
                            </button>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="text-muted small mb-3">
                                <i class="fa-solid fa-circle-info text-primary me-1"></i> Thiết lập số lượng tồn kho và giá bán cho từng màu sắc. Nếu để trống cột <strong>"Giá theo màu"</strong>, hệ thống sẽ tự động dùng <strong>"Giá bán hiện tại"</strong>. Tổng tồn kho của sản phẩm sẽ tự động tính bằng tổng tồn kho các màu.
                            </div>
                            <input type="hidden" name="colors_submitted" value="1">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm bg-white align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 35%;">Tên màu sắc <span class="text-danger">*</span></th>
                                            <th style="width: 25%;">Số lượng tồn kho <span class="text-danger">*</span></th>
                                            <th style="width: 30%;">Giá bán theo màu (VNĐ - tùy chọn)</th>
                                            <th style="width: 10%; text-align: center;">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody id="colorsTableBody">
                                        <!-- Hàng màu sẽ được thêm tại đây -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ảnh sản phẩm -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tải lên file ảnh thật (Upload Image):</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Định dạng JPG, PNG, WEBP tối đa 5MB</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Hoặc đường dẫn ảnh có sẵn (Image URL):</label>
                    <input type="text" name="image_url" class="form-control" placeholder="/images/microwave_sharp.jpg hoặc https://..." value="{{ old('image_url') }}">
                </div>

                <!-- Album ảnh phụ (Gallery Images) -->
                <div class="col-md-12">
                    <label class="form-label fw-bold">Album ảnh bổ sung (Gallery Images - Tùy chọn):</label>
                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    <small class="text-muted">Có thể chọn cùng lúc nhiều ảnh chi tiết các góc cạnh, khoang lò, phụ kiện đi kèm...</small>
                </div>

                <!-- Mô tả sản phẩm -->
                <div class="col-md-12">
                    <label class="form-label fw-bold">Mô tả sản phẩm:</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Mô tả công nghệ, tính năng nổi bật...">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-12 text-end mt-4">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Sản Phẩm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let colorRowIndex = 0;

    function addColorRow(color = '', stock = 10, price = '') {
        const tbody = document.getElementById('colorsTableBody');
        const tr = document.createElement('tr');
        tr.id = `color_row_${colorRowIndex}`;
        tr.innerHTML = `
            <td>
                <input type="text" name="colors[${colorRowIndex}][color]" class="form-control form-control-sm color-name-input" placeholder="VD: Đen Nhám, Bạc Inox..." value="${color}" required oninput="syncSummary()">
            </td>
            <td>
                <input type="number" name="colors[${colorRowIndex}][stock]" class="form-control form-control-sm color-stock-input" placeholder="VD: 15" value="${stock}" min="0" required onchange="calculateTotalStock()">
            </td>
            <td>
                <input type="number" name="colors[${colorRowIndex}][price]" class="form-control form-control-sm" placeholder="Để trống = Theo giá chung" value="${price}" min="0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeColorRow(${colorRowIndex})" title="Xóa màu này">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        colorRowIndex++;
        calculateTotalStock();
        syncSummary();
    }

    function removeColorRow(index) {
        const row = document.getElementById(`color_row_${index}`);
        if (row) {
            row.remove();
            calculateTotalStock();
            syncSummary();
        }
    }

    function calculateTotalStock() {
        const stockInputs = document.querySelectorAll('.color-stock-input');
        if (stockInputs.length > 0) {
            let total = 0;
            stockInputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            const mainStock = document.querySelector('input[name="stock"]');
            if (mainStock) {
                mainStock.value = total;
            }
        }
    }

    function syncSummary() {
        const nameInputs = document.querySelectorAll('.color-name-input');
        const names = [];
        nameInputs.forEach(input => {
            const val = input.value.trim();
            if (val) names.push(val);
        });
        const summary = document.getElementById('productColorSummary');
        if (summary) {
            summary.value = names.join(', ');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        @php
            $oldColors = old('colors', []);
        @endphp
        const oldColors = @json($oldColors);
        if (Array.isArray(oldColors) && oldColors.length > 0) {
            oldColors.forEach(c => {
                addColorRow(c.color || '', c.stock ?? 10, c.price || '');
            });
        } else {
            // Khởi tạo sẵn 2 màu mẫu
            addColorRow('Đen', 10, '');
            addColorRow('Bạc Inox', 10, '');
        }
    });
</script>
@endsection

