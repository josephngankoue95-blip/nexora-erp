<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'company_id',
        'client_id',
        'user_id',
        'reference',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Montant payé
    |--------------------------------------------------------------------------
    */

    public function getPaidAmountAttribute(): float
    {
        /*
        | Si les paiements sont déjà chargés, on utilise la collection
        | afin d'éviter une nouvelle requête SQL.
        */

        if ($this->relationLoaded('payments')) {
            return (float) $this->payments
                ->where('status', 'completed')
                ->sum('amount');
        }

        return (float) $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Reste à payer
    |--------------------------------------------------------------------------
    */

    public function getRemainingAmountAttribute(): float
    {
        $remaining = (float) $this->total - $this->paid_amount;

        return max(0, $remaining);
    }

    /*
    |--------------------------------------------------------------------------
    | Pourcentage payé
    |--------------------------------------------------------------------------
    */

    public function getPaidPercentageAttribute(): float
    {
        $total = (float) $this->total;

        if ($total <= 0) {
            return 0;
        }

        $percentage = (
            $this->paid_amount / $total
        ) * 100;

        return min(100, max(0, $percentage));
    }

    /*
    |--------------------------------------------------------------------------
    | Statut du paiement
    |--------------------------------------------------------------------------
    */

    public function getPaymentStatusAttribute(): string
    {
        $total = (float) $this->total;
        $paid = $this->paid_amount;

        if ($total <= 0) {
            return 'paid';
        }

        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $total) {
            return 'paid';
        }

        return 'partial';
    }

    /*
    |--------------------------------------------------------------------------
    | Libellé statut paiement
    |--------------------------------------------------------------------------
    */

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Payée',
            'partial' => 'Partiellement payée',
            'unpaid' => 'Impayée',
            default => 'Inconnue',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Vérifications pratiques
    |--------------------------------------------------------------------------
    */

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsPartiallyPaidAttribute(): bool
    {
        return $this->payment_status === 'partial';
    }

    public function getIsUnpaidAttribute(): bool
    {
        return $this->payment_status === 'unpaid';
    }
}