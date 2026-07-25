<?php
// app/Models/FacebookCampaign.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacebookCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'product_id',
        'fb_campaign_id',
        'fb_adset_id',
        'fb_ad_id',
        'name',
        'campaign_type',  // ← AJOUTER
        'daily_budget',
        'total_budget',
        'duration_days',
        'targeting',
        'audience_type',
        'status',
        'starts_at',
        'ends_at',
        'reach',
        'impressions',
        'clicks',
        'spent',
        'stats',
        'last_synced_at',
        'ctr',
        'cpc',
        'cpp',
        'landing_url',
        'post_message',
        'post_image',
    ];

    protected $casts = [
        'daily_budget' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'targeting' => 'array',
        'stats' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'paused' => 'yellow',
            'completed' => 'blue',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'pending' => 'En attente',
            'active' => 'Actif',
            'paused' => 'En pause',
            'completed' => 'Terminé',
            'rejected' => 'Rejeté',
            default => $this->status,
        };
    }
}
