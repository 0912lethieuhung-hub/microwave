@extends('layouts.frontend')

@section('title', 'Thanh Toán Đơn Hàng COD - Siêu Lò Nướng')

@section('extra-css')
<style>
    .checkout-layout {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 2rem;
    }
    .checkout-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        padding: 2rem;
    }
    .checkout-card h2 {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--dmx-blue);
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--card-border);
        padding-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-size: 0.88rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    .form-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.7rem 0.9rem;
        font-family: inherit;
        font-size: 0.9rem;
        outline: none;
        transition: border 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 3px rgba(0, 113, 175, 0.15);
    }
    .btn-submit-order {
        width: 100%;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #ffffff;
        border: none;
        padding: 0.95rem;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.2s;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }
    .btn-submit-order:hover {
        transform: translateY(-2px);
    }

    .order-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .order-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--dmx-red);
    }

    /* ADDRESS DROPDOWN STYLES */
    .address-area-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.1rem;
        margin-top: 0.35rem;
    }
    .address-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
        margin-bottom: 0.85rem;
    }
    .address-field {
        display: flex;
        flex-direction: column;
    }
    .address-field label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.3rem;
    }
    .address-field select, .address-field input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        font-family: inherit;
        font-size: 0.88rem;
        background-color: #ffffff;
        color: #1e293b;
        outline: none;
        transition: all 0.2s;
    }
    .address-field select:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .address-field select:focus, .address-field input:focus {
        border-color: var(--dmx-blue);
        box-shadow: 0 0 0 3px rgba(0, 113, 175, 0.15);
    }
    .address-preview-card {
        background: #ffffff;
        border: 1.5px dashed #0284c7;
        border-radius: 8px;
        padding: 0.75rem 0.9rem;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        margin-top: 0.5rem;
    }
    .address-preview-text {
        font-weight: 700;
        color: var(--dmx-blue);
        font-size: 0.9rem;
        line-height: 1.45;
        margin-top: 0.15rem;
    }
    .btn-toggle-address-mode {
        background: none;
        border: none;
        color: var(--dmx-blue);
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0;
        text-decoration: underline;
    }
    .btn-toggle-address-mode:hover {
        color: var(--dmx-blue-dark);
    }

    @media (max-width: 850px) {
        .checkout-layout {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 600px) {
        .address-grid {
            grid-template-columns: 1fr;
            gap: 0.65rem;
        }
    }
</style>
@endsection

@section('content')
<div class="checkout-layout">
    <!-- Form Giao Hàng -->
    <div class="checkout-card">
        <h2><i class="fa-solid fa-truck"></i> Thông Tin Nhận Hàng (Giao Tận Nơi)</h2>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 0.85rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.88rem;">
                <ul style="padding-left: 1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="form-group">
                <label>Họ và tên người nhận</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled style="background: #f1f5f9;">
            </div>

            <!-- SỐ ĐIỆN THOẠI LIÊN HỆ -->
            <div class="form-group">
                <label for="phone">Số điện thoại liên hệ *</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone', Auth::user()->phone) }}" placeholder="Ví dụ: 0988 123 456" required>
                <div style="font-size: 0.76rem; color: #64748b; margin-top: 0.35rem;">
                    <i class="fa-solid fa-phone"></i> Nhân viên giao hàng sẽ gọi số này để xác nhận đơn và hẹn giờ giao tận nơi.
                </div>
            </div>

            <!-- ĐỊA CHỈ NHẬN HÀNG VỚI DROPDOWN GHN (TÍNH PHÍ VẬN CHUYỂN TỰ ĐỘNG) -->
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                    <label style="margin-bottom: 0; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-location-dot" style="color: var(--dmx-red);"></i> Nơi nhận hàng & Tính cước GHN *
                    </label>
                    <button type="button" class="btn-toggle-address-mode" id="btnToggleMode" onclick="toggleAddressMode()">
                        <i class="fa-solid fa-pen-to-square"></i> <span id="btnToggleModeText">Nhập tự do dạng chữ</span>
                    </button>
                </div>

                <!-- CHẾ ĐỘ 1: CHỌN DROPDOWNS KHU VỰC TỰ ĐỘNG THEO GHN -->
                <div id="dropdownAddressBox" class="address-area-box">
                    <!-- Hàng 1: Tỉnh / Thành phố & Quận / Huyện -->
                    <div class="address-grid">
                        <div class="address-field">
                            <label for="province_select"><i class="fa-solid fa-city"></i> 1. Tỉnh / Thành phố *</label>
                            <select id="province_select">
                                <option value="">-- Đang tải danh sách Tỉnh/TP... --</option>
                            </select>
                        </div>

                        <div class="address-field">
                            <label for="district_select"><i class="fa-solid fa-map"></i> 2. Quận / Huyện *</label>
                            <select id="district_select" disabled>
                                <option value="">-- Chọn Tỉnh/TP trước --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hàng 2: Phường / Xã & Số nhà -->
                    <div class="address-grid">
                        <div class="address-field">
                            <label for="ward_select"><i class="fa-solid fa-signs-post"></i> 3. Phường / Xã / Thị trấn *</label>
                            <select id="ward_select" disabled>
                                <option value="">-- Chọn Quận/Huyện trước --</option>
                            </select>
                        </div>

                        <div class="address-field">
                            <label for="input_street"><i class="fa-solid fa-house-chimney"></i> 4. Số nhà, tên đường, tòa nhà *</label>
                            <input type="text" id="input_street" placeholder="Ví dụ: Số 96 Nguyễn Du, Tầng 3..." oninput="updateFullAddressText()">
                        </div>
                    </div>

                    <!-- Khung xem trước địa chỉ hoàn chỉnh trực quan -->
                    <div class="address-preview-card">
                        <i class="fa-solid fa-map-location-dot" style="color: var(--dmx-red); font-size: 1.25rem; margin-top: 0.1rem;"></i>
                        <div style="flex: 1;">
                            <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Địa chỉ giao hàng hoàn chỉnh:</div>
                            <div id="addressPreviewText" class="address-preview-text">
                                (Vui lòng chọn khu vực ở các mục 1, 2, 3, 4 ở trên)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHẾ ĐỘ 2: NHẬP TỰ DO DẠNG TEXTAREA (DÙNG KHI CẦN) -->
                <div id="manualAddressBox" style="display: none; margin-top: 0.5rem;">
                    <textarea id="manual_shipping_address" rows="3" class="form-control" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." oninput="onManualAddressInput()">{{ old('shipping_address', Auth::user()->address) }}</textarea>
                    <div style="font-size: 0.76rem; color: #64748b; margin-top: 0.35rem;">
                        <i class="fa-solid fa-circle-info"></i> Bạn có thể gõ trực tiếp hoặc dán địa chỉ nhận hàng tự do tại đây.
                    </div>
                </div>

                <!-- CÁC TRƯỜNG ẨN GỬI LÊN SERVER CHO GHN -->
                <input type="hidden" name="to_district_id" id="to_district_id" value="{{ old('to_district_id') }}">
                <input type="hidden" name="to_ward_code" id="to_ward_code" value="{{ old('to_ward_code') }}">
                <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">
                <input type="hidden" name="shipping_address" id="shipping_address" value="{{ old('shipping_address', Auth::user()->address) }}" required>

                @if(Auth::user()->address)
                    <div style="font-size: 0.78rem; color: #16a34a; margin-top: 0.45rem; display: flex; align-items: center; gap: 0.35rem;">
                        <i class="fa-solid fa-circle-check"></i> Đã lưu sẵn địa chỉ trong hồ sơ: <strong>{{ Auth::user()->address }}</strong>
                    </div>
                @endif
            </div>

            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; font-size: 0.95rem;">
                    <i class="fa-solid fa-hand-holding-dollar" style="color: var(--dmx-blue);"></i> Phương thức thanh toán
                </div>

                <!-- COD -->
                <label for="pm_cod" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem; border: 2px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.6rem; cursor: pointer; transition: border-color .2s;" id="pm_cod_label">
                    <input type="radio" name="payment_method" id="pm_cod" value="cod" checked onchange="updatePayBtn()"
                           style="margin-top: 0.2rem; accent-color: var(--dmx-blue); width: 18px; height: 18px; flex-shrink: 0;">
                    <div>
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">
                            <i class="fa-solid fa-money-bill-wave" style="color: #16a34a;"></i> Thanh toán tiền mặt khi nhận hàng (COD)
                        </div>
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
                            Được kiểm tra lò và chạy thử trước khi thanh toán. Miễn phí, không thu thêm phụ phí.
                        </div>
                    </div>
                </label>

                <!-- MoMo -->
                <label for="pm_momo" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: border-color .2s;" id="pm_momo_label">
                    <input type="radio" name="payment_method" id="pm_momo" value="momo" onchange="updatePayBtn()"
                           style="margin-top: 0.2rem; accent-color: #ae2070; width: 18px; height: 18px; flex-shrink: 0;">
                    <div>
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: linear-gradient(135deg, #ae2070, #d64a8b); color: #fff; padding: 0.12rem 0.55rem; border-radius: 5px; font-size: 0.82rem;">
                                <i class="fa-solid fa-wallet"></i> MoMo
                            </span>
                            Ví MoMo / Thẻ ATM nội địa (Test Sandbox)
                        </div>
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
                            Thanh toán trực tuyến an toàn — Xác nhận đơn ngay lập tức sau khi thanh toán thành công.
                        </div>

                        <!-- Hộp thông tin thẻ test MoMo Sandbox -->
                        <div id="momo_test_info" style="display: none; margin-top: 0.6rem; background: #fff5f8; border: 1px dashed #f472b6; border-radius: 6px; padding: 0.65rem; font-size: 0.8rem; color: #831843;">
                            <div style="font-weight: 700; margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.35rem;">
                                <i class="fa-solid fa-circle-info"></i> Thông tin thẻ ATM Test của MoMo Sandbox:
                            </div>
                            <ul style="margin: 0; padding-left: 1.2rem; line-height: 1.5;">
                                <li>Số thẻ: <strong style="font-family: monospace; font-size: 0.88rem; color: #9d174d;">9704000000000018</strong></li>
                                <li>Tên chủ thẻ: <strong>NGUYEN VAN A</strong></li>
                                <li>Ngày phát hành: <strong style="color: #be123c;">03/07</strong> <em>(Tháng 03/2007 — Không phải ngày hết hạn)</em></li>
                                <li>Mã OTP xác thực: <strong>000000</strong> (hoặc 123456)</li>
                            </ul>
                        </div>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn-submit-order" id="btnSubmitOrder">
                <i class="fa-solid fa-check" id="btnSubmitIcon"></i>
                <span id="btnSubmitText">ĐẶT MUA NGAY (COD)</span>
            </button>

            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('cart.index') }}" style="color: var(--dmx-blue); text-decoration: none; font-size: 0.88rem;">
                    ← Quay lại giỏ hàng
                </a>
            </div>
        </form>
    </div>

    <!-- Tóm Tắt Đơn Hàng -->
    <div class="checkout-card">
        <h2><i class="fa-solid fa-receipt"></i> Đơn Hàng Của Bạn</h2>

        @if(session('info'))
            <div style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.88rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-info"></i> {{ session('info') }}
            </div>
        @endif

        <div style="margin-bottom: 1.5rem;">
            @php 
                $subtotal = 0; 
                $coupon = session('coupon');
                $discount = $coupon['discount'] ?? 0;
            @endphp
            @foreach($cart->cartItems as $item)
                @php 
                    $itemPrice = $item->price;
                    $itemTotal = $itemPrice * $item->quantity; 
                    $subtotal += $itemTotal; 
                @endphp
                <div class="order-item-row">
                    <div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <span style="background: var(--dmx-blue-light); color: var(--dmx-blue); font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.8rem;">x{{ $item->quantity }}</span>
                            <span style="font-weight: 600; color: #334155;">{{ Str::limit($item->product->name, 28) }}</span>
                        </div>
                        @if($item->color)
                            <div style="font-size: 0.76rem; color: #64748b; margin-left: 2rem; margin-top: 0.1rem;">
                                Màu: <strong style="color: var(--dmx-blue);">{{ $item->color }}</strong>
                            </div>
                        @endif
                    </div>
                    <span style="font-weight: 700; color: #1e293b;">{{ number_format($itemTotal, 0, ',', '.') }}₫</span>
                </div>
            @endforeach
        </div>

        <div style="border-top: 1px dashed #cbd5e1; padding-top: 1rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; color: #64748b;">
                <span>Tiền hàng:</span>
                <span style="font-weight: 600; color: #1e293b;">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; color: #64748b;">
                <span>Cước vận chuyển (GHN):</span>
                <span id="shipping_fee_text" style="color: #16a34a; font-weight: 700;">Chọn nơi nhận để tính phí</span>
            </div>

            @if($discount > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; color: #16a34a;">
                    <span>Giảm giá Voucher ({{ $coupon['code'] }}):</span>
                    <span style="font-weight: 700;">-{{ number_format($discount, 0, ',', '.') }}₫</span>
                </div>
            @endif

            <div class="order-total-row">
                <span>Tổng thanh toán:</span>
                <span id="final_total_text">{{ number_format(max(0, $subtotal - $discount), 0, ',', '.') }}₫</span>
            </div>

            <!-- Input lưu tiền hàng an toàn cho script -->
            <input type="hidden" id="total_price_input" value="{{ max(0, $subtotal - $discount) }}">
        </div>

        <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 0.85rem; border-radius: 8px; font-size: 0.82rem; color: #92400e;">
            <i class="fa-solid fa-phone-volume"></i> Cần hỗ trợ đơn hàng? Gọi ngay Hotline CSKH Hộ Kinh Doanh: <a href="tel:0913014602" style="color: var(--dmx-red); font-weight: 800; text-decoration: none;">0913 014 602</a>.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById('province_select');
    const districtSelect = document.getElementById('district_select');
    const wardSelect = document.getElementById('ward_select');
    const inputStreet = document.getElementById('input_street');
    const shippingFeeText = document.getElementById('shipping_fee_text');
    const finalTotalText = document.getElementById('final_total_text');
    const totalPriceInput = document.getElementById('total_price_input');
    const hiddenShippingAddress = document.getElementById('shipping_address');
    const hiddenToDistrict = document.getElementById('to_district_id');
    const hiddenToWard = document.getElementById('to_ward_code');
    const hiddenShippingFee = document.getElementById('shipping_fee_input');
    const addressPreviewText = document.getElementById('addressPreviewText');

    const districtsUrl = "{{ route('locations.districts', ['provinceId' => '__PROVINCE__']) }}";
    const wardsUrl = "{{ route('locations.wards', ['districtId' => '__DISTRICT__']) }}";

    // Lấy tiền hàng an toàn từ input ẩn
    const subtotal = parseInt(totalPriceInput ? totalPriceInput.value : 0) || 0;

    let isManualMode = false;

    // 1. Tải danh sách Tỉnh/Thành phố từ GHN API
    fetch("{{ route('locations.provinces') }}")
        .then(res => res.json())
        .then(res => {
            if (res.data && res.data.length > 0) {
                renderProvinceOptions(res.data);
            } else {
                useFallbackProvinces();
            }
        })
        .catch(err => {
            console.warn("Lỗi load tỉnh thành từ GHN API, chuyển sang danh sách dự phòng:", err);
            useFallbackProvinces();
        });

    function renderProvinceOptions(provinces) {
        let options = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
        const priorityIds = [201, 202, 203];
        const priorityList = provinces.filter(p => priorityIds.includes(parseInt(p.ProvinceID || p.id)));
        const otherList = provinces.filter(p => !priorityIds.includes(parseInt(p.ProvinceID || p.id)));

        if (priorityList.length > 0) {
            options += '<optgroup label="⭐ Tỉnh / Thành phố trung tâm">';
            priorityList.forEach(p => {
                const id = p.ProvinceID || p.id;
                const name = p.ProvinceName || p.name;
                options += `<option value="${id}" data-name="${name}">${name}</option>`;
            });
            options += '</optgroup>';
        }

        if (otherList.length > 0) {
            options += '<optgroup label="🏙️ Tất cả Tỉnh / Thành phố khác">';
            otherList.forEach(p => {
                const id = p.ProvinceID || p.id;
                const name = p.ProvinceName || p.name;
                options += `<option value="${id}" data-name="${name}">${name}</option>`;
            });
            options += '</optgroup>';
        }

        provinceSelect.innerHTML = options;
    }

    function useFallbackProvinces() {
        const fallbackProvinces = [
            { id: 201, name: "Hà Nội" },
            { id: 202, name: "TP. Hồ Chí Minh" },
            { id: 203, name: "Đà Nẵng" },
            { id: 204, name: "Hải Phòng" },
            { id: 205, name: "Cần Thơ" },
            { id: 206, name: "Bình Dương" },
            { id: 207, name: "Đồng Nai" },
            { id: 208, name: "Quảng Ninh" },
            { id: 209, name: "Bắc Ninh" },
            { id: 210, name: "Thừa Thiên Huế" },
            { id: 211, name: "Khánh Hòa" },
            { id: 212, name: "Nghệ An" },
            { id: 213, name: "Thanh Hóa" }
        ];
        renderProvinceOptions(fallbackProvinces);
    }

    // 2. Khi chọn Tỉnh -> Tải Quận/Huyện từ GHN API
    provinceSelect.addEventListener('change', function () {
        districtSelect.innerHTML = '<option value="">-- Đang tải Quận/Huyện... --</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;
        hiddenToDistrict.value = '';
        hiddenToWard.value = '';
        updateTotals(0);
        updateFullAddressText();

        if (!this.value) return;

        fetch(districtsUrl.replace('__PROVINCE__', this.value))
            .then(res => res.json())
            .then(res => {
                if (res.data && res.data.length > 0) {
                    let options = '<option value="">-- Chọn Quận / Huyện --</option>';
                    res.data.forEach(d => {
                        options += `<option value="${d.DistrictID}" data-name="${d.DistrictName}">${d.DistrictName}</option>`;
                    });
                    districtSelect.innerHTML = options;
                    districtSelect.disabled = false;
                } else {
                    districtSelect.innerHTML = '<option value="">-- Không tải được quận/huyện --</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi load quận huyện:", err);
                districtSelect.innerHTML = '<option value="1450" data-name="Quận/Huyện trung tâm">Quận/Huyện trung tâm</option>';
                districtSelect.disabled = false;
            });
    });

    // 3. Khi chọn Quận/Huyện -> Tải Phường/Xã từ GHN API
    districtSelect.addEventListener('change', function () {
        wardSelect.innerHTML = '<option value="">-- Đang tải Phường/Xã... --</option>';
        wardSelect.disabled = true;
        hiddenToDistrict.value = this.value;
        hiddenToWard.value = '';
        updateTotals(0);
        updateFullAddressText();

        if (!this.value) return;

        fetch(wardsUrl.replace('__DISTRICT__', this.value))
            .then(res => res.json())
            .then(res => {
                if (res.data && res.data.length > 0) {
                    let options = '<option value="">-- Chọn Phường / Xã --</option>';
                    res.data.forEach(w => {
                        options += `<option value="${w.WardCode}" data-name="${w.WardName}">${w.WardName}</option>`;
                    });
                    wardSelect.innerHTML = options;
                    wardSelect.disabled = false;
                } else {
                    wardSelect.innerHTML = '<option value="">-- Không tải được phường/xã --</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi load phường xã:", err);
                wardSelect.innerHTML = '<option value="1B1507" data-name="Phường/Xã trung tâm">Phường/Xã trung tâm</option>';
                wardSelect.disabled = false;
            });
    });

    // 4. Khi chọn Phường/Xã -> Tính cước vận chuyển GHN trực tiếp
    wardSelect.addEventListener('change', function () {
        hiddenToWard.value = this.value;
        updateFullAddressText();

        if (!this.value || !districtSelect.value) return;

        shippingFeeText.innerText = 'Đang tính cước GHN...';
        shippingFeeText.style.color = '#64748b';

        fetch("{{ route('locations.fee') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                to_district_id: parseInt(districtSelect.value),
                to_ward_code: this.value
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.code === 200 && res.data) {
                const fee = parseInt(res.data.total) || 0;
                if (res.data.is_fallback) {
                    // Phí dự phòng khi shop chưa cập nhật địa chỉ
                    updateTotals(fee, true);
                } else {
                    updateTotals(fee, false);
                }
            } else {
                // Fallback nếu API trả về mã khác 200
                updateTotals(35000, true);
            }
        })
        .catch(err => {
            console.error("Lỗi tính phí:", err);
            updateTotals(35000, true);
        });
    });

    // Cập nhật hiển thị tiền cước và tổng đơn hàng
    function updateTotals(fee, isFallback = false) {
        if (fee > 0) {
            const feeFormatted = new Intl.NumberFormat('vi-VN').format(fee) + ' VNĐ';
            if (isFallback) {
                shippingFeeText.innerHTML = feeFormatted + ' <small style="color:#94a3b8;font-weight:400;">(ước tính)</small>';
            } else {
                shippingFeeText.innerHTML = feeFormatted;
            }
            shippingFeeText.style.color = '#d97706';
        } else {
            shippingFeeText.innerText = '0 VNĐ (Miễn phí)';
            shippingFeeText.style.color = '#16a34a';
        }

        const finalAmount = subtotal + fee;
        finalTotalText.innerText = new Intl.NumberFormat('vi-VN').format(finalAmount) + '₫';

        if (hiddenShippingFee) {
            hiddenShippingFee.value = fee;
        }
    }

    // Ghép nối chuỗi địa chỉ giao hàng và hiển thị xem trước
    window.updateFullAddressText = function() {
        if (isManualMode) return;

        const pOpt = provinceSelect.options[provinceSelect.selectedIndex];
        const dOpt = districtSelect.options[districtSelect.selectedIndex];
        const wOpt = wardSelect.options[wardSelect.selectedIndex];

        const provinceName = (pOpt && pOpt.value) ? (pOpt.getAttribute('data-name') || pOpt.text) : '';
        const districtName = (dOpt && dOpt.value) ? (dOpt.getAttribute('data-name') || dOpt.text) : '';
        const wardName = (wOpt && wOpt.value) ? (wOpt.getAttribute('data-name') || wOpt.text) : '';
        const street = inputStreet.value.trim();

        let parts = [];
        if (street) parts.push(street);
        if (wardName) parts.push(wardName);
        if (districtName) parts.push(districtName);
        if (provinceName) parts.push(provinceName);

        const full = parts.join(', ');

        if (full) {
            hiddenShippingAddress.value = full;
            addressPreviewText.textContent = full;
            addressPreviewText.style.color = 'var(--dmx-blue)';
        } else {
            hiddenShippingAddress.value = '';
            addressPreviewText.textContent = '(Vui lòng chọn khu vực ở các mục 1, 2, 3, 4 ở trên)';
            addressPreviewText.style.color = '#94a3b8';
        }
    };

    window.onManualAddressInput = function() {
        const val = document.getElementById('manual_shipping_address').value.trim();
        hiddenShippingAddress.value = val;
    };

    window.toggleAddressMode = function() {
        isManualMode = !isManualMode;
        const dropdownBox = document.getElementById('dropdownAddressBox');
        const manualBox = document.getElementById('manualAddressBox');
        const toggleBtnText = document.getElementById('btnToggleModeText');

        if (isManualMode) {
            dropdownBox.style.display = 'none';
            manualBox.style.display = 'block';
            toggleBtnText.textContent = 'Chọn theo Dropdown khu vực';
            document.getElementById('manual_shipping_address').value = hiddenShippingAddress.value;
        } else {
            dropdownBox.style.display = 'block';
            manualBox.style.display = 'none';
            toggleBtnText.textContent = 'Nhập tự do dạng chữ';
            updateFullAddressText();
        }
    };

    // Khởi tạo hiển thị địa chỉ nếu user đã có trong hồ sơ
    const initialAddress = "{{ old('shipping_address', Auth::user()->address) }}";
    if (initialAddress) {
        addressPreviewText.textContent = initialAddress;
        addressPreviewText.style.color = 'var(--dmx-blue)';
    }
});

// ────────────────────────────────────────
// Phương thức thanh toán — cập nhật UI động
// ────────────────────────────────────────
function updatePayBtn() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
    const btnText = document.getElementById('btnSubmitText');
    const btnIcon = document.getElementById('btnSubmitIcon');
    const codLabel = document.getElementById('pm_cod_label');
    const momoLabel = document.getElementById('pm_momo_label');
    const btn = document.getElementById('btnSubmitOrder');

    const momoTestInfo = document.getElementById('momo_test_info');

    if (selected === 'momo') {
        btnText.textContent = 'THANH TOÁN QUA VÍ MOMO';
        btnIcon.className = 'fa-solid fa-wallet';
        btn.style.background = 'linear-gradient(135deg, #ae2070, #d64a8b)';
        codLabel.style.borderColor = '#e2e8f0';
        momoLabel.style.borderColor = '#ae2070';
        momoLabel.style.background = '#fdf2f8';
        if (momoTestInfo) momoTestInfo.style.display = 'block';
    } else {
        btnText.textContent = 'ĐẶT MUA NGAY (COD)';
        btnIcon.className = 'fa-solid fa-check';
        btn.style.background = '';
        codLabel.style.borderColor = 'var(--dmx-blue)';
        codLabel.style.background = '#eff6ff';
        momoLabel.style.borderColor = '#e2e8f0';
        momoLabel.style.background = '';
        if (momoTestInfo) momoTestInfo.style.display = 'none';
    }
}

// Chạy ngay khi trang load để highlight mặc định
document.addEventListener('DOMContentLoaded', updatePayBtn);
</script>
@endsection
