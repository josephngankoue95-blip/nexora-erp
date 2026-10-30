<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use App\Models\SchoolTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolPrincipalAcademicController extends Controller
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

        return compact('user', 'companyId', 'year');
    }

    public function classes(Request $request): View
    {
        $context = $this->context($request);

        $classes = SchoolClass::query()
            ->with('level')
            ->where('company_id', $context['companyId'])
            ->when(
                $context['year'],
                fn ($query, $year) =>
                    $query->where('school_year_id', $year->id),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->orderBy('name')
            ->paginate(20);

        return view('company.school.principal.classes', [
            'classes' => $classes,
            'year' => $context['year'],
        ]);
    }

    public function subjects(Request $request): View
    {
        $context = $this->context($request);

        $subjects = SchoolSubject::query()
            ->with('group')
            ->where('company_id', $context['companyId'])
            ->orderBy('name')
            ->paginate(20);

        return view('company.school.principal.subjects', [
            'subjects' => $subjects,
            'year' => $context['year'],
        ]);
    }

    public function assignments(Request $request): View
    {
        $context = $this->context($request);

        $assignments = SchoolTeacherAssignment::query()
            ->with(['teacher', 'subject', 'schoolClass'])
            ->where('company_id', $context['companyId'])
            ->when(
                $context['year'],
                fn ($query, $year) =>
                    $query->where('school_year_id', $year->id),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->where('is_active', true)
            ->orderBy('class_id')
            ->paginate(20);

        return view('company.school.principal.assignments', [
            'assignments' => $assignments,
            'year' => $context['year'],
        ]);
    }
}