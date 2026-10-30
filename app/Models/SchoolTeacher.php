<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolTeacher extends Model
{
    protected $table = 'school_teachers';

    protected $fillable = [
        'company_id',
        'user_id',
        'matricule',
        'first_name',
        'last_name',
        'email',
        'phone',
        'speciality',
        'qualification',
        'recruitment_date',
        'status',
    ];

    protected $casts = [
        'recruitment_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(
            SchoolTeacherAssignment::class,
            'teacher_id'
        );
    }
}
