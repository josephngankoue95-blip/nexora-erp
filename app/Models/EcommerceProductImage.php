<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceProductImage extends Model
{
    protected $fillable = [
        'company_id',
        'ecommerce_product_id',
        'image_path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function ecommerceProduct(): BelongsTo
    {
        return $this->belongsTo(
            EcommerceProduct::class,
            'ecommerce_product_id'
        );
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
