<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolPrincipalDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Vérification de l'utilisateur et de son rôle
        abort_unless(
            $user
            && $user->role === 'school_principal'
            && $user->is_active,
            403,
            'Accès réservé au Proviseur.'
        );

        // 2. Vérification de l'entreprise
        $company = $user->company;

        abort_unless(
            $user->company_id
            && $company
            && $company->is_active,
            403,
            'Entreprise introuvable ou inactive.'
        );

        $companyId = (int) $user->company_id;

        // 3. Année scolaire active
        $year = DB::table('school_years')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->first();

        // 4. Initialisation des statistiques
        $stats = [
            'students' => 0,
            'classes' => 0,
            'teachers' => 0,
        ];

        // 5. Statistiques pour l'année scolaire active
        if ($year) {

            // Nombre d'élèves inscrits
            $stats['students'] = DB::table('school_enrollments')
                ->where('company_id', $companyId)
                ->where('school_year_id', $year->id)
                ->where('status', 'enrolled')
                ->distinct()
                ->count('school_student_id');

            // Nombre de classes
            $stats['classes'] = DB::table('school_classes')
                ->where('company_id', $companyId)
                ->where('school_year_id', $year->id)
                ->count();
        }

        // 6. Nombre d'enseignants actifs
        $stats['teachers'] = DB::table('school_teachers')
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        // 7. Tableau de bord du Proviseur
        return view('company.school.principal.dashboard', [
            'user' => $user,
            'company' => $company,
            'year' => $year,
            'stats' => $stats,
        ]);
    }
}