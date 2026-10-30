<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;

class Company extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Utilisateurs appartenant à l'entreprise.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function paymentSettings(): HasOne
{
    return $this->hasOne(CompanyPaymentSetting::class);
}

public function subscriptions(): HasMany
{
    return $this->hasMany(Subscription::class);
}

public function services(): HasMany
{
    return $this->hasMany(Service::class);
}

/**
 * Modules de l'entreprise.
 */
public function modules(): BelongsToMany
{
    return $this->belongsToMany(
        Module::class,
        'company_modules'
    )
        ->withPivot([
            'is_active',
            'activated_at',
            'deactivated_at',
        ])
        ->withTimestamps();
}


/**
 * Modules actuellement actifs pour l'entreprise.
 */
public function activeModules(): BelongsToMany
{
    return $this->modules()
        ->where('modules.is_active', true)
        ->wherePivot('is_active', true)
        ->orderBy('modules.sort_order')
        ->orderBy('modules.name');
}


/**
 * Vérifie si un module est actif pour l'entreprise.
 */
public function hasModule(string $slug): bool
{
    return $this->activeModules()
        ->where('modules.slug', $slug)
        ->exists();
}

public function schoolSetting(): HasOne
{
    return $this->hasOne(SchoolSetting::class);
}

}