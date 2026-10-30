<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SchoolTeacherAssignmentController extends Controller
{
    private function companyId(): int { return (int) auth()->user()->company_id; }

    private function ownedTeacher(int $id): object
    {
        return DB::table('school_teachers')->where('company_id', $this->companyId())->where('id', $id)->firstOrFail();
    }

    private function classOptions()
    {
        return DB::table('school_classes as c')
            ->join('school_levels as l', 'l.id', '=', 'c.school_level_id')
            ->join('school_years as y', 'y.id', '=', 'c.school_year_id')
            ->where('c.company_id', $this->companyId())
            ->where('l.company_id', $this->companyId())
            ->where('y.company_id', $this->companyId())
            ->orderByDesc('y.starts_on')->orderBy('c.name')
            ->get(['c.id', 'c.name', 'c.school_year_id', 'l.school_section_id', 'y.name as year_name', 'y.is_archived']);
    }

    private function configuredSubjects()
    {
        return DB::table('school_class_subjects as cs')
            ->join('school_subjects as s', 's.id', '=', 'cs.school_subject_id')
            ->join('school_classes as c', 'c.id', '=', 'cs.school_class_id')
            ->where('cs.company_id', $this->companyId())
            ->where('s.company_id', $this->companyId())
            ->where('c.company_id', $this->companyId())
            ->where('cs.is_active', true)
            ->where('s.is_active', true)
            ->orderBy('cs.sort_order')->orderBy('s.name')
            ->get(['cs.school_class_id as class_id', 'cs.school_subject_id as subject_id', 's.name', 'cs.coefficient']);
    }

    private function teacherAssignments(int $teacherId)
    {
        return DB::table('school_teacher_assignments as a')
            ->join('school_classes as c', 'c.id', '=', 'a.class_id')
            ->join('school_subjects as s', 's.id', '=', 'a.subject_id')
            ->join('school_years as y', 'y.id', '=', 'a.school_year_id')
            ->where('a.company_id', $this->companyId())
            ->where('c.company_id', $this->companyId())
            ->where('s.company_id', $this->companyId())
            ->where('y.company_id', $this->companyId())
            ->where('a.teacher_id', $teacherId)
            ->orderByDesc('a.id')
            ->get(['a.id', 'a.class_id', 'a.subject_id', 'a.school_year_id', 'a.is_active', 'c.name as class_name', 's.name as subject_name', 'y.name as year_name']);
    }

    public function index(Request $request)
    {
        $items = SchoolTeacherAssignment::where('company_id', $this->companyId())->latest('id')->paginate(20);
        $teachers = DB::table('school_teachers')->where('company_id', $this->companyId())->get(['id', 'first_name', 'last_name']);
        $classes = $this->classOptions();
        $subjects = DB::table('school_subjects')->where('company_id', $this->companyId())->get(['id', 'name']);
        return view('company.school.pedagogy.index', compact('items', 'teachers', 'classes', 'subjects') + ['type' => 'assignments']);
    }

    public function teacher(int $teacherId)
    {
        $teacher = $this->ownedTeacher($teacherId);
        $classes = $this->classOptions();
        $configuredSubjects = $this->configuredSubjects();
        $assignments = $this->teacherAssignments($teacherId);
        return view('company.school.pedagogy.teacher-assignments', compact('teacher', 'classes', 'configuredSubjects', 'assignments'));
    }

    public function create()
    {
        $teachers = DB::table('school_teachers')->where('company_id', $this->companyId())->where('status', 'active')->orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        return view('company.school.pedagogy.choose-teacher', compact('teachers'));
    }

    public function edit(int $id)
    {
        $assignment = SchoolTeacherAssignment::where('company_id', $this->companyId())->findOrFail($id);
        return redirect()->route('company.school.assignments.teacher', $assignment->teacher_id);
    }

    private function validated(Request $request): array
    {
        $companyId = $this->companyId();
        $data = $request->validate([
            'teacher_id' => ['required', 'integer', Rule::exists('school_teachers', 'id')->where('company_id', $companyId)],
            'class_id' => ['required', 'integer', Rule::exists('school_classes', 'id')->where('company_id', $companyId)],
            'subject_id' => ['required', 'integer', Rule::exists('school_subjects', 'id')->where('company_id', $companyId)],
            'is_active' => ['required', 'boolean'],
        ]);
        $teacher = $this->ownedTeacher((int) $data['teacher_id']);
        if ($teacher->status !== 'active') {
            throw ValidationException::withMessages(['teacher_id' => 'Cet enseignant n’est pas actif.']);
        }
        $class = DB::table('school_classes as c')
            ->join('school_levels as l', 'l.id', '=', 'c.school_level_id')
            ->join('school_years as y', 'y.id', '=', 'c.school_year_id')
            ->where('c.company_id', $companyId)->where('l.company_id', $companyId)->where('y.company_id', $companyId)
            ->where('c.id', $data['class_id'])
            ->first(['c.school_year_id', 'l.school_section_id', 'y.is_archived']);
        if (!$class || $class->is_archived) {
            throw ValidationException::withMessages(['class_id' => 'Classe introuvable ou année scolaire archivée.']);
        }
        $configured = DB::table('school_class_subjects as cs')
            ->join('school_subjects as s', 's.id', '=', 'cs.school_subject_id')
            ->where('cs.company_id', $companyId)->where('s.company_id', $companyId)
            ->where('cs.school_class_id', $data['class_id'])
            ->where('cs.school_subject_id', $data['subject_id'])
            ->where('cs.is_active', true)->where('s.is_active', true)
            ->where('s.section_id', $class->school_section_id)
            ->exists();
        if (!$configured) {
            throw ValidationException::withMessages(['subject_id' => 'Cette matière doit d’abord être configurée et activée pour cette classe.']);
        }
        $data['school_year_id'] = $class->school_year_id;
        return $data;
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $duplicate = SchoolTeacherAssignment::where('company_id', $this->companyId())
            ->where('teacher_id', $data['teacher_id'])->where('class_id', $data['class_id'])
            ->where('subject_id', $data['subject_id'])->where('school_year_id', $data['school_year_id'])->exists();
        if ($duplicate) throw ValidationException::withMessages(['subject_id' => 'Cette affectation existe déjà pour cet enseignant.']);
        SchoolTeacherAssignment::create(['company_id' => $this->companyId()] + $data);
        return redirect()->route('company.school.assignments.teacher', $data['teacher_id'])->with('success', 'Affectation enregistrée.');
    }

    public function update(Request $request, int $id)
    {
        $assignment = SchoolTeacherAssignment::where('company_id', $this->companyId())->findOrFail($id);
        $data = $this->validated($request);
        $duplicate = SchoolTeacherAssignment::where('company_id', $this->companyId())->whereKeyNot($id)
            ->where('teacher_id', $data['teacher_id'])->where('class_id', $data['class_id'])
            ->where('subject_id', $data['subject_id'])->where('school_year_id', $data['school_year_id'])->exists();
        if ($duplicate) throw ValidationException::withMessages(['subject_id' => 'Cette affectation existe déjà.']);
        $assignment->update($data);
        return redirect()->route('company.school.assignments.teacher', $data['teacher_id'])->with('success', 'Affectation mise à jour.');
    }

    public function destroy(int $id)
    {
        $assignment = SchoolTeacherAssignment::where('company_id', $this->companyId())->findOrFail($id);
        $teacherId = $assignment->teacher_id;
        $assignment->delete();
        return redirect()->route('company.school.assignments.teacher', $teacherId)->with('success', 'Affectation supprimée.');
    }
}
