<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EcommerceCollection extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'image',
        'seo_title',
        'seo_description',
        'sort_order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            EcommerceProduct::class,
            'ecommerce_collection_product',
            'ecommerce_collection_id',
            'ecommerce_product_id'
        )
        ->withPivot('sort_order')
        ->withTimestamps();
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}