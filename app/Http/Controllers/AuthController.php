<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký người dùng
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            Log::info('Registering user with email: '.$request->email);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'is_admin' => false,
            ]);

            Log::info('User registered successfully');

            // Gửi email xác thực
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $mailEx) {
                Log::error('Lỗi gửi mail xác thực (SMTP error): '.$mailEx->getMessage());
            }

            // Tự động đăng nhập
            Auth::login($user);

            return redirect()->route('verification.notice')
                ->with('success', 'Đăng ký tài khoản thành công! Vui lòng kiểm tra email để xác thực tài khoản.');
        } catch (Exception $e) {
            Log::error('Registration failed: '.$e->getMessage());

            return redirect()->back()->with('error', 'Đăng ký thất bại. Vui lòng thử lại!')->withInput();
        }
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập người dùng
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            // Phân quyền chuyển hướng:
            if (Auth::user()->role === 'admin' || Auth::user()->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Nếu là khách hàng chưa xác thực email -> Chặn lại và yêu cầu xác thực
            if (! Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')->with('warning', 'Vui lòng xác thực email của bạn trước khi tiếp tục.');
            }

            return redirect()->intended(route('home'));
        }

        return redirect()->back()->with('error', 'Email hoặc mật khẩu không chính xác!')->withInput($request->only('email'));
    }

    // Xử lý đăng xuất người dùng
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đã đăng xuất tài khoản thành công!');
    }
}
