<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->cartItems->sum(function ($item) {
            return $item->total_price;
        });
    }

    public function getItemsCountAttribute(): int
    {
        return (int) $this->cartItems->sum('quantity');
    }
}
