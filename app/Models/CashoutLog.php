<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashoutLog extends Model
{
    protected $fillable = [
        'shop_id',
        'admin_id',
        'site',
        'service_code',
        'phone',
        'amount',
        'external_id',
        'status',
        'response',
        'callback_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getServiceLabelAttribute()
    {
        $labels = [
            'OM_SN_CASHIN' => 'Orange Money',
            'WAVE_SN_CASHIN' => 'Wave',
            'FM_SN_CASHIN' => 'Free Money',
            'WIZALL_SN_CASHIN' => 'Wizall',
        ];
        return $labels[$this->service_code] ?? $this->service_code;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'initiated' => 'bg-yellow-100 text-yellow-800',
            'success' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}
