<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class SchoolStudent extends Model
{
    protected $fillable = [
        'company_id', 'student_number', 'last_name', 'first_name',
        'date_of_birth', 'place_of_birth', 'gender', 'guardian_name',
        'guardian_phone', 'guardian_email', 'address', 'photo',
    ];

    protected function casts(): array { return ['date_of_birth' => 'date']; }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function enrollments(): HasMany { return $this->hasMany(SchoolEnrollment::class); }
}
