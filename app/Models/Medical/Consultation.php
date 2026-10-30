<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    protected $table = 'medical_consultations';

    protected $fillable = [
        'company_id',
        'patient_id',
        'appointment_id',
        'practitioner_id',
        'consultation_date',
        'reason',
        'symptoms',
        'clinical_observations',
        'diagnosis',
        'treatment_plan',
        'status',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function scopeForCompany(
        Builder $query,
        int $companyId
    ): Builder {
        return $query->where('company_id', $companyId);
    }
}