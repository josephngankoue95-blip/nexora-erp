<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolPrincipalTuitionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user
            && $user->company_id
            && $user->role === 'school_principal'
            && $user->is_active
            && $user->company
            && $user->company->is_active,
            403,
            'Accès réservé au Proviseur.'
        );

        $companyId = (int) $user->company_id;

        $year = DB::table('school_years')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->first();

        $classes = collect();
        $students = collect();
        $byClass = collect();
        $byMethod = collect();

        $stats = [
            'expected' => 0,
            'collected' => 0,
            'remaining' => 0,
            'overpaid' => 0,
            'rate' => 0,
            'students' => 0,
            'paid' => 0,
            'partial' => 0,
            'unpaid' => 0,
        ];

        if (!$year) {
            return view(
                'company.school.principal.tuition.index',
                compact(
                    'year',
                    'classes',
                    'students',
                    'byClass',
                    'byMethod',
                    'stats'
                )
            );
        }

        $yearId = (int) $year->id;

        $classes = DB::table('school_classes')
            ->where('company_id', $companyId)
            ->where('school_year_id', $yearId)
            ->orderBy('name')
            ->get();

        // Une ligne par dossier de frais, avec ses paiements cumulés.
        $paymentTotals = DB::table('school_payments')
            ->where('company_id', $companyId)
            ->select(
                'school_student_fee_id',
                DB::raw('SUM(amount) as total_paid')
            )
            ->groupBy('school_student_fee_id');

        $students = DB::table('school_student_fees as sf')
            ->join('school_enrollments as e', function ($join) {
                $join->on('e.id', '=', 'sf.school_enrollment_id')
                    ->on('e.company_id', '=', 'sf.company_id');
            })
            ->join('school_students as s', function ($join) {
                $join->on('s.id', '=', 'e.school_student_id')
                    ->on('s.company_id', '=', 'e.company_id');
            })
            ->join('school_classes as c', function ($join) {
                $join->on('c.id', '=', 'e.school_class_id')
                    ->on('c.company_id', '=', 'e.company_id');
            })
            ->leftJoinSub($paymentTotals, 'pt', function ($join) {
                $join->on('pt.school_student_fee_id', '=', 'sf.id');
            })
            ->where('sf.company_id', $companyId)
            ->where('e.school_year_id', $yearId)
            ->where('c.school_year_id', $yearId)
            ->where('e.status', 'enrolled')
            ->select(
                'sf.id as fee_id',
                's.id as student_id',
                's.student_number',
                's.first_name',
                's.last_name',
                'c.id as class_id',
                'c.name as class_name',
                'sf.registration_fee',
                'sf.installment_1',
                'sf.installment_2',
                'sf.installment_3',
                DB::raw('COALESCE(pt.total_paid, 0) as total_paid')
            )
            ->get()
            ->map(function ($student) {
                $student->expected =
                    (float) $student->registration_fee +
                    (float) $student->installment_1 +
                    (float) $student->installment_2 +
                    (float) $student->installment_3;

                $student->paid_amount = (float) $student->total_paid;

                $student->remaining = max(
                    0,
                    $student->expected - $student->paid_amount
                );

                $student->overpaid = max(
                    0,
                    $student->paid_amount - $student->expected
                );

                $student->status = $student->remaining <= 0
                    ? 'paid'
                    : ($student->paid_amount > 0 ? 'partial' : 'unpaid');

                return $student;
            });

        $stats['expected'] = $students->sum('expected');
        $stats['collected'] = $students->sum('paid_amount');
        $stats['remaining'] = $students->sum('remaining');
        $stats['overpaid'] = $students->sum('overpaid');

        $stats['rate'] = $stats['expected'] > 0
            ? round(
                $stats['collected'] * 100 / $stats['expected'],
                2
            )
            : 0;

        $stats['students'] = $students->count();
        $stats['paid'] = $students->where('status', 'paid')->count();
        $stats['partial'] = $students->where('status', 'partial')->count();
        $stats['unpaid'] = $students->where('status', 'unpaid')->count();

        $byClass = $students
            ->groupBy('class_id')
            ->map(function ($items) {
                $expected = $items->sum('expected');
                $collected = $items->sum('paid_amount');

                return (object) [
                    'class_name' => $items->first()->class_name,
                    'students' => $items->count(),
                    'expected' => $expected,
                    'collected' => $collected,
                    'remaining' => $items->sum('remaining'),
                    'rate' => $expected > 0
                        ? round($collected * 100 / $expected, 2)
                        : 0,
                ];
            })
            ->sortBy('class_name')
            ->values();

        $feeIds = $students->pluck('fee_id');

        if ($feeIds->isNotEmpty()) {
            $byMethod = DB::table('school_payments')
                ->where('company_id', $companyId)
                ->whereIn('school_student_fee_id', $feeIds)
                ->select(
                    'method',
                    DB::raw('COUNT(*) as payment_count'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('method')
                ->orderByDesc('total')
                ->get();
        }

        return view(
            'company.school.principal.tuition.index',
            compact(
                'year',
                'classes',
                'students',
                'byClass',
                'byMethod',
                'stats'
            )
        );
    }
}