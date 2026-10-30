<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\{SchoolEnrollment, SchoolStudent, SchoolYear, SchoolClass, SchoolLevel, SchoolSection};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    private function cid(Request $request): int
    {
        return (int) $request->user()->company_id;
    }

    public function index(Request $request)
    {
        $cid = $this->cid($request);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'year_id' => ['nullable', 'integer'],
            'section_id' => ['nullable', 'integer'],
            'level_id' => ['nullable', 'integer'],
            'class_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:enrolled,transferred,withdrawn'],
            'sort' => ['nullable', 'in:recent,name,oldest'],
        ]);

        $query = SchoolEnrollment::query()
            ->where('company_id', $cid)
            ->with(['student', 'year', 'schoolClass.level.section']);

        if ($request->filled('q')) {
            $term = trim($filters['q']);
            $query->whereHas('student', function ($q) use ($term, $cid) {
                $q->where('company_id', $cid)->where(function ($inner) use ($term) {
                    $inner->where('student_number', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%");
                });
            });
        }
        if ($request->filled('year_id')) $query->where('school_year_id', $filters['year_id']);
        if ($request->filled('class_id')) $query->where('school_class_id', $filters['class_id']);
        if ($request->filled('status')) $query->where('status', $filters['status']);
        if ($request->filled('level_id') || $request->filled('section_id')) {
            $query->whereHas('schoolClass', function ($q) use ($filters, $cid, $request) {
                $q->where('company_id', $cid);
                if ($request->filled('level_id')) $q->where('school_level_id', $filters['level_id']);
                if ($request->filled('section_id')) {
                    $q->whereHas('level', fn ($level) => $level->where('company_id', $cid)
                        ->where('school_section_id', $filters['section_id']));
                }
            });
        }

        switch ($filters['sort'] ?? 'recent') {
            case 'name':
                $query->orderBy(
                    SchoolStudent::select('last_name')->whereColumn('school_students.id', 'school_enrollments.school_student_id')->limit(1)
                )->orderBy('id');
                break;
            case 'oldest': $query->orderBy('enrolled_on')->orderBy('id'); break;
            default: $query->orderByDesc('enrolled_on')->orderByDesc('id');
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $years = SchoolYear::where('company_id', $cid)->orderByDesc('starts_on')->get();
        $sections = SchoolSection::where('company_id', $cid)->orderBy('name')->get();
        $levels = SchoolLevel::where('company_id', $cid)->orderBy('sort_order')->orderBy('name')->get();
        $classes = SchoolClass::where('company_id', $cid)->orderBy('name')->get();

        return view('company.school.enrollments.index', compact('enrollments', 'years', 'sections', 'levels', 'classes'));
    }

    public function create(Request $request)
    {
        $cid = $this->cid($request);
        $students = SchoolStudent::where('company_id', $cid)->orderBy('last_name')->orderBy('first_name')->get();
        $years = SchoolYear::where('company_id', $cid)->where('is_archived', false)->orderByDesc('starts_on')->get();
        $classes = SchoolClass::with('level.section')->where('company_id', $cid)->orderBy('name')->get();
        return view('company.school.enrollments.create', compact('students', 'years', 'classes'));
    }

    public function store(Request $request)
    {
        $cid = $this->cid($request);
        $data = $request->validate([
            'school_student_id' => ['required', 'integer'],
            'school_year_id' => ['required', 'integer'],
            'school_class_id' => ['required', 'integer'],
            'enrolled_on' => ['required', 'date'],
            'status' => ['required', 'in:enrolled,transferred,withdrawn'],
        ]);
        $student = SchoolStudent::where('company_id', $cid)->findOrFail($data['school_student_id']);
        $year = SchoolYear::where('company_id', $cid)->where('is_archived', false)->findOrFail($data['school_year_id']);
        SchoolClass::where('company_id', $cid)->where('school_year_id', $year->id)->findOrFail($data['school_class_id']);
        if (SchoolEnrollment::where('company_id', $cid)->where('school_student_id', $student->id)
            ->where('school_year_id', $year->id)->exists()) {
            throw ValidationException::withMessages(['school_student_id' => 'Cet élève possède déjà une inscription pour cette année scolaire.']);
        }
        DB::transaction(fn () => SchoolEnrollment::create($data + ['company_id' => $cid]));
        return redirect()->route('company.school.enrollments.index')->with('success', 'Inscription enregistrée.');
    }
}
