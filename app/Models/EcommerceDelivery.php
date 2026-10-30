<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceDelivery extends Model
{
    protected $fillable = [
        'company_id', 'order_id', 'reference', 'recipient_name',
        'recipient_phone', 'recipient_email', 'address_line', 'city',
        'zone', 'delivery_fee', 'carrier_name', 'carrier_phone',
        'status', 'scheduled_at', 'shipped_at', 'delivered_at',
        'notes', 'created_by',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'scheduled_at' => 'date',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending' => 'En attente',
        'preparing' => 'En préparation',
        'shipped' => 'Expédiée',
        'delivered' => 'Livrée',
        'failed' => 'Échec',
        'cancelled' => 'Annulée',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
