<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'company_id',
        'subscription_id',
        'reference',
        'amount',
        'method',
        'provider',
        'transaction_reference',
        'phone',
        'status',
        'paid_at',
        'notes',
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
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /*
    |--------------------------------------------------------------------------
    | LABEL MÉTHODE
    |--------------------------------------------------------------------------
    */

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Virement bancaire',
            'card' => 'Carte bancaire',
            'other' => 'Autre',
            default => 'Inconnue',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | LABEL STATUT
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'completed' => 'Terminé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            default => 'Inconnu',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | STATUTS
    |--------------------------------------------------------------------------
    */

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }


}