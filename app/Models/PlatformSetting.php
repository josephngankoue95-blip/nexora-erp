<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $table = 'platform_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Récupérer un paramètre de la plateforme.
     */
    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Créer ou mettre à jour un paramètre.
     */
    public static function set(
        string $key,
        mixed $value
    ): self {
        return static::updateOrCreate(
            [
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }

    /**
     * Supprimer un paramètre.
     */
    public static function remove(string $key): bool
    {
        return static::where('key', $key)->delete() > 0;
    }
}