<?php

namespace App\Models\Medical;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    protected $table = 'medical_patients';

    protected $fillable = [
        'company_id',
        'patient_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'city',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany(
        Builder $query,
        int $companyId
    ): Builder {
        return $query->where('company_id', $companyId);
    }

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->last_name . ' ' . $this->first_name
        );
    }
}