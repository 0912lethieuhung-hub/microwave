<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoService
{
    private string $endpoint;

    private string $partnerCode;

    private string $accessKey;

    private string $secretKey;

    private string $redirectUrl;

    private string $ipnUrl;

    private bool $verifySsl;

    public function __construct()
    {
        $cfg = config('services.momo');
        $this->endpoint = $cfg['endpoint'] ?? 'https://test-payment.momo.vn/v2/gateway/api/create';
        $this->partnerCode = $cfg['partner_code'] ?? '';
        $this->accessKey = $cfg['access_key'] ?? '';
        $this->secretKey = $cfg['secret_key'] ?? '';
        $this->redirectUrl = $cfg['redirect_url'] ?? (route('user.payment.momo.callback'));
        $this->ipnUrl = $cfg['ipn_url'] ?? (route('payment.momo.ipn'));
        $this->verifySsl = filter_var($cfg['verify_ssl'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    // ──────────────────────────────────────────────
    // Tạo yêu cầu thanh toán MoMo (Lab 6)
    // ──────────────────────────────────────────────

    /**
     * Tạo yêu cầu thanh toán MoMo và trả về response từ API.
     */
    public function createPayment(Order $order, PaymentTransaction $transaction): array
    {
        $orderId = $order->id.'_'.$transaction->id.'_'.time();
        $amount = (string) ((int) $order->total_price);
        $requestId = (string) time();
        $orderInfo = 'Thanh toan don hang #'.$order->id;
        $redirectUrl = $this->redirectUrl;
        $ipnUrl = $this->ipnUrl;
        $extraData = (string) $order->id;
        $requestType = config('services.momo.request_type', 'payWithATM');

        // Tạo chuỗi rawHash theo đúng thứ tự chuẩn Lab 6
        $rawHash = 'accessKey='.$this->accessKey
                 .'&amount='.$amount
                 .'&extraData='.$extraData
                 .'&ipnUrl='.$ipnUrl
                 .'&orderId='.$orderId
                 .'&orderInfo='.$orderInfo
                 .'&partnerCode='.$this->partnerCode
                 .'&redirectUrl='.$redirectUrl
                 .'&requestId='.$requestId
                 .'&requestType='.$requestType;

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'Siêu Lò Nướng',
            'storeId' => 'MomoStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        // Cập nhật thông tin yêu cầu vào transaction
        $transaction->update([
            'gateway_order_id' => $orderId,
            'request_payload' => $data,
        ]);

        try {
            $response = Http::withOptions([
                'verify' => $this->verifySsl,
            ])->timeout(15)->post($this->endpoint, $data);

            $result = $response->json() ?? [];

            // Cập nhật phản hồi từ MoMo vào transaction
            $transaction->update([
                'response_payload' => $result,
                'result_code' => isset($result['resultCode']) ? (int) $result['resultCode'] : null,
                'message' => $result['message'] ?? null,
                'status' => isset($result['payUrl']) ? 'initiated' : 'failed',
            ]);

            return $result;

        } catch (\Throwable $e) {
            Log::error('[MoMo] Exception during createPayment: '.$e->getMessage());

            $transaction->update([
                'status' => 'failed',
                'message' => 'Lỗi kết nối tới MoMo: '.$e->getMessage(),
            ]);

            return ['error' => 'Không thể kết nối tới cổng thanh toán MoMo: '.$e->getMessage()];
        }
    }

    // ──────────────────────────────────────────────
    // Thẩm định chữ ký callback / IPN từ MoMo
    // ──────────────────────────────────────────────

    /**
     * Kiểm tra giao dịch thành công (resultCode === '0' hoặc 0).
     */
    public function isSuccessful(array $payload): bool
    {
        return (string) ($payload['resultCode'] ?? '') === '0';
    }

    /**
     * Kiểm tra callback / IPN MoMo thành công đầy đủ (cả chữ ký + kết quả).
     */
    public function isValidSuccessfulResponse(array $payload): bool
    {
        return $this->isValidResponse($payload) && $this->isSuccessful($payload);
    }

    /**
     * Xác thực chữ ký SHA256 trong dữ liệu MoMo trả về (dùng hash_equals chống timing attack).
     */
    public function isValidResponse(array $payload): bool
    {
        if (! isset($payload['signature'])) {
            return false;
        }

        // Xây dựng lại rawHash theo đúng thứ tự chữ cái chuẩn MoMo v2 (Lab 6 Page 10)
        $rawHash = 'accessKey='.$this->accessKey
                 .'&amount='.($payload['amount'] ?? '')
                 .'&extraData='.($payload['extraData'] ?? '')
                 .'&message='.($payload['message'] ?? '')
                 .'&orderId='.($payload['orderId'] ?? '')
                 .'&orderInfo='.($payload['orderInfo'] ?? '')
                 .'&orderType='.($payload['orderType'] ?? '')
                 .'&partnerCode='.($payload['partnerCode'] ?? '')
                 .'&payType='.($payload['payType'] ?? '')
                 .'&requestId='.($payload['requestId'] ?? '')
                 .'&responseTime='.($payload['responseTime'] ?? '')
                 .'&resultCode='.($payload['resultCode'] ?? '')
                 .'&transId='.($payload['transId'] ?? '');

        return hash_equals(
            hash_hmac('sha256', $rawHash, $this->secretKey),
            (string) $payload['signature']
        );
    }

    /**
     * Lấy order_id nội bộ của hệ thống từ payload MoMo (Lab 6 Page 10).
     */
    public function orderId(array $payload): ?int
    {
        // 1. Kiểm tra extraData (chứa order->id theo thiết kế Lab 6)
        $orderId = $payload['extraData'] ?? null;
        if (is_numeric($orderId)) {
            return (int) $orderId;
        }

        // 2. Fallback: Parse từ orderId format {order_id}_{transaction_id}_{timestamp}
        $rawOrderId = $payload['orderId'] ?? '';
        $parts = explode('_', $rawOrderId);
        if (isset($parts[0]) && is_numeric($parts[0])) {
            return (int) $parts[0];
        }
        if (isset($parts[1]) && is_numeric($parts[1])) {
            return (int) $parts[1];
        }

        return null;
    }

    // ──────────────────────────────────────────────
    // Cập nhật trạng thái giao dịch
    // ──────────────────────────────────────────────

    /**
     * Đánh dấu giao dịch thành công và cập nhật thời gian thanh toán.
     */
    public function markPaid(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id' => $payload['transId'] ?? null,
            'result_code' => (int) ($payload['resultCode'] ?? 0),
            'message' => $payload['message'] ?? 'Thanh toán thành công',
            'response_payload' => $payload,
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
    }

    /**
     * Đánh dấu giao dịch thất bại.
     */
    public function markFailed(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id' => $payload['transId'] ?? null,
            'result_code' => isset($payload['resultCode']) ? (int) $payload['resultCode'] : null,
            'message' => $payload['message'] ?? 'Thanh toán thất bại',
            'response_payload' => $payload,
            'status' => 'failed',
        ]);
    }
}
