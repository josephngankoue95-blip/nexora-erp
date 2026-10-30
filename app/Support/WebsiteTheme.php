<?php

namespace App\Support;

class WebsiteTheme
{
    public static function all(): array
    {
        return [
            'immobilier' => [
                'primary' => '#b68b46',
                'secondary' => '#17243b',
                'surface' => '#faf7f1',
            ],
            'ecole' => [
                'primary' => '#2563eb',
                'secondary' => '#142b55',
                'surface' => '#eff6ff',
            ],
            'services' => [
                'primary' => '#0d9488',
                'secondary' => '#123b40',
                'surface' => '#f0fdfa',
            ],
            'commerce' => [
                'primary' => '#ea580c',
                'secondary' => '#292524',
                'surface' => '#fff7ed',
            ],
            'stock' => [
                'primary' => '#2563eb',
                'secondary' => '#1e293b',
                'surface' => '#eff6ff',
            ],
            'hotel' => [
                'primary' => '#a16207',
                'secondary' => '#292524',
                'surface' => '#fefce8',
            ],
            'restaurant' => [
                'primary' => '#b91c1c',
                'secondary' => '#450a0a',
                'surface' => '#fef2f2',
            ],
            'clinique' => [
                'primary' => '#0891b2',
                'secondary' => '#164e63',
                'surface' => '#ecfeff',
            ],
            'hopital' => [
                'primary' => '#2563eb',
                'secondary' => '#1e3a8a',
                'surface' => '#eff6ff',
            ],
            'pharmacie' => [
                'primary' => '#16a34a',
                'secondary' => '#14532d',
                'surface' => '#f0fdf4',
            ],
            'location-vehicules' => [
                'primary' => '#f59e0b',
                'secondary' => '#111827',
                'surface' => '#fffbeb',
            ],
            'ressources-humaines' => [
                'primary' => '#7c3aed',
                'secondary' => '#312e81',
                'surface' => '#f5f3ff',
            ],
            'projets' => [
                'primary' => '#4f46e5',
                'secondary' => '#1e1b4b',
                'surface' => '#eef2ff',
            ],
            'maintenance' => [
                'primary' => '#ea580c',
                'secondary' => '#1f2937',
                'surface' => '#fff7ed',
            ],
            'documents' => [
                'primary' => '#0284c7',
                'secondary' => '#0c4a6e',
                'surface' => '#f0f9ff',
            ],
            'site-web' => [
                'primary' => '#6366f1',
                'secondary' => '#1e1b4b',
                'surface' => '#eef2ff',
            ],
            'elearning' => [
                'primary' => '#7c3aed',
                'secondary' => '#2e1065',
                'surface' => '#faf5ff',
            ],
        ];
    }

    public static function find(?string $key): array
    {
        return self::all()[$key ?? 'services']
            ?? self::all()['services'];
    }
}
