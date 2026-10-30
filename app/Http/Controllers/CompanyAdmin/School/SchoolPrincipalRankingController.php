<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolPrincipalRankingController extends Controller
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

        $classes = collect();
        $periods = collect();
        $subjects = collect();
        $results = collect();

        $selectedClass = null;
        $selectedPeriod = null;

        $summary = [
            'students' => 0,
            'complete' => 0,
            'class_average' => null,
            'best_average' => null,
            'success_rate' => null,
        ];

        if (!$year) {
            return view('company.school.principal.results.ranking', compact(
                'year', 'classes', 'periods', 'subjects',
                'results', 'selectedClass', 'selectedPeriod', 'summary'
            ));
        }

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

        $selectedClass = $classes->firstWhere(
            'id',
            (int) $request->query('class_id', 0)
        );

        $selectedPeriod = $periods->firstWhere(
            'id',
            (int) $request->query('period_id', 0)
        );

        if (!$selectedClass || !$selectedPeriod) {
            return view('company.school.principal.results.ranking', compact(
                'year', 'classes', 'periods', 'subjects',
                'results', 'selectedClass', 'selectedPeriod', 'summary'
            ));
        }

        $classId = (int) $selectedClass->id;
        $periodId = (int) $selectedPeriod->id;

        $subjects = DB::table('school_class_subjects as cs')
            ->join('school_subjects as s', function ($join) {
                $join->on('s.id', '=', 'cs.school_subject_id')
                    ->on('s.company_id', '=', 'cs.company_id');
            })
            ->where('cs.company_id', $companyId)
            ->where('cs.school_class_id', $classId)
            ->where('cs.is_active', 1)
            ->select(
                'cs.id',
                's.name',
                'cs.coefficient'
            )
            ->orderBy('cs.sort_order')
            ->get();

        $students = DB::table('school_enrollments as e')
            ->join('school_students as s', function ($join) {
                $join->on('s.id', '=', 'e.school_student_id')
                    ->on('s.company_id', '=', 'e.company_id');
            })
            ->where('e.company_id', $companyId)
            ->where('e.school_year_id', $yearId)
            ->where('e.school_class_id', $classId)
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

        $evaluations = DB::table('school_evaluations')
            ->where('company_id', $companyId)
            ->where('school_year_id', $yearId)
            ->where('school_class_id', $classId)
            ->where('school_evaluation_period_id', $periodId)
            ->whereIn('school_class_subject_id', $subjects->pluck('id'))
            ->get()
            ->groupBy('school_class_subject_id');

        $evaluationIds = $evaluations
            ->flatten()
            ->pluck('id');

        $grades = $evaluationIds->isNotEmpty() && $students->isNotEmpty()
            ? DB::table('school_grades')
                ->where('company_id', $companyId)
                ->whereIn('school_evaluation_id', $evaluationIds)
                ->whereIn('school_student_id', $students->pluck('id'))
                ->get()
                ->keyBy(fn ($grade) =>
                    $grade->school_student_id . ':' .
                    $grade->school_evaluation_id
                )
            : collect();

        $results = $students->map(function ($student) use (
            $subjects,
            $evaluations,
            $grades
        ) {
            $subjectAverages = [];
            $weightedSum = 0.0;
            $coefficientSum = 0.0;
            $missing = 0;
            $expected = 0;
            $configuredSubjects = 0;

            foreach ($subjects as $subject) {
                $subjectEvaluations = $evaluations->get(
                    $subject->id,
                    collect()
                );

                $coefficient = (float) $subject->coefficient;

                $total = 0.0;
                $weights = 0.0;

                if ($coefficient > 0 && $subjectEvaluations->isNotEmpty()) {
                    $configuredSubjects++;
                }

                foreach ($subjectEvaluations as $evaluation) {
                    $expected++;

                    $grade = $grades->get(
                        $student->id . ':' . $evaluation->id
                    );

                    if (!$grade || $grade->score === null) {
                        $missing++;
                        continue;
                    }

                    $maxScore = (float) $evaluation->max_score;
                    $weight = (float) $evaluation->weight;

                    if ($maxScore <= 0 || $weight <= 0) {
                        $missing++;
                        continue;
                    }

                    $score = (float) $grade->score;

                    $normalized = ($score / $maxScore) * 20;

                    $total += $normalized * $weight;
                    $weights += $weight;
                }

                $average = $weights > 0
                    ? round($total / $weights, 2)
                    : null;

                $subjectAverages[$subject->id] = $average;

                if ($average !== null && $coefficient > 0) {
                    $weightedSum += $average * $coefficient;
                    $coefficientSum += $coefficient;
                }
            }

            $student->subject_averages = $subjectAverages;

            $student->general_average = $coefficientSum > 0
                ? round($weightedSum / $coefficientSum, 2)
                : null;

            $student->missing = $missing;
            $student->expected = $expected;

            $student->is_complete =
                $expected > 0
                && $missing === 0
                && $configuredSubjects === $subjects->count()
                && $coefficientSum > 0;

            $student->rank = null;

            return $student;
        });

        // Classement provisoire uniquement des dossiers complets.
        // Les ex aequo partagent le même rang.
        $sorted = $results
            ->filter(fn ($student) =>
                $student->is_complete
                && $student->general_average !== null
            )
            ->sortByDesc('general_average')
            ->values();

        $previousAverage = null;
        $previousRank = null;

        foreach ($sorted as $index => $student) {
            if (
                $previousAverage !== null
                && $student->general_average === $previousAverage
            ) {
                $student->rank = $previousRank;
            } else {
                $student->rank = $index + 1;
            }

            $previousAverage = $student->general_average;
            $previousRank = $student->rank;
        }

        $complete = $results->filter(
            fn ($student) => $student->is_complete
        );

        $summary['students'] = $results->count();
        $summary['complete'] = $complete->count();

        if ($complete->isNotEmpty()) {
            $summary['class_average'] = round(
                $complete->avg('general_average'),
                2
            );

            $summary['best_average'] = $complete
                ->max('general_average');

            $summary['success_rate'] = round(
                $complete->filter(
                    fn ($student) => $student->general_average >= 10
                )->count() * 100 / $complete->count(),
                2
            );
        }

        $results = $results
            ->sortBy([
                ['last_name', 'asc'],
                ['first_name', 'asc'],
            ])
            ->values();

        return view('company.school.principal.results.ranking', compact(
            'year', 'classes', 'periods', 'subjects',
            'results', 'selectedClass', 'selectedPeriod', 'summary'
        ));
    }
}