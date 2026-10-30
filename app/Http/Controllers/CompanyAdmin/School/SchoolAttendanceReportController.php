<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Services\SchoolPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchoolAttendanceReportController extends Controller
{
    private function companyId(): int
    {
        return (int) auth()->user()->company_id;
    }

    private function checkAccess(): void
    {
        abort_unless(app(SchoolPermissionService::class)->allowed(auth()->user(), 'school.attendance.manage'), 403);
    }

    private function teacherId(): int
    {
        $id = DB::table('school_teachers')->where('company_id', $this->companyId())
            ->where('user_id', auth()->id())->where('status', 'active')->value('id');
        abort_unless($id, 403, 'Profil enseignant actif introuvable.');
        return (int) $id;
    }

    private function classes(int $yearId)
    {
        $query = DB::table('school_classes')->where('company_id', $this->companyId())
            ->where('school_year_id', $yearId);
        if (auth()->user()->role === 'school_teacher') {
            $teacherId = $this->teacherId();
            $query->whereExists(function ($q) use ($teacherId, $yearId) {
                $q->selectRaw('1')->from('school_teacher_assignments as ta')
                    ->whereColumn('ta.class_id', 'school_classes.id')
                    ->whereColumn('ta.company_id', 'school_classes.company_id')
                    ->where('ta.school_year_id', $yearId)->where('ta.teacher_id', $teacherId)
                    ->where('ta.is_active', 1);
            });
        }
        return $query->orderBy('name')->get();
    }

    private function filters(Request $request): array
    {
        $data = $request->validate([
            'year_id' => ['nullable', 'integer', Rule::exists('school_years', 'id')->where('company_id', $this->companyId())],
            'class_id' => ['nullable', 'integer'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
        $years = DB::table('school_years')->where('company_id', $this->companyId())->orderByDesc('starts_on')->get();
        $yearId = (int) ($data['year_id'] ?? ($years->firstWhere('is_active', 1)->id ?? $years->first()->id ?? 0));
        $classes = $yearId ? $this->classes($yearId) : collect();
        $classId = (int) ($data['class_id'] ?? 0);
        if ($classId) abort_unless($classes->contains('id', $classId), 403, 'Classe non autorisée.');
        return compact('years', 'yearId', 'classes', 'classId') + [
            'from' => $data['from'] ?? now()->startOfMonth()->toDateString(),
            'to' => $data['to'] ?? now()->toDateString(),
        ];
    }

    private function records(array $f)
    {
        $query = DB::table('school_attendances as a')
            ->join('school_attendance_sessions as ss', function ($join) {
                $join->on('ss.id', '=', 'a.school_attendance_session_id')->on('ss.company_id', '=', 'a.company_id');
            })
            ->join('school_students as st', function ($join) {
                $join->on('st.id', '=', 'a.school_student_id')->on('st.company_id', '=', 'a.company_id');
            })
            ->join('school_classes as c', function ($join) {
                $join->on('c.id', '=', 'ss.school_class_id')->on('c.company_id', '=', 'ss.company_id');
            })
            ->leftJoin('school_attendance_justifications as j', function ($join) {
                $join->on('j.school_attendance_id', '=', 'a.id')->on('j.company_id', '=', 'a.company_id');
            })
            ->where('a.company_id', $this->companyId())
            ->where('ss.school_year_id', $f['yearId'])
            ->whereBetween('ss.attendance_date', [$f['from'], $f['to']]);
        if ($f['classId']) $query->where('ss.school_class_id', $f['classId']);
        if (auth()->user()->role === 'school_teacher') {
            $teacherId = $this->teacherId();
            $query->whereExists(function ($q) use ($teacherId) {
                $q->selectRaw('1')->from('school_teacher_assignments as ta')
                    ->whereColumn('ta.company_id', 'ss.company_id')
                    ->whereColumn('ta.class_id', 'ss.school_class_id')
                    ->whereColumn('ta.school_year_id', 'ss.school_year_id')
                    ->where('ta.teacher_id', $teacherId)->where('ta.is_active', 1);
            });
        }
        return $query->select('ss.attendance_date', 'c.name as class_name', 'st.student_number',
            'st.last_name', 'st.first_name', 'a.status', 'a.minutes_late', 'a.reason',
            'j.status as justification_status');
    }

    public function index(Request $request)
    {
        $this->checkAccess();
        $f = $this->filters($request);
        $summary = (clone $this->records($f))->selectRaw('a.status, COUNT(*) as total')
            ->groupBy('a.status')->pluck('total', 'a.status');
        $total = (int) $summary->sum();
        $rows = $this->records($f)->orderByDesc('ss.attendance_date')->orderBy('c.name')
            ->orderBy('st.last_name')->paginate(30)->withQueryString();
        return view('company.school.attendance.reports', [
            'years' => $f['years'], 'yearId' => $f['yearId'], 'classes' => $f['classes'],
            'classId' => $f['classId'], 'from' => $f['from'], 'to' => $f['to'],
            'summary' => $summary, 'total' => $total, 'rows' => $rows,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->checkAccess();
        $f = $this->filters($request);
        $query = $this->records($f)->orderBy('ss.attendance_date')->orderBy('c.name')->orderBy('st.last_name');
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Date', 'Classe', 'Matricule', 'Nom', 'Prénom', 'Statut', 'Retard (min)', 'Observation', 'Justificatif'], ';');
            foreach ($query->cursor() as $r) {
                $values = [$r->attendance_date, $r->class_name, $r->student_number, $r->last_name,
                    $r->first_name, $r->status, $r->minutes_late, $r->reason, $r->justification_status];
                // Neutralise les formules lors de l'ouverture dans Excel/LibreOffice.
                $values = array_map(function ($value) {
                    $value = (string) ($value ?? '');
                    return preg_match('/^[\s\x00-\x1F]*[=+@\-]/u', $value) ? "'" . $value : $value;
                }, $values);
                fputcsv($out, $values, ';');
            }
            fclose($out);
        }, 'nexora_absences_' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
