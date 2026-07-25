<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',    // <-- AJOUTÉ ICI
        'plan',
        'entity_name',
        'status',
        'expires_at',
        'metadata'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec la boutique (pour récupérer facilement le badge)
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
