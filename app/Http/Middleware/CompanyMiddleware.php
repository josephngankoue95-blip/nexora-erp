<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Utilisateur non connecté
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | Le Super Admin utilise son propre espace
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {
            return redirect()
                ->route('superadmin.dashboard')
                ->withErrors([
                    'access' => 'Vous ne pouvez pas accéder à l’espace entreprise.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Rôles autorisés dans une entreprise
        |--------------------------------------------------------------------------
        */

        $allowedRoles = [
            'companyadmin',
            'manager',
            'seller',
            'cashier',
            'stock_manager',
            'accountant',
            'employee',
        ];

        if (!in_array($user->role, $allowedRoles, true)) {
            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Votre compte ne possède pas de rôle valide.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Une entreprise est obligatoire
        |--------------------------------------------------------------------------
        */

        if (!$user->company_id) {
            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Votre compte n’est associé à aucune entreprise.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification entreprise
        |--------------------------------------------------------------------------
        */

        $company = $user->company;

        if (!$company) {
            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Votre entreprise est introuvable.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Entreprise active ?
        |--------------------------------------------------------------------------
        */

        if (!$company->status) {
            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'Votre entreprise est actuellement inactive. Veuillez contacter le Super Administrateur.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Compte utilisateur actif ?
        |--------------------------------------------------------------------------
        */

        if (!$user->is_active) {
            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'Votre compte est désactivé. Veuillez contacter l’administrateur de votre entreprise.',
                ]);
        }

        return $next($request);
    }
}