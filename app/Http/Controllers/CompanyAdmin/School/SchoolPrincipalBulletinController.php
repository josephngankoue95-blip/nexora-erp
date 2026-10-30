<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolPrincipalBulletinController extends Controller
{
    private function context(Request $request): array
    {
        $user = $request->user();

        abort_unless(
            $user
            && $user->role === 'school_principal'
            && $user->is_active
            && $user->company_id
            && $user->company
            && $user->company->is_active,
            403,
            'Accès réservé au Proviseur.'
        );

        $companyId = (int) $user->company_id;

        $year = DB::table('school_years')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->first();

        return compact('companyId', 'year');
    }

    public function index(Request $request): View
    {
        $context = $this->context($request);

        $companyId = $context['companyId'];
        $year = $context['year'];

        $classes = collect();
        $students = collect();
        $config = null;

        $selectedClass = null;

        if ($year) {
            $classes = DB::table('school_classes')
                ->where('company_id', $companyId)
                ->where('school_year_id', $year->id)
                ->orderBy('name')
                ->get();

            $classId = (int) $request->query('class_id', 0);

            $selectedClass = $classes->firstWhere('id', $classId);

            $config = DB::table('school_grading_configs')
                ->where('company_id', $companyId)
                ->where('school_year_id', $year->id)
                ->first();

            if ($selectedClass) {
                $students = DB::table('school_enrollments as e')
                    ->join('school_students as s', function ($join) {
                        $join->on('s.id', '=', 'e.school_student_id')
                            ->on('s.company_id', '=', 'e.company_id');
                    })
                    ->where('e.company_id', $companyId)
                    ->where('e.school_year_id', $year->id)
                    ->where('e.school_class_id', $selectedClass->id)
                    ->where('e.status', 'enrolled')
                    ->select(
                        's.id',
                        's.student_number',
                        's.first_name',
                        's.last_name'
                    )
                    ->distinct()
                    ->orderBy('s.last_name')
                    ->orderBy('s.first_name')
                    ->get();
            }
        }

        return view('company.school.principal.bulletins.index', compact(
            'year',
            'classes',
            'students',
            'selectedClass',
            'config'
        ));
    }

    public function show(
        Request $request,
        int $classId,
        int $studentId
    ) {
        $context = $this->context($request);

        $companyId = $context['companyId'];

        // Réutilise le contrôleur officiel des bulletins.
        // Celui-ci vérifie également la classe,
        // l'inscription et l'appartenance à l'entreprise.

        return app(SchoolBulletinController::class)->show(
            $request,
            $classId,
            $studentId
        );
    }
}