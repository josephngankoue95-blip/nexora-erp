<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Entreprise propriétaire de la catégorie.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Produits appartenant à cette catégorie.
     *
     * Cette relation sera utilisée lorsque nous créerons
     * le module Produits.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Générer automatiquement un slug.
     */
    protected static function booted(): void
    {
        static::creating(function (Category $category) {

            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (Category $category) {

            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

}