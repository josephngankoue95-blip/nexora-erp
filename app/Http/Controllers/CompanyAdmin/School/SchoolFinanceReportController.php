<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolFinanceReportController extends Controller
{
    private function dataset(Request $request): array
    {
        $cid = (int) $request->user()->company_id;
        $filters = $request->validate([
            'year_id' => ['nullable', 'integer', Rule::exists('school_years', 'id')->where('company_id', $cid)],
            'class_id' => ['nullable', 'integer', Rule::exists('school_classes', 'id')->where('company_id', $cid)],
            'section_id' => ['nullable', 'integer', Rule::exists('school_sections', 'id')->where('company_id', $cid)],
        ]);
        $paid = DB::table('school_payments')
            ->select('school_student_fee_id')
            ->selectRaw('SUM(amount) as paid_total')
            ->where('company_id', $cid)
            ->groupBy('school_student_fee_id');
        $q = DB::table('school_student_fees as sf')
            ->join('school_enrollments as en', function ($join) use ($cid) {
                $join->on('en.id', '=', 'sf.school_enrollment_id')->where('en.company_id', '=', $cid);
            })
            ->join('school_classes as cl', function ($join) use ($cid) {
                $join->on('cl.id', '=', 'en.school_class_id')->where('cl.company_id', '=', $cid);
            })
            ->join('school_levels as lv', function ($join) use ($cid) {
                $join->on('lv.id', '=', 'cl.school_level_id')->where('lv.company_id', '=', $cid);
            })
            ->join('school_sections as sc', function ($join) use ($cid) {
                $join->on('sc.id', '=', 'lv.school_section_id')->where('sc.company_id', '=', $cid);
            })
            ->join('school_years as yr', function ($join) use ($cid) {
                $join->on('yr.id', '=', 'en.school_year_id')->where('yr.company_id', '=', $cid);
            })
            ->leftJoinSub($paid, 'pay', 'pay.school_student_fee_id', '=', 'sf.id')
            ->where('sf.company_id', $cid)
            ->select('sf.id', 'yr.name as year_name', 'sc.name as section_name', 'cl.name as class_name')
            ->selectRaw('(sf.registration_fee + sf.installment_1 + sf.installment_2 + sf.installment_3) as expected_total')
            ->selectRaw('COALESCE(pay.paid_total,0) as paid_total');
        if (!empty($filters['year_id'])) $q->where('en.school_year_id', $filters['year_id']);
        if (!empty($filters['class_id'])) $q->where('en.school_class_id', $filters['class_id']);
        if (!empty($filters['section_id'])) $q->where('lv.school_section_id', $filters['section_id']);
        $rows = $q->orderBy('yr.name')->orderBy('sc.name')->orderBy('cl.name')->get();
        $expected = (int) $rows->sum('expected_total');
        $received = (int) $rows->sum('paid_total');
        $outstanding = max(0, $expected - $received);
        $rate = $expected > 0 ? round($received / $expected * 100, 1) : 0;
        $byClass = $rows->groupBy(fn ($r) => $r->year_name.' / '.$r->section_name.' / '.$r->class_name)
            ->map(fn ($items, $label) => (object) [
                'label' => $label,
                'students' => $items->count(),
                'expected' => (int) $items->sum('expected_total'),
                'received' => (int) $items->sum('paid_total'),
                'outstanding' => max(0, (int) $items->sum('expected_total') - (int) $items->sum('paid_total')),
            ])->values();
        return compact('cid', 'filters', 'rows', 'expected', 'received', 'outstanding', 'rate', 'byClass');
    }

    public function index(Request $request)
    {
        $data = $this->dataset($request);
        $cid = $data['cid'];
        $data['years'] = SchoolYear::where('company_id', $cid)->orderByDesc('starts_on')->get();
        $data['classes'] = SchoolClass::where('company_id', $cid)->orderBy('name')->get();
        $data['sections'] = DB::table('school_sections')->where('company_id', $cid)->orderBy('name')->get();
        return view('company.school.finance.dashboard', $data);
    }

    public function excel(Request $request)
    {
        $data = $this->dataset($request);
        $filename = 'recouvrement-scolaire-'.now()->format('Ymd-His').'.csv';
        return response()->streamDownload(function () use ($data) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, ['Annee scolaire','Section','Classe','Dossiers','Attendu FCFA','Encaisse FCFA','Reste FCFA'], ';');
            foreach ($data['byClass'] as $r) {
                $parts = explode(' / ', $r->label, 3);
                fputcsv($f, [($parts[0] ?? ''), ($parts[1] ?? ''), ($parts[2] ?? ''), $r->students, $r->expected, $r->received, $r->outstanding], ';');
            }
            fputcsv($f, ['TOTAL','','',$data['rows']->count(),$data['expected'],$data['received'],$data['outstanding']], ';');
            fclose($f);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pdf(Request $request)
    {
        $data = $this->dataset($request);
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(503, 'Installer barryvdh/laravel-dompdf pour exporter en PDF.');
        }
        $data['company'] = $request->user()->company;
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('company.school.finance.dashboard-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->download('recouvrement-scolaire-'.now()->format('Ymd').'.pdf');
    }
}
