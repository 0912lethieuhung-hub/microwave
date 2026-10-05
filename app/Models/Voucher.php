<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'is_active',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'discount_value' => 'float',
            'min_order_amount' => 'float',
            'max_discount' => 'float',
            'expires_at' => 'date',
        ];
    }

    // Kiểm tra voucher có hợp lệ với số tiền đơn hàng không
    public function isValidForAmount($amount): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'Mã giảm giá này hiện đang tạm khóa.'];
        }

        if ($this->expires_at && Carbon::now()->startOfDay()->gt($this->expires_at)) {
            return ['valid' => false, 'message' => 'Mã giảm giá này đã hết hạn sử dụng.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Mã giảm giá này đã hết lượt sử dụng.'];
        }

        if ($this->min_order_amount > 0 && $amount < $this->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu '.number_format($this->min_order_amount, 0, ',', '.').'₫ để áp dụng mã này.',
            ];
        }

        return ['valid' => true, 'message' => 'Mã hợp lệ'];
    }

    // Tính toán số tiền được giảm
    public function calculateDiscount($amount): float
    {
        if ($this->discount_type === 'percent') {
            $discount = ($amount * $this->discount_value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }

            return (float) $discount;
        }

        return (float) min($amount, $this->discount_value);
    }
}
