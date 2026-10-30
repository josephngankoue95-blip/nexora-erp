<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Services\SchoolPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SchoolAttendanceController extends Controller
{
    private function cid(): int { return (int) auth()->user()->company_id; }

    private function isTeacher(): bool { return auth()->user()->role === 'school_teacher'; }

    private function ensureAccess(): void
    {
        abort_unless(app(SchoolPermissionService::class)->allowed(auth()->user(), 'school.attendance.manage'), 403);
    }

    private function teacherId(): int
    {
        $id = DB::table('school_teachers')->where('company_id', $this->cid())
            ->where('user_id', auth()->id())->where('status', 'active')->value('id');
        abort_unless($id, 403, 'Profil enseignant actif introuvable.');
        return (int) $id;
    }

    private function permittedClasses(int $yearId)
    {
        $query = DB::table('school_classes')->where('company_id', $this->cid())
            ->where('school_year_id', $yearId);
        if ($this->isTeacher()) {
            $teacherId = $this->teacherId();
            $query->whereIn('id', DB::table('school_teacher_assignments')
                ->where('company_id', $this->cid())->where('teacher_id', $teacherId)
                ->where('school_year_id', $yearId)->where('is_active', 1)
                ->select('class_id'));
        }
        return $query->orderBy('name')->get();
    }

    private function selectedClass(int $classId, int $yearId): object
    {
        $class = $this->permittedClasses($yearId)->firstWhere('id', $classId);
        abort_unless($class, 403, 'Classe non autorisée.');
        return $class;
    }

    private function enrolledStudents(int $yearId, int $classId)
    {
        return DB::table('school_enrollments as e')
            ->join('school_students as s', function ($join) {
                $join->on('s.id', '=', 'e.school_student_id')
                    ->on('s.company_id', '=', 'e.company_id');
            })
            ->where('e.company_id', $this->cid())
            ->where('e.school_year_id', $yearId)
            ->where('e.school_class_id', $classId)
            ->where('e.status', 'enrolled')
            ->select('s.id', 's.student_number', 's.last_name', 's.first_name')
            ->distinct()->orderBy('s.last_name')->orderBy('s.first_name')->get();
    }

    public function index(Request $request)
    {
        $this->ensureAccess();
        $years = DB::table('school_years')->where('company_id', $this->cid())
            ->orderByDesc('starts_on')->get();
        $defaultYear = $years->firstWhere('is_active', 1) ?? $years->first();
        $yearId = (int) $request->query('year_id', $defaultYear->id ?? 0);
        $year = $yearId ? $years->firstWhere('id', $yearId) : null;
        abort_if($yearId && !$year, 404);
        $classes = $year ? $this->permittedClasses($yearId) : collect();
        $classId = (int) $request->query('class_id', 0);
        $class = $classId ? $this->selectedClass($classId, $yearId) : null;
        $date = $request->validate(['date' => ['nullable','date_format:Y-m-d']])['date'] ?? now()->toDateString();
        $students = $class ? $this->enrolledStudents($yearId, $classId) : collect();
        $session = $class ? DB::table('school_attendance_sessions')
            ->where('company_id', $this->cid())->where('school_year_id', $yearId)
            ->where('school_class_id', $classId)->where('attendance_date', $date)
            ->where('session_label', 'journee')->first() : null;
        $entries = $session ? DB::table('school_attendances')
            ->where('company_id', $this->cid())->where('school_attendance_session_id', $session->id)
            ->get()->keyBy('school_student_id') : collect();
        $history = $class ? DB::table('school_attendance_sessions as a')
            ->leftJoin('school_attendances as d', function ($join) {
                $join->on('d.school_attendance_session_id', '=', 'a.id')
                    ->on('d.company_id', '=', 'a.company_id');
            })
            ->where('a.company_id', $this->cid())->where('a.school_year_id', $yearId)
            ->where('a.school_class_id', $classId)
            ->groupBy('a.id', 'a.attendance_date')
            ->select('a.id', 'a.attendance_date',
                DB::raw("SUM(CASE WHEN d.status = 'absent' THEN 1 ELSE 0 END) as absent_count"),
                DB::raw("SUM(CASE WHEN d.status = 'late' THEN 1 ELSE 0 END) as late_count"))
            ->orderByDesc('a.attendance_date')->limit(20)->get() : collect();
        return view('company.school.attendance.index', compact(
            'years','year','classes','class','date','students','session','entries','history'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureAccess();
        $data = $request->validate([
            'school_year_id' => ['required','integer', Rule::exists('school_years','id')->where('company_id', $this->cid())],
            'school_class_id' => ['required','integer'],
            'attendance_date' => ['required','date_format:Y-m-d','before_or_equal:today'],
            'attendance' => ['required','array'],
            'attendance.*.status' => ['required',Rule::in(['present','absent','late','excused'])],
            'attendance.*.minutes_late' => ['nullable','integer','min:0','max:1440'],
            'attendance.*.reason' => ['nullable','string','max:500'],
            'notes' => ['nullable','string','max:2000'],
        ]);
        $yearId = (int) $data['school_year_id'];
        $classId = (int) $data['school_class_id'];
        $year = DB::table('school_years')->where('company_id', $this->cid())->where('id', $yearId)->firstOrFail();
        if ($year->is_archived) {
            throw ValidationException::withMessages(['school_year_id' => 'Année scolaire archivée.']);
        }
        $this->selectedClass($classId, $yearId);
        $students = $this->enrolledStudents($yearId, $classId);
        $ids = $students->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all();
        $submitted = array_keys($data['attendance']);
        $submitted = array_map('strval', $submitted);
        sort($submitted);
        if ($ids !== $submitted) {
            throw ValidationException::withMessages(['attendance' => 'L’appel doit inclure exactement les élèves inscrits dans cette classe.']);
        }
        DB::transaction(function () use ($data, $yearId, $classId) {
            $key = [
                'company_id' => $this->cid(), 'school_year_id' => $yearId,
                'school_class_id' => $classId, 'attendance_date' => $data['attendance_date'],
                'session_label' => 'journee',
            ];
            DB::table('school_attendance_sessions')->upsert([
                $key + ['recorded_by' => auth()->id(), 'notes' => $data['notes'] ?? null,
                    'created_at' => now(), 'updated_at' => now()],
            ], ['company_id','school_class_id','attendance_date','session_label'], ['recorded_by','notes','updated_at']);
            $sessionId = DB::table('school_attendance_sessions')->where($key)->value('id');
            foreach ($data['attendance'] as $studentId => $entry) {
                $status = $entry['status'];
                $rowKey = ['company_id' => $this->cid(), 'school_attendance_session_id' => $sessionId,
                    'school_student_id' => (int) $studentId];
                DB::table('school_attendances')->updateOrInsert($rowKey, [
                    'status' => $status,
                    'minutes_late' => $status === 'late' ? ($entry['minutes_late'] ?? null) : null,
                    'reason' => $entry['reason'] ?? null,
                    'recorded_by' => auth()->id(), 'updated_at' => now(),
                    'created_at' => DB::table('school_attendances')->where($rowKey)->value('created_at') ?? now(),
                ]);
            }
        });
        return redirect()->route('company.school.attendance.index', [
            'year_id' => $yearId, 'class_id' => $classId, 'date' => $data['attendance_date'],
        ])->with('success', 'Appel enregistré.');
    }
}
