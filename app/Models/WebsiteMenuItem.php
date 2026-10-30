<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteMenuItem extends Model
{
    protected $fillable = [
        'company_id',
        'label',
        'type',
        'website_page_id',
        'url',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'website_page_id' => 'integer',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Entreprise propriétaire du menu.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Page WebStudio associée au menu.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(
            WebsitePage::class,
            'website_page_id'
        );
    }
}
