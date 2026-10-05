<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Danh sách giao dịch thanh toán
     */
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['order.user'])->latest();

        // Lọc theo cổng thanh toán
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo mã giao dịch, mã đơn hàng
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('gateway_order_id', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('order.user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) ($request->per_page ?? 20);
        $transactions = $query->paginate($perPage)->withQueryString();

        // Thống kê giao dịch
        $totalTransactions = PaymentTransaction::count();
        $paidCount = PaymentTransaction::where('status', 'paid')->count();
        $totalPaidAmount = PaymentTransaction::where('status', 'paid')->sum('amount');
        $failedCount = PaymentTransaction::where('status', 'failed')->count();

        return view('admin.transactions.index', compact(
            'transactions',
            'totalTransactions',
            'paidCount',
            'totalPaidAmount',
            'failedCount'
        ));
    }
}
