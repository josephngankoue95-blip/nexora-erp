<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchoolGradeSheetController extends Controller
{
    private function companyId(): int
    {
        return (int) auth()->user()->company_id;
    }

    private function options(Request $request): array
    {
        $companyId = $this->companyId();
        $years = DB::table('school_years')->where('company_id', $companyId)
            ->orderByDesc('starts_on')->get();
        $defaultYear = $years->firstWhere('is_active', 1) ?? $years->first();
        $yearId = (int) $request->query('year_id', $defaultYear?->id ?? 0);
        $year = $yearId ? DB::table('school_years')->where('company_id', $companyId)
            ->where('id', $yearId)->firstOrFail() : null;

        $classes = $year ? DB::table('school_classes')
            ->where('company_id', $companyId)->where('school_year_id', $year->id)
            ->orderBy('name')->get(['id', 'name']) : collect();
        $classId = (int) $request->query('class_id', 0);
        $class = $classId ? $classes->firstWhere('id', $classId) : null;
        if ($classId && !$class) abort(404, 'Classe introuvable pour cette année.');

        $assignments = $class ? DB::table('school_teacher_assignments as a')
            ->join('school_teachers as t', function ($join) {
                $join->on('t.id', '=', 'a.teacher_id')->on('t.company_id', '=', 'a.company_id');
            })
            ->join('school_subjects as s', function ($join) {
                $join->on('s.id', '=', 'a.subject_id')->on('s.company_id', '=', 'a.company_id');
            })
            ->join('school_class_subjects as cs', function ($join) {
                $join->on('cs.school_class_id', '=', 'a.class_id')
                    ->on('cs.school_subject_id', '=', 'a.subject_id')
                    ->on('cs.company_id', '=', 'a.company_id');
            })
            ->where('a.company_id', $companyId)
            ->where('a.school_year_id', $year->id)
            ->where('a.class_id', $classId)
            ->where('a.is_active', true)
            ->where('cs.is_active', true)
            ->where('s.is_active', true)
            ->where('t.status', 'active')
            ->orderBy('s.name')->orderBy('t.last_name')
            ->get(['a.id', 'a.teacher_id', 'a.subject_id', 's.name as subject_name',
                't.first_name as teacher_first_name', 't.last_name as teacher_last_name',
                'cs.coefficient']) : collect();

        $periods = $year ? DB::table('school_evaluation_periods')
            ->where('company_id', $companyId)->where('school_year_id', $year->id)
            ->orderBy('number')->get(['id', 'name']) : collect();

        $assignmentId = (int) $request->query('assignment_id', 0);
        $assignment = $assignmentId ? $assignments->firstWhere('id', $assignmentId) : null;
        if ($assignmentId && !$assignment) abort(404, 'Affectation invalide.');
        $periodId = (int) $request->query('period_id', 0);
        $period = $periodId ? $periods->firstWhere('id', $periodId) : null;
        if ($periodId && !$period) abort(404, 'Période invalide.');

        return compact('years', 'year', 'classes', 'class', 'assignments', 'assignment', 'periods', 'period');
    }

    public function index(Request $request)
    {
        return view('company.school.grade-sheets.index', $this->options($request));
    }

    public function print(Request $request)
    {
        $data = $this->options($request);
        if (!$data['year'] || !$data['class'] || !$data['assignment'] || !$data['period']) {
            throw ValidationException::withMessages([
                'selection' => 'Sélectionnez une année, une classe, une affectation et une période.',
            ]);
        }

        $companyId = $this->companyId();
        $students = DB::table('school_enrollments as e')
            ->join('school_students as s', function ($join) {
                $join->on('s.id', '=', 'e.school_student_id')
                    ->on('s.company_id', '=', 'e.company_id');
            })
            ->where('e.company_id', $companyId)
            ->where('e.school_year_id', $data['year']->id)
            ->where('e.school_class_id', $data['class']->id)
            ->where('e.status', 'enrolled')
            ->select('s.id', 's.student_number', 's.last_name', 's.first_name')
            ->distinct()->orderBy('s.last_name')->orderBy('s.first_name')->get();

        $company = DB::table('companies')->where('id', $companyId)->firstOrFail();
        $columns = $request->integer('columns', 4);
        if (!in_array($columns, [3, 4, 5, 6], true)) $columns = 4;

        return view('company.school.grade-sheets.print', array_merge($data, compact('students', 'company', 'columns')));
    }
}
