<?php

namespace App\Http\Middleware;

use App\Services\ModulePermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(
        Request $request,
        Closure $next,
        string $module
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $modulePermissionService = app(
            ModulePermissionService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | Le Super Admin gère la plateforme et peut accéder aux modules
        | même lorsqu'ils ne sont pas activés pour une entreprise.
        |
        */

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification du module
        |--------------------------------------------------------------------------
        */

        if (!$modulePermissionService->moduleExists($module)) {
            abort(
                404,
                'Ce module n’existe pas.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification de l'activation pour l'entreprise
        |--------------------------------------------------------------------------
        */

        if (!$modulePermissionService->canAccessModule(
            $user,
            $module
        )) {
            abort(
                403,
                'Ce module n’est pas activé pour votre entreprise.'
            );
        }

        return $next($request);
    }
}