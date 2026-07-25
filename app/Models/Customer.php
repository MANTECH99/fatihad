<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'shop_id', 'name', 'phone', 'email', 'tag',
        'total_orders', 'total_spent', 'last_order_at'
    ];

    protected $casts = [
        'total_orders' => 'integer',
        'total_spent' => 'decimal:2',
        'last_order_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
