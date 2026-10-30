<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_days',
        'max_users',
        'max_products',
        'features',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'max_users' => 'integer',
        'max_products' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function subscriptions(): HasMany
    {
        return $this->hasMany(
            Subscription::class,
            'plan_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mutateurs
    |--------------------------------------------------------------------------
    */

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;

        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
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

    public function getDurationLabelAttribute(): string
    {
        return match (true) {
            $this->duration_days === 1 => '1 jour',
            $this->duration_days === 7 => '7 jours',
            $this->duration_days === 30 => '1 mois',
            $this->duration_days === 90 => '3 mois',
            $this->duration_days === 180 => '6 mois',
            $this->duration_days === 365 => '1 an',
            default => $this->duration_days . ' jours',
        };
    }

    public function getActiveSubscriptionsCountAttribute(): int
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->whereDate(
                'ends_at',
                '>=',
                now()->toDateString()
            )
            ->count();
    }

    public function getUsersLimitLabelAttribute(): string
    {
        return $this->max_users === null
            ? 'Illimité'
            : $this->max_users . ' utilisateurs';
    }

    public function getProductsLimitLabelAttribute(): string
    {
        return $this->max_products === null
            ? 'Illimité'
            : number_format($this->max_products, 0, ',', ' ')
                . ' produits';
    }
}