<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'description',
        'price',
        'sale_price',
        'image',
        'gallery',
        'options',
        'is_available',
        'is_featured',
        'order',
        'sku',
        'stock',
        'stock_alert', // ← AJOUTER
        'supplier',    // ← AJOUTER
        'track_inventory',
        'cost_price',
        'facebook_post_id',  // ← AJOUTER ICI
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'gallery' => 'array',
        'options' => 'array',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'stock' => 'integer',
        'stock_alert' => 'integer', // ← AJOUTER
        'track_inventory' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery) return [];
        return array_map(function($image) {
            return asset('storage/' . $image);
        }, $this->gallery);
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function hasDiscount()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->hasDiscount()) return 0;
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
