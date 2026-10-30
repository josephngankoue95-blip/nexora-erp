<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolTeacher;
use App\Models\SchoolTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolPrincipalTeacherController extends Controller
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

    public function index(Request $request): View
    {
        $context = $this->context($request);

        $companyId = $context['companyId'];
        $year = $context['year'];

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        if (!in_array($status, ['', 'active', 'inactive'], true)) {
            $status = '';
        }

        $teachers = SchoolTeacher::query()
            ->where('company_id', $companyId)

            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('matricule', 'like', "%{$search}%")
                        ->orWhere('speciality', 'like', "%{$search}%");
                });
            })

            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })

            ->withCount([
                'assignments as assignments_count' => function ($query) use ($companyId, $year) {
                    $query->where('company_id', $companyId)
                        ->where('is_active', true);

                    if ($year) {
                        $query->where('school_year_id', $year->id);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])

            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => SchoolTeacher::where('company_id', $companyId)->count(),

            'active' => SchoolTeacher::where('company_id', $companyId)
                ->where('status', 'active')
                ->count(),

            'inactive' => SchoolTeacher::where('company_id', $companyId)
                ->where('status', 'inactive')
                ->count(),
        ];

        return view('company.school.principal.teachers.index', [
            'teachers' => $teachers,
            'stats' => $stats,
            'year' => $year,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(Request $request, SchoolTeacher $teacher): View
    {
        $context = $this->context($request);

        $companyId = $context['companyId'];
        $year = $context['year'];

        abort_unless(
            (int) $teacher->company_id === $companyId,
            404
        );

        $assignments = collect();

        if ($year) {
            $assignments = SchoolTeacherAssignment::query()
                ->with([
                    'subject' => fn ($query) =>
                        $query->where('company_id', $companyId),

                    'schoolClass' => fn ($query) =>
                        $query->where('company_id', $companyId),
                ])
                ->where('company_id', $companyId)
                ->where('teacher_id', $teacher->id)
                ->where('school_year_id', $year->id)
                ->where('is_active', true)
                ->get();
        }

        $workload = [
            'assignments' => $assignments->count(),
            'classes' => $assignments
                ->whereNotNull('schoolClass')
                ->pluck('class_id')
                ->unique()
                ->count(),
            'subjects' => $assignments
                ->whereNotNull('subject')
                ->pluck('subject_id')
                ->unique()
                ->count(),
        ];

        return view('company.school.principal.teachers.show', [
            'teacher' => $teacher,
            'year' => $year,
            'assignments' => $assignments,
            'workload' => $workload,
        ]);
    }
}
