<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'cover_image',
        'whatsapp_phone',
        'contact_phone',
        'contact_email',
        'address',
        'city',
        'delivery_zones',
        'delivery_fee',
        'min_order',
        'currency',
        'opening_hours',
        'is_open',
        'is_active',
        'status',
        'wave_number',
        'orange_money_number',
        'payout_method',
        'stamp',
        'facebook_page_id',
        'facebook_access_token',
        'facebook_page_name',
        'facebook_connected_at',
        'facebook_ad_account_id',  // ← AJOUTER
        'facebook_catalog_id',
        'facebook_pixel_id',  // ← AJOUTER
        'facebook_product_set_id',
        'facebook_capi_token',
    ];

    protected $casts = [
        'delivery_zones' => 'array',
        'opening_hours' => 'array',
        'is_open' => 'boolean',
        'is_active' => 'boolean',
        'delivery_fee' => 'decimal:2',
        'min_order' => 'decimal:2',
        'facebook_connected_at' => 'datetime',
        'facebook_pixel_id' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shop) {
            if (empty($shop->slug)) {
                $shop->slug = Str::slug($shop->name) . '-' . Str::random(6);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class)->orderBy('order');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function getStampUrlAttribute()
    {
        return $this->stamp ? asset('storage/' . $this->stamp) : null;
    }

    // Relation avec l'abonnement marketplace
    public function marketplaceSubscription()
    {
        return $this->hasOne(MarketplaceSubscription::class);
    }

    // Ajouter ces méthodes :
    public function hasFacebookConnected()
    {
        return !empty($this->facebook_access_token) && !empty($this->facebook_page_id);
    }

    public function getFacebookPageUrlAttribute()
    {
        return $this->facebook_page_id
            ? "https://facebook.com/{$this->facebook_page_id}"
            : null;
    }
}
