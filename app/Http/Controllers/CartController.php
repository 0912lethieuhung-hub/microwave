<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['cartItems.product.category', 'cartItems.product.colors'])->where('user_id', Auth::id())->first();

        // Lấy danh sách sản phẩm gợi ý mua kèm (không trùng sản phẩm đã có trong giỏ)
        $existingProductIds = $cart ? $cart->cartItems->pluck('product_id')->toArray() : [];
        $suggestedProducts = Product::whereNotIn('id', $existingProductIds)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('cart.index', compact('cart', 'suggestedProducts'));
    }

    public function add(Request $request, $productId)
    {
        $product = Product::with('colors')->findOrFail($productId);

        $color = $request->input('color') ?? ($product->color ? explode(',', $product->color)[0] : null);
        if ($color) {
            $color = trim($color);
        }

        $availableStock = $product->getColorStock($color);

        if ($availableStock <= 0) {
            $msg = $color ? "Màu {$color} của sản phẩm hiện đang tạm hết hàng!" : 'Sản phẩm hiện đang tạm hết hàng!';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        if ($quantity > $availableStock) {
            $msg = $color
                ? "Rất tiếc, màu {$color} trong kho chỉ còn {$availableStock} sản phẩm!"
                : "Rất tiếc, trong kho chỉ còn {$availableStock} sản phẩm!";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('color', $color)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $quantity;
            if ($newQty > $availableStock) {
                $msg = $color
                    ? "Số lượng trong giỏ ({$cartItem->quantity}) cộng thêm vượt quá tồn kho của màu {$color} ({$availableStock})!"
                    : "Số lượng trong giỏ ({$cartItem->quantity}) cộng thêm vượt quá tồn kho ({$availableStock})!";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }

                return redirect()->back()->with('error', $msg);
            }
            $cartItem->quantity = $newQty;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'color' => $color,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm '.$quantity.' sản phẩm ('.($color ?: 'mặc định').') vào giỏ hàng thành công!',
                'cart_count' => $cartCount,
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm '.$quantity.' sản phẩm vào giỏ hàng thành công!');
    }

    // Mua ngay sản phẩm (Chuyển thẳng tới màn hình thanh toán & xác nhận mua hàng)
    public function buyNow(Request $request, $productId)
    {
        $product = Product::with('colors')->findOrFail($productId);

        $color = $request->input('color') ?? ($product->color ? explode(',', $product->color)[0] : null);
        if ($color) {
            $color = trim($color);
        }

        $availableStock = $product->getColorStock($color);

        if ($availableStock <= 0) {
            $msg = $color ? "Màu {$color} của sản phẩm hiện đang tạm hết hàng!" : 'Sản phẩm hiện đang tạm hết hàng!';

            return redirect()->back()->with('error', $msg);
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        if ($quantity > $availableStock) {
            $msg = $color
                ? "Rất tiếc, màu {$color} trong kho chỉ còn {$availableStock} sản phẩm!"
                : "Rất tiếc, trong kho chỉ còn {$availableStock} sản phẩm!";

            return redirect()->back()->with('error', $msg);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('color', $color)
            ->first();

        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'color' => $color,
            ]);
        }

        return redirect()->route('checkout.index')->with('info', 'Vui lòng xác nhận thông tin nhận hàng để mua ngay sản phẩm!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // 1. Cập nhật cho Database CartItem
        $cartItem = CartItem::with(['product.colors'])->find($id);
        if ($cartItem && $cartItem->cart->user_id == Auth::id()) {
            $qty = (int) $request->quantity;
            if ($cartItem->product) {
                $availableStock = $cartItem->product->getColorStock($cartItem->color);
                if ($qty > $availableStock) {
                    $colorText = $cartItem->color ? "màu {$cartItem->color} của " : '';

                    return redirect()->route('cart.index')->with('error', "Số lượng yêu cầu ({$qty}) vượt quá tồn kho hiện có ({$availableStock}) của {$colorText}sản phẩm {$cartItem->product->name}!");
                }
            }
            $cartItem->quantity = $qty;
            $cartItem->save();

            return redirect()->route('cart.index')->with('success', 'Đã cập nhật số lượng sản phẩm!');
        }

        // 2. Hỗ trợ cho Session Cart (nếu dùng)
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = (int) $request->quantity;
            session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đã được cập nhật!');
        }

        // Nếu không tìm thấy item hợp lệ -> redirect về giỏ hàng
        return redirect()->route('cart.index')->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng!');
    }

    public function remove($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        if ($cartItem->cart->user_id == Auth::id()) {
            $cartItem->delete();
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    // Xóa toàn bộ giỏ hàng
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->cartItems()->delete();
        }

        session()->forget('coupon');

        return redirect()->route('cart.index')->with('success', 'Đã làm trống toàn bộ giỏ hàng!');
    }

    // Áp dụng mã giảm giá (Coupon) từ cơ sở dữ liệu
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->coupon_code));

        $voucher = Voucher::where('code', $code)->first();

        if (! $voucher) {
            return redirect()->route('cart.index')->with('error', 'Mã giảm giá ['.$code.'] không tồn tại trên hệ thống!');
        }

        // Tính tổng tiền giỏ hàng hiện tại
        $cart = Cart::with('cartItems.product')->where('user_id', Auth::id())->first();
        if (! $cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $cartTotal = $cart->cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Kiểm tra tính hợp lệ của voucher (hạn dùng, đơn tối thiểu, kích hoạt...)
        $check = $voucher->isValidForAmount($cartTotal);
        if (! $check['valid']) {
            return redirect()->route('cart.index')->with('error', $check['message']);
        }

        $discount = $voucher->calculateDiscount($cartTotal);

        session()->put('coupon', [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'discount' => $discount,
            'discount_type' => $voucher->discount_type,
            'discount_value' => $voucher->discount_value,
        ]);

        return redirect()->route('cart.index')->with('success', 'Áp dụng mã giảm giá ['.$code.'] thành công! Tiết kiệm '.number_format($discount, 0, ',', '.').'₫');
    }

    // Hủy mã giảm giá
    public function removeCoupon()
    {
        session()->forget('coupon');

        return redirect()->route('cart.index')->with('success', 'Đã hủy áp dụng mã giảm giá!');
    }
}
