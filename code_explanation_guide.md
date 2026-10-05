# CẨM NANG HƯỚNG DẪN & GIẢI THÍCH CHI TIẾT TOÀN BỘ MÃ NGUỒN (CODE)
## DỰ ÁN WEBSITE "SIÊU LÒ NƯỚNG" (LARAVEL 11 / PHP 8.3)

---

# MỤC LỤC
1. [Tổng Quan Cấu Trúc Thư Mục Dự Án](#1-tổng-quan-cấu-trúc-thư-mục-dự-án)
2. [Hệ Thống Định Tuyến (Routes - `routes/web.php`)](#2-hệ-thống-định-tuyến-routes---routeswebphp)
3. [Bộ Điều Khiển Xác Thực & Phân Quyền (`AuthController.php`)](#3-bộ-điều-khiển-xác-thực--phân-quyền-authcontrollerphp)
4. [Lớp Bảo Vệ Quyền Quản Trị (`AdminMiddleware.php`)](#4-lớp-bảo-vệ-quyền-quản-trị-adminmiddlewarephp)
5. [Bộ Điều Khiển Quản Trị Trung Tâm (`AdminController.php`)](#5-bộ-điều-khiển-quản-trị-trung-tâm-admincontrollerphp)
6. [Bộ Điều Khiển Trang Chủ & Xem Sản Phẩm (`HomeController.php`)](#6-bộ-điều-khiển-trang-chủ--xem-sản-phẩm-homecontrollerphp)
7. [Hệ Thống Giỏ Hàng & Đặt Hàng (`CartController.php` & `CheckoutController.php`)](#7-hệ-thống-giỏ-hàng--đặt-hàng-cartcontrollerphp--checkoutcontrollerphp)
8. [Bộ Điều Khiển Thống Kê & Báo Cáo (`Admin/DashboardController.php`)](#8-bộ-điều-khiển-thống-kê--báo-cáo-admindashboardcontrollerphp)
9. [Các Bộ Điều Khiển Nghiệp Vụ Admin (`ProductController`, `CategoryController`, `OrderController`)](#9-các-bộ-điều-khiển-nghiệp-vụ-admin)
10. [Lớp Theo Dõi Lượt Truy Cập (`TrackVisitor.php`)](#10-lớp-theo-dõi-lượt-truy-cập-trackvisitorphp)
11. [Cơ Sở Dữ Liệu & Các Models (Database & Eloquent Models)](#11-cơ-sở-dữ-liệu--các-models)
12. [Hệ Thống Giao Diện Blade Views (Frontend Điện Máy XANH & Admin)](#12-hệ-thống-giao-diện-blade-views)
13. [Hướng Dẫn Vận Hành & Sử Dụng Từ A Đến Z](#13-hướng-dẫn-vận-hành--sử-dụng-từ-a-đến-z)

---

## 1. TỔNG QUAN CẤU TRÚC THƯ MỤC DỰ ÁN

Toàn bộ dự án nằm tại thư mục: `D:\laragon\www\microwave`

```text
D:\laragon\www\microwave/
│
├── 📁 app/
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── AuthController.php              # Xử lý Đăng ký, Đăng nhập, Đăng xuất & Phân quyền
│   │   │   ├── AdminController.php             # Điều phối các trang quản trị Admin
│   │   │   ├── HomeController.php              # Xử lý Trang chủ, Tìm kiếm, Lọc & Chi tiết sản phẩm
│   │   │   ├── CartController.php              # Thêm, xem, xóa món hàng trong giỏ
│   │   │   ├── CheckoutController.php          # Xử lý đặt hàng COD và lưu đơn hàng
│   │   │   └── 📁 Admin/
│   │   │       ├── DashboardController.php     # Thống kê Doanh thu, Lượt xem, Người truy cập
│   │   │       ├── ProductController.php       # Thêm, sửa, xóa sản phẩm trong Admin
│   │   │       ├── CategoryController.php      # Thêm, sửa, xóa danh mục trong Admin
│   │   └── 📁 Middleware/
│   │       ├── AdminMiddleware.php             # Chặn người dùng thường, chỉ cho role admin vào
│   │       └── TrackVisitor.php                # Ghi nhận IP khách để thống kê lượt truy cập
│   │
│   └── 📁 Models/
│       ├── User.php                            # Tài khoản (Admin & Customer)
│       ├── Product.php                         # Sản phẩm lò vi sóng (giá, ảnh, lượt xem)
│       ├── Category.php                        # Danh mục loại lò (cơ, điện tử, có nướng, âm tủ)
│       ├── Cart.php & CartItem.php             # Giỏ hàng & chi tiết từng món
│       ├── Order.php & OrderItem.php           # Đơn đặt hàng & chi tiết sản phẩm mua
│       └── PageView.php                        # Lưu vết IP và URL lượt xem trang
│
├── 📁 database/
│   ├── database.sqlite                         # File chứa toàn bộ dữ liệu thật
│   ├── 📁 migrations/                          # Các file tạo bảng cơ sở dữ liệu
│   └── 📁 seeders/
│       ├── DatabaseSeeder.php                  # Dữ liệu mẫu sản phẩm và danh mục
│       └── AdminUserSeeder.php                 # Tạo tài khoản Admin mặc định
│
├── 📁 public/
│   └── 📁 images/                              # Chứa hình ảnh lò vi sóng hiển thị trên web
│
├── 📁 resources/
│   └── 📁 views/
│       ├── 📁 layouts/
│       │   ├── frontend.blade.php              # Khung giao diện chuẩn Điện Máy XANH
│       │   └── admin.blade.php                 # Khung giao diện Quản trị viên Bootstrap
│       ├── 📁 auth/
│       │   ├── login.blade.php                 # Form đăng nhập (có alert thông báo)
│       │   └── register.blade.php              # Form đăng ký tài khoản mới
│       ├── 📁 products/
│       │   ├── index.blade.php                 # Trang chủ: Banner ĐMX, Bộ lọc, Lưới card
│       │   └── show.blade.php                  # Chi tiết lò vi sóng: Nút MUA NGAY, Cam kết 12th
│       ├── 📁 pages/
│       │   ├── promotions.blade.php            # Trang Khuyến mãi Lễ Tết (Tết, 2/9, Noel)
│       │   ├── guides.blade.php                # Cẩm nang mẹo vặt & quy tắc an toàn
│       │   └── contact.blade.php               # Showroom 96 Nguyễn Du - Quản lý Lê Thiều Hưng
│       ├── 📁 cart/
│       │   └── index.blade.php                 # Trang hiển thị giỏ hàng & tính tiền
│       ├── 📁 checkout/
│       │   ├── index.blade.php                 # Form nhập địa chỉ và số điện thoại nhận hàng COD
│       │   └── success.blade.php               # Trang thông báo đặt hàng thành công
│       └── 📁 admin/                           # THƯ MỤC DÀNH RIÊNG CHO QUẢN TRỊ VIÊN
│           ├── 📁 dashboard/index.blade.php    # Giao diện Thống kê Doanh thu & Top sản phẩm
│           ├── 📁 products/                    # Quản lý danh sách, tạo mới, sửa sản phẩm
│           ├── 📁 categories/                  # Quản lý danh mục loại lò
│           └── 📁 orders/                      # Quản lý đơn hàng và cập nhật Pending/Completed
│
└── 📁 routes/
    └── web.php                                 # Khai báo toàn bộ đường dẫn URL của website
```

---

## 2. HỆ THỐNG ĐỊNH TUYẾN (ROUTES - `routes/web.php`)

```php
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;

// 1. TRANG KHÁCH HÀNG
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');
Route::view('/promotions', 'pages.promotions')->name('promotions');
Route::view('/guides', 'pages.guides')->name('guides');
Route::view('/contact', 'pages.contact')->name('contact');

// 2. NHÓM ĐĂNG KÝ / ĐĂNG NHẬP
Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// 3. NHÓM ADMIN (Bảo vệ bởi middleware 'admin')
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index']);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
});

// 4. NHÓM KHÁCH HÀNG ĐĂNG NHẬP (Giỏ hàng & Thanh toán)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{item}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
});
```

---

## 3. BỘ ĐIỀU KHIỂN XÁC THỰC & PHÂN QUYỀN (`AuthController.php`)

```php
<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký: Tạo user với role 'customer'
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            Log::info('Registering user with email: ' . $request->email);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'is_admin' => false,
            ]);

            Log::info('User registered successfully');

            return redirect()->route('login')->with('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đăng ký thất bại. Vui lòng thử lại!')->withInput();
        }
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập & Phân quyền chuyển hướng
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            // Nếu là Admin -> Vào trang quản trị
            if (Auth::user()->role === 'admin' || Auth::user()->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Nếu là Khách hàng -> Về trang chủ mua sắm
            return redirect()->intended(route('home'));
        }

        return redirect()->back()->with('error', 'Email hoặc mật khẩu không chính xác!')->withInput($request->only('email'));
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đã đăng xuất tài khoản thành công!');
    }
}
```

---

## 4. LỚP BẢO VỆ QUYỀN QUẢN TRỊ (`AdminMiddleware.php`)

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem đã đăng nhập và có quyền Admin hay không
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin)) {
            return $next($request);
        }

        // Chặn khách thường truy cập trái phép
        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang Quản trị viên!');
    }
}
```

---

## 5. BỘ ĐIỀU KHIỂN TRANG CHỦ & TÌM KIẾM (`HomeController.php`)

```php
<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        // 1. Lọc theo Loại lò
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // 2. Tìm kiếm theo từ khóa (Sharp, Inverter, có nướng)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 3. Lọc theo Mức giá
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'under_2m':
                    $query->where('price', '<', 2000000);
                    break;
                case '2m_5m':
                    $query->whereBetween('price', [2000000, 5000000]);
                    break;
                case 'over_5m':
                    $query->where('price', '>', 5000000);
                    break;
            }
        }

        // 4. Lọc theo Hãng sản xuất
        if ($request->filled('brand')) {
            $query->where('name', 'like', "%{$request->brand}%");
        }

        $products = $query->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $product->increment('views'); // Tự động tăng lượt xem
        return view('products.show', compact('product'));
    }
}
```

---

## 6. THỐNG KÊ DOANH THU & LƯỢT TRUY CẬP (`Admin/DashboardController.php`)

```php
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PageView;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Doanh thu: Tổng tiền các đơn hàng đã 'completed'
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // 2. Lượt truy cập (Pageviews): Tổng số bản ghi
        $totalVisits = PageView::count();

        // 3. Số người truy cập (Unique Visitors): Đếm số IP duy nhất
        $uniqueVisitors = PageView::distinct('ip_address')->count('ip_address');

        // 4. Top 5 sản phẩm quan tâm nhất (lượt views cao nhất)
        $topProducts = Product::orderBy('views', 'desc')->take(5)->get();

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'totalVisits',
            'uniqueVisitors',
            'topProducts'
        ));
    }
}
```

---

## 7. BẢNG TỔNG HỢP CƠ SỞ DỮ LIỆU & MODELS

| Bảng | Model | Các trường dữ liệu chính |
| :--- | :--- | :--- |
| `users` | `User` | `id`, `name`, `email`, `password`, `role` (`admin`/`customer`), `is_admin` |
| `products` | `Product` | `id`, `name`, `description`, `price`, `image_url`, `category_id`, `views` |
| `categories` | `Category` | `id`, `name` (Lò cơ, điện tử, có nướng, âm tủ), `slug` |
| `carts` & `cart_items` | `Cart`, `CartItem` | `user_id`, `product_id`, `quantity` |
| `orders` & `order_items` | `Order`, `OrderItem` | `user_id`, `total_price`, `status`, `shipping_status`, `ghn_order_code`, `ghn_total_fee`, `to_district_id`, `to_ward_code` |
| `page_views` | `PageView` | `ip_address`, `url`, `created_at` |

---

## 8. TÍCH HỢP VẬN CHUYỂN GIAO HÀNG NHANH SANDBOX (5sao.ghn.dev - LAB 5)

1. **Môi trường GHN Sandbox (5sao.ghn.dev):**
   - **Portal người dùng Test:** `https://5sao.ghn.dev/`
   - **API Gateway Test:** `https://dev-online-gateway.ghn.vn/shiip/public-api`
   - **Đơn vị vận hành:** **HỘ KINH DOANH SIÊU LÒ NƯỚNG** (GPĐKKD: 01E8039281, MST: 0109887766, Hotline: 0913 014 602).
   - **Địa chỉ kho lấy hàng & hoàn hàng:** 277 Ngõ 277 Quan Hoa, Quận Cầu Giấy, Hà Nội (`district_id: 1485`, `ward_code: 1A0606`).
   - **Cấu hình Sandbox trong `.env`:**
     ```ini
     GHN_BASE_URL=https://dev-online-gateway.ghn.vn/shiip/public-api
     GHN_PORTAL_URL=https://5sao.ghn.dev
     GHN_TOKEN=3955ea90-aa9c-11f1-a973-aee5264794df
     GHN_SHOP_ID=217565
     GHN_VERIFY_SSL=false
     GHN_FROM_DISTRICT_ID=1485
     GHN_DEFAULT_WEIGHT=200
     ```

2. **Services cốt lõi:**
   - [`app/Services/GHNService.php`](file:///d:/laragon/www/microwave/app/Services/GHNService.php):
     - `getProvinces()`: Tải danh mục 63+ Tỉnh/Thành phố từ GHN.
     - `getDistricts($provinceId)`: Tải danh mục Quận/Huyện.
     - `getWards($districtId)`: Tải danh mục Phường/Xã.
     - `calculateFee($params)`: Tính cước vận chuyển thực tế từ kho Cầu Giấy đến tận nhà khách.
     - `createOrder($orderData)`: Đẩy đơn tạo vận đơn tự động trên GHN Dev Gateway.
     - `cancelOrder($orderCodes)`: Hủy đơn trên hệ thống GHN khi Admin hủy đơn hàng.
     - `getOrderDetail($orderCode)`: Tra cứu trạng thái vận chuyển thời gian thực từ GHN `/v2/shipping-order/detail`.
     - `mapGHNStatus($ghnStatus)`: Ánh xạ trạng thái GHN sang trạng thái nội bộ (`ready_to_pick`, `picking`, `delivering`, `delivered`, `delivery_fail`, `returning`, `returned`, `cancelled`).
   - [`app/Services/GHNOrderService.php`](file:///d:/laragon/www/microwave/app/Services/GHNOrderService.php):
     - `create(Order $order)`: Đóng gói đơn hàng, gắn địa chỉ hoàn hàng của HKD Siêu Lò Nướng, cấu hình `payment_type_id = 1` (Shop trả cước qua đối soát, shipper chỉ thu đúng tổng tiền COD trên web, không thu trùng cước).
     - `syncStatus(Order $order)`: Lấy trạng thái GHN mới nhất về cập nhật cho đơn hàng và tự động cập nhật trạng thái đơn sang `completed` nếu GHN báo `delivered`.
     - `cancel(Order $order)`: Gọi API hủy đơn GHN khi Admin chọn trạng thái `cancelled`.

3. **Quản trị đơn hàng Admin:**
   - Nút **"🔄 Đồng bộ GHN"** trên từng đơn hàng và nút **"Đồng bộ trạng thái GHN"** quét hàng loạt đơn đang vận chuyển.
   - Nút liên kết **"Tra cứu trên GHN (5sao)"** mở thẳng trang tra cứu đơn `https://5sao.ghn.dev/?order_code=...`.
   - Tự động hủy vận đơn trên GHN khi Admin chuyển đơn sang trạng thái `cancelled`.

---

## 9. HƯỚNG DẪN VẬN HÀNH DỰ ÁN

1. **Khởi động dự án:** Mở phần mềm Laragon ➔ Bấm **Start All** ➔ Truy cập: `http://microwave.test`.
2. **Tài khoản Quản trị viên (Admin):**
   - Email: **`admin@example.com`**
   - Mật khẩu: **`password`**
3. **Tài khoản Khách hàng (Customer):**
   - Email: **`0912lethieuhung@gmail.com`**
   - Mật khẩu: **`password`**
4. **Cơ sở dữ liệu:** File `D:\laragon\www\microwave\database\database.sqlite` (xem trực tiếp trên HeidiSQL hoặc DB Browser for SQLite).
5. **Cẩm nang chi tiết đầy đủ:** Xem tại file [HUONG_DAN_CHI_TIET_TOAN_BO_CODE.md](file:///d:/laragon/www/microwave/HUONG_DAN_CHI_TIET_TOAN_BO_CODE.md).

