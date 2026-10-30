<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Collection;

class CompanyModuleService
{
    /**
     * Retourne les modules actifs d'une entreprise.
     */
    public function getActiveModules(?Company $company): Collection
    {
        if (!$company) {
            return collect();
        }

        return $company->activeModules()
            ->get();
    }

    /**
     * Retourne uniquement les slugs des modules actifs.
     */
    public function getActiveModuleSlugs(?Company $company): array
    {
        if (!$company) {
            return [];
        }

        return $company->activeModules()
            ->pluck('slug')
            ->toArray();
    }

    /**
     * Vérifie si un module est actif.
     */
    public function isEnabled(
        ?Company $company,
        string $slug
    ): bool {
        if (!$company) {
            return false;
        }

        return in_array(
            $slug,
            $this->getActiveModuleSlugs($company),
            true
        );
    }
}