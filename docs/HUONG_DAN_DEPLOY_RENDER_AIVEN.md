# HƯỚNG DẪN TRIỂN KHAI DỰ ÁN MICROWAVE LÊN RENDER & AIVEN MYSQL

> **Tài liệu chuẩn mực kỹ thuật:** Hướng dẫn toàn diện quy trình đóng gói, cấu hình và đưa ứng dụng Laravel **Siêu Lò Nướng (Microwave)** từ môi trường phát triển cục bộ (Local) lên hạ tầng đám mây Internet sử dụng **Docker Web Service trên Render** và cơ sở dữ liệu **MySQL trên Aiven**.

---

## 1. Mục tiêu và Kiến trúc Triển khai

### 1.1. Mục tiêu
Tài liệu hướng dẫn quy trình chuyển giao dự án Laravel hoàn chỉnh lên Internet với độ ổn định cao, chi phí tối ưu (tận dụng gói Free của Render & Aiven), bảo mật SSL/TLS cho cả máy chủ web và kết nối cơ sở dữ liệu.

### 1.2. Mô hình hoạt động
```
[Máy Local] ──(git push)──> [GitHub Repository]
                                  │
                                  ▼ (Webhook / Auto Deploy)
[Aiven Cloud] ───────────> [Render Web Service]
(Managed MySQL Service)    (Docker Container: Alpine + PHP 8.3 FPM + Nginx)
      ▲                           │
      └───(Kết nối SSL CA pem)────┘
```
1. **Mã nguồn:** Quản lý phiên bản trên GitHub.
2. **Web Service (Render):** Tự động kéo mã nguồn từ GitHub, build Docker multi-stage (PHP 8.3 FPM Alpine + Nginx).
3. **Database (Aiven):** Cung cấp MySQL Server độc lập, yêu cầu xác thực chứng chỉ CA (`ca.pem`).
4. **Điều phối (Entrypoint):** Tự động cấp quyền chứng chỉ SSL, đồng bộ migration, khởi tạo tài khoản quản trị và kích hoạt đồng thời PHP-FPM + Nginx.

---

## 2. Công cụ và Thuật ngữ cần biết

| Công cụ / Thuật ngữ | Định nghĩa và vai trò trong dự án Microwave |
| :--- | :--- |
| **Composer** | Quản lý thư viện PHP. File `composer.lock` cố định phiên bản thư viện ổn định cho production. |
| **Docker & Dockerfile** | Đóng gói mã nguồn Laravel cùng môi trường PHP 8.3, Nginx, các thư viện mở rộng (`pdo_mysql`, `mbstring`, `zip`, `gd`, `bcmath`, `opcache`). |
| **Nginx & PHP-FPM** | Nginx đón nhận HTTP request từ Internet (qua cổng động `$PORT`), chuyển tiếp FastCGI đến PHP-FPM tại `127.0.0.1:9000` để xử lý code Laravel. |
| **Render** | Nền tảng Cloud PaaS chạy ứng dụng dưới dạng Web Service. Quản lý biến môi trường (`Environment`) và tệp tin bí mật (`Secret Files`). |
| **Aiven** | Dịch vụ Managed Cloud Database cung cấp MySQL Server ổn định, an toàn, hỗ trợ chứng chỉ SSL CA. |
| **Secret File (`ca.pem`)** | Tập tin chứng chỉ CA công khai của Aiven, được Render nạp vào thư mục bảo mật `/etc/secrets/ca.pem`. |
| **Entrypoint script** | Tệp tin `docker/entrypoint.sh` điều phối khởi động container, xử lý SSL, chạy migrations và seeders. |
| **Health Check (`/up`)** | Endpoint do Laravel 11/13 cung cấp sẵn giúp Render giám sát trạng thái sống/chết của ứng dụng. |

---

## 3. Trình tự Triển khai Chi tiết (9 Bước)

---

### Bước 1. Kiểm tra dự án tại máy cá nhân (Local)

1. Mở terminal tại thư mục gốc dự án `d:\laragon\www\microwave`.
2. Kiểm tra phiên bản PHP và thư viện Composer:
   ```powershell
   php -v
   composer install
   ```
3. Kiểm tra file môi trường `.env` cục bộ:
   ```ini
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=microwave
   DB_USERNAME=root
   DB_PASSWORD=
   MYSQL_ATTR_SSL_CA=
   SESSION_SECURE_COOKIE=false
   ```
   > **Lưu ý:** Với MySQL local thông thường, để trống `MYSQL_ATTR_SSL_CA`. Không trỏ chứng chỉ CA ở local để tránh lỗi kết nối khi chạy XAMPP/Laragon.

4. Xóa cache và chạy kiểm thử tự động:
   ```powershell
   php artisan config:clear
   php artisan test
   ```

---

### Bước 2. Chuẩn bị các tập tin triển khai (Docker & Config)

Dự án đã được cấu hình đầy đủ bộ tập tin triển khai chuẩn:

```
microwave/
├── Dockerfile                  # Multi-stage build (PHP 8.3 FPM Alpine + Nginx)
├── .dockerignore               # Loại trừ vendor, cache, env khỏi docker build
├── docker/
│   ├── nginx.conf              # Cấu hình Nginx reverse proxy FastCGI
│   ├── php.ini                 # Cấu hình runtime PHP chuẩn production
│   ├── php-fpm.conf            # Cấu hình tiến trình FPM ondemand tiết kiệm RAM
│   ├── entrypoint.sh           # Script điều phối khởi động container
│   ├── check-ca.php            # Script kiểm tra hợp lệ chứng chỉ SSL CA
│   └── render.env.example      # File mẫu biến môi trường dành cho Render
├── config/
│   └── seeding.php             # Cấu hình nạp admin credentials qua env
```

---

### Bước 3. Khởi tạo Git và Đưa mã nguồn lên GitHub

1. Khởi tạo Git repository trong thư mục dự án (nếu chưa có):
   ```powershell
   git init
   git branch -M main
   ```

2. Kiểm tra trạng thái và thêm toàn bộ tệp tin vào Git:
   ```powershell
   git status --short
   git add .
   git commit -m "feat: setup docker deployment for render and aiven mysql"
   ```

3. Kết nối với repository trên GitHub và đẩy mã nguồn lên:
   ```powershell
   # Thay đổi username và tên repo tương ứng của bạn trên GitHub
   git remote add origin https://github.com/<YOUR_GITHUB_USERNAME>/microwave.git
   git push -u origin main
   ```
   > **Cảnh báo bảo mật:** Trước khi push, luôn đảm bảo file `.env` chứa mật khẩu thật đã nằm trong `.gitignore` và không bị đưa lên GitHub.

---

### Bước 4. Chuẩn bị Cơ sở dữ liệu trên Aiven (aiven.io)

1. Đăng ký/Đăng nhập tài khoản tại [Aiven Console](https://console.aiven.io/).
2. Nhấn nút **Create service**:
   - **Service type:** Chọn **MySQL**.
   - **Cloud provider & Region:** Chọn đám mây (AWS / Google Cloud) tại khu vực gần Việt Nam (ví dụ: **Singapore** - `ap-southeast-1`).
   - **Service Plan:** Chọn gói **Free** (hoặc gói cơ bản theo nhu cầu).
   - **Service name:** Đặt tên dịch vụ (ví dụ: `mysql-microwave`).
   - Nhấn **Create service**.
3. Chờ trạng thái dịch vụ chuyển sang **Running** (xanh lá).
4. Vào trang **Overview** của service vừa tạo để lấy thông tin kết nối:
   - **Host:** Ví dụ `mysql-3b...aivencloud.com`
   - **Port:** Ví dụ `16933`
   - **Database name:** Mặc định là `defaultdb`
   - **User:** Mặc định là `avnadmin`
   - **Password:** Nhấn hiển thị mật khẩu để copy.
5. Tại mục **CA certificate**, bấm biểu tượng **Download** để tải file chứng chỉ về máy (tên file thường là `ca.pem`). Giữ nguyên nội dung file này.

---

### Bước 5. Tạo Web Service trên Render (render.com)

1. Đăng nhập vào [Render Dashboard](https://dashboard.render.com/).
2. Nhấn nút **New +** -> Chọn **Web Service**.
3. Chọn tùy chọn **Build and deploy from a Git repository** -> Kết nối tài khoản GitHub và chọn kho lưu trữ `microwave`.
4. Thiết lập thông số dịch vụ:
   - **Name:** `microwave-shop` (hoặc tên tùy chọn của bạn).
   - **Region:** Chọn **Singapore** (cùng khu vực với Aiven để tối ưu độ trễ mạng).
   - **Branch:** `main`.
   - **Root Directory:** Để **trống** (dự án nằm ngay tại thư mục gốc).
   - **Runtime / Language:** Chọn **Docker**.
   - **Instance Type:** Chọn gói **Free**.
5. Mở rộng mục **Advanced**:
   - **Health Check Path:** Điền `/up`.
   - Giữ nguyên Docker Command mặc định (được chỉ định bởi `ENTRYPOINT` trong Dockerfile).

---

### Bước 6. Khai báo Biến môi trường và Secret File trên Render

#### A. Thêm Secret File (Chứng chỉ SSL CA)
1. Tại màn hình cấu hình Web Service, chuyển tới tab **Environment** hoặc **Secret Files**.
2. Nhấn **Add Secret File**:
   - **Filename:** `ca.pem`
   - **Contents:** Mở file `ca.pem` đã tải từ Aiven bằng Notepad, copy toàn bộ nội dung (bao gồm cả dòng `-----BEGIN CERTIFICATE-----` và `-----END CERTIFICATE-----`) dán vào ô này.
3. Khi container khởi động, Render sẽ đặt file này tại đường dẫn: `/etc/secrets/ca.pem`.

#### B. Khai báo Environment Variables
Tại phần **Environment Variables**, đối chiếu với file `docker/render.env.example` và nhập các cặp key-value sau:

| Tên biến (Key) | Giá trị mẫu (Value) | Giải thích |
| :--- | :--- | :--- |
| `APP_NAME` | `Microwave Store` | Tên hiển thị của website |
| `APP_ENV` | `production` | Chế độ chạy ứng dụng |
| `APP_DEBUG` | `false` | Tắt debug để bảo mật |
| `APP_URL` | `https://microwave-shop.onrender.com` | Địa chỉ HTTPS do Render cấp cho service |
| `APP_KEY` | *(Sinh mã bằng `php artisan key:generate --show`)* | Khóa mã hóa bảo mật của Laravel |
| `TRUSTED_PROXIES` | `*` | Nhận diện Reverse Proxy của Render để bật HTTPS |
| `DB_CONNECTION` | `mysql` | Loại kết nối cơ sở dữ liệu |
| `DB_HOST` | *(Host lấy từ Aiven)* | Ví dụ: `mysql-3b...aivencloud.com` |
| `DB_PORT` | *(Port lấy từ Aiven)* | Ví dụ: `16933` |
| `DB_DATABASE` | `defaultdb` | Tên database trên Aiven |
| `DB_USERNAME` | `avnadmin` | Tên đăng nhập database Aiven |
| `DB_PASSWORD` | *(Mật khẩu lấy từ Aiven)* | Mật khẩu truy cập database |
| `MYSQL_ATTR_SSL_CA`| `/etc/secrets/ca.pem` | Đường dẫn đến Secret File CA trên container |
| `SESSION_DRIVER` | `database` | Lưu session người dùng vào bảng `sessions` |
| `SESSION_SECURE_COOKIE` | `true` | Bắt buộc cookie bảo mật trên HTTPS |
| `CACHE_STORE` | `database` | Lưu cache ứng dụng vào bảng cơ sở dữ liệu |
| `QUEUE_CONNECTION`| `sync` | Xử lý queue trực tiếp |
| `LOG_CHANNEL` | `stderr` | Ghi log ra console container để xem trên Render |
| `LOG_LEVEL` | `info` | Mức độ ghi log |
| `PORT` | `10000` | Cổng HTTP lắng nghe bên trong container |
| `RUN_MIGRATIONS` | `true` | Tự động chạy `artisan migrate` khi start container |
| `RUN_SEEDERS` | `true` *(lần đầu)* / `false` *(sau đó)* | Kích hoạt nạp dữ liệu mẫu và tạo admin |
| `SEED_ADMIN_NAME` | `Shop Admin` | Tên của tài khoản Quản trị |
| `SEED_ADMIN_EMAIL`| `admin@example.com` | Email quản trị để đăng nhập |
| `SEED_ADMIN_PASSWORD` | *(Mật khẩu riêng dài >= 12 ký tự)* | Mật khẩu bảo mật cho tài khoản admin |
| `MAIL_MAILER` | `log` | Ghi log email thay vì gửi mail SMTP thật |

---

### Bước 7. Khởi tạo Bảng dữ liệu, Dữ liệu mẫu và Admin

1. Trong lần triển khai đầu tiên, đảm bảo biến **`RUN_SEEDERS=true`** và các biến `SEED_ADMIN_*` đã được điền.
2. Nhấn **Save Changes** / **Create Web Service** để Render tiến hành build và khởi động image.
3. Cơ chế hoạt động của `docker/entrypoint.sh`:
   - Kiểm tra và sao chép chứng chỉ `/etc/secrets/ca.pem` vào `/run/app-certificates/mysql-ca.pem` với quyền đọc bảo mật `chmod 400` cho người dùng `www-data`.
   - Chạy `check-ca.php` xác nhận chứng chỉ hợp lệ.
   - Chạy `php artisan migrate --force --no-interaction` để tạo toàn bộ bảng trong database Aiven (bảng users, products, categories, sessions, orders...).
   - Chạy `php artisan db:seed --force --no-interaction`:
     - `AdminUserSeeder`: Tạo tài khoản admin với email và mật khẩu bạn đã cấu hình, cấp quyền `admin` và tự động kích hoạt `email_verified_at`.
     - `CategorySeeder`: Tạo 4 danh mục lò vi sóng chuẩn.
     - `ProductSeeder`: Tạo 10 sản phẩm lò vi sóng mẫu kèm hình ảnh và giá niêm yết.
4. **Quan trọng sau khi deploy thành công:** Vào lại tab **Environment**, đổi giá trị **`RUN_SEEDERS=false`** và lưu lại. Điều này giúp các lần triển khai (redeploy) tiếp theo không chạy lại seeder, bảo toàn dữ liệu giao dịch thực tế của shop.

---

### Bước 8. Theo dõi Triển khai và Kiểm tra Kết quả (Verification)

1. Mở tab **Logs** trên Render để quan sát:
   - Quá trình kéo image Alpine, cài đặt `pdo_mysql`, biên dịch Nginx & PHP.
   - Thông báo `[check-ca] SUCCESS: CA certificate verified`.
   - Quá trình chạy Migrations và Seeders.
   - Thông báo `ready to handle connections` của PHP-FPM và Nginx.
2. **Kiểm tra Health Check công khai:**
   Sử dụng PowerShell tại máy local để kiểm tra endpoint sức khỏe:
   ```powershell
   (Invoke-WebRequest -Uri "https://<YOUR-SERVICE>.onrender.com/up" -UseBasicParsing).StatusCode
   ```
   *Kết quả mong đợi:* Mã HTTP **`200`**.
3. **Kiểm tra Nghiệp vụ trên trình duyệt:**
   - Mở `https://<YOUR-SERVICE>.onrender.com`: Trang chủ hiển thị đầy đủ danh mục và các sản phẩm lò vi sóng mẫu.
   - Đăng nhập Admin: Truy cập `/login`, nhập email và mật khẩu đã khai báo trong `SEED_ADMIN_*`. Đăng nhập thành công và truy cập được `/admin/dashboard`.
   - Mua hàng thử nghiệm: Thêm một sản phẩm vào giỏ hàng, tiến hành thanh toán thử nghiệm.

---

### Bước 9. Quy trình Cập nhật Mã nguồn sau Triển khai (CI/CD)

Khi bạn thực hiện chỉnh sửa tính năng, giao diện hoặc sửa lỗi mã nguồn tại máy local:

1. Kiểm tra và chạy test tại local:
   ```powershell
   php artisan test
   ```
2. Commit và đẩy thay đổi lên GitHub:
   ```powershell
   git status --short
   git add .
   git commit -m "fix: update product layout styling"
   git push origin main
   ```
3. Render đã bật tính năng **Auto-Deploy**, hệ thống sẽ tự động phát hiện commit mới trên nhánh `main`, kích hoạt build lại Docker image và triển khai phiên bản mới mà không làm gián đoạn dịch vụ.
4. Kiểm tra lại tab **Logs** trên Render để đảm bảo phiên bản mới vận hành ổn định.

---

## 4. Bảng Tra cứu Sự cố thường gặp (Troubleshooting)

| Sự cố | Nguyên nhân | Cách khắc phục |
| :--- | :--- | :--- |
| **Lỗi `Cannot read MySQL CA file`** | Thiếu file chứng chỉ hoặc đường dẫn sai | Kiểm tra Secret File trên Render đã tạo đúng tên `ca.pem` và biến `MYSQL_ATTR_SSL_CA=/etc/secrets/ca.pem`. |
| **Lỗi `SQLSTATE[HY000] [2002] Connection timed out`** | Sai thông tin Host/Port của Aiven | Kiểm tra lại `DB_HOST` và `DB_PORT` từ trang quản trị Aiven. |
| **Lỗi `Mixed Content` (ảnh/style bị HTTP)** | Thiếu cấu hình Proxy HTTPS | Đảm bảo biến `TRUSTED_PROXIES=*` đã được khai báo và `bootstrap/app.php` có `$middleware->trustProxies(at: '*')`. |
| **Lỗi `RUN_SEEDERS must be true or false`** | Điền sai định dạng giá trị | Chỉ điền `true` hoặc `false` (viết thường, không có dấu ngoặc kép) vào ô Value trên Render. |
| **Lỗi `Set SEED_ADMIN_PASSWORD to at least 12 characters`** | Mật khẩu admin ngắn hơn 12 ký tự | Cập nhật biến `SEED_ADMIN_PASSWORD` có độ dài tối thiểu 12 ký tự trên Render. |
