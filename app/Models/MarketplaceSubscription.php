<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceSubscription extends Model
{
    protected $fillable = ['user_id', 'shop_id', 'plan', 'status', 'expires_at', 'metadata'];
    protected $casts = ['expires_at' => 'datetime', 'metadata' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function shop() { return $this->belongsTo(Shop::class); }

    public function isActive(): bool {
        return $this->status === 'active' && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
