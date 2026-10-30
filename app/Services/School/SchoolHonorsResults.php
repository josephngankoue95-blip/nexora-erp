<?php

namespace App\Services\School;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/** Calcule les moyennes selon la logique de SchoolBulletinController::subjectResults(). */
class SchoolHonorsResults
{
    public function calculate(int $companyId, int $yearId, int $classId, string $kind, int $number): Collection
    {
        $students = DB::table('school_enrollments as e')
            ->join('school_students as s', function ($j) {
                $j->on('s.id', '=', 'e.school_student_id')->on('s.company_id', '=', 'e.company_id');
            })
            ->where('e.company_id', $companyId)->where('e.school_year_id', $yearId)
            ->where('e.school_class_id', $classId)->where('e.status', 'enrolled')
            ->select('s.id', 's.student_number', 's.last_name', 's.first_name')
            ->distinct()->orderBy('s.last_name')->orderBy('s.first_name')->get();

        $subjects = DB::table('school_class_subjects as cs')
            ->join('school_subjects as s', function ($j) {
                $j->on('s.id', '=', 'cs.school_subject_id')->on('s.company_id', '=', 'cs.company_id');
            })
            ->where('cs.company_id', $companyId)->where('cs.school_class_id', $classId)
            ->select('cs.id', 'cs.coefficient', 'cs.group_number', 's.name')
            ->orderBy('cs.group_number')->orderBy('cs.sort_order')->orderBy('cs.id')->get();

        $periods = DB::table('school_evaluation_periods')->where('company_id', $companyId)
            ->where('school_year_id', $yearId)->get();

        $grades = DB::table('school_evaluations as v')
            ->join('school_grades as g', function ($j) {
                $j->on('g.school_evaluation_id', '=', 'v.id')->on('g.company_id', '=', 'v.company_id');
            })
            ->where('v.company_id', $companyId)->where('v.school_year_id', $yearId)
            ->where('v.school_class_id', $classId)
            ->select('v.school_class_subject_id', 'v.school_evaluation_period_id', 'v.max_score', 'v.weight', 'g.school_student_id', 'g.score')->get()
            ->groupBy('school_student_id');

        $rows = $students->map(function ($student) use ($subjects, $periods, $grades, $kind, $number) {
            $studentGrades = $grades->get($student->id, collect());
            $sum = 0.0;
            $coefficients = 0.0;
            foreach ($subjects as $subject) {
                $averages = [];
                foreach ($periods as $period) {
                    $weighted = 0.0;
                    $weights = 0.0;
                    foreach ($studentGrades as $evaluation) {
                        if ((int)$evaluation->school_class_subject_id !== (int)$subject->id ||
                            (int)$evaluation->school_evaluation_period_id !== (int)$period->id ||
                            $evaluation->score === null || (float)$evaluation->max_score <= 0 || (float)$evaluation->weight <= 0) {
                            continue;
                        }
                        $weighted += ((float)$evaluation->score / (float)$evaluation->max_score) * 20 * (float)$evaluation->weight;
                        $weights += (float)$evaluation->weight;
                    }
                    if ($weights > 0) $averages[$period->id] = $weighted / $weights;
                }
                $values = [];
                if ($kind === 'sequence') {
                    foreach ($periods as $period) {
                        if ($period->kind === 'sequence' && (int)$period->number === $number && isset($averages[$period->id])) $values[] = $averages[$period->id];
                    }
                } elseif ($kind === 'trimester') {
                    foreach ($periods as $period) {
                        if ((int)$period->trimester_number === $number && isset($averages[$period->id])) $values[] = $averages[$period->id];
                    }
                } else {
                    for ($t = 1; $t <= 3; $t++) {
                        $part = [];
                        foreach ($periods as $period) {
                            if ((int)$period->trimester_number === $t && isset($averages[$period->id])) $part[] = $averages[$period->id];
                        }
                        if ($part) $values[] = array_sum($part) / count($part);
                    }
                }
                $average = $values ? array_sum($values) / count($values) : null;
                $coefficient = (float)$subject->coefficient;
                if ($average !== null && $coefficient > 0) {
                    $sum += $average * $coefficient;
                    $coefficients += $coefficient;
                }
            }
            $student->overall = $coefficients > 0 ? $sum / $coefficients : null;
            return $student;
        });
        $averages = $rows->pluck('overall')->filter(fn ($v) => $v !== null)->values();
        return $rows->map(function ($student) use ($averages) {
            $student->rank = $student->overall === null ? null : 1 + $averages->filter(fn ($v) => $v > $student->overall + 0.00001)->count();
            return $student;
        });
    }
}
