<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolLevel extends Model
{
    protected $fillable = [
        'company_id',
        'school_section_id',
        'name',
        'sort_order',
        'is_exam_class',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_exam_class' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(
            SchoolSection::class,
            'school_section_id'
        );
    }

    public function classes(): HasMany
    {
        return $this->hasMany(
            SchoolClass::class,
            'school_level_id'
        );
    }
}