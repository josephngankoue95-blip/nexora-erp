<?php

namespace App\Models;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'tax_number',
        'notes',
        'is_active',
    ];

    protected $casts = [
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
     * Ventes du client
     *
     * Cette relation sera utilisée lorsque
     * le module Ventes sera créé.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function ecommerceOrders(): HasMany
{
    return $this->hasMany(
        EcommerceOrder::class,
        'client_id'
    );
}

}