<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'description',
        'price',
        'original_price',
        'stock',
        'color',
        'capacity',
        'power',
        'image_url',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class)->orderBy('id', 'asc');
    }

    /**
     * Accessor alias for image_url to ensure compatibility with views using $product->image
     */
    public function getImageAttribute(): ?string
    {
        return $this->image_url;
    }

    public function getColorStock(?string $colorName): int
    {
        if (! $colorName) {
            return max(0, (int) $this->stock);
        }

        $colors = $this->relationLoaded('colors') ? $this->colors : $this->colors()->get();

        if ($colors->isEmpty()) {
            return max(0, (int) $this->stock);
        }

        $colorVariant = $colors->firstWhere('color', $colorName);

        return $colorVariant ? max(0, (int) $colorVariant->stock) : 0;
    }

    public function getColorPrice(?string $colorName): float
    {
        if (! $colorName) {
            return (float) $this->price;
        }

        $colors = $this->relationLoaded('colors') ? $this->colors : $this->colors()->get();

        if ($colors->isEmpty()) {
            return (float) $this->price;
        }

        $colorVariant = $colors->firstWhere('color', $colorName);

        return $colorVariant ? (float) $colorVariant->effective_price : (float) $this->price;
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true)->latest();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isWishlistedBy($userId = null): bool
    {
        $uid = $userId ?: auth()->id();
        if (! $uid) {
            return false;
        }
        if ($this->relationLoaded('wishlists')) {
            return $this->wishlists->contains('user_id', $uid);
        }

        return $this->wishlists()->where('user_id', $uid)->exists();
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        if ($avg) {
            return round((float) $avg, 1);
        }

        // Fallback seeded rating
        return round(4.7 + (($this->id % 4) * 0.1), 1);
    }

    public function getReviewsCountAttribute(): int
    {
        $count = $this->reviews()->count();
        if ($count > 0) {
            return $count;
        }

        // Fallback seeded count
        return (($this->id * 19 + 31) % 95) + 32;
    }
}
