<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\{SchoolPayment,SchoolStudentFee,SchoolYear,SchoolClass};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolFinanceFollowUpController extends Controller
{
    private function companyId(Request $request): int
    {
        return (int) $request->user()->company_id;
    }

    public function debts(Request $request)
    {
        $companyId = $this->companyId($request);
        $filters = $request->validate([
            'year_id' => ['nullable','integer'],
            'class_id' => ['nullable','integer'],
            'q' => ['nullable','string','max:100'],
        ]);
        $query = SchoolStudentFee::query()
            ->where('school_student_fees.company_id', $companyId)
            ->join('school_enrollments as se', function ($join) use ($companyId) {
                $join->on('se.id','=','school_student_fees.school_enrollment_id')
                    ->where('se.company_id','=',$companyId);
            })
            ->leftJoin('school_payments as sp', function ($join) use ($companyId) {
                $join->on('sp.school_student_fee_id','=','school_student_fees.id')
                    ->where('sp.company_id','=',$companyId);
            })
            ->select('school_student_fees.*')
            ->selectRaw('COALESCE(SUM(sp.amount),0) as paid_total')
            ->groupBy('school_student_fees.id')
            ->havingRaw('(school_student_fees.registration_fee + school_student_fees.installment_1 + school_student_fees.installment_2 + school_student_fees.installment_3) > COALESCE(SUM(sp.amount),0)');

        if (!empty($filters['year_id'])) $query->where('se.school_year_id',$filters['year_id']);
        if (!empty($filters['class_id'])) $query->where('se.school_class_id',$filters['class_id']);
        if (!empty($filters['q'])) {
            $term = $filters['q'];
            $query->whereHas('enrollment.student',function ($q) use ($companyId,$term) {
                $q->where('company_id',$companyId)->where(function ($q) use ($term) {
                    $q->where('student_number','like','%'.$term.'%')
                      ->orWhere('last_name','like','%'.$term.'%')
                      ->orWhere('first_name','like','%'.$term.'%');
                });
            });
        }
        $accounts = $query->with(['enrollment.student','enrollment.year','enrollment.schoolClass.level.section'])
            ->orderBy('school_student_fees.id')->paginate(20)->withQueryString();
        $years = SchoolYear::where('company_id',$companyId)->orderByDesc('starts_on')->get();
        $classes = SchoolClass::where('company_id',$companyId)->orderBy('name')->get();
        return view('company.school.finance.debts',compact('accounts','years','classes'));
    }

    public function receipt(Request $request, int $id)
    {
        $companyId = $this->companyId($request);
        $payment = SchoolPayment::where('company_id',$companyId)
            ->whereHas('studentFee', fn($q) => $q->where('company_id',$companyId)
                ->whereHas('enrollment',fn($q) => $q->where('company_id',$companyId)))
            ->with(['studentFee.enrollment.student','studentFee.enrollment.year','studentFee.enrollment.schoolClass'])
            ->findOrFail($id);
        $account = $payment->studentFee;
        $due = (int)$account->registration_fee+(int)$account->installment_1+(int)$account->installment_2+(int)$account->installment_3;
        $paidThroughReceipt = (int) SchoolPayment::where('company_id',$companyId)
            ->where('school_student_fee_id',$account->id)
            ->where(function($q) use ($payment) {
                $q->where('paid_on','<',$payment->paid_on->toDateString())
                  ->orWhere(function($q) use ($payment) {
                      $q->whereDate('paid_on',$payment->paid_on->toDateString())
                        ->where('id','<=',$payment->id);
                  });
            })->sum('amount');
        $company = $request->user()->company;
        $view = view('company.school.finance.receipt',compact('payment','account','company','due','paidThroughReceipt'));
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return response('Le package barryvdh/laravel-dompdf doit être installé.', 503);
        }
        return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($view->render())
            ->setPaper('a5','portrait')
            ->stream('recu-'.$payment->reference.'.pdf');
    }

    public function payments(Request $request)
    {
        $companyId = $this->companyId($request);
        $payments = SchoolPayment::where('company_id',$companyId)
            ->whereHas('studentFee',fn($q)=>$q->where('company_id',$companyId))
            ->with(['studentFee.enrollment.student','studentFee.enrollment.schoolClass'])
            ->orderByDesc('paid_on')->orderByDesc('id')->paginate(25);
        return view('company.school.finance.payments',compact('payments'));
    }
}
