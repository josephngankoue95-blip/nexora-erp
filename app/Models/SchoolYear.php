<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'starts_on',
        'ends_on',
        'is_active',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'is_active' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    /**
     * Entreprise propriétaire de l'année scolaire.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            'company_id'
        );
    }

    /**
     * Classes associées à cette année scolaire.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(
            SchoolClass::class,
            'school_year_id',
            'id'
        );
    }
}
