<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES ENTREPRISES
        |--------------------------------------------------------------------------
        */

        $totalCompanies = Company::count();

        $activeCompanies = Company::where('is_active', true)->count();

        $inactiveCompanies = Company::where('is_active', false)->count();


        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES UTILISATEURS
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::count();

        $companyUsers = User::whereNotNull('company_id')->count();

        $superAdmins = User::where('role', 'super_admin')->count();


        /*
        |--------------------------------------------------------------------------
        | NOUVELLES ENTREPRISES - 6 DERNIERS MOIS
        |--------------------------------------------------------------------------
        */

        $companyEvolution = collect();

        for ($i = 5; $i >= 0; $i--) {

            $date = now()->subMonths($i);

            $companyEvolution->push([
                'month' => $date->translatedFormat('M Y'),

                'count' => Company::query()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | NOUVEAUX UTILISATEURS - 6 DERNIERS MOIS
        |--------------------------------------------------------------------------
        */

        $userEvolution = collect();

        for ($i = 5; $i >= 0; $i--) {

            $date = now()->subMonths($i);

            $userEvolution->push([
                'month' => $date->translatedFormat('M Y'),

                'count' => User::query()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DERNIÈRES ENTREPRISES
        |--------------------------------------------------------------------------
        */

        $recentCompanies = Company::query()
            ->latest()
            ->take(6)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DERNIERS UTILISATEURS
        |--------------------------------------------------------------------------
        */

        $recentUsers = User::query()
            ->with('company')
            ->latest()
            ->take(6)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ENTREPRISES PAR STATUT
        |--------------------------------------------------------------------------
        */

        $companyStatus = [
            'active' => $activeCompanies,
            'inactive' => $inactiveCompanies,
        ];


        /*
        |--------------------------------------------------------------------------
        | RÉPARTITION DES UTILISATEURS
        |--------------------------------------------------------------------------
        */

        $userDistribution = [
            'company_users' => $companyUsers,
            'super_admins' => $superAdmins,
        ];


        return view('superadmin.dashboard', compact(
            'totalCompanies',
            'activeCompanies',
            'inactiveCompanies',
            'totalUsers',
            'companyUsers',
            'superAdmins',
            'companyEvolution',
            'userEvolution',
            'recentCompanies',
            'recentUsers',
            'companyStatus',
            'userDistribution'
        ));
    }
}