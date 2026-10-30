<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'company_id',
        'logo_path',
        'hero_path',
        'primary_color',
        'secondary_color',
        'hero_title',
        'hero_subtitle',
        'footer_text',
        'theme_key',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

    /**
     * Entreprise propriétaire des paramètres.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * URL publique du logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return filled($this->logo_path)
            ? Storage::disk('public')->url($this->logo_path)
            : null;
    }

    /**
     * URL publique de la bannière.
     */
    public function getHeroUrlAttribute(): ?string
    {
        return filled($this->hero_path)
            ? Storage::disk('public')->url($this->hero_path)
            : null;
    }
}