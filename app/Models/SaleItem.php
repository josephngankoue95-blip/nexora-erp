<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'type',
        'product_id',
        'service_id',
        'quantity',
        'unit_price',
        'discount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getIsProductAttribute(): bool
    {
        return $this->type === 'product';
    }

    public function getIsServiceAttribute(): bool
    {
        return $this->type === 'service';
    }

    public function getItemNameAttribute(): string
    {
        if ($this->type === 'service') {
            return $this->service?->name ?? 'Prestation supprimée';
        }

        return $this->product?->name ?? 'Produit supprimé';
    }

    public function getItemReferenceAttribute(): ?string
    {
        if ($this->type === 'service') {
            return $this->service?->reference;
        }

        return $this->product?->reference;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'product' => 'Produit',
            'service' => 'Prestation',
            default => 'Article',
        };
    }
}