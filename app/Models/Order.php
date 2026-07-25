<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_note',
        'subtotal',
        'delivery_fee',
        'payment_fee', // ← ajouter
        'tax',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'payment_metadata',
        'order_status',
        'whatsapp_notification_sent',
        'whatsapp_notified_at',
        'delivery_person',
        'delivery_person_phone',
        'status_history',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_metadata' => 'array',
        'status_history' => 'array',
        'whatsapp_notification_sent' => 'boolean',
        'whatsapp_notified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $prefix = 'CMD';
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $order->order_number = $prefix . '-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }

            if (empty($order->status_history)) {
                $order->status_history = [
                    [
                        'status' => 'pending',
                        'timestamp' => now()->toDateTimeString(),
                        'note' => 'Commande créée'
                    ]
                ];
            }
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function updateStatus($newStatus, $note = null)
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status' => $newStatus,
            'timestamp' => now()->toDateTimeString(),
            'note' => $note ?? $this->getStatusLabel($newStatus)
        ];

        $this->update([
            'order_status' => $newStatus,
            'status_history' => $history
        ]);
    }

    public function getStatusLabel($status = null)
    {
        $status = $status ?? $this->order_status;
        $labels = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'preparing' => 'En préparation',
            'ready' => 'Prête',
            'out_for_delivery' => 'En livraison',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            'rejected' => 'Rejetée',
        ];

        return $labels[$status] ?? $status;
    }

    public function getStatusColor($status = null)
    {
        $status = $status ?? $this->order_status;
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'preparing' => 'primary',
            'ready' => 'success',
            'out_for_delivery' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'rejected' => 'danger',
        ];

        return $colors[$status] ?? 'secondary';
    }

    public function getPaymentMethodLabel()
    {
        $labels = [
            'cash_on_delivery' => 'Paiement à la livraison',
            'wave' => 'Wave',
            'orange_money' => 'Orange Money',
            'card' => 'Carte bancaire',
            'other' => 'Autre',
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }
}
