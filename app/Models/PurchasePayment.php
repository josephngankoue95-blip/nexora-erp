<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePayment extends Model
{
    protected $fillable = [
        'company_id',
        'purchase_id',
        'user_id',
        'amount',
        'method',
        'provider',
        'transaction_reference',
        'phone',
        'note',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Entreprise
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Achat
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Utilisateur ayant enregistré le paiement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Libellé de la méthode de paiement
     */
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'card' => 'Carte bancaire',
            'bank_transfer' => 'Virement bancaire',
            'other' => 'Autre',
            default => 'Inconnu',
        };
    }

    /**
     * Libellé du fournisseur Mobile Money
     */
    public function getProviderLabelAttribute(): ?string
    {
        return match ($this->provider) {
            'orange_money' => 'Orange Money',
            'mtn_momo' => 'MTN Mobile Money',
            default => null,
        };
    }

    /**
     * Libellé du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Terminé',
            'pending' => 'En attente',
            'cancelled' => 'Annulé',
            default => 'Inconnu',
        };
    }
}