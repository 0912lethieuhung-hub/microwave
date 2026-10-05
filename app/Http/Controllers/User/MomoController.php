<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    // ──────────────────────────────────────────────
    // KHỞI TẠO THANH TOÁN MOMO (Lab 6)
    // ──────────────────────────────────────────────

    /**
     * Khởi tạo giao dịch MoMo cho đơn hàng mới tạo.
     * Route: GET /orders/{order}/start-momo
     */
    public function start(Order $order, MomoService $momo)
    {
        // Bảo mật: chỉ chủ đơn hàng mới được thanh toán
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Không cho phép thanh toán nếu đã hoàn thành hoặc đã hủy hoặc đã thanh toán trực tuyến
        if (in_array($order->status, ['cancelled', 'completed'], true) || $order->is_paid_online) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Đơn hàng này không thể thanh toán.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Thanh toán lại cho đơn MoMo chưa thanh toán / thất bại.
     * Route: GET /orders/{order}/pay/momo
     */
    public function payAgain(Order $order, MomoService $momo)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $order->can_pay_again) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Đơn hàng này không còn có thể thanh toán lại.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    // ──────────────────────────────────────────────
    // XỬ LÝ PHẢN HỒI TỪ MOMO (CALLBACK & IPN)
    // ──────────────────────────────────────────────

    /**
     * Callback — MoMo redirect khách hàng quay lại website sau khi thanh toán (Lab 6 Page 17).
     * Route: GET /payment/momo/callback
     */
    public function callback(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        $payload = $request->all();
        Log::info('[MoMo Callback] Received payload', [
            'payload' => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        // Tìm đơn hàng liên kết
        $orderId = $momo->orderId($payload);
        $order = $orderId ? Order::find($orderId) : null;

        // 1. Thẩm định kết quả và chữ ký
        if (! $momo->isValidSuccessfulResponse($payload)) {
            Log::warning('[MoMo Callback] Rejected', [
                'result_code' => $request->input('resultCode'),
                'order_id' => $request->input('orderId'),
                'signature_valid' => $momo->isValidResponse($payload),
            ]);

            if ($momo->isValidResponse($payload)) {
                $this->markFailed($payload, $momo);
            }

            $targetRoute = $order ? route('orders.show', $order->id) : route('profile.edit', ['tab' => 'orders']);
            $msg = 'Giao dịch MoMo không thành công: '.($payload['message'] ?? 'Đã bị huỷ hoặc từ chối.');

            return redirect($targetRoute)->with('error', $msg);
        }

        // 2. Hoàn tất thanh toán an toàn
        $result = $this->completePayment($payload, $ghnOrders, $momo);

        $message = in_array($result, ['created', 'already_created', 'already_paid'], true)
            ? 'Thanh toán MoMo thành công! Đơn hàng của bạn đang được xử lý.'
            : 'Thanh toán thành công! Đơn hàng đang chờ tạo vận đơn GHN.';

        return redirect()->route('checkout.success')->with([
            'success' => $message,
            'latest_order_id' => $order ? $order->id : null,
        ]);
    }

    /**
     * IPN (Instant Payment Notification) — MoMo server gọi trực tiếp (Lab 6 Page 17-18).
     * Route: POST /payment/momo/ipn  (KHÔNG qua auth, KHÔNG qua CSRF)
     */
    public function ipn(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        $payload = $request->all();
        Log::info('[MoMo IPN] Received payload', [
            'payload' => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        if ($momo->isValidSuccessfulResponse($payload)) {
            $this->completePayment($payload, $ghnOrders, $momo);
        } elseif ($momo->isValidResponse($payload)) {
            $this->markFailed($payload, $momo);
        } else {
            Log::error('[MoMo IPN] Invalid signature', $payload);

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        return response()->json(['message' => 'Received'], 200);
    }

    // ──────────────────────────────────────────────
    // INTERNAL HELPERS (Lab 6 Page 18-20)
    // ──────────────────────────────────────────────

    /**
     * Tạo một lần thử giao dịch mới cho đơn hàng (Lab 6 Page 18).
     */
    private function newTransaction(Order $order): PaymentTransaction
    {
        // Tái sử dụng bản ghi pending rỗng nếu vừa tạo từ checkout
        $existing = PaymentTransaction::where('order_id', $order->id)
            ->where('gateway', 'momo')
            ->where('status', 'pending')
            ->whereNull('gateway_order_id')
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        return PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'momo',
            'amount' => $order->total_price,
            'status' => 'pending',
            'message' => 'Khởi tạo yêu cầu thanh toán MoMo',
        ]);
    }

    /**
     * Gọi MomoService và chuyển hướng người dùng sang trang thanh toán MoMo.
     */
    private function redirectToMomo(Order $order, PaymentTransaction $transaction, MomoService $momo)
    {
        $result = $momo->createPayment($order, $transaction);

        if (isset($result['payUrl']) && ! empty($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        Log::error('[MoMo] redirectToMomo failed', $result);

        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Không thể kết nối tới cổng MoMo. '.($result['message'] ?? 'Vui lòng thử lại sau.'));
    }

    /**
     * Hoàn tất thanh toán an toàn chống Race Condition (Lab 6 Page 19-20).
     * Dùng DB Transaction + pessimistic lock (lockForUpdate) để bảo vệ số dư & đơn hàng.
     */
    private function completePayment(array $payload, GHNOrderService $ghnOrders, MomoService $momo): string
    {
        $result = DB::transaction(function () use ($payload, $momo) {
            $transaction = PaymentTransaction::where('gateway', 'momo')
                ->where('gateway_order_id', $payload['orderId'] ?? '')
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                return 'invalid';
            }

            // Nếu giao dịch đã hoàn tất trước đó (IPN hoặc Callback đã chạy trước)
            if ($transaction->status === 'paid') {
                return 'already_paid';
            }

            $order = Order::lockForUpdate()->find($transaction->order_id);
            if (! $order) {
                return 'invalid';
            }

            if ($order->ghn_order_code) {
                return 'already_created';
            }

            // Đối chiếu số tiền giao dịch và số tiền MoMo trả về (chống tấn công thay đổi giá)
            if ((int) $transaction->amount !== (int) ($payload['amount'] ?? 0)) {
                $momo->markFailed($transaction, $payload);

                return 'invalid';
            }

            // Cập nhật trạng thái đơn hàng sang processing (đã thanh toán)
            $order->update([
                'status' => 'processing',
                'shipping_status' => 'pending',
            ]);

            // Cập nhật giao dịch sang paid
            $momo->markPaid($transaction, $payload);

            return ['create', $order->id];
        });

        if (! is_array($result)) {
            return (string) $result;
        }

        // Tự động tạo vận đơn GHN sau khi thanh toán thành công (ngoài DB transaction để tránh lock DB)
        $order = Order::with('orderItems.product')->find($result[1]);
        if ($order && $order->to_district_id && $order->to_ward_code) {
            try {
                $response = $ghnOrders->create($order, true); // isPaid = true -> COD thu hộ = 0
                if (isset($response['code']) && $response['code'] == 200 && isset($response['data']['order_code'])) {
                    $order->update([
                        'ghn_order_code' => $response['data']['order_code'],
                        'shipping_status' => 'ready_to_pick',
                    ]);

                    return 'created';
                }
            } catch (\Throwable $e) {
                Log::error('[MoMo] GHN order create failed after MoMo payment', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $order->update(['shipping_status' => 'pending']);

            return 'failed';
        }

        return 'created';
    }

    /**
     * Ghi nhận giao dịch thất bại (Lab 6 Page 20-21).
     * Chỉ đánh dấu nếu giao dịch chưa paid để tránh ghi đè giao dịch đã thanh toán.
     */
    private function markFailed(array $payload, MomoService $momo): void
    {
        $transaction = PaymentTransaction::where('gateway', 'momo')
            ->where('gateway_order_id', $payload['orderId'] ?? '')
            ->first();

        if ($transaction && $transaction->status !== 'paid') {
            $momo->markFailed($transaction, $payload);
        }
    }
}
