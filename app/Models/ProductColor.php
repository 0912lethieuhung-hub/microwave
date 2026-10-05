<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;

    protected $table = 'product_colors';

    protected $fillable = [
        'product_id',
        'color',
        'stock',
        'price',
        'image_url',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Quan hệ tới sản phẩm cha
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Giá áp dụng thực tế của màu sắc: Nếu có giá riêng thì lấy giá riêng, ngược lại lấy giá gốc của sản phẩm
     */
    public function getEffectivePriceAttribute()
    {
        if ($this->price !== null && (float) $this->price > 0) {
            return (float) $this->price;
        }

        return $this->product ? (float) $this->product->price : 0;
    }
}
