# TÀI LIỆU HƯỚNG DẪN & THUYẾT MINH TOÀN BỘ MÃ NGUỒN (LAB 5 - SHIPPING)
## DỰ ÁN WEBSITE THƯƠNG MẠI ĐIỆN TỬ "SIÊU LÒ NƯỚNG" (LARAVEL 11)
### TÍCH HỢP HỆ THỐNG VẬN CHUYỂN GIAO HÀNG NHANH SANDBOX (5sao.ghn.dev) THEO MÔ HÌNH HỘ KINH DOANH

---

## 1. THÔNG TIN PHÁP LÝ & ĐƠN VỊ VẬN HÀNH (HỘ KINH DOANH)

Khác với mô hình tài khoản cá nhân thông thường, dự án được chuẩn hóa theo đúng tiêu chuẩn thực tế của một **Hộ Kinh Doanh (HKD)** chuyên phân phối thiết bị gia dụng và lò vi sóng chính hãng tại Việt Nam:

- **Tên cơ sở kinh doanh:** HỘ KINH DOANH SIÊU LÒ NƯỚNG
- **Giấy phép ĐKKD:** 01E8039281 do UBND Quận Hai Bà Trưng, TP. Hà Nội cấp ngày 15/03/2021
- **Mã số thuế (MST):** 0109887766
- **Trụ sở & Showroom chính:** 96 Nguyễn Du, Phường Cửa Nam, Quận Hai Bà Trưng, TP. Hà Nội
- **Kho hàng giao nhận GHN:** 277 Ngõ 277 Quan Hoa, Phường Quan Hoa, Quận Cầu Giấy, TP. Hà Nội (`district_id: 1485`, `ward_code: 1A0606`)
- **Hotline CSKH & Khiếu nại vận chuyển:** 0913 014 602
- **Email tiếp nhận thông tin:** `0912lethieuhung@gmail.com`

---

## 2. MÔI TRƯỜNG TÍCH HỢP GHN SANDBOX (5sao.ghn.dev)

Theo đúng yêu cầu của **Lab 5 (Shipping)**, toàn bộ quá trình tích hợp, tính cước động, tạo vận đơn và đồng bộ trạng thái đều vận hành trên môi trường **Sandbox của Giao Hàng Nhanh**:

| Tham số | Môi trường Sandbox (5sao.ghn.dev) | Ghi chú |
| :--- | :--- | :--- |
| **Portal Quản trị Sandbox** | `https://5sao.ghn.dev/` | Đăng nhập kiểm tra danh sách đơn test, in phiếu gửi, cập nhật hành trình |
| **API Gateway URL** | `https://dev-online-gateway.ghn.vn/shiip/public-api` | Cổng API lập trình dành riêng cho môi trường thử nghiệm |
| **API Token** | `3955ea90-aa9c-11f1-a973-aee5264794df` | Khởi tạo từ `sso.ghn.dev` (Sandbox) |
| **Shop ID** | `217565` | Cửa hàng đại diện của "HKD SIÊU LÒ NƯỚNG" tại Sandbox |
| **Quận gửi hàng (`GHN_FROM_DISTRICT_ID`)** | `1485` | Quận Cầu Giấy, Hà Nội |
| **Phường gửi hàng (`from_ward_code`)** | `1A0606` | Phường Quan Hoa, Quận Cầu Giấy, Hà Nội |
| **Trọng lượng mặc định (`GHN_DEFAULT_WEIGHT`)** | `200` (gram) | Chuẩn cho tính cước bưu kiện cơ sở |

File cấu hình môi trường `.env`:
```ini
# GIAO HÀNG NHANH (GHN) API CONFIG - MÔI TRƯỜNG TEST (5sao.ghn.dev THEO LAB 5)
GHN_BASE_URL=https://dev-online-gateway.ghn.vn/shiip/public-api
GHN_PORTAL_URL=https://5sao.ghn.dev
GHN_TOKEN=3955ea90-aa9c-11f1-a973-aee5264794df
GHN_SHOP_ID=217565
GHN_VERIFY_SSL=false
GHN_FROM_DISTRICT_ID=1485
GHN_DEFAULT_WEIGHT=200
```

---

## 3. KIẾN TRÚC MÃ NGUỒN VÀ DÒNG DỮ LIỆU 2 CHIỀU

### 3.1. Luồng Người Mua Hàng (Frontend Customer)
1. **Chọn Tỉnh/Thành ➔ Quận/Huyện ➔ Phường/Xã:**
   - Tại trang Thanh toán (`/checkout`), JavaScript gọi AJAX về backend:
     - `GET /ghn/provinces` ➔ Lấy 65 Tỉnh/Thành phố trực tiếp từ GHN Sandbox.
     - `GET /ghn/districts/{provinceId}` ➔ Lấy Quận/Huyện tương ứng.
     - `GET /ghn/wards/{districtId}` ➔ Lấy Phường/Xã tương ứng.
2. **Tính Cước Vận Chuyển Real-Time:**
   - Khi khách chọn xong Phường/Xã, JavaScript gửi `district_id` và `ward_code` lên `POST /ghn/calculate-fee`.
   - `GHNService` gọi sang GHN API `/v2/shipping-order/fee`, trả về tiền cước chính xác (ví dụ: gửi vào Q1 TP.HCM là 53.900₫).
   - Tổng tiền đơn hàng tự động cộng thêm phí ship.
3. **Tạo Đơn Hàng & Tự Động Bắn Vận Đơn GHN:**
   - Khách bấm **"ĐẶT HÀNG NGAY"**.
   - `CheckoutController@store` lưu đơn vào SQLite, đồng thời kích hoạt `GHNOrderService@create`.
   - Payload gửi lên GHN:
     - `payment_type_id = 1`: **Shop trả cước**. Khách chỉ thanh toán đúng tổng tiền COD trên web. Shipper không được thu thêm tiền ship lần 2.
     - `return_phone`: `0913014602`, `return_address`: `277 Ngõ 277 Quan Hoa, Cầu Giấy, Hà Nội`.
     - `note`: `HKD SIÊU LÒ NƯỚNG - Đơn hàng #...`
   - GHN Dev Gateway trả về mã vận đơn (ví dụ: `L8K8WX`, `L8K8AA`,...). Mã này được lưu vào cột `ghn_order_code` và đặt `shipping_status = 'ready_to_pick'`.

### 3.2. Luồng Quản Trị Viên & Đồng Bộ Trạng Thái 2 Chiều (Admin Logistics)
1. **Trực quan hóa trạng thái thực tế:**
   - Tại màn hình Quản lý đơn hàng (`admin/orders` và `admin/orders/{id}`), hệ thống hiển thị huy hiệu vận chuyển chuyên nghiệp:
     - 🟡 **Chờ lấy hàng (`ready_to_pick`)**: Đã tạo đơn trên GHN, chờ bưu tá qua kho HKD lấy hàng.
     - 🔵 **Đang lấy hàng (`picking`)**: Bưu tá GHN đang đến địa chỉ shop nhận hàng.
     - 🔷 **Đã nhập kho GHN (`picked` / `storing`)**: Kiện hàng đã ở bưu cục/kho khai thác GHN.
     - 🚚 **Đang giao hàng (`delivering` / `transporting`)**: Bưu tá đang giao đến tay khách.
     - 🟢 **Đã giao thành công (`delivered`)**: Khách đã nhận hàng.
     - 🔴 **Giao hàng thất bại (`delivery_fail`)**.
     - 🔄 **Đang hoàn hàng (`returning`)** / 📦 **Đã hoàn về shop (`returned`)**.
     - ⛔ **Đã hủy vận đơn (`cancelled`)**.
2. **Đồng Bộ Dữ Liệu Thời Gian Thực (Sync):**
   - **Tự động:** Khi Admin bấm vào xem chi tiết đơn hàng (`Admin/OrderController@show`), hệ thống tự động gọi API `/v2/shipping-order/detail` của GHN để lấy trạng thái bưu kiện mới nhất.
   - **Nút "🔄 Đồng bộ GHN" trên từng đơn:** Cho phép cập nhật tức thì trạng thái và dự kiến thời gian giao hàng (`leadtime`).
   - **Nút "Đồng bộ trạng thái GHN" hàng loạt:** Quét toàn bộ các đơn đang vận chuyển và đồng bộ trạng thái mới từ GHN.
   - **Tự động hoàn tất:** Khi GHN trả về trạng thái `delivered`, hệ thống tự động đánh dấu trạng thái đơn hàng sang `completed`.
3. **Liên kết Tra Cứu Trực Tiếp trên GHN 5sao:**
   - Link gắn kèm mã vận đơn: `https://5sao.ghn.dev/?order_code={order_code}`. Admin và khách hàng có thể click để mở thẳng hệ thống theo dõi đơn bưu cục GHN Sandbox.
4. **Hủy Vận Đơn Tự Động:**
   - Khi Admin đổi trạng thái đơn sang `cancelled`, hệ thống tự động gọi `GHNOrderService@cancel` (`/v2/switch-status/cancel`) để hủy bưu gửi trên GHN, đồng thời hoàn trả số lượng hàng vào kho tồn.

---

## 4. DANH SÁCH FILE MÃ NGUỒN CỐT LÕI

| Tệp tin | Vai trò chức năng |
| :--- | :--- |
| [`app/Services/GHNService.php`](file:///d:/laragon/www/microwave/app/Services/GHNService.php) | Tầng kết nối HTTP Client với GHN Dev Gateway (Tỉnh/Huyện/Xã, Cước phí, Tạo đơn, Hủy đơn, Tra cứu chi tiết). |
| [`app/Services/GHNOrderService.php`](file:///d:/laragon/www/microwave/app/Services/GHNOrderService.php) | Đóng gói Order Eloquent thành Payload GHN với thông tin HKD Siêu Lò Nướng; đồng bộ trạng thái 2 chiều `syncStatus()`. |
| [`app/Models/Order.php`](file:///d:/laragon/www/microwave/app/Models/Order.php) | Khai báo 5 trường GHN, Accessor `shipping_badge` tạo nhãn màu/icon/giải thích trạng thái và `ghn_tracking_url`. |
| [`app/Http/Controllers/CheckoutController.php`](file:///d:/laragon/www/microwave/app/Http/Controllers/CheckoutController.php) | Xử lý thanh toán, cộng cước GHN, lưu đơn và tự động kích hoạt tạo vận đơn GHN Dev. |
| [`app/Http/Controllers/Admin/OrderController.php`](file:///d:/laragon/www/microwave/app/Http/Controllers/Admin/OrderController.php) | Quản trị đơn hàng Admin, tự động sync khi xem chi tiết, sync hàng loạt, hủy vận đơn GHN khi hủy đơn. |
| [`resources/views/checkout/index.blade.php`](file:///d:/laragon/www/microwave/resources/views/checkout/index.blade.php) | Form chọn Tỉnh/Huyện/Xã dropdown tự động từ GHN API, tính cước dynamic real-time. |
| [`resources/views/admin/orders/index.blade.php`](file:///d:/laragon/www/microwave/resources/views/admin/orders/index.blade.php) | Danh sách đơn hàng Admin với cột Vận chuyển GHN, nút sync hàng loạt, link portal 5sao. |
| [`resources/views/admin/orders/show.blade.php`](file:///d:/laragon/www/microwave/resources/views/admin/orders/show.blade.php) | Màn hình chi tiết vận đơn GHN, trạng thái thực tế từ GHN, dự kiến giao hàng, nút đồng bộ. |
| [`resources/views/orders/show.blade.php`](file:///d:/laragon/www/microwave/resources/views/orders/show.blade.php) | Màn hình theo dõi đơn hàng của Khách hàng, hiển thị cước GHN, mã vận đơn và link tra cứu 5sao. |
| [`resources/views/layouts/frontend.blade.php`](file:///d:/laragon/www/microwave/resources/views/layouts/frontend.blade.php) | Header/Footer hiển thị đầy đủ pháp nhân Hộ Kinh Doanh Siêu Lò Nướng, ĐKKD, MST, Showroom, Hotline. |

---

## 5. CÁCH KIỂM THỬ VÀ DEMO ĐÁNH GIÁ LAB 5

1. **Khởi động:** Bật Laragon (`Start All`), truy cập: `http://microwave.test`.
2. **Tài khoản Admin:** `admin@example.com` / `password`.
3. **Tài khoản Khách hàng:** `0912lethieuhung@gmail.com` / `password`.
4. **Quy trình đặt hàng test:**
   - Vào Giỏ hàng ➔ Bấm **"TIẾN HÀNH ĐẶT HÀNG"**.
   - Tại phần Nơi nhận hàng: Chọn **TP. Hồ Chí Minh** ➔ Chọn **Quận 1** ➔ Chọn **Phường Bến Nghé**.
   - Hệ thống sẽ hiển thị tiền cước GHN tính tự động từ kho Hà Nội vào HCM là **53.900₫**.
   - Bấm **"ĐẶT HÀNG NGAY"**.
   - Đơn hàng mới được tạo lập tức trên `https://5sao.ghn.dev/` với mã vận đơn dạng `L8K8...`.
5. **Quy trình kiểm tra Admin:**
   - Vào `http://microwave.test/admin/orders`.
   - Xem mã vận đơn vừa tạo, bấm **"Tra cứu trên portal GHN (5sao)"** để mở trực tiếp trang `https://5sao.ghn.dev/`.
   - Thử bấm nút **"🔄 Đồng bộ GHN"** để thấy dữ liệu trạng thái được cập nhật trực tiếp từ GHN API.
