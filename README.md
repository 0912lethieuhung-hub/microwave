# 🔥 Siêu Lò Nướng — Website TMĐT Laravel 11

Dự án website thương mại điện tử bán lò vi sóng & thiết bị gia dụng xây dựng trên **Laravel 11**, tích hợp thanh toán **MoMo** và vận chuyển **GHN Sandbox** theo mô hình Hộ Kinh Doanh.

---

## ✨ Tính Năng Nổi Bật

### 🛒 Trải Nghiệm Mua Sắm
- Trang chủ hiển thị sản phẩm, danh mục, sản phẩm nổi bật
- Bộ lọc & tìm kiếm sản phẩm theo danh mục, giá, màu sắc
- Giỏ hàng với hỗ trợ biến thể màu sắc
- Wishlist yêu thích sản phẩm
- Đánh giá sản phẩm (Product Review) sau khi mua
- Mã giảm giá (Voucher) theo % hoặc số tiền cố định

### 💳 Thanh Toán
- **COD** (Thanh toán khi nhận hàng)
- **MoMo** (Thanh toán QR qua ví điện tử MoMo, hỗ trợ IPN webhook & callback)

### 🚚 Tích Hợp GHN Shipping (Giao Hàng Nhanh Sandbox)
- Dropdown chọn Tỉnh / Quận / Phường lấy từ GHN API real-time
- Tính cước vận chuyển động (ví dụ: HN → Q1 HCM ≈ 53.900₫)
- Tự động tạo vận đơn GHN khi đặt hàng COD hoặc sau khi thanh toán MoMo
- Đồng bộ trạng thái 2 chiều: hệ thống ↔ GHN Sandbox
- Hủy vận đơn tự động khi đơn hàng bị hủy
- Link theo dõi trực tiếp trên portal `5sao.ghn.dev`

### 🔐 Xác Thực & Người Dùng
- Đăng ký / Đăng nhập truyền thống
- Đăng nhập qua **Google** & **Facebook** (Laravel Socialite)
- Xác minh email
- Quản lý hồ sơ, đổi mật khẩu

### 🛠️ Quản Trị Admin
- Dashboard thống kê: Doanh thu, đơn hàng, khách hàng, lượt truy cập
- Quản lý sản phẩm (CRUD + đa ảnh + biến thể màu + tồn kho)
- Quản lý danh mục, đơn hàng, khách hàng, voucher
- Đồng bộ trạng thái GHN hàng loạt cho tất cả đơn đang vận chuyển
- Tự động hoàn tất đơn khi GHN trả về `delivered`

---

## 🏗️ Công Nghệ Sử Dụng

| Thành phần | Công nghệ |
|---|---|
| Backend Framework | Laravel 11 (PHP 8.3+) |
| Database | SQLite |
| Frontend Styling | Tailwind CSS + Vite |
| Xác thực Social | Laravel Socialite (Google, Facebook) |
| Thanh toán | MoMo Payment Gateway |
| Vận chuyển | GHN (Giao Hàng Nhanh) Sandbox API |
| Queue / Cache | Database Driver |

---

## ⚙️ Cài Đặt & Khởi Chạy

### Yêu cầu hệ thống
- PHP >= 8.3
- Composer
- Node.js & NPM
- [Laragon](https://laragon.org/) (khuyên dùng) hoặc bất kỳ web server local nào

### 1. Clone & cài đặt dependencies

```bash
git clone <repo-url> microwave
cd microwave

composer install
npm install
```

### 2. Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Mở file `.env` và điền các thông tin sau:

```ini
APP_NAME="Siêu Lò Nướng"
APP_URL=http://microwave.test

DB_CONNECTION=sqlite

# MoMo Payment
MOMO_PARTNER_CODE=your_partner_code
MOMO_ACCESS_KEY=your_access_key
MOMO_SECRET_KEY=your_secret_key
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create

# GHN Shipping (Sandbox)
GHN_BASE_URL=https://dev-online-gateway.ghn.vn/shiip/public-api
GHN_PORTAL_URL=https://5sao.ghn.dev
GHN_TOKEN=your_ghn_token
GHN_SHOP_ID=your_shop_id
GHN_FROM_DISTRICT_ID=1485
GHN_DEFAULT_WEIGHT=200

# Social Login
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

FACEBOOK_CLIENT_ID=your_client_id
FACEBOOK_CLIENT_SECRET=your_client_secret
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"
```

### 3. Migrate & Seed database

```bash
touch database/database.sqlite
php artisan migrate --seed
```

### 4. Build assets & khởi chạy

```bash
npm run dev
php artisan serve
```

Truy cập: `http://microwave.test` (Laragon) hoặc `http://localhost:8000` (artisan serve)

---

## 🧪 Tài Khoản Demo

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| Khách hàng | `0912lethieuhung@gmail.com` | `password` |

---

## 🚚 Kiểm Thử GHN Sandbox

1. Vào giỏ hàng → **"TIẾN HÀNH ĐẶT HÀNG"**
2. Tại phần địa chỉ nhận hàng: chọn **TP. Hồ Chí Minh** → **Quận 1** → **Phường Bến Nghé**
3. Hệ thống tự động tính cước từ kho HN đến HCM: **≈ 53.900₫**
4. Bấm **"ĐẶT HÀNG NGAY"** — vận đơn GHN được tạo ngay lập tức
5. Kiểm tra đơn tại: [`https://5sao.ghn.dev`](https://5sao.ghn.dev)

---

## 📁 Cấu Trúc Thư Mục Quan Trọng

```
app/
├── Http/Controllers/
│   ├── Admin/          # Controllers quản trị (Dashboard, Order, Product...)
│   ├── User/           # Controllers user (MoMo, GHN...)
│   ├── CartController.php
│   ├── CheckoutController.php
│   └── UserOrderController.php
├── Models/             # Eloquent Models (Order, Product, Cart, Voucher...)
└── Services/
    ├── GHNService.php          # HTTP Client giao tiếp GHN API
    ├── GHNOrderService.php     # Logic tạo/hủy/sync vận đơn GHN
    └── MomoService.php         # Logic thanh toán MoMo

resources/views/
├── admin/              # Blade views giao diện Admin
├── checkout/           # Trang thanh toán + dropdown GHN
├── orders/             # Trang theo dõi đơn hàng
└── layouts/            # Layout chính có header/footer HKD
```

---

## 📜 License

Dự án được phát triển theo giấy phép [MIT](https://opensource.org/licenses/MIT).
