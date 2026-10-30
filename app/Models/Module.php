<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Entreprises possédant ce module.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(
            Company::class,
            'company_modules'
        )
            ->withPivot([
                'is_active',
                'activated_at',
                'deactivated_at',
            ])
            ->withTimestamps();
    }
}