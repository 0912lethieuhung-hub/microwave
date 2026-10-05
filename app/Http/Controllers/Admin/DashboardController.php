<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PageView;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Doanh thu (từ các đơn hàng completed)
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // Doanh thu tháng này và tháng trước
        $thisMonthRevenue = Order::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        $newCustomersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 2. Tổng đơn hàng phát sinh trên hệ thống
        $totalOrders = Order::count();

        // 3. Đơn hàng theo trạng thái
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // 4. Tổng số khách hàng
        $totalCustomers = User::where(function ($q) {
            $q->where('is_admin', false)->orWhereNull('is_admin');
        })->where('role', '!=', 'admin')->count();

        // Lượt truy cập
        $totalVisits = PageView::count();
        $uniqueVisitors = PageView::distinct('ip_address')->count('ip_address');

        // 5. Sản phẩm được quan tâm nhiều nhất
        $topProducts = Product::orderBy('views', 'desc')->take(5)->get();

        // 6. Cảnh báo sản phẩm sắp hết hàng (tồn kho <= 5)
        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->take(6)->get();

        // 7. Thống kê doanh thu theo số ngày được chọn (7 hoặc 30 ngày)
        $chartDays = in_array((int) $request->get('days'), [7, 30]) ? (int) $request->get('days') : 7;
        $revenueDates = [];
        $revenueValues = [];
        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $revenueDates[] = $day->format('d/m');
            $dailyRev = Order::where('status', 'completed')
                ->whereDate('created_at', $day)
                ->sum('total_price');
            $revenueValues[] = (float) $dailyRev;
        }

        // 8. Đơn hàng gần đây
        $recentOrders = Order::with('user')->latest()->take(6)->get();

        // 9. Thống kê top bán chạy từ OrderItems (loại trừ các đơn hàng bị hủy)
        $topSelling = OrderItem::with('product')
            ->whereHas('order', function ($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(price * quantity) as total_sales'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'thisMonthRevenue',
            'newCustomersThisMonth',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalCustomers',
            'totalVisits',
            'uniqueVisitors',
            'topProducts',
            'lowStockProducts',
            'chartDays',
            'revenueDates',
            'revenueValues',
            'recentOrders',
            'topSelling'
        ));
    }
}
