<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'product_id',
        'sale_price',
        'is_published',
        'is_featured',
        'published_at',
        'slug',
        'seo_title',
        'seo_description',
        'short_description',
        'description',
        'sort_order',
        'ecommerce_category_id',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    /**
     * Entreprise propriétaire du produit e-commerce.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Produit associé dans l'ERP.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope des produits publiés.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope des produits vedettes publiés.
     */
    public function scopeFeatured($query)
    {
        return $query
            ->where('is_published', true)
            ->where('is_featured', true);
    }

    
public function category()
{
    return $this->belongsTo(
        \App\Models\EcommerceCategory::class,
        'ecommerce_category_id'
    );
}


public function collections()
{
    return $this->belongsToMany(
        \App\Models\EcommerceCollection::class,
        'ecommerce_collection_product',
        'ecommerce_product_id',
        'ecommerce_collection_id'
    )
    ->withPivot('sort_order')
    ->withTimestamps();
}


/**
 * Toutes les images du produit.
 */
public function images(): HasMany
{
    return $this->hasMany(
        EcommerceProductImage::class,
        'ecommerce_product_id'
    )->orderBy('sort_order')->orderBy('id');
}

/**
 * Image principale du produit.
 */
public function primaryImage()
{
    return $this->hasOne(
        EcommerceProductImage::class,
        'ecommerce_product_id'
    )->where('is_primary', true);
}


}