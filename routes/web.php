<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\User\GHNController;
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\WishlistController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────────
// MoMo Webhook/Callback — KHÔNG qua Auth/CSRF
// ──────────────────────────────────────────────
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');

// Trang chủ & Chi tiết sản phẩm
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');
Route::post('/product/{id}/review', [ProductReviewController::class, 'store'])->middleware('auth')->name('product.review.store');
Route::get('/wishlist', [WishlistController::class, 'index'])->middleware('auth')->name('wishlist.index');
Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->middleware('auth')->name('wishlist.toggle');

// Trang tĩnh
Route::view('/promotions', 'pages.promotions')->name('promotions');
Route::view('/guides', 'pages.guides')->name('guides');
Route::view('/contact', 'pages.contact')->name('contact');

// Giao Hàng Nhanh (GHN) API Routes (Địa giới hành chính & Tính cước)
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [GHNController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [GHNController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [GHNController::class, 'getShippingFee'])->name('fee');
});

// Xác thực & Phân quyền (Theo tài liệu AuthController)
Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Quên mật khẩu & Đặt lại mật khẩu
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Xác thực Email & Xác nhận Mật khẩu
Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // 1. Hiển thị thông báo xác thực email
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // 2. Xử lý link xác nhận (từ email)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('home')->with('success', 'Xác thực email thành công! Chào mừng bạn đến với hệ thống.');
    })->middleware(['signed'])->name('verification.verify');

    // 3. Gửi lại email xác nhận
    Route::post('/email/verification-notification', function (Request $request) {
        try {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('message', 'Đã gửi lại link xác thực đến email của bạn!');
        } catch (Throwable $e) {
            Log::error('SMTP Mail Error: '.$e->getMessage());

            return back()->with('error', 'Lỗi gửi mail: Sai tài khoản hoặc Mật khẩu ứng dụng Gmail (App Password) trong file .env. Vui lòng kiểm tra lại!');
        }
    })->middleware(['throttle:6,1'])->name('verification.send');
});

Route::get('/dashboard', function () {
    return redirect()->route('profile.edit');
})->middleware(['auth'])->name('dashboard');

// Route dành cho Admin (sử dụng middleware 'admin' để kiểm tra quyền)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('categories', CategoryController::class);
    Route::delete('products/images/{id}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('products', ProductController::class);
    Route::post('orders/sync-all-ghn', [OrderController::class, 'syncAllGHN'])->name('orders.sync-all-ghn');
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('orders/bulk-status', [OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-status');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/sync-ghn', [OrderController::class, 'syncGHN'])->name('orders.sync-ghn');
    Route::post('orders/{order}/create-ghn', [OrderController::class, 'createGHN'])->name('orders.create-ghn');
    Route::resource('vouchers', VoucherController::class);
    Route::post('vouchers/{voucher}/toggle', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggle');
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggleRole');

    // Báo cáo doanh thu & biểu đồ (Admin Reports)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/charts', [ReportController::class, 'charts'])->name('reports.charts');

    // Giao dịch thanh toán (Admin Transactions)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Xử lý, báo cáo giao dịch thanh toán (Finance)
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/transactions', [FinanceController::class, 'transactions'])->name('finance.transactions');
    Route::patch('/finance/{order}/status', [FinanceController::class, 'updateStatus'])->name('finance.update-status');

    // Livechat Admin Routes
    Route::get('/chat/users', [AdminChatController::class, 'getUsers'])->name('chat.users');
    Route::get('/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [AdminChatController::class, 'send'])->name('chat.send');
});

// Livechat User Routes (chuẩn đặc tả tài liệu)
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
});

// Route dành cho Khách hàng đã đăng nhập & đã xác thực email (Hồ sơ cá nhân, Lịch sử đơn hàng, Giỏ hàng & Thanh toán)
Route::middleware(['auth', 'verified'])->group(function () {
    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/my-orders', function () {
        return redirect()->route('profile.edit', ['tab' => 'orders']);
    })->name('user.orders');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [UserOrderController::class, 'cancel'])->name('user.orders.cancel');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/buy-now/{product}', [CartController::class, 'buyNow'])->name('buy.now');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // MoMo Payment Routes (chuẩn dự án & Lab 6)
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('orders.momo.pay');
    // Lab 6 alias URIs để cả user.orders.momo.* và orders.momo.* đều hoạt động trơn tru
    Route::get('/user/orders/{order}/start-momo', [MomoController::class, 'start'])->name('user.orders.momo.start');
    Route::get('/user/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('user.orders.momo.pay');
});
