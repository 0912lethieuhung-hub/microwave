<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'shipping_address',
        'phone',
        // Phương thức thanh toán: 'cod' | 'momo'
        'payment_method',
        // Các trường tích hợp Giao Hàng Nhanh (GHN)
        'shipping_status',
        'ghn_order_code',
        'ghn_total_fee',
        'to_district_id',
        'to_ward_code',
    ];

    // Một đơn hàng thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Một đơn hàng có nhiều mục sản phẩm
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Tương thích với quan hệ cũ
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ──────────────────────────────────────────────
    // Quan hệ Giao dịch thanh toán (Lab 6)
    // ──────────────────────────────────────────────

    /** Một đơn hàng có nhiều lần giao dịch thanh toán */
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /** Giao dịch thanh toán mới nhất */
    public function latestPaymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }

    /** Kiểm tra đơn hàng đã được thanh toán trực tuyến thành công chưa */
    public function getIsPaidOnlineAttribute(): bool
    {
        if ($this->relationLoaded('paymentTransactions')) {
            return $this->paymentTransactions->contains(fn ($t) => $t->status === 'paid');
        }

        return $this->paymentTransactions()
            ->where('status', 'paid')
            ->exists();
    }

    /** Kiểm tra đơn MoMo đang chờ / thất bại (có thể thanh toán lại) */
    public function getCanPayAgainAttribute(): bool
    {
        return $this->payment_method === 'momo'
            && in_array($this->status, ['pending', 'processing'], true)
            && ! $this->is_paid_online;
    }

    // Accessor lấy tên người nhận
    public function getNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Khách hàng';
    }

    // Accessor lấy địa chỉ
    public function getAddressAttribute(): string
    {
        return $this->shipping_address ?? '';
    }

    /**
     * Accessor lấy thông tin chi tiết và huy hiệu hiển thị của trạng thái vận chuyển GHN.
     */
    public function getShippingBadgeAttribute(): array
    {
        return match ($this->shipping_status) {
            'ready_to_pick' => [
                'label' => 'Chờ lấy hàng (Chờ bàn giao)',
                'badge_class' => 'bg-warning text-dark',
                'icon' => 'fa-clock',
                'desc' => 'Đã tạo đơn trên GHN. Chờ bưu tá GHN qua lấy hoặc shop mang ra bưu cục.',
            ],
            'picking' => [
                'label' => 'Đang lấy hàng',
                'badge_class' => 'bg-info text-white',
                'icon' => 'fa-dolly',
                'desc' => 'Bưu tá GHN đang đến địa chỉ shop để nhận kiện hàng.',
            ],
            'picked', 'storing' => [
                'label' => 'Đã nhập kho GHN',
                'badge_class' => 'bg-primary text-white',
                'icon' => 'fa-warehouse',
                'desc' => 'Kiện hàng đã ở bưu cục / trung tâm khai thác GHN.',
            ],
            'delivering', 'transporting', 'sorting' => [
                'label' => 'Đang giao hàng',
                'badge_class' => 'bg-primary text-white',
                'icon' => 'fa-truck-fast',
                'desc' => 'Bưu tá GHN đang đi giao đến địa chỉ của người nhận.',
            ],
            'delivered' => [
                'label' => 'Đã giao thành công',
                'badge_class' => 'bg-success text-white',
                'icon' => 'fa-check-circle',
                'desc' => 'Khách hàng đã nhận kiện hàng thành công.',
            ],
            'delivery_fail' => [
                'label' => 'Giao hàng thất bại',
                'badge_class' => 'bg-danger text-white',
                'icon' => 'fa-circle-exclamation',
                'desc' => 'Không thể giao hàng tới người nhận.',
            ],
            'returning', 'return' => [
                'label' => 'Đang hoàn hàng',
                'badge_class' => 'bg-secondary text-white',
                'icon' => 'fa-rotate-left',
                'desc' => 'Kiện hàng đang trên đường chuyển hoàn lại cho shop.',
            ],
            'returned' => [
                'label' => 'Đã hoàn về shop',
                'badge_class' => 'bg-dark text-white',
                'icon' => 'fa-box-archive',
                'desc' => 'Shop đã nhận lại hàng hoàn.',
            ],
            'cancelled', 'cancel' => [
                'label' => 'Đã hủy vận đơn',
                'badge_class' => 'bg-secondary text-white',
                'icon' => 'fa-ban',
                'desc' => 'Vận đơn trên hệ thống GHN đã bị hủy.',
            ],
            default => [
                'label' => 'Chờ bàn giao',
                'badge_class' => 'bg-warning text-dark',
                'icon' => 'fa-box',
                'desc' => 'Chờ bàn giao cho đơn vị vận chuyển GHN.',
            ],
        };
    }
}
