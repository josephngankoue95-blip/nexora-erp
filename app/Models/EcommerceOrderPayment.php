<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceOrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'ecommerce_order_id',
        'reference',
        'amount',
        'method',
        'provider',
        'transaction_id',
        'status',
        'note',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            EcommerceOrder::class,
            'ecommerce_order_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }


    /*
    |--------------------------------------------------------------------------
    | LABELS
    |--------------------------------------------------------------------------
    */

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {

            'cash' => 'Espèces',

            'mobile_money' => 'Mobile Money',

            'card' => 'Carte bancaire',

            'bank_transfer' => 'Virement bancaire',

            'other' => 'Autre',

            default => ucfirst(
                str_replace('_', ' ', $this->method)
            ),
        };
    }

    public function getProviderLabelAttribute(): ?string
    {
        if (!$this->provider) {
            return null;
        }

        return match ($this->provider) {

            'orange_money' => 'Orange Money',

            'mtn_momo' => 'MTN MoMo',

            default => ucfirst(
                str_replace('_', ' ', $this->provider)
            ),
        };
    }
}