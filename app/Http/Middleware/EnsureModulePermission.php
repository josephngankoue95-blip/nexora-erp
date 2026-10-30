<?php

namespace App\Http\Middleware;

use App\Services\ModulePermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModulePermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Déterminer le module
        |--------------------------------------------------------------------------
        |
        | Exemple :
        |
        | purchases.view
        | purchases.create
        | purchases.cancel
        |
        | devient :
        |
        | module = purchases
        |
        */

        $module = explode('.', $permission)[0] ?? null;

        if (!$module) {
            abort(
                403,
                'Permission invalide.'
            );
        }

        $service = app(
            ModulePermissionService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Vérification module + permission
        |--------------------------------------------------------------------------
        */

        if (!$service->can(
            $user,
            $module,
            $permission
        )) {
            abort(
                403,
                'Vous n’avez pas l’autorisation d’accéder à cette fonctionnalité.'
            );
        }

        return $next($request);
    }
}