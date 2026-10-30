<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SchoolSetting extends Model
{
    protected $fillable = [
        'company_id',
        'school_name',
        'school_code',
        'school_type',
        'authorization_number',
        'motto',
        'logo',
        'stamp',
        'country',
        'region',
        'city',
        'address',
        'phone',
        'email',
        'website',
        'principal_name',
        'director_name',
        'academic_system',
        'grading_scale',
        'bulletin_footer',
    ];

    protected $casts = [
        'grading_scale' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
