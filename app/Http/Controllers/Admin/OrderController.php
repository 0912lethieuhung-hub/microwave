<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductColor;
use App\Services\GHNOrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const TABS = [
        'all' => ['label' => 'Tất cả', 'color' => 'blue', 'statuses' => []],
        'pending' => ['label' => 'Chờ xử lý', 'color' => 'slate', 'statuses' => [
            'pending', 'not_shipped', 'processing',
        ]],
        'ready' => ['label' => 'Chờ lấy hàng', 'color' => 'cyan', 'statuses' => ['ready_to_pick']],
        'picking' => ['label' => 'Đang lấy hàng', 'color' => 'cyan', 'statuses' => ['picking']],
        'delivering' => [
            'label' => 'Đang giao', 'color' => 'amber', 'statuses' => [
                'delivering', 'picked', 'storing', 'transporting', 'sorting',
            ],
        ],
        'delivered' => ['label' => 'Thành công', 'color' => 'green', 'statuses' => ['delivered']],
        'return' => [
            'label' => 'Hoàn hàng', 'color' => 'orange', 'statuses' => [
                'return', 'returning', 'returned', 'return_transporting', 'return_sorting',
            ],
        ],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'red', 'statuses' => ['cancelled']],
    ];

    public function index(Request $request)
    {
        $paymentLabels = [
            'pending' => 'Chờ thanh toán',
            'initiated' => 'Đang chờ MoMo',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'cancelled' => 'Đã hủy',
            'refund_pending' => 'Chờ hoàn tiền',
            'refunded' => 'Đã hoàn tiền',
        ];

        $shippingLabels = [
            'pending' => 'Chờ tạo vận đơn',
            'not_shipped' => 'Chưa giao hàng',
            'processing' => 'Đang tạo vận đơn',
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking' => 'Đang lấy hàng',
            'picked' => 'Đã lấy hàng',
            'storing' => 'Đang lưu kho',
            'transporting' => 'Đang trung chuyển',
            'sorting' => 'Đang phân loại',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Giao hàng thành công',
            'return' => 'Chờ hoàn hàng',
            'returning' => 'Đang hoàn hàng',
            'returned' => 'Đã hoàn hàng',
            'return_transporting' => 'Đang chuyển hoàn',
            'return_sorting' => 'Đang phân loại hoàn',
            'cancelled' => 'Đã hủy',
        ];

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'paid_momo', 'cod_ordered', 'cod_paid', 'completed', 'cancelled'])],
            'payment_status' => ['nullable', Rule::in(array_keys($paymentLabels))],
            'shipping_status' => ['nullable', Rule::in(array_keys($shippingLabels))],
            'gateway' => ['nullable', Rule::in(['cod', 'momo', 'unknown'])],
            'tab' => ['nullable', Rule::in(array_keys(self::TABS))],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])],
            'per_page' => ['nullable', 'integer', Rule::in([15, 25, 50, 100])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'amount_desc', 'amount_asc'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'date_to.after_or_equal' => 'Ngày kết thúc phải từ ngày bắt đầu trở đi.',
            '*.date_format' => 'Ngày lọc không hợp lệ.',
            '*.in' => 'Giá trị bộ lọc không hợp lệ.',
        ]);

        $paymentId = DB::table('payment_transactions')->select('id')->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')->limit(1);

        $source = DB::table('orders')->leftJoin('payment_transactions as payment', function ($join) use ($paymentId) {
            $join->on('payment.order_id', '=', 'orders.id')->where('payment.id', '=', $paymentId);
        })->select('orders.*')
            ->selectRaw("COALESCE(payment.gateway, CASE WHEN orders.status IN ('cod_ordered', 'cod_paid') THEN 'cod' WHEN orders.status IN ('paid', 'paid_momo') THEN 'momo' ELSE 'unknown' END) as gateway")
            ->selectRaw("COALESCE(payment.status, CASE WHEN orders.status = 'cod_ordered' THEN 'pending' WHEN orders.status IN ('cod_paid', 'paid_momo') THEN 'paid' ELSE orders.status END) as payment_status");

        $query = Order::query()->fromSub($source, 'orders');

        foreach (['status', 'payment_status', 'gateway'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $filters[$field]);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('ghn_order_code', 'like', '%'.$search.'%')
                    ->orWhereHas('items.product', fn ($products) => $products->where('name', 'like', '%'.$search.'%'));
                if (preg_match('/^(?:#|DH)?0*(\d+)$/i', $search, $matches)) {
                    $query->orWhere('orders.id', $matches[1]);
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
        }

        // Số trên tab theo bộ lọc chung, không bị giới hạn bởi trang hiện tại.
        $shippingCounts = (clone $query)->select('shipping_status')->selectRaw('COUNT(*) as total')
            ->groupBy('shipping_status')->pluck('total', 'shipping_status');

        $tabs = collect(self::TABS)->map(function ($tab, $key) use ($shippingCounts) {
            $tab['count'] = $key === 'all'
                ? (int) $shippingCounts->sum()
                : (int) collect($tab['statuses'])->sum(fn ($status) => $shippingCounts->get($status, 0));

            return $tab;
        });

        $activeTab = $filters['tab'] ?? 'all';
        if ($activeTab !== 'all') {
            $query->whereIn('shipping_status', self::TABS[$activeTab]['statuses']);
        }

        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $filters['shipping_status']);
        }

        [$column, $direction] = match ($filters['sort'] ?? 'newest') {
            'oldest' => ['created_at', 'asc'],
            'amount_desc' => ['total_price', 'desc'],
            'amount_asc' => ['total_price', 'asc'],
            default => ['created_at', 'desc'],
        };

        if ($request->has('export')) {
            $exportOrders = (clone $query)->with('user', 'items.product')->orderBy($column, $direction)->get();
            $filename = 'don-hang-'.now()->format('Ymd-His').'.csv';

            return response()->streamDownload(function () use ($exportOrders) {
                $handle = fopen('php://output', 'w');
                // UTF-8 BOM để mở tiếng Việt không bị lỗi font trên Excel
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['Mã đơn hàng', 'Ngày tạo', 'Khách hàng', 'Số điện thoại', 'Sản phẩm', 'Tổng tiền', 'COD cần thu', 'Mã vận đơn', 'Trạng thái giao hàng', 'Trạng thái thanh toán']);

                foreach ($exportOrders as $o) {
                    $itemsText = $o->items->map(fn ($item) => ($item->product->name ?? 'Sản phẩm').' x '.$item->quantity)->join(', ');
                    $isPaid = in_array($o->payment_status, ['paid', 'completed']) || in_array($o->status, ['paid', 'cod_paid', 'paid_momo', 'completed']);
                    $codAmount = $isPaid ? 0 : $o->total_price;

                    fputcsv($handle, [
                        'DH'.str_pad((string) $o->id, 6, '0', STR_PAD_LEFT),
                        $o->created_at ? $o->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') : '',
                        $o->user->name ?? 'Khách vãng lai',
                        $o->phone ?? '',
                        $itemsText,
                        $o->total_price,
                        $codAmount,
                        $o->ghn_order_code ?? '',
                        $o->shipping_badge['label'] ?? $o->shipping_status,
                        $o->payment_status ?? $o->status,
                    ]);
                }
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        $orders = $query->with('user', 'items.product')
            ->orderBy($column, $direction)
            ->orderBy('id', $direction)
            ->paginate((int) ($filters['per_page'] ?? 25))
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'filters', 'tabs', 'activeTab', 'paymentLabels', 'shippingLabels'));
    }

    public function show(Order $order, GHNOrderService $ghnOrderService)
    {
        $order->load([
            'orderItems.product',
            'user',
            'paymentTransactions' => fn ($q) => $q->latest(),
        ]);

        $ghnDetail = null;
        // Tự động kiểm tra và đồng bộ trạng thái thực tế từ GHN nếu có mã vận đơn
        if ($order->ghn_order_code) {
            $syncResult = $ghnOrderService->syncStatus($order);
            if ($syncResult['success'] ?? false) {
                $ghnDetail = $syncResult['data'] ?? null;
            }
        }

        return view('admin.orders.show', compact('order', 'ghnDetail'));
    }

    /**
     * Đồng bộ thủ công trạng thái của một đơn hàng từ GHN.
     */
    public function syncGHN(Order $order, GHNOrderService $ghnOrderService)
    {
        $res = $ghnOrderService->syncStatus($order);

        if ($res['success'] ?? false) {
            return redirect()->back()->with('success', $res['message']);
        }

        return redirect()->back()->with('error', $res['message'] ?? 'Không thể đồng bộ từ GHN.');
    }

    /**
     * Đồng bộ hàng loạt trạng thái các đơn hàng đang giao từ GHN.
     */
    public function syncAllGHN(GHNOrderService $ghnOrderService)
    {
        $orders = Order::whereNotNull('ghn_order_code')
            ->whereNotIn('shipping_status', ['delivered', 'cancelled'])
            ->get();

        $synced = 0;
        foreach ($orders as $order) {
            $res = $ghnOrderService->syncStatus($order);
            if ($res['success'] ?? false) {
                $synced++;
            }
        }

        return redirect()->back()->with('success', "Đã đồng bộ thành công {$synced}/{$orders->count()} đơn hàng từ hệ thống GHN!");
    }

    public function updateStatus(Request $request, Order $order, GHNOrderService $ghnOrderService)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,canceled',
        ]);

        $newStatus = $request->status === 'canceled' ? 'cancelled' : $request->status;
        $oldStatus = $order->status;
        $ghnNotice = '';

        // Không cho hủy nếu đơn hàng đang ở trạng thái giao hàng
        $deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];
        if ($newStatus === 'cancelled' && in_array($order->shipping_status, $deliveringStatuses, true)) {
            return redirect()->back()->with('error', 'Đơn hàng đang ở trạng thái đang giao hàng, KHÔNG thể hủy!');
        }

        // Không cho hoàn tất nếu thanh toán online (MoMo) bị thất bại
        $latestPayment = $order->paymentTransactions()->latest('id')->first();
        $isOnlinePaymentFailed = ($order->payment_method === 'momo' || ($latestPayment && $latestPayment->gateway === 'momo'))
            && ($latestPayment && $latestPayment->status === 'failed');

        if ($newStatus === 'completed' && $isOnlinePaymentFailed) {
            return redirect()->back()->with('error', 'Không thể hoàn tất đơn hàng #'.$order->id.' do giao dịch thanh toán MoMo bị THẤT BẠI. Khách chưa thanh toán tiền cho đơn này!');
        }

        if ($newStatus !== $oldStatus) {
            DB::transaction(function () use ($newStatus, $oldStatus, $order) {
                // Khôi phục số lượng tồn kho sản phẩm và màu sắc nếu đơn bị hủy
                if ($newStatus === 'cancelled') {
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
                } elseif ($oldStatus === 'cancelled') {
                    // Trừ lại tồn kho nếu hoàn tác từ trạng thái đã hủy (chống âm)
                    foreach ($order->orderItems as $item) {
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
                        $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                        if ($product) {
                            $product->update([
                                'stock' => max(0, (int) $product->stock - (int) $item->quantity),
                            ]);
                        }
                    }
                    $updateData = ['status' => $newStatus];
                    if ($newStatus === 'completed') {
                        if (! empty($order->ghn_order_code)) {
                            $updateData['shipping_status'] = 'delivered';
                        }
                    } elseif ($newStatus === 'processing') {
                        $updateData['shipping_status'] = 'ready_to_pick';
                    } elseif ($newStatus === 'pending') {
                        $updateData['shipping_status'] = 'pending';
                    }
                    $order->update($updateData);
                } else {
                    $updateData = ['status' => $newStatus];
                    if ($newStatus === 'completed') {
                        if (! empty($order->ghn_order_code)) {
                            $updateData['shipping_status'] = 'delivered';
                        }
                    } elseif ($newStatus === 'processing' && in_array($order->shipping_status, ['pending', 'cancelled'], true)) {
                        $updateData['shipping_status'] = 'ready_to_pick';
                    }
                    $order->update($updateData);
                }
            });

            // Tự động hủy vận đơn trên GHN nếu đơn đã tạo vận đơn GHN
            if ($newStatus === 'cancelled' && $order->ghn_order_code) {
                $ghnOrderService->cancel($order);
            }

            // TỰ ĐỘNG TẠO VẬN ĐƠN GHN khi chuyển sang "Đang xử lý / Đóng gói" (nếu chưa có mã vận đơn GHN)
            if ($newStatus === 'processing' && empty($order->ghn_order_code) && $request->boolean('create_ghn', true)) {
                if ($order->to_district_id && $order->to_ward_code) {
                    try {
                        $order->load('orderItems.product', 'user');
                        $isPaid = (bool) $order->is_paid_online;
                        $res = $ghnOrderService->create($order, $isPaid);

                        if (isset($res['code']) && $res['code'] == 200 && ! empty($res['data']['order_code'])) {
                            $order->update([
                                'ghn_order_code' => $res['data']['order_code'],
                                'shipping_status' => 'ready_to_pick',
                            ]);
                            $ghnNotice = " và đã tự động tạo vận đơn GHN thành công (Mã: <strong>{$res['data']['order_code']}</strong>)!";
                        } else {
                            $err = $res['message'] ?? 'Lỗi không xác định từ GHN';
                            $ghnNotice = ", nhưng chưa thể tạo vận đơn GHN ({$err}).";
                        }
                    } catch (\Throwable $e) {
                        $ghnNotice = ', nhưng lỗi kết nối GHN: '.$e->getMessage();
                    }
                } else {
                    $ghnNotice = ', nhưng đơn hàng thiếu mã Quận/Huyện hoặc Phường/Xã để tạo vận đơn GHN.';
                }
            }
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng #'.$order->id.' thành công'.$ghnNotice);
    }

    /**
     * Tạo thủ công vận đơn GHN cho đơn hàng bất cứ lúc nào.
     */
    public function createGHN(Order $order, GHNOrderService $ghnOrderService)
    {
        if ($order->ghn_order_code) {
            return redirect()->back()->with('error', "Đơn hàng này đã có mã vận đơn GHN [{$order->ghn_order_code}].");
        }

        if ($order->status === 'cancelled') {
            return redirect()->back()->with('error', 'Không thể tạo vận đơn cho đơn hàng đã bị hủy.');
        }

        if (! $order->to_district_id || ! $order->to_ward_code) {
            return redirect()->back()->with('error', 'Đơn hàng chưa có thông tin Quận/Huyện hoặc Phường/Xã chuẩn của GHN.');
        }

        try {
            $order->load('orderItems.product', 'user');
            $isPaid = (bool) $order->is_paid_online;
            $res = $ghnOrderService->create($order, $isPaid);

            if (isset($res['code']) && $res['code'] == 200 && ! empty($res['data']['order_code'])) {
                $order->update([
                    'ghn_order_code' => $res['data']['order_code'],
                    'shipping_status' => 'ready_to_pick',
                ]);

                return redirect()->back()->with('success', "Đã tạo vận đơn GHN thành công cho đơn #{$order->id}! Mã vận đơn: {$res['data']['order_code']}");
            }

            $errMsg = $res['message'] ?? 'Lỗi từ API GHN';

            return redirect()->back()->with('error', "Không thể tạo vận đơn GHN: {$errMsg}");

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Lỗi khi gọi API GHN: '.$e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái hàng loạt cho nhiều đơn hàng được chọn.
     */
    public function bulkUpdateStatus(Request $request, GHNOrderService $ghnOrderService)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'required|integer|exists:orders,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $newStatus = $request->status;
        $orderIds = $request->order_ids;
        $orders = Order::whereIn('id', $orderIds)->get();

        $successCount = 0;
        $skippedShipping = [];
        $skippedFailedPayment = [];
        $deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];

        foreach ($orders as $order) {
            // Không cho hủy nếu đơn hàng đang ở trạng thái giao hàng
            if ($newStatus === 'cancelled' && in_array($order->shipping_status, $deliveringStatuses, true)) {
                $skippedShipping[] = '#'.$order->id;

                continue;
            }

            // Không cho hoàn tất nếu thanh toán online (MoMo) bị thất bại
            $latestPayment = $order->paymentTransactions()->latest('id')->first();
            $isOnlinePaymentFailed = ($order->payment_method === 'momo' || ($latestPayment && $latestPayment->gateway === 'momo'))
                && ($latestPayment && $latestPayment->status === 'failed');

            if ($newStatus === 'completed' && $isOnlinePaymentFailed) {
                $skippedFailedPayment[] = '#'.$order->id;

                continue;
            }

            $oldStatus = $order->status;
            if ($newStatus !== $oldStatus) {
                DB::transaction(function () use ($newStatus, $oldStatus, $order) {
                    if ($newStatus === 'cancelled') {
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
                    } elseif ($oldStatus === 'cancelled') {
                        // Trừ lại tồn kho nếu hoàn tác từ trạng thái đã hủy (chống âm)
                        foreach ($order->orderItems as $item) {
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
                            $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                            if ($product) {
                                $product->update([
                                    'stock' => max(0, (int) $product->stock - (int) $item->quantity),
                                ]);
                            }
                        }
                        $updateData = ['status' => $newStatus];
                        if ($newStatus === 'completed') {
                            if (! empty($order->ghn_order_code)) {
                                $updateData['shipping_status'] = 'delivered';
                            }
                        } elseif ($newStatus === 'processing') {
                            $updateData['shipping_status'] = 'ready_to_pick';
                        } elseif ($newStatus === 'pending') {
                            $updateData['shipping_status'] = 'pending';
                        }
                        $order->update($updateData);
                    } else {
                        $updateData = ['status' => $newStatus];
                        if ($newStatus === 'completed') {
                            if (! empty($order->ghn_order_code)) {
                                $updateData['shipping_status'] = 'delivered';
                            }
                        } elseif ($newStatus === 'processing' && in_array($order->shipping_status, ['pending', 'cancelled'], true)) {
                            $updateData['shipping_status'] = 'ready_to_pick';
                        }
                        $order->update($updateData);
                    }
                });

                if ($newStatus === 'cancelled' && $order->ghn_order_code) {
                    $ghnOrderService->cancel($order);
                }

                $successCount++;
            }
        }

        $alreadyInStatusCount = count($orders) - $successCount - count($skippedShipping) - count($skippedFailedPayment);

        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang chuẩn bị',
            'completed' => 'Hoàn tất đơn',
            'cancelled' => 'Đã hủy',
        ];
        $targetLabel = $statusLabels[$newStatus] ?? $newStatus;

        if ($successCount > 0) {
            $message = "Đã cập nhật trạng thái {$successCount} đơn hàng sang '{$targetLabel}' thành công!";
            if (! empty($skippedShipping)) {
                $message .= ' Bỏ qua các đơn đang giao hàng: '.implode(', ', $skippedShipping).' (không thể hủy).';
            }
            if (! empty($skippedFailedPayment)) {
                $message .= ' Bỏ qua các đơn thanh toán thất bại: '.implode(', ', $skippedFailedPayment).' (không thể hoàn tất khi chưa thu tiền).';
            }
            if ($alreadyInStatusCount > 0) {
                $message .= " ({$alreadyInStatusCount} đơn đã ở trạng thái '{$targetLabel}' từ trước).";
            }

            return redirect()->back()->with('success', $message);
        }

        if (! empty($skippedShipping)) {
            return redirect()->back()->with('error', 'Không thể hủy các đơn hàng đang giao GHN: '.implode(', ', $skippedShipping));
        }

        if (! empty($skippedFailedPayment)) {
            return redirect()->back()->with('error', 'Không thể hoàn tất các đơn hàng có thanh toán trực tuyến bị THẤT BẠI: '.implode(', ', $skippedFailedPayment));
        }

        $orderCodes = $orders->map(fn ($o) => 'DH'.str_pad((string) $o->id, 6, '0', STR_PAD_LEFT))->implode(', ');

        return redirect()->back()->with('info', "Đơn hàng được chọn ({$orderCodes}) hiện tại vốn đã ở trạng thái '{$targetLabel}' từ trước, không cần thay đổi.");
    }
}
