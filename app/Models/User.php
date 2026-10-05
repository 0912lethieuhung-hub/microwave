<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'birthday',
        'gender',
        'bio',
        'role',
        'is_admin',
        'google_id',
        'facebook_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Relationship with Orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * Relationship with Cart.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Relationship with Reviews.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->latest();
    }

    /**
     * Relationship with Wishlist.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class)->latest();
    }

    /**
     * Accessor for user avatar URL with graceful fallback.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            if (file_exists(public_path($this->avatar))) {
                return asset($this->avatar);
            }
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0071af&color=ffffff&size=200&bold=true';
    }

    /**
     * Accessor for role display name.
     */
    public function getRoleNameAttribute(): string
    {
        if ($this->is_admin || $this->role === 'admin') {
            return 'Quản trị viên';
        }

        return 'Khách hàng thân thiết';
    }

    /**
     * Calculate total money spent on successful/active orders.
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->orders()->where('status', '!=', 'cancelled')->sum('total_price');
    }
}
