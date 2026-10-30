<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolTeacherAssignment extends Model
{
    protected $table = 'school_teacher_assignments';

    protected $fillable = [
        'company_id',
        'teacher_id',
        'subject_id',
        'class_id',
        'school_year_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(SchoolTeacher::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }
}