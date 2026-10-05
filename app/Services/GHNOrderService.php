<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class GHNOrderService
{
    public function __construct(private GHNService $ghn) {}

    /**
     * Tạo vận đơn GHN từ Order của hệ thống.
     */
    public function create(Order $order, bool $isPaid = false): array
    {
        $items = [];
        $weight = 0;

        $orderItems = $order->items ?? $order->orderItems;
        if ($orderItems) {
            foreach ($orderItems as $item) {
                $itemWeight = (int) ($item->product->weight ?? 200);
                $weight += $itemWeight * (int) $item->quantity;
                $items[] = [
                    'name' => $item->product->name ?? 'Sản phẩm',
                    'quantity' => (int) $item->quantity,
                    'price' => (int) $item->price,
                    'weight' => $itemWeight,
                ];
            }
        }

        $payload = [
            'payment_type_id' => 1, // 1: Shop trả cước (KH chỉ trả đúng tổng tiền COD trên web, không bị shipper thu thêm cước)
            'note' => 'HKD SIÊU LÒ NƯỚNG - Đơn hàng #'.$order->id,
            'required_note' => 'KHONGCHOXEMHANG',
            'return_phone' => '0913014602',
            'return_address' => '277 Ngõ 277 Quan Hoa, Cầu Giấy, Hà Nội',
            'to_name' => $order->user->name ?? $order->name ?? 'Khách hàng',
            'to_phone' => $order->phone,
            'to_address' => $order->shipping_address ?? $order->address,
            'to_ward_code' => (string) ($order->to_ward_code ?? ''),
            'to_district_id' => (int) ($order->to_district_id ?? 0),
            'cod_amount' => $isPaid ? 0 : (int) $order->total_price,
            'weight' => $weight > 0 ? $weight : 300,
            'length' => 15,
            'width' => 15,
            'height' => 10,
            'service_type_id' => 2,
            'items' => $items,
        ];

        return $this->ghn->createOrder($payload);
    }

    /**
     * Đồng bộ trạng thái thực tế từ hệ thống GHN về database.
     */
    public function syncStatus(Order $order): array
    {
        if (empty($order->ghn_order_code)) {
            return [
                'success' => false,
                'message' => 'Đơn hàng này chưa có mã vận đơn GHN.',
            ];
        }

        try {
            $response = $this->ghn->getOrderDetail($order->ghn_order_code);

            if (($response['code'] ?? null) === 200 && ! empty($response['data'])) {
                $data = $response['data'];
                $ghnRawStatus = $data['status'] ?? '';
                $mappedShippingStatus = $this->ghn->mapGHNStatus($ghnRawStatus);

                $updates = [
                    'shipping_status' => $mappedShippingStatus,
                ];

                // Nếu GHN báo đã giao hàng thành công, tự động hoàn tất đơn hàng nếu chưa hoàn tất
                if ($mappedShippingStatus === 'delivered' && $order->status !== 'completed' && $order->status !== 'cancelled') {
                    $updates['status'] = 'completed';
                }

                // Nếu GHN báo đã hủy vận đơn, cập nhật trạng thái vận chuyển
                if ($mappedShippingStatus === 'cancelled' && $order->shipping_status !== 'cancelled') {
                    $updates['shipping_status'] = 'cancelled';
                }

                $order->update($updates);

                return [
                    'success' => true,
                    'order_code' => $order->ghn_order_code,
                    'ghn_raw_status' => $ghnRawStatus,
                    'shipping_status' => $mappedShippingStatus,
                    'leadtime' => $data['leadtime'] ?? null,
                    'data' => $data,
                    'message' => "Đã đồng bộ trạng thái từ GHN: {$ghnRawStatus} (".($order->shipping_badge['label'] ?? $mappedShippingStatus).')',
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Không thể lấy thông tin chi tiết từ GHN.',
            ];
        } catch (\Throwable $e) {
            Log::error('Lỗi khi đồng bộ trạng thái GHN: '.$e->getMessage(), [
                'order_id' => $order->id,
                'ghn_order_code' => $order->ghn_order_code,
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi kết nối tới hệ thống GHN: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Hủy vận đơn trên GHN khi đơn bị hủy.
     */
    public function cancel(Order $order): array
    {
        if (empty($order->ghn_order_code)) {
            return [
                'success' => false,
                'message' => 'Đơn hàng không có mã vận đơn GHN.',
            ];
        }

        try {
            $response = $this->ghn->cancelOrder([$order->ghn_order_code]);

            if (($response['code'] ?? null) === 200) {
                $order->update([
                    'shipping_status' => 'cancelled',
                ]);

                return [
                    'success' => true,
                    'message' => 'Đã hủy thành công vận đơn trên GHN.',
                ];
            }

            Log::warning('Hủy vận đơn GHN trả về lỗi', [
                'order_code' => $order->ghn_order_code,
                'response' => $response,
            ]);

            return [
                'success' => false,
                'message' => $response['message'] ?? 'GHN từ chối hủy vận đơn (có thể hàng đã được lấy hoặc đang giao).',
            ];
        } catch (\Throwable $e) {
            Log::error('Lỗi ngoại lệ khi hủy vận đơn GHN: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Lỗi kết nối GHN khi hủy đơn: '.$e->getMessage(),
            ];
        }
    }
}
