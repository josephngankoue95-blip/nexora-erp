<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyPaymentSetting extends Model
{
    protected $fillable = [

        'company_id',

        'orange_enabled',
        'orange_client_id',
        'orange_client_secret',
        'orange_merchant_id',

        'mtn_enabled',
        'mtn_api_user',
        'mtn_api_key',
        'mtn_subscription_key',

        'environment',
    ];

    protected $casts = [
        'orange_enabled' => 'boolean',
        'mtn_enabled' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}