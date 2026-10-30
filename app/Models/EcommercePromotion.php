<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcommercePromotion extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'type',
        'value',
        'minimum_order_amount',
        'usage_limit',
        'usage_count',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isCurrentlyValid(): bool
    {
        $now = now();

        return $this->is_active
            && (
                !$this->starts_at
                || $this->starts_at->lte($now)
            )
            && (
                !$this->ends_at
                || $this->ends_at->gte($now)
            )
            && (
                !$this->usage_limit
                || $this->usage_count < $this->usage_limit
            );
    }
}
