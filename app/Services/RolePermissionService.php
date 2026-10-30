<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;

class RolePermissionService
{
    /**
     * Vérifie si un utilisateur possède une permission.
     */
    public function can(User $user, string $permission): bool
    {
        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRATEUR ENTREPRISE
        |--------------------------------------------------------------------------
        |
        | L'administrateur entreprise possède toutes les permissions.
        |
        */

        if ($user->isCompanyAdmin()) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | PERMISSION INDIVIDUELLE
        |--------------------------------------------------------------------------
        |
        | Une permission personnalisée est prioritaire sur celle du rôle.
        |
        */

        $customPermission = $user->userPermissions()
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->first();

        if ($customPermission) {
            return (bool) $customPermission->allowed;
        }

        /*
        |--------------------------------------------------------------------------
        | PERMISSION DU RÔLE
        |--------------------------------------------------------------------------
        */

        return RolePermission::query()
            ->where('role', $user->role)
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    /**
     * Retourne l'état d'une permission personnalisée.
     *
     * true  = ALLOW
     * false = DENY
     * null  = DEFAULT
     */
    public function state(
        User $user,
        Permission $permission
    ): ?bool {

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN / ADMINISTRATEUR ENTREPRISE
        |--------------------------------------------------------------------------
        */

        if (
            $user->isSuperAdmin() ||
            $user->isCompanyAdmin()
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | OVERRIDE UTILISATEUR
        |--------------------------------------------------------------------------
        */

        $customPermission = $user->userPermissions()
            ->where('permission_id', $permission->id)
            ->first();

        if ($customPermission) {
            return (bool) $customPermission->allowed;
        }

        /*
        |--------------------------------------------------------------------------
        | PERMISSION PAR DÉFAUT DU RÔLE
        |--------------------------------------------------------------------------
        */

        return null;
    }

    /**
     * Retourne la permission effective.
     */
    public function effective(
        User $user,
        Permission $permission
    ): bool {

        $state = $this->state(
            $user,
            $permission
        );

        /*
        |--------------------------------------------------------------------------
        | OVERRIDE PERSONNALISÉ
        |--------------------------------------------------------------------------
        */

        if ($state !== null) {
            return $state;
        }

        /*
        |--------------------------------------------------------------------------
        | PERMISSION DU RÔLE
        |--------------------------------------------------------------------------
        */

        return $this->can(
            $user,
            $permission->name
        );
    }

    /**
     * Retourne toutes les permissions effectives
     * d'un utilisateur.
     */
    public function forUser(User $user): array
    {
        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN / ADMINISTRATEUR ENTREPRISE
        |--------------------------------------------------------------------------
        */

        if (
            $user->isSuperAdmin() ||
            $user->isCompanyAdmin()
        ) {
            return $this->allPermissions();
        }

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS DU RÔLE
        |--------------------------------------------------------------------------
        */

        $rolePermissions = RolePermission::query()
            ->where('role', $user->role)
            ->with('permission')
            ->get()
            ->pluck('permission.name')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS PERSONNALISÉES
        |--------------------------------------------------------------------------
        */

        $customPermissions = $user->userPermissions()
            ->with('permission')
            ->get();

        foreach ($customPermissions as $custom) {

            $permissionName = $custom->permission->name;

            /*
            |----------------------------------------------------------------------
            | AUTORISER
            |----------------------------------------------------------------------
            */

            if ($custom->allowed) {

                if (!in_array(
                    $permissionName,
                    $rolePermissions,
                    true
                )) {
                    $rolePermissions[] = $permissionName;
                }

            /*
            |----------------------------------------------------------------------
            | REFUSER
            |----------------------------------------------------------------------
            */

            } else {

                $rolePermissions = array_values(
                    array_diff(
                        $rolePermissions,
                        [$permissionName]
                    )
                );
            }
        }

        return $rolePermissions;
    }

    /**
     * Toutes les permissions disponibles dans le système.
     */
    public function allPermissions(): array
    {
        return Permission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    /**
     * Permissions regroupées par module.
     */
    public function groupedPermissions()
    {
        return Permission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
    }

    /**
     * Permissions disponibles.
     */
    public function availablePermissions()
    {
        return Permission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get();
    }

    /**
     * Permissions d'un rôle.
     */
    public function permissionsForRole(string $role): array
    {
        return RolePermission::query()
            ->where('role', $role)
            ->with('permission')
            ->get()
            ->pluck('permission.name')
            ->toArray();
    }

    /**
     * Permissions de tous les rôles.
     */
    public function all(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | RÔLES AVEC TOUS LES DROITS
            |--------------------------------------------------------------------------
            */

            'super_admin' =>
                $this->allPermissions(),

            'companyadmin' =>
                $this->allPermissions(),

            /*
            |--------------------------------------------------------------------------
            | DIRECTEUR
            |--------------------------------------------------------------------------
            |
            | Les permissions viennent exclusivement de RolePermissionSeeder.
            | Le Directeur n'a donc pas automatiquement tous les droits.
            |
            */

            'director' =>
                $this->permissionsForRole('director'),

            /*
            |--------------------------------------------------------------------------
            | AUTRES RÔLES
            |--------------------------------------------------------------------------
            */

            'manager' =>
                $this->permissionsForRole('manager'),

            'seller' =>
                $this->permissionsForRole('seller'),

            'cashier' =>
                $this->permissionsForRole('cashier'),

            'stock_manager' =>
                $this->permissionsForRole('stock_manager'),

            'accountant' =>
                $this->permissionsForRole('accountant'),

            'employee' =>
                $this->permissionsForRole('employee'),
        ];
    }
}