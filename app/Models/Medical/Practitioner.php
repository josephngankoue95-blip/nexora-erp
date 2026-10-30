<?php

namespace App\Models\Medical;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Practitioner extends Model
{
    protected $table = 'medical_practitioners';

    protected $fillable = [
        'company_id',
        'user_id',
        'registration_number',
        'first_name',
        'last_name',
        'profession',
        'specialty',
        'phone',
        'email',
        'hire_date',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForCompany(
        Builder $query,
        int $companyId
    ): Builder {
        return $query->where('company_id', $companyId);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->last_name . ' ' . $this->first_name);
    }
}