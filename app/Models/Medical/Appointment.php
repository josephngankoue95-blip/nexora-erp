<?php

namespace App\Models\Medical;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $table = 'medical_appointments';

    protected $fillable = [
        'company_id',
        'patient_id',
        'practitioner_id',
        'scheduled_at',
        'duration_minutes',
        'reason',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }

    public function scopeForCompany(
        Builder $query,
        int $companyId
    ): Builder {
        return $query->where('company_id', $companyId);
    }
}