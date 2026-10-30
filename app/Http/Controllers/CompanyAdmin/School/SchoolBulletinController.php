<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Services\School\SchoolCouncilDecisions;
use App\Services\School\SchoolDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolBulletinController extends Controller
{
    private function cid(): int
    {
        return (int) auth()->user()->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->cid();

        $years = DB::table('school_years')
            ->where('company_id', $companyId)
            ->orderByDesc('starts_on')
            ->get();

        $defaultYear = $years->firstWhere('is_active', 1)
            ?? $years->first();

        $yearId = (int) $request->query(
            'year_id',
            $defaultYear?->id ?? 0
        );

        $year = $yearId
            ? DB::table('school_years')
                ->where('company_id', $companyId)
                ->where('id', $yearId)
                ->firstOrFail()
            : null;

        $classes = $year
            ? DB::table('school_classes')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->orderBy('name')
                ->get()
            : collect();

        $classId = (int) $request->query('class_id', 0);

        $class = $classId
            ? DB::table('school_classes')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->where('id', $classId)
                ->firstOrFail()
            : null;

        $students = $class
            ? $this->students($classId, $yearId)
            : collect();

        $periods = $year
            ? DB::table('school_evaluation_periods')
                ->where('company_id', $companyId)
                ->where('school_year_id', $yearId)
                ->orderBy('number')
                ->get()
            : collect();

        return view(
            'company.school.grades.bulletins',
            compact(
                'years',
                'year',
                'classes',
                'class',
                'students',
                'periods'
            )
        );
    }

    private function students(int $classId, int $yearId)
    {
        return DB::table('school_enrollments as e')
            ->join('school_students as s', function ($join) {
                $join->on('s.id', '=', 'e.school_student_id')
                    ->on('s.company_id', '=', 'e.company_id');
            })
            ->where('e.company_id', $this->cid())
            ->where('e.school_class_id', $classId)
            ->where('e.school_year_id', $yearId)
            ->where('e.status', 'enrolled')
            ->select(
                's.id',
                's.student_number',
                's.first_name',
                's.last_name',
                's.date_of_birth',
                's.place_of_birth',
                's.gender'
            )
            ->distinct()
            ->orderBy('s.last_name')
            ->orderBy('s.first_name')
            ->get();
    }

    private function subjectResults(
        $subjects,
        $periods,
        $evaluations,
        string $kind,
        int $number
    ): array {
        $rows = [];
        $sum = 0.0;
        $coefficients = 0.0;

        foreach ($subjects as $subject) {
            $averages = [];

            foreach ($periods as $period) {
                $subset = $evaluations->filter(fn ($e) =>
                    (int) $e->school_class_subject_id === (int) $subject->id &&
                    (int) $e->school_evaluation_period_id === (int) $period->id
                );

                $weighted = 0.0;
                $weights = 0.0;

                foreach ($subset as $evaluation) {
                    if (
                        $evaluation->score === null ||
                        (float) $evaluation->max_score <= 0 ||
                        (float) $evaluation->weight <= 0
                    ) {
                        continue;
                    }

                    $weighted += (
                        (float) $evaluation->score /
                        (float) $evaluation->max_score
                    ) * 20 * (float) $evaluation->weight;

                    $weights += (float) $evaluation->weight;
                }

                if ($weights > 0) {
                    $averages[$period->id] = $weighted / $weights;
                }
            }

            $values = [];

            if ($kind === 'sequence') {
                foreach ($periods as $p) {
                    if (
                        $p->kind === 'sequence' &&
                        (int) $p->number === $number &&
                        isset($averages[$p->id])
                    ) {
                        $values[] = $averages[$p->id];
                    }
                }
            } elseif ($kind === 'trimester') {
                foreach ($periods as $p) {
                    if (
                        (int) $p->trimester_number === $number &&
                        isset($averages[$p->id])
                    ) {
                        $values[] = $averages[$p->id];
                    }
                }
            } else {
                for ($t = 1; $t <= 3; $t++) {
                    $part = [];

                    foreach ($periods as $p) {
                        if (
                            (int) $p->trimester_number === $t &&
                            isset($averages[$p->id])
                        ) {
                            $part[] = $averages[$p->id];
                        }
                    }

                    if ($part) {
                        $values[] = array_sum($part) / count($part);
                    }
                }
            }

            $average = $values
                ? array_sum($values) / count($values)
                : null;

            $coefficient = (float) $subject->coefficient;

            if ($average !== null && $coefficient > 0) {
                $sum += $average * $coefficient;
                $coefficients += $coefficient;
            }

            $rows[] = [
                'subject_id' => (int) $subject->id,
                'name' => $subject->name,
                'group' => $subject->group_number,
                'coefficient' => $coefficient,
                'average' => $average,
                'points' => $average === null
                    ? null
                    : $average * $coefficient,
            ];
        }

        return [
            'rows' => $rows,
            'overall' => $coefficients > 0
                ? $sum / $coefficients
                : null,
            'points' => $sum,
            'coefficients' => $coefficients,
        ];
    }

    public function show(
        Request $request,
        int $classId,
        int $studentId
    ) {
        $companyId = $this->cid();

        $class = DB::table('school_classes')
            ->where('company_id', $companyId)
            ->where('id', $classId)
            ->firstOrFail();

        $year = DB::table('school_years')
            ->where('company_id', $companyId)
            ->where('id', $class->school_year_id)
            ->firstOrFail();

        $students = $this->students($class->id, $year->id);

        $student = $students->firstWhere('id', $studentId);

        abort_unless($student, 404);

        $config = DB::table('school_grading_configs')
            ->where('company_id', $companyId)
            ->where('school_year_id', $year->id)
            ->first();

        abort_unless($config, 404);

        $kind = $request->query('kind', 'trimester');

        abort_unless(
            in_array($kind, ['sequence', 'trimester', 'annual'], true),
            404
        );

        abort_if(
            $config->mode === 'trimesters' &&
            $kind === 'sequence',
            404
        );

        $number = (int) $request->query('number', 1);

        if ($kind === 'sequence') {
            abort_unless($number >= 1 && $number <= 6, 404);
        }

        if ($kind === 'trimester') {
            abort_unless($number >= 1 && $number <= 3, 404);
        }

        $section = DB::table('school_levels as l')
            ->join('school_sections as s', function ($j) {
                $j->on('s.id', '=', 'l.school_section_id')
                    ->on('s.company_id', '=', 'l.company_id');
            })
            ->where('l.company_id', $companyId)
            ->where('l.id', $class->school_level_id)
            ->select(
                's.name',
                's.code',
                'l.name as level_name'
            )
            ->first();

        $sectionKey = strtolower(
            ($section->code ?? '') . ' ' .
            ($section->name ?? '')
        );

        $english = str_contains($sectionKey, 'anglo')
            || str_contains($sectionKey, 'english')
            || str_contains($sectionKey, 'en');

        $subjects = DB::table('school_class_subjects as cs')
            ->join('school_subjects as s', function ($j) {
                $j->on('s.id', '=', 'cs.school_subject_id')
                    ->on('s.company_id', '=', 'cs.company_id');
            })
            ->where('cs.company_id', $companyId)
            ->where('cs.school_class_id', $class->id)
            ->select(
                'cs.id',
                'cs.coefficient',
                'cs.group_number',
                's.name'
            )
            ->orderBy('cs.group_number')
            ->orderBy('cs.sort_order')
            ->orderBy('cs.id')
            ->get();

        $periods = DB::table('school_evaluation_periods')
            ->where('company_id', $companyId)
            ->where('school_year_id', $year->id)
            ->get();

        $evaluations = DB::table('school_evaluations as v')
            ->join('school_grades as g', function ($j) {
                $j->on('g.school_evaluation_id', '=', 'v.id')
                    ->on('g.company_id', '=', 'v.company_id');
            })
            ->where('v.company_id', $companyId)
            ->where('v.school_class_id', $class->id)
            ->where('v.school_year_id', $year->id)
            ->select(
                'v.school_class_subject_id',
                'v.school_evaluation_period_id',
                'v.max_score',
                'v.weight',
                'g.school_student_id',
                'g.score'
            )
            ->get();

        $results = [];

        foreach ($students as $classmate) {
            $grades = $evaluations->where(
                'school_student_id',
                $classmate->id
            );

            $results[$classmate->id] = $this->subjectResults(
                $subjects,
                $periods,
                $grades,
                $kind,
                $number
            );
        }

        // Statistiques par matière.
        // Les élèves sans note sont exclus.
        // Les ex æquo partagent leur rang.

        $subjectStats = [];

        foreach ($subjects as $subject) {
            $subjectId = (int) $subject->id;

            $marks = collect($results)
                ->map(function ($studentResult) use ($subjectId) {
                    foreach ($studentResult['rows'] as $row) {
                        if (
                            (int) $row['subject_id'] === $subjectId
                        ) {
                            return $row['average'];
                        }
                    }

                    return null;
                })
                ->filter(fn ($mark) => $mark !== null)
                ->map(fn ($mark) => (float) $mark)
                ->values();

            $subjectStats[$subjectId] = [
                'class_average' => $marks->isNotEmpty()
                    ? $marks->avg()
                    : null,
                'graded_count' => $marks->count(),
                'passed_count' => $marks
                    ->filter(fn ($mark) => $mark >= 10)
                    ->count(),
                'marks' => $marks,
            ];
        }

        $result = $results[$studentId];

        foreach ($result['rows'] as &$row) {
            $stats = $subjectStats[
                (int) $row['subject_id']
            ];

            $mark = $row['average'];

            $row['rank'] = $mark === null
                ? null
                : 1 + $stats['marks']
                    ->filter(
                        fn ($other) =>
                            $other > (float) $mark + 0.00001
                    )
                    ->count();

            $row['class_average'] = $stats['class_average'];
            $row['graded_count'] = $stats['graded_count'];
            $row['passed_count'] = $stats['passed_count'];
        }

        unset($row);

        $averages = collect($results)
            ->pluck('overall')
            ->filter(fn ($a) => $a !== null)
            ->sortDesc()
            ->values();

        $rank = $result['overall'] === null
            ? null
            : 1 + $averages
                ->filter(
                    fn ($a) =>
                        $a > $result['overall'] + 0.00001
                )
                ->count();

        $classAverage = $averages->isNotEmpty()
            ? $averages->avg()
            : null;

        $highest = $averages->isNotEmpty()
            ? $averages->max()
            : null;

        $lowest = $averages->isNotEmpty()
            ? $averages->min()
            : null;

        $successRate = $averages->isNotEmpty()
            ? 100 * $averages
                ->filter(fn ($a) => $a >= 10)
                ->count() / $averages->count()
            : null;

        $title = $kind === 'annual'
            ? ($english ? 'Annual report' : 'Bulletin annuel')
            : (
                $kind === 'sequence'
                    ? ($english ? 'Sequence ' : 'Séquence ')
                    : ($english ? 'Term ' : 'Trimestre ')
            ) . $number;

        $company = DB::table('companies')
            ->where('id', $companyId)
            ->first();

        // Paramètres officiels de l'établissement.
        $schoolDocument = app(
            SchoolDocumentService::class
        )->data($companyId);

        $data = array_merge(
            compact(
                'student',
                'class',
                'year',
                'section',
                'english',
                'title',
                'kind',
                'number',
                'company',
                'schoolDocument',
                'rank',
                'classAverage',
                'highest',
                'lowest',
                'successRate'
            ),
            $result
        );

        $data['classSize'] = $students->count();
        $data['gradedCount'] = $averages->count();

        // Décision officielle du conseil de classe.
        $decisionService = app(
            SchoolCouncilDecisions::class
        );

        $data['councilDecision'] = $decisionService->forStudent(
            $companyId,
            (int) $year->id,
            (int) $class->id,
            (int) $student->id,
            $kind,
            $number
        );

        $data['councilDecisionLabel'] = $decisionService->label(
            $data['councilDecision']?->decision
        );

        $view = 'company.school.grades.bulletin-print';

        if ($request->boolean('pdf')) {
            abort_unless(
                class_exists(
                    \Barryvdh\DomPDF\Facade\Pdf::class
                ),
                501,
                'Installer barryvdh/laravel-dompdf pour exporter en PDF.'
            );

            return \Barryvdh\DomPDF\Facade\Pdf::loadView(
                $view,
                $data
            )
                ->setPaper('a4', 'portrait')
                ->download(
                    'bulletin-' . $studentId . '.pdf'
                );
        }

        return view($view, $data);
    }
}