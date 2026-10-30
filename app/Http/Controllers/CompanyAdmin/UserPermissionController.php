<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\ModulePermissionService;
use App\Support\ModuleRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserPermissionController extends Controller
{
    /**
     * Modifier les permissions d'un utilisateur.
     */
    public function edit(User $user): View
    {
        $currentUser = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Même entreprise
        |--------------------------------------------------------------------------
        */

        if (
            $user->company_id !== $currentUser->company_id
        ) {
            abort(
                403,
                'Cet utilisateur n’appartient pas à votre entreprise.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Protection Super Admin
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {
            abort(
                403,
                'Les permissions du Super Administrateur ne peuvent pas être modifiées.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Service des modules
        |--------------------------------------------------------------------------
        */

        $modulePermissionService = app(
            ModulePermissionService::class
        );


        /*
        |--------------------------------------------------------------------------
        | Modules accessibles à l'entreprise
        |--------------------------------------------------------------------------
        */

        $accessibleModules = $modulePermissionService
            ->accessibleModules($currentUser);


        /*
        |--------------------------------------------------------------------------
        | Permissions disponibles
        |
        | On utilise ModuleRegistry comme source de vérité.
        |--------------------------------------------------------------------------
        */

        $registryPermissions = [];

        foreach ($accessibleModules as $moduleSlug => $module) {

            $modulePermissions = ModuleRegistry::permissions(
                $moduleSlug
            );

            if (empty($modulePermissions)) {
                continue;
            }

            $registryPermissions[$moduleSlug] = $modulePermissions;
        }


        /*
        |--------------------------------------------------------------------------
        | Récupération des permissions présentes en base
        |--------------------------------------------------------------------------
        */

        $permissionNames = collect($registryPermissions)
            ->flatten()
            ->unique()
            ->values();


        $permissionsFromDatabase = Permission::query()
            ->whereIn('name', $permissionNames)
            ->get()
            ->keyBy('name');


        /*
        |--------------------------------------------------------------------------
        | Construction des permissions pour la vue
        |
        | On conserve la structure :
        |
        | [
        |     'products' => Collection,
        |     'stock'    => Collection,
        |     ...
        | ]
        |--------------------------------------------------------------------------
        */

        $permissions = collect();

        foreach (
            $registryPermissions
            as $moduleSlug => $modulePermissionNames
        ) {

            $modulePermissions = collect();

            foreach (
                $modulePermissionNames
                as $permissionName
            ) {

                $permission = $permissionsFromDatabase
                    ->get($permissionName);

                if (!$permission) {
                    continue;
                }

                $modulePermissions->push($permission);
            }

            if ($modulePermissions->isNotEmpty()) {

                $permissions->put(
                    $moduleSlug,
                    $modulePermissions
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Permissions personnalisées de l'utilisateur
        |--------------------------------------------------------------------------
        */

        $userPermissions = $user->userPermissions()
            ->get()
            ->keyBy('permission_id');


        /*
        |--------------------------------------------------------------------------
        | Permissions disponibles pour l'affichage
        |--------------------------------------------------------------------------
        */

        return view(
            'company.users.permissions',
            compact(
                'user',
                'permissions',
                'userPermissions',
                'accessibleModules'
            )
        );
    }


    /**
     * Enregistrer les permissions.
     */
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {

        $currentUser = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | Même entreprise
        |--------------------------------------------------------------------------
        */

        if (
            $user->company_id !== $currentUser->company_id
        ) {
            abort(
                403,
                'Cet utilisateur n’appartient pas à votre entreprise.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Protection Super Admin
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {
            abort(
                403,
                'Les permissions du Super Administrateur ne peuvent pas être modifiées.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*.id' => [
                'required',
                'integer',
                'exists:permissions,id',
            ],

            'permissions.*.allowed' => [
                'required',
                'boolean',
            ],

        ]);


        $submittedPermissions =
            $validated['permissions'] ?? [];


        /*
        |--------------------------------------------------------------------------
        | Modules accessibles
        |--------------------------------------------------------------------------
        */

        $modulePermissionService = app(
            ModulePermissionService::class
        );

        $accessibleModules = $modulePermissionService
            ->accessibleModules($currentUser);


        /*
        |--------------------------------------------------------------------------
        | Construire la liste blanche des permissions autorisées
        |
        | Une permission doit :
        |
        | 1. exister dans ModuleRegistry
        | 2. appartenir à un module actif
        |--------------------------------------------------------------------------
        */

        $allowedPermissionNames = collect();

        foreach (
            array_keys($accessibleModules)
            as $moduleSlug
        ) {

            $allowedPermissionNames = $allowedPermissionNames
                ->merge(
                    ModuleRegistry::permissions(
                        $moduleSlug
                    )
                );
        }

        $allowedPermissionNames = $allowedPermissionNames
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Récupérer les IDs correspondant aux permissions autorisées
        |--------------------------------------------------------------------------
        */

        $allowedPermissionIds = Permission::query()
            ->whereIn(
                'name',
                $allowedPermissionNames
            )
            ->pluck('id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Filtrer les permissions envoyées
        |
        | Cela empêche notamment :
        |
        | purchases.edit
        |
        | ou toute permission ne faisant pas partie
        | des modules actuellement accessibles.
        |--------------------------------------------------------------------------
        */

        $permissions = collect(
            $submittedPermissions
        )
            ->filter(function ($permission) use (
                $allowedPermissionIds
            ) {

                return in_array(
                    (int) $permission['id'],
                    $allowedPermissionIds,
                    true
                );
            })
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | Enregistrement
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $user,
            $permissions
        ) {

            /*
            |------------------------------------------------------------------
            | Supprimer les anciennes permissions personnalisées
            |------------------------------------------------------------------
            */

            UserPermission::where(
                'user_id',
                $user->id
            )->delete();


            /*
            |------------------------------------------------------------------
            | Enregistrer les nouvelles
            |------------------------------------------------------------------
            */

            foreach ($permissions as $permission) {

                UserPermission::create([
                    'user_id' => $user->id,

                    'permission_id' => (int) $permission['id'],

                    'allowed' => (bool) $permission['allowed'],
                ]);
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('company.users.index')
            ->with(
                'success',
                'Les permissions de l’utilisateur ont été mises à jour.'
            );
    }
}