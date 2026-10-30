<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id',
        'name',
        'price',
        'duration_days',
        'starts_at',
        'ends_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class
        );
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionPlan::class,
            'plan_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active'
            && $this->ends_at
            && $this->ends_at->isFuture();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'expired'
            || (
                $this->ends_at
                && $this->ends_at->isPast()
            );
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Actif',
            'expired' => 'Expiré',
            'cancelled' => 'Annulé',
            default => 'Inconnu',
        };
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->ends_at) {
            return 0;
        }

        if ($this->ends_at->isPast()) {
            return 0;
        }

        return now()
            ->startOfDay()
            ->diffInDays(
                $this->ends_at->startOfDay()
            );
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format(
            (float) $this->price,
            0,
            ',',
            ' '
        ) . ' FCFA';
    }

    public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}

}