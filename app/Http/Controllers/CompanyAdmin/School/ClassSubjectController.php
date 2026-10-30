<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\SchoolClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClassSubjectController extends Controller
{
    private function companyId(): int
    {
        return (int) auth()->user()->company_id;
    }

    private function schoolClass(int $classId): SchoolClass
    {
        return SchoolClass::with(['year', 'level.section'])
            ->where('company_id', $this->companyId())
            ->findOrFail($classId);
    }

    private function checkWritable(SchoolClass $class): void
    {
        if (!$class->year || $class->year->is_archived) {
            throw ValidationException::withMessages([
                'school_class_id' => 'Impossible de modifier une classe sans année scolaire valide ou appartenant à une année archivée.',
            ]);
        }
    }

    public function index(int $classId)
    {
        $class = $this->schoolClass($classId);
        $companyId = $this->companyId();
        $sectionId = (int) $class->level->school_section_id;

        $subjects = DB::table('school_subjects')
            ->where('company_id', $companyId)
            ->where('section_id', $sectionId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $assignments = SchoolClassSubject::with(['subject', 'group'])
            ->where('company_id', $companyId)
            ->where('school_class_id', $class->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('company.school.classes.subjects', compact('class', 'subjects', 'assignments'));
    }

    private function validated(Request $request, SchoolClass $class, ?int $excludeId = null): array
    {
        $companyId = $this->companyId();
        $sectionId = (int) $class->level->school_section_id;

        $data = $request->validate([
            'school_subject_id' => [
                'required',
                'integer',
                Rule::exists('school_subjects', 'id')
                    ->where('company_id', $companyId)
                    ->where('section_id', $sectionId),
            ],
            'group_number' => ['required', 'integer', Rule::in([1, 2, 3])],
            'coefficient' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['required', 'boolean'],
        ]);

        $duplicate = SchoolClassSubject::query()
            ->where('company_id', $companyId)
            ->where('school_class_id', $class->id)
            ->where('school_subject_id', $data['school_subject_id']);

        if ($excludeId !== null) {
            $duplicate->where('id', '!=', $excludeId);
        }

        if ($duplicate->exists()) {
            throw ValidationException::withMessages([
                'school_subject_id' => 'Cette matière est déjà configurée pour cette classe.',
            ]);
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        // Le groupe numérique est indépendant de la table school_subject_groups.
        // L'ancienne clé étrangère reste vide pour les nouvelles configurations.
        $data['school_subject_group_id'] = null;

        return $data;
    }

    public function store(Request $request, int $classId)
    {
        $class = $this->schoolClass($classId);
        $this->checkWritable($class);
        $data = $this->validated($request, $class);

        $assignment = new SchoolClassSubject();
        $assignment->forceFill(array_merge($data, [
            'company_id' => $this->companyId(),
            'school_class_id' => $class->id,
        ]));
        $assignment->save();

        return back()->with('success', 'Matière ajoutée à la classe.');
    }

    public function update(Request $request, int $classId, int $id)
    {
        $class = $this->schoolClass($classId);
        $this->checkWritable($class);

        $assignment = SchoolClassSubject::query()
            ->where('company_id', $this->companyId())
            ->where('school_class_id', $class->id)
            ->findOrFail($id);

        $assignment->forceFill($this->validated($request, $class, $assignment->id));
        $assignment->save();

        return back()->with('success', 'Configuration mise à jour.');
    }

    public function destroy(int $classId, int $id)
    {
        $class = $this->schoolClass($classId);
        $this->checkWritable($class);

        SchoolClassSubject::query()
            ->where('company_id', $this->companyId())
            ->where('school_class_id', $class->id)
            ->findOrFail($id)
            ->delete();

        return back()->with('success', 'Matière retirée de la classe.');
    }
}
