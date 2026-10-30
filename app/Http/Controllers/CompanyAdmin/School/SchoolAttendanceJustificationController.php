<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Services\SchoolPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SchoolAttendanceJustificationController extends Controller
{
    private function cid(): int { return (int) auth()->user()->company_id; }

    private function canManage(): bool
    {
        return app(SchoolPermissionService::class)->allowed(auth()->user(), 'school.attendance.manage');
    }

    private function canReview(): bool
    {
        return in_array(auth()->user()->role, ['companyadmin', 'school_admin', 'school_principal', 'school_director', 'school_supervisor', 'school_sector_supervisor'], true)
            && $this->canManage();
    }

    private function teacherId(): ?int
    {
        return DB::table('school_teachers')->where('company_id', $this->cid())
            ->where('user_id', auth()->id())->where('status', 'active')->value('id');
    }

    private function scopedAttendances()
    {
        $q = DB::table('school_attendances as a')
            ->join('school_attendance_sessions as ss', function ($join) {
                $join->on('ss.id', '=', 'a.school_attendance_session_id')
                    ->on('ss.company_id', '=', 'a.company_id');
            })
            ->join('school_students as st', function ($join) {
                $join->on('st.id', '=', 'a.school_student_id')->on('st.company_id', '=', 'a.company_id');
            })
            ->join('school_classes as c', function ($join) {
                $join->on('c.id', '=', 'ss.school_class_id')->on('c.company_id', '=', 'ss.company_id');
            })
            ->where('a.company_id', $this->cid())
            ->whereIn('a.status', ['absent', 'late', 'excused']);

        if (auth()->user()->role === 'school_teacher') {
            $teacherId = $this->teacherId();
            abort_unless($teacherId, 403, 'Profil enseignant actif introuvable.');
            $q->whereExists(function ($sub) use ($teacherId) {
                $sub->selectRaw('1')->from('school_teacher_assignments as ta')
                    ->whereColumn('ta.company_id', 'ss.company_id')
                    ->whereColumn('ta.class_id', 'ss.school_class_id')
                    ->whereColumn('ta.school_year_id', 'ss.school_year_id')
                    ->where('ta.teacher_id', $teacherId)->where('ta.is_active', 1);
            });
        }
        return $q;
    }

    private function attendance(int $id): object
    {
        $row = $this->scopedAttendances()->where('a.id', $id)
            ->select('a.id', 'a.status', 'a.school_student_id', 'ss.attendance_date', 'c.name as class_name', 'st.last_name', 'st.first_name')
            ->first();
        abort_unless($row, 404);
        return $row;
    }

    public function index(Request $request)
    {
        abort_unless($this->canManage(), 403);
        $status = $request->validate(['status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'none'])]])['status'] ?? null;
        $q = $this->scopedAttendances()
            ->leftJoin('school_attendance_justifications as j', function ($join) {
                $join->on('j.school_attendance_id', '=', 'a.id')->on('j.company_id', '=', 'a.company_id');
            })
            ->select('a.id', 'a.status as attendance_status', 'ss.attendance_date', 'c.name as class_name',
                'st.last_name', 'st.first_name', 'j.status as justification_status', 'j.reason', 'j.attachment_path', 'j.review_note');
        if ($status === 'none') $q->whereNull('j.id');
        elseif ($status) $q->where('j.status', $status);
        $rows = $q->orderByDesc('ss.attendance_date')->orderByDesc('a.id')->paginate(25)->withQueryString();
        $canReview = $this->canReview();
        return view('company.school.attendance.justifications', compact('rows', 'status', 'canReview'));
    }

    public function submit(Request $request, int $id)
    {
        abort_unless($this->canManage(), 403);
        $this->attendance($id);
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);
        $existing = DB::table('school_attendance_justifications')
            ->where('company_id', $this->cid())->where('school_attendance_id', $id)->first();
        if ($existing && $existing->status !== 'pending') {
            throw ValidationException::withMessages(['reason' => 'Une justification déjà examinée ne peut pas être modifiée.']);
        }
        $path = $existing->attachment_path ?? null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('school/attendance/justifications/' . $this->cid(), 'local');
        }
        DB::table('school_attendance_justifications')->updateOrInsert(
            ['company_id' => $this->cid(), 'school_attendance_id' => $id],
            ['reason' => $data['reason'], 'attachment_path' => $path, 'status' => 'pending',
                'submitted_by' => auth()->id(), 'updated_at' => now(), 'created_at' => $existing->created_at ?? now()]
        );
        if ($existing && $request->hasFile('attachment') && $existing->attachment_path) {
            Storage::disk('local')->delete($existing->attachment_path);
        }
        return back()->with('success', 'Justificatif soumis pour validation.');
    }

    public function review(Request $request, int $id)
    {
        abort_unless($this->canReview(), 403, 'Validation réservée à la direction et à la surveillance.');
        $this->attendance($id);
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approved', 'rejected'])],
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);
        DB::transaction(function () use ($id, $data) {
            $just = DB::table('school_attendance_justifications')
                ->where('company_id', $this->cid())->where('school_attendance_id', $id)
                ->lockForUpdate()->first();
            abort_unless($just, 404, 'Aucun justificatif soumis.');
            if ($just->status !== 'pending') {
                throw ValidationException::withMessages(['decision' => 'Ce justificatif a déjà été traité.']);
            }
            DB::table('school_attendance_justifications')->where('id', $just->id)
                ->where('company_id', $this->cid())->update([
                    'status' => $data['decision'], 'review_note' => $data['review_note'] ?? null,
                    'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'updated_at' => now(),
                ]);
            // Ne pas réécrire l'appel original : le statut « excused » reste indépendant de la décision.
        });
        return back()->with('success', 'Décision enregistrée.');
    }
}
