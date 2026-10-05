<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GHNService
{
    protected string $baseUrl;

    protected string $token;

    protected int $shopId;

    // Ward code của kho lấy hàng (Phường Quan Hoa, Quận Cầu Giấy, Hà Nội)
    protected string $fromWardCode = '1A0606';

    // Service IDs thực tế của GHN (lấy từ available-services API)
    // 53320 = Hàng nhẹ (< 20kg) | 100039 = Hàng nặng (>= 20kg)
    protected int $serviceIdLight = 53320;

    protected int $serviceIdHeavy = 100039;

    public function __construct()
    {
        $this->baseUrl = config('services.ghn.base_url', 'https://dev-online-gateway.ghn.vn/shiip/public-api');
        $this->token = config('services.ghn.token') ?? '';
        $this->shopId = (int) config('services.ghn.shop_id', 0);
    }

    protected function client()
    {
        $headers = [
            'Token' => $this->token,
            'Content-Type' => 'application/json',
        ];
        if ($this->shopId > 0) {
            $headers['ShopId'] = (string) $this->shopId;
        }

        return Http::baseUrl($this->baseUrl)
            ->withOptions([
                'verify' => filter_var(config('services.ghn.verify_ssl', false), FILTER_VALIDATE_BOOLEAN),
            ])
            ->acceptJson()
            ->timeout(15)
            ->withHeaders($headers);
    }

    // Lấy danh sách Tỉnh/Thành từ GHN (đã làm sạch dữ liệu rác và sắp xếp chuẩn)
    public function getProvinces(): array
    {
        $res = $this->get('/master-data/province');
        if (! empty($res['data']) && is_array($res['data'])) {
            $res['data'] = $this->cleanAndSortProvinces($res['data']);
        }

        return $res;
    }

    // Lấy danh sách Quận/Huyện từ GHN theo Province ID (đã lọc rác và sắp xếp theo bảng chữ cái)
    public function getDistricts(int $provinceId): array
    {
        $res = $this->get('/master-data/district', [
            'province_id' => $provinceId,
        ]);
        if (! empty($res['data']) && is_array($res['data'])) {
            $res['data'] = $this->cleanAndSortDistricts($res['data']);
        }

        return $res;
    }

    // Lấy danh sách Phường/Xã từ GHN theo District ID (đã lọc rác và sắp xếp theo bảng chữ cái)
    public function getWards(int $districtId): array
    {
        $res = $this->get('/master-data/ward', [
            'district_id' => $districtId,
        ]);
        if (! empty($res['data']) && is_array($res['data'])) {
            $res['data'] = $this->cleanAndSortWards($res['data']);
        }

        return $res;
    }

    /**
     * Lọc bỏ các tỉnh/thành test rác của sandbox GHN và sắp xếp ưu tiên Hà Nội, TP. HCM, Đà Nẵng
     */
    protected function cleanAndSortProvinces(array $data): array
    {
        $clean = array_filter($data, function ($p) {
            $name = trim($p['ProvinceName'] ?? '');
            if (empty($name)) {
                return false;
            }
            if (stripos($name, 'test') !== false || stripos($name, 'alert') !== false) {
                return false;
            }
            if (preg_match('/\b0[2-9]\b/', $name) || preg_match('/\d{2,}$/', $name)) {
                return false;
            }
            if (in_array((int) ($p['ProvinceID'] ?? 0), [298, 2002], true)) {
                return false;
            }

            return true;
        });

        foreach ($clean as &$p) {
            if ($p['ProvinceName'] === 'Hồ Chí Minh') {
                $p['ProvinceName'] = 'TP. Hồ Chí Minh';
            }
        }
        unset($p);

        $priorityIds = [201, 202, 203]; // Hà Nội, TP. HCM, Đà Nẵng
        $priority = [];
        $others = [];

        foreach ($clean as $p) {
            $id = (int) ($p['ProvinceID'] ?? 0);
            if (in_array($id, $priorityIds, true)) {
                $priority[$id] = $p;
            } else {
                $others[] = $p;
            }
        }

        $sortedPriority = [];
        foreach ($priorityIds as $pid) {
            if (isset($priority[$pid])) {
                $sortedPriority[] = $priority[$pid];
            }
        }

        $collator = class_exists('\Collator') ? new \Collator('vi_VN') : null;
        usort($others, function ($a, $b) use ($collator) {
            if ($collator) {
                return $collator->compare($a['ProvinceName'] ?? '', $b['ProvinceName'] ?? '');
            }

            return strcoll($a['ProvinceName'] ?? '', $b['ProvinceName'] ?? '');
        });

        return array_values(array_merge($sortedPriority, $others));
    }

    /**
     * Lọc và sắp xếp quận/huyện
     */
    protected function cleanAndSortDistricts(array $data): array
    {
        $clean = array_filter($data, function ($d) {
            $name = trim($d['DistrictName'] ?? '');
            if (empty($name)) {
                return false;
            }
            if (stripos($name, 'test') !== false || stripos($name, 'alert') !== false) {
                return false;
            }

            return true;
        });

        $collator = class_exists('\Collator') ? new \Collator('vi_VN') : null;
        usort($clean, function ($a, $b) use ($collator) {
            if ($collator) {
                return $collator->compare($a['DistrictName'] ?? '', $b['DistrictName'] ?? '');
            }

            return strcoll($a['DistrictName'] ?? '', $b['DistrictName'] ?? '');
        });

        return array_values($clean);
    }

    /**
     * Lọc và sắp xếp phường/xã
     */
    protected function cleanAndSortWards(array $data): array
    {
        $clean = array_filter($data, function ($w) {
            $name = trim($w['WardName'] ?? '');
            if (empty($name)) {
                return false;
            }
            if (stripos($name, 'test') !== false || stripos($name, 'alert') !== false) {
                return false;
            }

            return true;
        });

        $collator = class_exists('\Collator') ? new \Collator('vi_VN') : null;
        usort($clean, function ($a, $b) use ($collator) {
            if ($collator) {
                return $collator->compare($a['WardName'] ?? '', $b['WardName'] ?? '');
            }

            return strcoll($a['WardName'] ?? '', $b['WardName'] ?? '');
        });

        return array_values($clean);
    }

    // Lấy danh sách dịch vụ khả dụng cho tuyến vận chuyển
    public function getAvailableServices(int $toDistrictId): array
    {
        return $this->post('/v2/shipping-order/available-services', [
            'shop_id' => $this->shopId,
            'from_district' => (int) config('services.ghn.from_district_id', 1485),
            'to_district' => $toDistrictId,
        ]);
    }

    // Tham số quy cách đóng gói cho lò vi sóng
    public function packageParameters(int $weight = 200): array
    {
        $weight = max($weight, 200);
        $serviceId = $weight >= 20000 ? $this->serviceIdHeavy : $this->serviceIdLight;

        return [
            'weight' => $weight,
            'length' => 30,
            'width' => 30,
            'height' => 25,
            'service_type_id' => 2, // Gói chuẩn GHN (áp dụng mọi đơn)
            'service_id' => $serviceId,
        ];
    }

    // Tính phí vận chuyển thực tế theo địa chỉ khách
    public function calculateFee(array $params): array
    {
        $weight = (int) ($params['weight'] ?? 200);
        $serviceId = $params['service_id'] ?? ($weight >= 20000 ? $this->serviceIdHeavy : $this->serviceIdLight);

        return $this->post('/v2/shipping-order/fee', array_merge([
            'shop_id' => $this->shopId,
            'from_ward_code' => $this->fromWardCode,
            'insurance_value' => 0,
            'coupon' => null,
            'service_id' => $serviceId,
        ], $params));
    }

    // Tạo đơn giao hàng trên GHN
    public function createOrder(array $orderData): array
    {
        return $this->post('/v2/shipping-order/create', array_merge([
            'shop_id' => $this->shopId,
        ], $orderData));
    }

    // Hủy đơn hàng trên GHN
    public function cancelOrder(array $orderCodes): array
    {
        return $this->post('/v2/switch-status/cancel', [
            'order_codes' => $orderCodes,
            'shop_id' => $this->shopId,
        ]);
    }

    // Lấy chi tiết vận đơn từ GHN (bao gồm trạng thái giao hàng thực tế)
    public function getOrderDetail(string $orderCode): array
    {
        return $this->post('/v2/shipping-order/detail', [
            'order_code' => $orderCode,
        ]);
    }

    /**
     * Map trạng thái GHN → trạng thái nội bộ của hệ thống
     * GHN status docs: https://api.ghn.vn/home/docs/detail?id=37
     */
    public function mapGHNStatus(string $ghnStatus): string
    {
        return match ($ghnStatus) {
            'ready_to_pick' => 'ready_to_pick',   // Chờ lấy hàng
            'picking' => 'picking',           // Đang lấy hàng
            'money_collect_picking' => 'picking',
            'picked' => 'picked',            // Đã lấy hàng
            'storing' => 'picked',            // Đang lưu kho
            'transporting' => 'delivering',        // Đang vận chuyển
            'sorting' => 'delivering',        // Đang phân loại
            'delivering' => 'delivering',        // Đang giao hàng
            'money_collect_delivering' => 'delivering',
            'delivered' => 'delivered',         // Đã giao thành công
            'delivery_fail' => 'delivery_fail',    // Giao thất bại
            'waiting_to_return' => 'delivery_fail',
            'return' => 'returning',         // Đang hoàn hàng
            'returning' => 'returning',
            'returned' => 'returned',          // Đã hoàn hàng
            'cancel', 'cancelled' => 'cancelled',        // Đã hủy
            default => $ghnStatus,
        };
    }

    protected function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->client()->get($uri, $query);

            if (! $response->successful()) {
                Log::warning('GHN GET request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return ['code' => $response->status(), 'message' => 'GHN API request failed.', 'data' => []];
            }

            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.', 'data' => []];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);

            return ['code' => -1, 'message' => 'Unable to connect to GHN.', 'data' => []];
        } catch (\Throwable $exception) {
            Log::error('GHN GET Error: '.$exception->getMessage(), ['uri' => $uri]);

            return ['code' => -1, 'message' => $exception->getMessage(), 'data' => []];
        }
    }

    protected function post(string $uri, array $payload): array
    {
        try {
            $response = $this->client()->post($uri, $payload);

            if (! $response->successful()) {
                Log::warning('GHN POST request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return ['code' => $response->status(), 'message' => 'GHN API request failed.'];
            }

            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.'];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);

            return ['code' => -1, 'message' => 'Unable to connect to GHN.'];
        } catch (\Throwable $exception) {
            Log::error('GHN POST Error: '.$exception->getMessage(), ['uri' => $uri]);

            return ['code' => -1, 'message' => $exception->getMessage()];
        }
    }
}
