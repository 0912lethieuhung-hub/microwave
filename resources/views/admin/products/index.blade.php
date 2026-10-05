@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">Quản Lý Sản Phẩm (Products Management)</h2>
            <p class="text-muted mb-0">Quản lý danh sách, số lượng tồn kho, màu sắc và thông số kỹ thuật lò vi sóng.</p>
        </div>
        <a class="btn btn-success" href="{{ route('admin.products.create') }}">
            <i class="fa-solid fa-plus me-1"></i> Thêm Sản Phẩm Mới
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" width="50">ID</th>
                        <th width="80">Ảnh</th>
                        <th>Tên & Thương Hiệu</th>
                        <th>Danh Mục</th>
                        <th>Thông Số / Màu Sắc</th>
                        <th>Giá Bán</th>
                        <th>Tồn Kho</th>
                        <th class="text-center" width="180">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#{{ $product->id }}</td>
                        <td>
                            @if($product->image_url)
                                <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 58px; height: 58px; object-fit: contain; padding: 2px;">
                            @else
                                <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded" style="width: 58px; height: 58px; font-size: 0.75rem;">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('product.show', $product->id) }}" target="_blank" class="fw-bold text-decoration-none text-dark d-block mb-1" title="Xem trên website">
                                {{ $product->name }} <i class="fa-solid fa-arrow-up-right-from-square text-muted" style="font-size: 0.75rem;"></i>
                            </a>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-secondary" style="font-size: 0.72rem;">{{ $product->brand ?? 'Chính hãng' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark" style="font-size: 0.75rem;">
                                {{ $product->category ? $product->category->name : 'Chưa phân loại' }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 0.84rem; line-height: 1.4;">
                                @if($product->capacity)
                                    <div><i class="fa-solid fa-box text-muted me-1"></i>Dung tích: <strong>{{ $product->capacity }}</strong></div>
                                @endif
                                @if($product->power)
                                    <div><i class="fa-solid fa-bolt text-warning me-1"></i>Công suất: <strong>{{ $product->power }}</strong></div>
                                @endif
                                @if($product->color)
                                    <div><i class="fa-solid fa-palette text-primary me-1"></i>Màu: <strong>{{ $product->color }}</strong></div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                            @if($product->original_price && $product->original_price > $product->price)
                                <small class="text-muted text-decoration-line-through">{{ number_format($product->original_price, 0, ',', '.') }}₫</small>
                            @endif
                        </td>
                        <td>
                            @if($product->stock > 5)
                                <span class="badge bg-success" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-check me-1"></i>Còn: {{ $product->stock }}
                                </span>
                            @elseif($product->stock > 0)
                                <span class="badge bg-warning text-dark" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>Sắp hết: {{ $product->stock }}
                                </span>
                            @else
                                <span class="badge bg-danger" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-xmark me-1"></i>Hết hàng (0)
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a class="btn btn-primary btn-sm me-1" href="{{ route('admin.products.edit', $product->id) }}" title="Chỉnh sửa">
                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-box-open fs-2 mb-2 d-block"></i>
                            Chưa có sản phẩm nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

