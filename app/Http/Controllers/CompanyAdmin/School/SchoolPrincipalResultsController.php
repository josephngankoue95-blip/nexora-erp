<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolPrincipalResultsController extends Controller
{
    public function index(Request $request): View
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

        $stats = [
            'evaluations' => 0,
            'locked' => 0,
            'grades' => 0,
            'students' => 0,
        ];

        $evaluations = collect();
        $periods = collect();
        $classes = collect();

        $selectedClass = null;
        $selectedPeriod = null;

        if ($year) {
            $yearId = (int) $year->id;

            $classes = DB::table('school_classes')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->orderBy('name')
                ->get();

            $periods = DB::table('school_evaluation_periods')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->orderBy('number')
                ->get();

            $classId = (int) $request->query('class_id', 0);
            $periodId = (int) $request->query('period_id', 0);

            $selectedClass = $classes->firstWhere('id', $classId);
            $selectedPeriod = $periods->firstWhere('id', $periodId);

            $query = DB::table('school_evaluations as e')
                ->join('school_classes as c', function ($join) {
                    $join->on('c.id', '=', 'e.school_class_id')
                        ->on('c.company_id', '=', 'e.company_id');
                })
                ->join('school_class_subjects as cs', function ($join) {
                    $join->on('cs.id', '=', 'e.school_class_subject_id')
                        ->on('cs.company_id', '=', 'e.company_id')
                        ->on('cs.school_class_id', '=', 'e.school_class_id');
                })
                ->join('school_subjects as s', function ($join) {
                    $join->on('s.id', '=', 'cs.school_subject_id')
                        ->on('s.company_id', '=', 'e.company_id');
                })
                ->join('school_evaluation_periods as p', function ($join) {
                    $join->on('p.id', '=', 'e.school_evaluation_period_id')
                        ->on('p.company_id', '=', 'e.company_id')
                        ->on('p.school_year_id', '=', 'e.school_year_id');
                })
                ->where('e.company_id', $companyId)
                ->where('e.school_year_id', $yearId);

            $stats['evaluations'] = DB::table('school_evaluations')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->count();

            $stats['locked'] = DB::table('school_evaluations')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->where('is_locked', 1)
                ->count();

            $stats['grades'] = DB::table('school_grades as g')
                ->join('school_evaluations as e', function ($join) {
                    $join->on('e.id', '=', 'g.school_evaluation_id')
                        ->on('e.company_id', '=', 'g.company_id');
                })
                ->where('g.company_id', $companyId)
                ->where('e.school_year_id', $yearId)
                ->count();

            $stats['students'] = DB::table('school_enrollments')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->where('status', 'enrolled')
                ->distinct()
                ->count('school_student_id');

            if ($selectedClass) {
                $query->where('e.school_class_id', $selectedClass->id);
            }

            if ($selectedPeriod) {
                $query->where(
                    'e.school_evaluation_period_id',
                    $selectedPeriod->id
                );
            }

            $evaluations = $query
                ->select(
                    'e.id',
                    'e.title',
                    'e.max_score',
                    'e.weight',
                    'e.is_locked',
                    'c.name as class_name',
                    's.name as subject_name',
                    'p.name as period_name'
                )
                ->selectSub(function ($sub) {
                    $sub->from('school_grades as g')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('g.school_evaluation_id', 'e.id')
                        ->whereColumn('g.company_id', 'e.company_id');
                }, 'grades_count')
                ->orderByDesc('e.id')
                ->paginate(15)
                ->withQueryString();
        }

        return view('company.school.principal.results.index', [
            'year' => $year,
            'stats' => $stats,
            'evaluations' => $evaluations,
            'classes' => $classes,
            'periods' => $periods,
            'selectedClass' => $selectedClass,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }

    
public function byClass(Request $request): \Illuminate\View\View
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

    $classes = collect();
    $subjects = collect();
    $periods = collect();
    $evaluations = collect();
    $students = collect();

    $selectedClass = null;
    $selectedSubject = null;
    $selectedPeriod = null;

    if ($year) {
        $yearId = (int) $year->id;

        $classes = DB::table('school_classes')
            ->where('company_id', $companyId)
            ->where('school_year_id', $yearId)
            ->orderBy('name')
            ->get();

        $periods = DB::table('school_evaluation_periods')
            ->where('company_id', $companyId)
            ->where('school_year_id', $yearId)
            ->orderBy('number')
            ->get();

        $classId = (int) $request->query('class_id', 0);
        $subjectId = (int) $request->query('subject_id', 0);
        $periodId = (int) $request->query('period_id', 0);

        $selectedClass = $classes->firstWhere('id', $classId);
        $selectedPeriod = $periods->firstWhere('id', $periodId);

        if ($selectedClass) {
            $subjects = DB::table('school_class_subjects as cs')
                ->join('school_subjects as s', function ($join) {
                    $join->on('s.id', '=', 'cs.school_subject_id')
                        ->on('s.company_id', '=', 'cs.company_id');
                })
                ->where('cs.company_id', $companyId)
                ->where('cs.school_class_id', $selectedClass->id)
                ->where('cs.is_active', 1)
                ->select(
                    'cs.id',
                    's.name',
                    'cs.coefficient'
                )
                ->orderBy('s.name')
                ->get();

            $selectedSubject = $subjects->firstWhere('id', $subjectId);
        }

        if ($selectedClass && $selectedSubject && $selectedPeriod) {

            $evaluations = DB::table('school_evaluations')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->where('school_class_id', $selectedClass->id)
                ->where('school_class_subject_id', $selectedSubject->id)
                ->where(
                    'school_evaluation_period_id',
                    $selectedPeriod->id
                )
                ->orderBy('id')
                ->get();

            $enrolledStudents = DB::table('school_enrollments as e')
                ->join('school_students as s', function ($join) {
                    $join->on('s.id', '=', 'e.school_student_id')
                        ->on('s.company_id', '=', 'e.company_id');
                })
                ->where('e.company_id', $companyId)
                ->where('e.school_year_id', $yearId)
                ->where('e.school_class_id', $selectedClass->id)
                ->where('e.status', 'enrolled')
                ->select(
                    's.id',
                    's.student_number',
                    's.last_name',
                    's.first_name'
                )
                ->distinct()
                ->orderBy('s.last_name')
                ->orderBy('s.first_name')
                ->get();

            $grades = $evaluations->isNotEmpty()
                ? DB::table('school_grades')
                    ->where('company_id', $companyId)
                    ->whereIn(
                        'school_evaluation_id',
                        $evaluations->pluck('id')
                    )
                    ->whereIn(
                        'school_student_id',
                        $enrolledStudents->pluck('id')
                    )
                    ->get()
                    ->keyBy(fn ($grade) =>
                        $grade->school_student_id . ':' .
                        $grade->school_evaluation_id
                    )
                : collect();

            $students = $enrolledStudents->map(function ($student) use (
                $evaluations,
                $grades
            ) {
                $scores = [];
                $weightedTotal = 0.0;
                $weightTotal = 0.0;
                $entered = 0;

                foreach ($evaluations as $evaluation) {
                    $key = $student->id . ':' . $evaluation->id;
                    $grade = $grades->get($key);

                    if (!$grade || $grade->score === null) {
                        $scores[$evaluation->id] = null;
                        continue;
                    }

                    $score = (float) $grade->score;
                    $maxScore = (float) $evaluation->max_score;
                    $weight = (float) $evaluation->weight;

                    $scores[$evaluation->id] = $score;
                    $entered++;

                    if ($maxScore > 0 && $weight > 0) {
                        $normalized = ($score / $maxScore) * 20;

                        $weightedTotal += $normalized * $weight;
                        $weightTotal += $weight;
                    }
                }

                $student->scores = $scores;
                $student->entered = $entered;
                $student->missing = $evaluations->count() - $entered;

                $student->average = $weightTotal > 0
                    ? round($weightedTotal / $weightTotal, 2)
                    : null;

                return $student;
            });
        }
    }

    return view('company.school.principal.results.by-class', [
        'year' => $year,
        'classes' => $classes,
        'subjects' => $subjects,
        'periods' => $periods,
        'evaluations' => $evaluations,
        'students' => $students,
        'selectedClass' => $selectedClass,
        'selectedSubject' => $selectedSubject,
        'selectedPeriod' => $selectedPeriod,
    ]);
}

}
