<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GHNController extends Controller
{
    /**
     * Lấy danh sách Tỉnh/Thành từ GHN API
     */
    public function getProvinces(GHNService $ghn)
    {
        $res = $ghn->getProvinces();

        return response()->json($res);
    }

    /**
     * Lấy danh sách Quận/Huyện từ GHN API theo Province ID
     */
    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        $res = $ghn->getDistricts($provinceId);

        return response()->json($res);
    }

    /**
     * Lấy danh sách Phường/Xã từ GHN API theo District ID
     */
    public function getWards(int $districtId, GHNService $ghn)
    {
        $res = $ghn->getWards($districtId);

        return response()->json($res);
    }

    /**
     * Tính cước phí vận chuyển thực tế từ GHN API
     */
    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
        ]);

        // Tính tổng khối lượng từ giỏ hàng hiện tại
        $weight = 0;
        $cart = Cart::with('cartItems.product')->where('user_id', Auth::id())->first();
        if ($cart && $cart->cartItems) {
            foreach ($cart->cartItems as $item) {
                $itemWeight = (int) ($item->product->weight ?? config('services.ghn.default_weight', 200));
                $weight += $itemWeight * (int) $item->quantity;
            }
        }

        // Kiểm tra bổ sung nếu có session cart
        if ($weight <= 0) {
            $sessionCart = session('cart', []);
            foreach ($sessionCart as $item) {
                $itemWeight = (int) ($item['weight'] ?? config('services.ghn.default_weight', 200));
                $weight += $itemWeight * (int) ($item['quantity'] ?? 1);
            }
        }

        if ($weight <= 0) {
            $weight = (int) config('services.ghn.default_weight', 200);
        }

        $params = array_merge([
            'from_district_id' => (int) config('services.ghn.from_district_id', 1450),
            'to_district_id' => (int) $request->to_district_id,
            'to_ward_code' => (string) $request->to_ward_code,
        ], $ghn->packageParameters($weight));

        $feeResult = $ghn->calculateFee($params);

        // Trường hợp API GHN trả về lỗi hoặc timeout, fallback phí vận chuyển tiêu chuẩn 35.000đ
        if (! isset($feeResult['code']) || $feeResult['code'] !== 200 || ! isset($feeResult['data'])) {
            return response()->json([
                'code' => 200,
                'message' => 'Standard shipping fee',
                'data' => [
                    'total' => 35000,
                    'service_fee' => 35000,
                    'is_fallback' => true,
                ],
            ]);
        }

        return response()->json($feeResult);
    }
}
