<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Support\ModuleRegistry;

class ModulePermissionService
{
     /**
     * Vérifie si un module existe dans le registre.
     */
    public function moduleExists(string $module): bool
    {
        return ModuleRegistry::exists($module);
    }

    /**
     * Récupère les permissions déclarées pour un module.
     */
    public function permissions(string $module): array
    {
        return ModuleRegistry::permissions($module);
    }

    /**
     * Vérifie si une permission appartient à un module.
     */
    public function permissionBelongsToModule(
        string $module,
        string $permission
    ): bool {
        return in_array(
            $permission,
            $this->permissions($module),
            true
        );
    }

    /**
     * Retrouve le module parent qui déclare une permission.
     *
     * Exemple :
     * stock.view    => inventory
     * products.view => inventory
     * sales.view    => inventory
     */
    public function moduleForPermission(
        string $permission
    ): ?string {
        foreach (ModuleRegistry::all() as $slug => $module) {
            if (in_array(
                $permission,
                $module['permissions'] ?? [],
                true
            )) {
                return $slug;
            }
        }

        return null;
    }

    /**
     * Vérifie si un module est activé pour une entreprise.
     */
    public function moduleIsEnabled(
        ?Company $company,
        string $module
    ): bool {
        if (!$company) {
            return false;
        }

        return $company->hasModule($module);
    }

    /**
     * Vérifie l'accès complet à une fonctionnalité.
     *
     * 1. La permission doit être déclarée dans le registre.
     * 2. Le module parent doit être activé pour l'entreprise.
     * 3. L'utilisateur doit disposer de la permission.
     *
     * Le paramètre $module correspond au module extrait par le
     * middleware depuis la permission. Il peut être un module
     * interne comme "stock", même si le parent est "inventory".
     */
    public function can(
        User $user,
        string $module,
        string $permission
    ): bool {
        /*
        |--------------------------------------------------------------------------
        | Recherche du module parent
        |--------------------------------------------------------------------------
        */

        $parentModule = $this->moduleForPermission($permission);

        if (!$parentModule) {
            return false;
        }

        if (!$this->moduleExists($parentModule)) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification de la permission dans le registre
        |--------------------------------------------------------------------------
        */

        if (!$this->permissionBelongsToModule(
            $parentModule,
            $permission
        )) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Activation du module parent
        |--------------------------------------------------------------------------
        */

        if (!$this->moduleIsEnabled(
            $user->company,
            $parentModule
        )) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Permission effective de l'utilisateur
        |--------------------------------------------------------------------------
        */

        return $user->hasPermission($permission);
    }

    /**
     * Vérifie uniquement l'accès à un module.
     */
    public function canAccessModule(
        User $user,
        string $module
    ): bool {
        if (!$this->moduleExists($module)) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $this->moduleIsEnabled(
            $user->company,
            $module
        );
    }

    /**
     * Récupère les modules accessibles par un utilisateur.
     */
    public function accessibleModules(
        User $user
    ): array {
        if ($user->isSuperAdmin()) {
            return ModuleRegistry::sorted();
        }

        $company = $user->company;

        if (!$company) {
            return [];
        }

        $accessible = [];

        foreach (ModuleRegistry::sorted() as $slug => $module) {
            if ($this->moduleIsEnabled($company, $slug)) {
                $accessible[$slug] = $module;
            }
        }

        return $accessible;
    }
}