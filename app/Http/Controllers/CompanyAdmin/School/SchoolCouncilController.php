<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Services\School\SchoolHonorsResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SchoolCouncilController extends Controller
{
    private function cid(): int
    {
        return (int) auth()->user()->company_id;
    }

    private function context(Request $request): array
    {
        $cid = $this->cid();
        $years = DB::table('school_years')->where('company_id', $cid)->orderByDesc('id')->get();
        $classes = DB::table('school_classes')->where('company_id', $cid)->orderBy('name')->get();
        $yearId = (int) $request->query('year_id', $years->first()?->id ?? 0);
        $classId = (int) $request->query('class_id', 0);
        $kind = $request->query('kind', 'sequence');
        $number = (int) $request->query('number', 1);

        abort_unless($years->contains('id', $yearId), 404, 'Année scolaire introuvable.');
        abort_unless(in_array($kind, ['sequence', 'trimester', 'annual'], true), 422);
        abort_unless($number >= 1 && $number <= ($kind === 'sequence' ? 6 : 3), 422);
        if ($kind === 'annual') $number = 1;
        if ($classId) abort_unless($classes->contains('id', $classId), 404, 'Classe introuvable.');

        return compact('cid', 'years', 'classes', 'yearId', 'classId', 'kind', 'number');
    }

    private function council(array $ctx): ?object
    {
        return DB::table('school_class_councils')
            ->where('company_id', $ctx['cid'])
            ->where('school_year_id', $ctx['yearId'])
            ->where('school_class_id', $ctx['classId'])
            ->where('period_kind', $ctx['kind'])
            ->where('period_number', $ctx['number'])
            ->first();
    }

    private function roster(array $ctx): \Illuminate\Support\Collection
    {
        if (!$ctx['classId']) return collect();

        $students = DB::table('school_enrollments as e')
            ->join('school_students as s', 's.id', '=', 'e.school_student_id')
            ->where('e.company_id', $ctx['cid'])
            ->where('s.company_id', $ctx['cid'])
            ->where('e.school_year_id', $ctx['yearId'])
            ->where('e.school_class_id', $ctx['classId'])
            ->where('e.status', 'enrolled')
            ->select('s.id', 's.first_name', 's.last_name', 's.student_number')
            ->orderBy('s.last_name')->orderBy('s.first_name')->get();

        // Utilise le même service de calcul que les tableaux d'honneur.
        $results = app(SchoolHonorsResults::class)->calculate(
            $ctx['cid'], $ctx['yearId'], $ctx['classId'], $ctx['kind'], $ctx['number']
        );
        $byStudent = collect($results)->keyBy(fn ($r) => (int) ($r->student_id ?? $r->school_student_id ?? $r->id ?? 0));

        return $students->map(function ($student) use ($byStudent) {
            $r = $byStudent->get((int) $student->id);
            $student->overall = $r->overall ?? null;
            $student->rank = $r->rank ?? null;
            return $student;
        });
    }

    public function index(Request $request)
    {
        $ctx = $this->context($request);
        $council = $ctx['classId'] ? $this->council($ctx) : null;
        $students = $this->roster($ctx);
        $decisions = $council ? DB::table('school_class_council_decisions')
            ->where('company_id', $ctx['cid'])
            ->where('school_class_council_id', $council->id)
            ->get()->keyBy('school_student_id') : collect();

        return view('company.school.councils.index', compact('ctx', 'council', 'students', 'decisions'));
    }

    public function save(Request $request)
    {
        $ctx = $this->context($request);
        abort_unless($ctx['classId'], 422, 'Sélectionnez une classe.');
        $validated = $request->validate([
            'general_observation' => ['nullable', 'string', 'max:10000'],
            'students' => ['required', 'array'],
            'students.*.distinction' => ['nullable', Rule::in(['none','honor','encouragement','congratulations','excellence'])],
            'students.*.decision' => ['nullable', Rule::in(['none','warning_work','warning_conduct','blame','promotion','repeat'])],
            'students.*.appreciation' => ['nullable', 'string', 'max:2000'],
        ]);
        $roster = $this->roster($ctx)->keyBy('id');
        $submitted = $validated['students'];
        foreach (array_keys($submitted) as $id) {
            abort_unless(ctype_digit((string) $id) && $roster->has((int) $id), 422, 'Élève non autorisé.');
        }

        DB::transaction(function () use ($ctx, $validated, $submitted, $roster) {
            $keys = [
                'company_id' => $ctx['cid'], 'school_year_id' => $ctx['yearId'],
                'school_class_id' => $ctx['classId'], 'period_kind' => $ctx['kind'],
                'period_number' => $ctx['number'],
            ];
            $existing = DB::table('school_class_councils')->where($keys)->lockForUpdate()->first();
            abort_if($existing && $existing->status === 'validated', 403, 'Conseil déjà validé.');
            if (!$existing) {
                $id = DB::table('school_class_councils')->insertGetId($keys + [
                    'status' => 'draft', 'general_observation' => $validated['general_observation'] ?? null,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            } else {
                $id = $existing->id;
                DB::table('school_class_councils')->where('id', $id)->update([
                    'general_observation' => $validated['general_observation'] ?? null, 'updated_at' => now(),
                ]);
            }
            foreach ($submitted as $studentId => $data) {
                $student = $roster->get((int) $studentId);
                DB::table('school_class_council_decisions')->updateOrInsert(
                    ['school_class_council_id' => $id, 'school_student_id' => (int) $studentId],
                    [
                        'company_id' => $ctx['cid'],
                        'overall_average' => $student->overall,
                        'class_rank' => $student->rank,
                        'distinction' => ($data['distinction'] ?? 'none') === 'none' ? null : $data['distinction'],
                        'decision' => $data['decision'] ?? 'none',
                        'appreciation' => $data['appreciation'] ?? null,
                        'updated_at' => now(),
                    ]
                );
            }
        });

        return redirect()->route('company.school.councils.index', $request->only(['year_id','class_id','kind','number']))
            ->with('success', 'Conseil enregistré en brouillon.');
    }

    public function validateCouncil(Request $request)
    {
        $ctx = $this->context($request);
        abort_unless($ctx['classId'], 422);
        DB::transaction(function () use ($ctx) {
            $council = DB::table('school_class_councils')->where([
                'company_id' => $ctx['cid'], 'school_year_id' => $ctx['yearId'],
                'school_class_id' => $ctx['classId'], 'period_kind' => $ctx['kind'],
                'period_number' => $ctx['number'],
            ])->lockForUpdate()->first();
            abort_unless($council, 404, 'Enregistrez le conseil avant validation.');
            abort_if($council->status === 'validated', 403, 'Déjà validé.');
            $count = DB::table('school_class_council_decisions')
                ->where('company_id', $ctx['cid'])->where('school_class_council_id', $council->id)->count();
            abort_if($count === 0, 422, 'Aucune décision enregistrée.');
            DB::table('school_class_councils')->where('id', $council->id)->update([
                'status' => 'validated', 'validated_at' => now(),
                'validated_by' => auth()->id(), 'updated_at' => now(),
            ]);
        });
        return redirect()->route('company.school.councils.index', $request->only(['year_id','class_id','kind','number']))
            ->with('success', 'Conseil validé. Les décisions sont désormais officielles.');
    }

    public function pv(Request $request)
    {
        $ctx = $this->context($request);
        abort_unless($ctx['classId'], 422);
        $council = $this->council($ctx);
        abort_unless($council && $council->status === 'validated', 404, 'PV disponible uniquement après validation.');
        $order = $request->query('order', 'merit');
        abort_unless(in_array($order, ['merit','alphabetical'], true), 422);
        $decisions = DB::table('school_class_council_decisions')
            ->where('company_id', $ctx['cid'])
            ->where('school_class_council_id', $council->id)->get()->keyBy('school_student_id');
        $students = $this->roster($ctx)->map(function ($student) use ($decisions) {
            $student->councilDecision = $decisions->get($student->id);
            return $student;
        });
        $students = $order === 'merit'
            ? $students->sort(fn ($a, $b) => ($b->overall ?? -1) <=> ($a->overall ?? -1))->values()
            : $students->sortBy(fn ($s) => mb_strtolower($s->last_name.' '.$s->first_name))->values();
        $stats = [
            'total' => $students->count(),
            'graded' => $students->filter(fn ($s) => $s->overall !== null)->count(),
            'passed' => $students->filter(fn ($s) => $s->overall !== null && $s->overall >= 10)->count(),
            'average' => $students->filter(fn ($s) => $s->overall !== null)->avg('overall'),
        ];
        return view('company.school.councils.pv', compact('ctx', 'council', 'students', 'order', 'stats'));
    }
}
