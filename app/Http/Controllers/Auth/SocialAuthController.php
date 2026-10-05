<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth or handle Demo mode if credentials not configured.
     */
    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id');
        if (empty($clientId) || $clientId === 'your-google-client-id') {
            // Chế độ thử nghiệm tiện lợi khi máy dev chưa cấu hình Google Client ID trong .env
            return $this->handleMockSocialLogin('google', 'Tài Khoản Google', 'google_user@gmail.com');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Lỗi kết nối Google: '.$e->getMessage());
        }
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            return $this->loginOrCreateUser($googleUser, 'google');
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Đăng nhập Google không thành công: '.$e->getMessage());
        }
    }

    /**
     * Redirect to Facebook OAuth or handle Demo mode.
     */
    public function redirectToFacebook(Request $request)
    {
        $clientId = config('services.facebook.client_id');
        if (empty($clientId) || $clientId === 'your-facebook-app-id') {
            // Chế độ thử nghiệm tiện lợi khi máy dev chưa cấu hình Facebook App ID trong .env
            return $this->handleMockSocialLogin('facebook', 'Tài Khoản Facebook', 'facebook_user@gmail.com');
        }

        try {
            return Socialite::driver('facebook')->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Lỗi kết nối Facebook: '.$e->getMessage());
        }
    }

    /**
     * Handle Facebook OAuth callback.
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            return $this->loginOrCreateUser($facebookUser, 'facebook');
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Đăng nhập Facebook không thành công: '.$e->getMessage());
        }
    }

    /**
     * Login or create user from social provider payload.
     */
    protected function loginOrCreateUser($socialUser, $provider)
    {
        $idColumn = $provider.'_id';
        $email = $socialUser->getEmail() ?? ($provider.'_'.$socialUser->getId().'@social.local');

        // Tìm theo social_id trước, hoặc tìm theo email
        $user = User::where($idColumn, $socialUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            // Cập nhật social id nếu tài khoản email đã tồn tại nhưng chưa liên kết
            if (empty($user->$idColumn)) {
                $user->$idColumn = $socialUser->getId();
            }
            if (empty($user->avatar) && $socialUser->getAvatar()) {
                $user->avatar = $socialUser->getAvatar();
            }
            $user->save();
        } else {
            // Tạo tài khoản người dùng mới
            $user = User::create([
                'name' => $socialUser->getName() ?? ucfirst($provider).' User',
                'email' => $email,
                $idColumn => $socialUser->getId(),
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(), // Đã xác thực qua mạng xã hội
                'avatar' => $socialUser->getAvatar(),
                'role' => 'customer',
                'is_admin' => false,
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công qua '.ucfirst($provider).'! Chào mừng '.$user->name);
    }

    /**
     * Mock social login helper for instant 1-click test when API keys are not yet added.
     */
    protected function handleMockSocialLogin($provider, $defaultName, $defaultEmail)
    {
        $idColumn = $provider.'_id';
        $mockId = $provider.'_'.rand(100000, 999999);

        $user = User::where('email', $defaultEmail)->first();
        if (! $user) {
            $user = User::create([
                'name' => $defaultName,
                'email' => $defaultEmail,
                $idColumn => $mockId,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
                'role' => 'customer',
                'is_admin' => false,
            ]);
        }

        Auth::login($user, true);

        $providerTitle = $provider === 'google' ? 'Google' : 'Facebook';

        return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công qua '.$providerTitle.'! (Đã tự động kết nối tài khoản '.$providerTitle.')');
    }
}
