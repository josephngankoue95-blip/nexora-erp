<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcommerceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'client_id',
        'reference',
        'status',

        'customer_name',
        'customer_email',
        'customer_phone',

        'shipping_address',
        'shipping_city',
        'shipping_region',
        'shipping_country',

        'subtotal',
        'discount',
        'shipping_amount',
        'tax',
        'total',

        'payment_method',
        'payment_status',

        'customer_note',
        'internal_note',

        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',

        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            Client::class
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            EcommerceOrderItem::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STATUTS
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPreparing(): bool
    {
        return $this->status === 'preparing';
    }

    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /*
    |--------------------------------------------------------------------------
    | PAIEMENT
    |--------------------------------------------------------------------------
    */

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === 'partial';
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPaymentFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    public function payments(): HasMany
{
    return $this->hasMany(
        EcommerceOrderPayment::class,
        'ecommerce_order_id'
    )->latest();
}
public function paidAmount(): float
{
    return (float) $this->payments()
        ->where('status', 'completed')
        ->sum('amount');
}

public function remainingAmount(): float
{
    return max(
        0,
        (float) $this->total - $this->paidAmount()
    );
}

}