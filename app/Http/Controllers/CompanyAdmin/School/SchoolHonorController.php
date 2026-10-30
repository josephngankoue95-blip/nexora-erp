<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Services\School\SchoolHonorsResults;
use App\Services\School\SchoolCouncilDecisions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SchoolHonorController extends Controller
{
    private function cid(): int { return (int)auth()->user()->company_id; }

    private function settings(): object
    {
        return DB::table('school_honor_settings')->where('company_id', $this->cid())->first()
            ?? (object)['honor_min'=>12, 'encouragement_min'=>14, 'congratulation_min'=>16, 'excellence_min'=>18];
    }

    private function context(Request $request): array
    {
        $cid = $this->cid();
        $years = DB::table('school_years')->where('company_id', $cid)->orderByDesc('starts_on')->get();
        $default = $years->firstWhere('is_active', 1) ?? $years->first();
        $yearId = (int)$request->query('year_id', $default?->id ?? 0);
        $year = $yearId ? DB::table('school_years')->where('company_id', $cid)->where('id', $yearId)->firstOrFail() : null;
        $classes = $year ? DB::table('school_classes')->where('company_id', $cid)->where('school_year_id', $yearId)->orderBy('name')->get() : collect();
        $classId = (int)$request->query('class_id', 0);
        $class = $classId ? DB::table('school_classes')->where('company_id', $cid)->where('school_year_id', $yearId)->where('id', $classId)->firstOrFail() : null;
        $config = $year ? DB::table('school_grading_configs')->where('company_id', $cid)->where('school_year_id', $yearId)->first() : null;
        $kind = (string)$request->query('kind', 'trimester');
        $number = (int)$request->query('number', 1);
        abort_unless(in_array($kind, ['sequence', 'trimester', 'annual'], true), 422);
        abort_unless($kind === 'annual' || ($number >= 1 && $number <= ($kind === 'sequence' ? 6 : 3)), 422);
        if ($config && $config->mode === 'trimesters') abort_if($kind === 'sequence', 422);
        $periods = $year ? DB::table('school_evaluation_periods')->where('company_id', $cid)->where('school_year_id', $yearId)->orderBy('number')->get() : collect();
        return compact('years', 'year', 'classes', 'class', 'config', 'kind', 'number', 'periods');
    }

    private function label(object $settings, ?float $average): ?string
    {
        if ($average === null) return null;
        if ($average >= (float)$settings->excellence_min) return 'Excellence';
        if ($average >= (float)$settings->congratulation_min) return 'Félicitations';
        if ($average >= (float)$settings->encouragement_min) return 'Encouragements';
        if ($average >= (float)$settings->honor_min) return "Tableau d'honneur";
        return null;
    }

    public function index(Request $request, SchoolHonorsResults $calculator, SchoolCouncilDecisions $councilDecisions)
    {
        $ctx = $this->context($request);
        $settings = $this->settings();
        $students = collect();
        $councilValidated = false;
        if ($ctx['class'] && $ctx['config']) {
            $official = $councilDecisions->validatedCouncil($this->cid(), (int)$ctx['year']->id, (int)$ctx['class']->id, $ctx['kind'], $ctx['number']);
            $councilValidated = (bool) $official;
            $approved = $councilDecisions->forClass($this->cid(), (int)$ctx['year']->id, (int)$ctx['class']->id, $ctx['kind'], $ctx['number']);
            $students = $calculator->calculate($this->cid(), (int)$ctx['year']->id, (int)$ctx['class']->id, $ctx['kind'], $ctx['number'])
                ->map(function ($student) use ($settings, $approved, $councilValidated, $councilDecisions) {
                    $student->proposed_distinction = $this->label($settings, $student->overall);
                    $student->official_decision = $councilValidated ? $councilDecisions->label($approved->get($student->id)?->decision) : null;
                    $student->distinction = $councilValidated ? (in_array($approved->get($student->id)?->decision, ['felicitations','encouragements','tableau_honneur'], true) ? $student->official_decision : null) : $student->proposed_distinction;
                    return $student;
                })->sort(function ($a, $b) {
                    if ($a->overall === null) return $b->overall === null ? strcmp($a->last_name, $b->last_name) : 1;
                    if ($b->overall === null) return -1;
                    return ($b->overall <=> $a->overall) ?: strcmp($a->last_name, $b->last_name);
                })->values();
        }
        return view('company.school.honors.index', $ctx + compact('settings', 'students', 'councilValidated'));
    }

    public function print(Request $request, SchoolHonorsResults $calculator, SchoolCouncilDecisions $councilDecisions)
    {
        $ctx = $this->context($request);
        abort_unless($ctx['class'] && $ctx['config'], 422, 'Choisissez une classe et configurez les notes.');
        $settings = $this->settings();
        $official = $councilDecisions->validatedCouncil($this->cid(), (int)$ctx['year']->id, (int)$ctx['class']->id, $ctx['kind'], $ctx['number']);
        abort_unless($official, 403, 'Le conseil doit être validé avant de délivrer les tableaux d’honneur officiels.');
        $approved = $councilDecisions->forClass($this->cid(), (int)$ctx['year']->id, (int)$ctx['class']->id, $ctx['kind'], $ctx['number']);
        $students = $calculator->calculate($this->cid(), (int)$ctx['year']->id, (int)$ctx['class']->id, $ctx['kind'], $ctx['number'])
            ->filter(fn ($s) => in_array($approved->get($s->id)?->decision, ['felicitations','encouragements','tableau_honneur'], true))
            ->map(function ($s) use ($approved, $councilDecisions) {
                $s->distinction = $councilDecisions->label($approved->get($s->id)?->decision);
                return $s;
            })->sortBy('rank')->values();
        $company = DB::table('companies')->where('id', $this->cid())->firstOrFail();
        return view('company.school.honors.print', $ctx + compact('settings', 'students', 'company'));
    }

    public function settingsForm()
    {
        $settings = $this->settings();
        return view('company.school.honors.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'honor_min' => ['required', 'numeric', 'between:0,20'],
            'encouragement_min' => ['required', 'numeric', 'between:0,20'],
            'congratulation_min' => ['required', 'numeric', 'between:0,20'],
            'excellence_min' => ['required', 'numeric', 'between:0,20'],
        ]);
        if (!((float)$data['honor_min'] < (float)$data['encouragement_min'] &&
            (float)$data['encouragement_min'] < (float)$data['congratulation_min'] &&
            (float)$data['congratulation_min'] < (float)$data['excellence_min'])) {
            return back()->withErrors(['honor_min' => 'Les quatre seuils doivent être strictement croissants.'])->withInput();
        }
        DB::table('school_honor_settings')->updateOrInsert(['company_id' => $this->cid()], $data + ['updated_at' => now(), 'created_at' => DB::table('school_honor_settings')->where('company_id', $this->cid())->value('created_at') ?? now()]);
        return redirect()->route('company.school.honors.settings')->with('success', 'Seuils enregistrés.');
    }
}
