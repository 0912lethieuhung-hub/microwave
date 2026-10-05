<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductColor;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserOrderController extends Controller
{
    /**
     * Display a specific order for the authenticated user.
     */
    public function show($id, GHNOrderService $ghnOrderService)
    {
        $order = Order::with('orderItems.product', 'user')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($order->ghn_order_code && ! in_array($order->shipping_status, ['delivered', 'cancelled'])) {
            $ghnOrderService->syncStatus($order);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel a pending order by the customer (including GHN shipping cancellation).
     */
    public function cancel(Request $request, $id, GHNOrderService $ghnOrderService)
    {
        $order = Order::with('orderItems.product')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($order->status === 'cancelled') {
            return redirect()->back()->with('error', 'Đơn hàng này đã được hủy trước đó.');
        }

        if ($order->status !== 'pending' || ! in_array($order->shipping_status, ['ready_to_pick', 'pending', null], true)) {
            return redirect()->back()->with('error', 'Đơn hàng đã bước vào giai đoạn xử lý/giao hàng, không thể tự hủy.');
        }

        // Nếu đơn đã tạo vận đơn GHN, gọi API GHN hủy vận đơn
        if ($order->ghn_order_code) {
            $result = $ghnOrderService->cancel($order);
            if (! $result['success']) {
                Log::warning('Hủy vận đơn GHN không thành công:', ['order_code' => $order->ghn_order_code, 'result' => $result]);
            }
        }

        DB::transaction(function () use ($order) {
            // Khôi phục lại số lượng tồn kho cho từng sản phẩm và từng màu sắc trong đơn
            foreach ($order->orderItems as $item) {
                if ($item->color) {
                    $colorVariant = ProductColor::where('product_id', $item->product_id)
                        ->where('color', $item->color)
                        ->first();
                    if ($colorVariant) {
                        $colorVariant->increment('stock', $item->quantity);
                    }
                }

                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'shipping_status' => 'cancelled',
            ]);
        });

        return redirect()->back()->with('success', "Đã hủy đơn hàng #{$order->id} thành công! Số lượng sản phẩm đã được tự động hoàn trả vào kho.");
    }
}
