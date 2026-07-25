<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    protected $fillable = [
        'shop_id', 'customer_phone', 'customer_name',
        'cart_items', 'total', 'reminder_sent',
        'reminder_sent_at', 'recovered'
    ];

    protected $casts = [
        'cart_items' => 'array',
        'reminder_sent' => 'boolean',
        'recovered' => 'boolean',
        'reminder_sent_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
