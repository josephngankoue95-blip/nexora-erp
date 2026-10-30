<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | UTILISATEUR NON CONNECTÉ
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            return redirect()->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'super_admin') {
            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | COMPANY ADMIN
        |--------------------------------------------------------------------------
        |
        | Le Company Admin possède tous les droits.
        |
        */

        if ($user->role === 'companyadmin') {
            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | VÉRIFIER LE RÔLE
        |--------------------------------------------------------------------------
        */

        if (!in_array($user->role, $roles, true)) {

            abort(
                403,
                'Vous n’avez pas l’autorisation d’accéder à cette section.'
            );
        }


        return $next($request);
    }
}