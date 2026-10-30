<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceStoreSetting extends Model
{
    protected $table = 'ecommerce_store_settings';

    protected $fillable = [
        'company_id',
        'store_name',
        'support_email',
        'support_phone',
        'description',
        'currency',
        'free_shipping_threshold',
        'allow_guest_checkout',
        'reviews_enabled',
        'is_active',
    ];

    protected $casts = [
        'free_shipping_threshold' => 'decimal:2',
        'allow_guest_checkout' => 'boolean',
        'reviews_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'entreprise.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}