<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    protected $fillable = [
        'company_id',
        'sale_id',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'completed' => 'Terminé',

            'pending' => 'En attente',

            'cancelled' => 'Annulé',

            default => 'Inconnu',
        };
    }

    public function getProviderLabelAttribute(): ?string
{
    return match ($this->provider) {

        'orange_money' => 'Orange Money',

        'mtn_momo' => 'MTN Mobile Money',

        default => null,
    };
}
}