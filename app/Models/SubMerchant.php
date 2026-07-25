<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubMerchant extends Model
{
    protected $fillable = [
        'sub_merchant_id',
        'name',
        'commercial_name',
        'site',
        'is_active',
        'is_default',
        'data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'data' => 'array',
    ];
}
