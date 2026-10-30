<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyModule extends Model
{
    use HasFactory;

    protected $table = 'company_modules';

    protected $fillable = [
        'company_id',
        'module_id',
        'is_active',
        'activated_at',
        'deactivated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    /**
     * Entreprise.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class
        );
    }

    /**
     * Module.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(
            Module::class
        );
    }
}