<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and filters.
     */
    public function index(Request $request)
    {
        $query = User::withCount(['orders', 'reviews'])->latest();

        // Filter theo role
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where(function ($q) {
                    $q->where('is_admin', true)->orWhere('role', 'admin');
                });
            } elseif ($request->role === 'customer') {
                $query->where(function ($q) {
                    $q->where('is_admin', false)->where('role', '!=', 'admin');
                });
            }
        }

        // Search theo tên, email, sđt
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Thống kê người dùng
        $totalUsers = User::count();
        $adminCount = User::where('is_admin', true)->orWhere('role', 'admin')->count();
        $customerCount = User::where('is_admin', false)->where('role', '!=', 'admin')->count();
        $customersWithOrders = User::has('orders')->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'adminCount',
            'customerCount',
            'customersWithOrders'
        ));
    }

    /**
     * Display the specified user profile and orders.
     */
    public function show(User $user)
    {
        $user->load(['orders' => function ($q) {
            $q->with('orderItems.product')->latest();
        }, 'reviews.product', 'wishlists.product']);

        $totalSpent = $user->orders()->where('status', '!=', 'cancelled')->sum('total_price');

        return view('admin.users.show', compact('user', 'totalSpent'));
    }

    /**
     * Toggle admin privilege for a user.
     */
    public function toggleRole(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không thể tự thay đổi quyền hạn của chính mình!');
        }

        $isAdmin = (bool) ($user->is_admin || $user->role === 'admin');
        $newAdminState = ! $isAdmin;
        $newRole = $newAdminState ? 'admin' : 'customer';

        $user->update([
            'is_admin' => $newAdminState,
            'role' => $newRole,
        ]);

        $message = $newAdminState
            ? "Đã cấp quyền Quản trị viên (Admin) cho người dùng {$user->name}!"
            : "Đã chuyển người dùng {$user->name} về quyền Khách hàng!";

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hiển thị form tạo người dùng mới.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới vào CSDL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user,customer',
        ]);

        $role = $request->role === 'user' ? 'customer' : $request->role;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_admin' => $role === 'admin',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa người dùng.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,user,customer',
        ]);

        $role = $request->role === 'user' ? 'customer' : $request->role;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'is_admin' => $role === 'admin',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công.');
    }

    /**
     * Xóa người dùng.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công.');
    }
}
