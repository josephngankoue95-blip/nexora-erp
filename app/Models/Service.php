<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'reference',
        'description',
        'category',
        'price',
        'tax',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Entreprise propriétaire de la prestation.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Prix formaté.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format(
            (float) $this->price,
            0,
            ',',
            ' '
        ) . ' FCFA';
    }

    /**
     * Libellé du statut.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active
            ? 'Actif'
            : 'Inactif';
    }

    /**
     * Classe CSS du badge de statut.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active
            ? 'success'
            : 'secondary';
    }

    /**
     * Calcul du montant de la taxe.
     */
    public function getTaxAmountAttribute(): float
    {
        return (float) $this->price
            * ((float) $this->tax / 100);
    }

    /**
     * Prix TTC.
     */
    public function getPriceWithTaxAttribute(): float
    {
        return (float) $this->price
            + $this->tax_amount;
    }

    /**
     * Prix TTC formaté.
     */
    public function getFormattedPriceWithTaxAttribute(): string
    {
        return number_format(
            $this->price_with_tax,
            0,
            ',',
            ' '
        ) . ' FCFA';
    }
}