<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\ProductColor;
use App\Models\Voucher;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::with('cartItems.product')->where('user_id', Auth::id())->first();
        if (! $cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^(0|\+84)[3|5|7|8|9][0-9]{8}$/'],
            'payment_method' => 'required|in:cod,momo',
            'shipping_fee' => 'nullable|integer|min:0',
            'to_district_id' => 'nullable|integer',
            'to_ward_code' => 'nullable|string|max:50',
        ], [
            'shipping_address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
            'phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'phone.regex' => 'Số điện thoại không hợp lệ! Vui lòng nhập số điện thoại Việt Nam gồm 10 chữ số (ví dụ: 0988 123 456).',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        $cart = Cart::with(['cartItems.product.colors'])->where('user_id', Auth::id())->first();
        if (! $cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('home');
        }

        // 1. Kiểm tra tính khả dụng và số lượng tồn kho của từng sản phẩm và từng màu sắc
        $totalPrice = 0;
        foreach ($cart->cartItems as $item) {
            if (! $item->product) {
                return redirect()->route('cart.index')->with('error', 'Một sản phẩm trong giỏ hàng không còn tồn tại trên hệ thống. Vui lòng kiểm tra lại!');
            }
            $availableStock = $item->product->getColorStock($item->color);
            if ($item->quantity > $availableStock) {
                $colorText = $item->color ? "màu {$item->color} của " : '';

                return redirect()->route('cart.index')->with('error', "Sản phẩm {$colorText}{$item->product->name} hiện chỉ còn {$availableStock} chiếc trong kho, không đủ số lượng bạn đặt ({$item->quantity})!");
            }
            $totalPrice += $item->price * $item->quantity;
        }

        $coupon = session()->get('coupon');
        $discount = 0;

        // Re-validate và tính lại discount tại thời điểm checkout để đảm bảo tính chính xác
        if ($coupon && ! empty($coupon['id'])) {
            $voucher = Voucher::find($coupon['id']);
            if ($voucher) {
                $check = $voucher->isValidForAmount($totalPrice);
                if ($check['valid']) {
                    $discount = $voucher->calculateDiscount($totalPrice);
                } else {
                    // Voucher không còn hợp lệ -> xóa khỏi session và thông báo
                    session()->forget('coupon');

                    return redirect()->route('cart.index')
                        ->with('error', 'Mã giảm giá ['.$coupon['code'].'] không còn hợp lệ: '.$check['message'].' Vui lòng kiểm tra lại giỏ hàng.');
                }
            } else {
                session()->forget('coupon');
            }
        }

        $shippingFee = max(0, (int) ($request->shipping_fee ?? 0));
        $finalPrice = max(0, ($totalPrice - $discount) + $shippingFee);
        $paymentMethod = $request->payment_method; // 'cod' | 'momo'

        // 2. Thực thi tạo đơn hàng trong Transaction để đảm bảo toàn vẹn dữ liệu
        $order = DB::transaction(function () use ($cart, $finalPrice, $shippingFee, $coupon, $request, $paymentMethod) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $finalPrice,
                'status' => 'pending',
                'shipping_status' => 'pending',
                'payment_method' => $paymentMethod,
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone,
                'ghn_total_fee' => $shippingFee,
                'to_district_id' => $request->to_district_id ? (int) $request->to_district_id : null,
                'to_ward_code' => $request->to_ward_code ? (string) $request->to_ward_code : null,
            ]);

            foreach ($cart->cartItems as $item) {
                $itemPrice = $item->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $itemPrice,
                    'color' => $item->color,
                ]);

                // Trừ tồn kho biến thể màu sắc nếu có (chống âm)
                if ($item->color) {
                    $colorVariant = ProductColor::where('product_id', $item->product_id)
                        ->where('color', $item->color)
                        ->lockForUpdate()
                        ->first();
                    if ($colorVariant) {
                        $colorVariant->update([
                            'stock' => max(0, (int) $colorVariant->stock - (int) $item->quantity),
                        ]);
                    }
                }

                // Trừ tồn kho tổng của sản phẩm (chống âm)
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->update([
                        'stock' => max(0, (int) $product->stock - (int) $item->quantity),
                    ]);
                }
            }

            // Tăng số lượt sử dụng của Voucher nếu có áp dụng
            if ($coupon && ! empty($coupon['id'])) {
                $voucher = Voucher::find($coupon['id']);
                if ($voucher) {
                    $voucher->increment('used_count');
                }
            }

            // Dọn sạch giỏ hàng trong DB
            $cart->cartItems()->delete();

            return $order;
        });

        session()->forget('coupon');

        // 3. Phân luồng theo phương thức thanh toán
        if ($paymentMethod === 'momo') {
            // Chuyển sang cổng thanh toán MoMo
            return redirect()->route('orders.momo.start', $order->id);
        }

        // COD — tạo giao dịch COD pending và tạo vận đơn GHN
        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'cod',
            'amount' => $order->total_price,
            'status' => 'pending',
            'message' => 'Thanh toán tiền mặt khi nhận hàng (COD)',
        ]);

        // Tự động tạo vận đơn qua Giao Hàng Nhanh (GHN) nếu có mã quận/huyện
        if ($order->to_district_id && $order->to_ward_code) {
            try {
                $ghnOrderService = app(GHNOrderService::class);
                $ghnRes = $ghnOrderService->create($order, false); // isPaid = false → thu COD
                if (isset($ghnRes['code']) && $ghnRes['code'] == 200 && isset($ghnRes['data']['order_code'])) {
                    $order->update([
                        'ghn_order_code' => $ghnRes['data']['order_code'],
                        'shipping_status' => 'ready_to_pick',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Tạo vận đơn GHN tự động: '.$e->getMessage());
            }
        }

        return redirect()->route('checkout.success')->with([
            'success' => 'Đặt hàng thành công!',
            'latest_order_id' => $order->id,
        ]);
    }

    public function success()
    {
        return view('checkout.success');
    }
}
