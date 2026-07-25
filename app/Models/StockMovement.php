<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id', 'product_id', 'type', 'quantity', 'reason',
        'new_cost_price', 'new_sale_price'
    ];



    // 🔥 AJOUTER CETTE MÉTHODE ICI
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
