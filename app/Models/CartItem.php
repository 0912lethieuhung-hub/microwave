<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_id', 'quantity', 'color'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function colorVariant()
    {
        return $this->hasOne(ProductColor::class, 'product_id', 'product_id')
            ->where('color', $this->color);
    }

    /**
     * Lấy đơn giá áp dụng theo màu sắc (nếu có giá riêng) hoặc giá gốc của sản phẩm
     */
    public function getPriceAttribute(): float
    {
        if (! $this->product) {
            return 0;
        }

        if ($this->color) {
            return (float) $this->product->getColorPrice($this->color);
        }

        return (float) $this->product->price;
    }

    /**
     * Thành tiền cho item này
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
