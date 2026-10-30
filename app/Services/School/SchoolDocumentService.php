<?php

namespace App\Services\School;

use App\Models\Company;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Storage;

class SchoolDocumentService
{
    public function data(int $companyId): array
    {
        $company = Company::query()->findOrFail($companyId);

        $settings = SchoolSetting::query()
            ->where('company_id', $companyId)
            ->first();

        $filePath = static function (?string $path): ?string {
            return $path && Storage::disk('public')->exists($path)
                ? Storage::disk('public')->path($path)
                : null;
        };

        return [
            'name' => $settings?->school_name ?: $company->name,
            'motto' => $settings?->motto,
            'city' => $settings?->city,
            'region' => $settings?->region,
            'phone' => $settings?->phone,
            'email' => $settings?->email,
            'principal' => $settings?->principal_name,
            'director' => $settings?->director_name,
            'footer' => $settings?->bulletin_footer,
            'logo_path' => $filePath($settings?->logo),
            'stamp_path' => $filePath($settings?->stamp),
        ];
    }
}