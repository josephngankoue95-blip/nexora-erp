<?php

namespace App\Models;

use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'sku',
        'barcode',
        'description',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Entreprise propriétaire
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Catégorie du produit
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Vérifie si le stock est faible
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock;
    }

    /**
     * Vérifie si le produit est en rupture
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function stockMovements(): HasMany
{
    return $this->hasMany(StockMovement::class);
}

public function saleItems(): HasMany
{
    return $this->hasMany(SaleItem::class);
}

public function ecommerce(): HasOne
{
    return $this->hasOne(
        EcommerceProduct::class
    );
}

}