<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's comprehensive profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Eager load customer orders with products and payment transactions
        $orders = $user->orders()->with(['orderItems.product', 'paymentTransactions'])->get();

        $totalOrders = $orders->count();
        $totalSpent = $orders->where('status', '!=', 'cancelled')->sum('total_price');
        $pendingOrders = $orders->where('status', 'pending')->count();
        $completedOrders = $orders->where('status', 'completed')->count();

        return view('profile.edit', [
            'user' => $user,
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
        ]);
    }

    /**
     * Update the user's personal profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle Avatar File Upload
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $uploadDir = public_path('uploads/avatars');

            if (! File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            // Remove old uploaded avatar if it exists
            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $filename = 'avatar_'.$user->id.'_'.time().'.'.$avatarFile->getClientOriginalExtension();
            $avatarFile->move($uploadDir, $filename);
            $data['avatar'] = 'uploads/avatars/'.$filename;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit', ['tab' => 'info'])->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.edit', ['tab' => 'password'])->with('success', 'Mật khẩu của bạn đã được thay đổi thành công!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu để xác nhận xóa tài khoản.',
            'password.current_password' => 'Mật khẩu không chính xác.',
        ]);

        $user = $request->user();

        // Delete uploaded avatar file if exists
        if ($user->avatar && File::exists(public_path($user->avatar))) {
            File::delete(public_path($user->avatar));
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Tài khoản của bạn đã được xóa thành công khỏi hệ thống.');
    }
}
