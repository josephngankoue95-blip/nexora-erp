<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyAdminMiddleware
{
    private const ERP_ROLES = [
        'companyadmin', 'director', 'manager', 'seller', 'cashier',
        'stock_manager', 'accountant', 'employee',
    ];

    private const SCHOOL_ROLES = [
        'school_admin', 'school_principal', 'school_director',
        'school_teacher', 'school_bursar', 'school_supervisor',
        'school_sector_supervisor', 'school_student', 'school_parent',
        'school_librarian',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard')
                ->withErrors(['access' => 'Vous ne pouvez pas accéder à l’espace entreprise.']);
        }

        $isErpRole = in_array($user->role, self::ERP_ROLES, true);
        $isSchoolRole = in_array($user->role, self::SCHOOL_ROLES, true);

        if (!$isErpRole && !$isSchoolRole) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte ne possède pas de rôle valide.']);
        }

        if (!$user->company_id) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte n’est associé à aucune entreprise.']);
        }

        $company = $user->company;
        if (!$company) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre entreprise est introuvable.']);
        }

        if (!$company->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre entreprise est actuellement inactive. Veuillez contacter le Super Administrateur.']);
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte est désactivé. Veuillez contacter l’administrateur de votre entreprise.']);
        }

        // Les rôles purement scolaires ne peuvent accéder qu'aux routes scolaires.
        // Le préfixe de nom est plus fiable que l'URL, et préserve les sous-routes.
        if ($isSchoolRole && !$isErpRole && !$request->routeIs('company.school.*')) {
            abort(403, 'Votre rôle scolaire ne permet pas d’accéder aux autres modules de l’entreprise.');
        }

        return $next($request);
    }
}
