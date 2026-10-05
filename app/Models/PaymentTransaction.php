<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_order_id',
        'transaction_id',
        'amount',
        'status',
        'result_code',
        'message',
        'request_payload',
        'response_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'result_code' => 'integer',
            'request_payload' => 'array',
            'response_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Quan hệ
    // ──────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ──────────────────────────────────────────────
    // Helpers / Scopes
    // ──────────────────────────────────────────────

    /** Giao dịch đã thanh toán thành công */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /** Giao dịch đang chờ xử lý */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /** Giao dịch thất bại */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /** Tên cổng thanh toán dạng hiển thị */
    public function getGatewayLabelAttribute(): string
    {
        return match ($this->gateway) {
            'momo' => 'Ví MoMo',
            'cod' => 'COD - Tiền mặt khi nhận hàng',
            default => strtoupper($this->gateway),
        };
    }

    /** Badge class Bootstrap theo trạng thái */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid' => ['class' => 'bg-success text-white', 'label' => 'Đã thanh toán', 'icon' => 'fa-circle-check'],
            'pending' => ['class' => 'bg-warning text-dark', 'label' => 'Chờ thanh toán', 'icon' => 'fa-clock'],
            'failed' => ['class' => 'bg-danger text-white', 'label' => 'Thất bại', 'icon' => 'fa-circle-xmark'],
            'cancelled' => ['class' => 'bg-secondary text-white', 'label' => 'Đã hủy', 'icon' => 'fa-ban'],
            default => ['class' => 'bg-light text-dark', 'label' => ucfirst($this->status), 'icon' => 'fa-question'],
        };
    }
}
