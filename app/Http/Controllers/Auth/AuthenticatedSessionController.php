<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Afficher la page de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTIFICATION
        |--------------------------------------------------------------------------
        */

        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | COMPTE UTILISATEUR ACTIF
        |--------------------------------------------------------------------------
        */

        if (!$user->is_active) {
            return $this->rejectLogin(
                $request,
                'Votre compte est désactivé. Veuillez contacter l’administrateur de votre entreprise.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | RÔLES ENTREPRISE
        |--------------------------------------------------------------------------
        */

        $companyRoles = [
            'companyadmin',
            'director',
            'manager',
            'seller',
            'cashier',
            'stock_manager',
            'accountant',
            'employee',
        ];

        /*
        |--------------------------------------------------------------------------
        | RÔLES SCOLAIRES
        |--------------------------------------------------------------------------
        */

        $schoolRoles = [
            'school_admin',
            'school_principal',
            'school_director',
            'school_teacher',
            'school_supervisor',
            'school_sector_supervisor',
            'school_bursar',
            'school_librarian',
            'school_student',
            'school_parent',
        ];

        /*
        |--------------------------------------------------------------------------
        | VALIDATION DU RÔLE
        |--------------------------------------------------------------------------
        */

        $isCompanyUser = in_array(
            $user->role,
            $companyRoles,
            true
        );

        $isSchoolUser = in_array(
            $user->role,
            $schoolRoles,
            true
        );

        if (!$isCompanyUser && !$isSchoolUser) {
            return $this->rejectLogin(
                $request,
                'Votre compte ne possède pas de rôle valide.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ENTREPRISE OBLIGATOIRE
        |--------------------------------------------------------------------------
        */

        if (!$user->company_id) {
            return $this->rejectLogin(
                $request,
                'Votre compte n’est associé à aucune entreprise.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RÉCUPÉRER L'ENTREPRISE
        |--------------------------------------------------------------------------
        */

        $company = $user->company;

        if (!$company) {
            return $this->rejectLogin(
                $request,
                'Votre entreprise est introuvable.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VÉRIFIER SI L'ENTREPRISE EST ACTIVE
        |--------------------------------------------------------------------------
        */

        if (!$company->is_active) {
            return $this->rejectLogin(
                $request,
                'Votre entreprise est actuellement inactive. '
                . 'Veuillez contacter le Super Administrateur.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECTION DES UTILISATEURS SCOLAIRES
        |--------------------------------------------------------------------------
        |
        | Chaque rôle scolaire possède son espace personnel.
        | Les routes doivent être créées avant d'activer
        | les espaces correspondants.
        |
        */

        $schoolDashboards = [
            'school_admin' => 'company.school.dashboard',

            'school_principal' =>
                'company.school.principal.dashboard',

            'school_director' =>
                'company.school.director.dashboard',

            'school_teacher' =>
                'company.school.teacher-space.dashboard',

            'school_supervisor' =>
                'company.school.supervisor.dashboard',

            'school_sector_supervisor' =>
                'company.school.sector-supervisor.dashboard',

            'school_bursar' =>
                'company.school.bursar.dashboard',

            'school_librarian' =>
                'company.school.librarian.dashboard',

            'school_student' =>
                'company.school.student.dashboard',

            'school_parent' =>
                'company.school.parent.dashboard',
        ];

        if ($isSchoolUser) {

            $dashboardRoute = $schoolDashboards[$user->role];

            /*
            |--------------------------------------------------------------------------
            | VÉRIFIER QUE L'ESPACE EST INSTALLÉ
            |--------------------------------------------------------------------------
            */

            if (!Route::has($dashboardRoute)) {
                return $this->rejectLogin(
                    $request,
                    'Votre espace scolaire n’est pas encore disponible. '
                    . 'Veuillez contacter votre administrateur.'
                );
            }

            return redirect()->route($dashboardRoute);
        }

        /*
        |--------------------------------------------------------------------------
        | ESPACE ENTREPRISE
        |--------------------------------------------------------------------------
        |
        | Les rôles entreprise conservent leur redirection actuelle.
        | Les permissions contrôlent les fonctionnalités accessibles.
        |
        */

        return redirect()->route('company.modules.index');
    }

    /**
     * Refuser une connexion et nettoyer la session.
     */
    private function rejectLogin(
        Request $request,
        string $message
    ): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => $message,
            ]);
    }

    /**
     * Déconnexion.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}