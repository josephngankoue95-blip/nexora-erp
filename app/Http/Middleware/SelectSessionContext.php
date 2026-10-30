<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SelectSessionContext
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Déterminer l'espace demandé
        |--------------------------------------------------------------------------
        | Les routes Super Admin utilisent /superadmin.
        | Toutes les autres routes utilisent la session entreprise.
        */

        $isSuperAdmin = $request->is(
            'superadmin',
            'superadmin/*'
        );

        /*
        |--------------------------------------------------------------------------
        | Sauvegarder la configuration initiale
        |--------------------------------------------------------------------------
        */

        $originalConfig = [
            'session.cookie' => config('session.cookie'),
            'session.path' => config('session.path'),
            'session.domain' => config('session.domain'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Sélectionner la session
        |--------------------------------------------------------------------------
        */

        if ($isSuperAdmin) {
            config([
                'session.cookie' => 'nexora_super_session',
                'session.path' => '/superadmin',
                'session.domain' => null,
            ]);
        } else {
            config([
                'session.cookie' => 'nexora_company_session',
                'session.path' => '/',
                'session.domain' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Exécuter la requête avec le bon contexte
        |--------------------------------------------------------------------------
        */

        try {
            return $next($request);
        } finally {
            config($originalConfig);
        }
    }
}