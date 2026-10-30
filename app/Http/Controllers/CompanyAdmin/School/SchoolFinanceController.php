<?php
namespace App\Http\Controllers\CompanyAdmin\School;
use App\Http\Controllers\Controller;
use App\Models\{SchoolEnrollment,SchoolFeeStructure,SchoolStudentFee,SchoolPayment,SchoolYear,SchoolLevel};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class SchoolFinanceController extends Controller {
 private function cid(Request $r): int {return (int)$r->user()->company_id;}
 public function index(Request $r) {
  $cid=$this->cid($r);
  $q=SchoolStudentFee::where('company_id',$cid)->with(['enrollment.student','enrollment.year','enrollment.schoolClass.level.section','payments']);
  if($r->filled('year_id'))$q->whereHas('enrollment',fn($x)=>$x->where('company_id',$cid)->where('school_year_id',$r->integer('year_id')));
  if($r->filled('class_id'))$q->whereHas('enrollment',fn($x)=>$x->where('company_id',$cid)->where('school_class_id',$r->integer('class_id')));
  if($r->filled('q')) { $term=trim($r->input('q'));$q->whereHas('enrollment.student',fn($x)=>$x->where('company_id',$cid)->where(fn($s)=>$s->where('student_number','like',"%{$term}%")->orWhere('last_name','like',"%{$term}%")->orWhere('first_name','like',"%{$term}%"))); }
  $accounts=$q->orderByDesc('id')->paginate(20)->withQueryString();
  $enrollments=SchoolEnrollment::with(['student','year','schoolClass.level'])->where('company_id',$cid)->whereNotIn('id',SchoolStudentFee::where('company_id',$cid)->select('school_enrollment_id'))->orderByDesc('id')->limit(500)->get();
  $years=SchoolYear::where('company_id',$cid)->orderByDesc('starts_on')->get();
  $classes=\App\Models\SchoolClass::where('company_id',$cid)->orderBy('name')->get();
  return view('company.school.finance.accounts',compact('accounts','enrollments','years','classes'));
 }
 public function assign(Request $r) {
  $cid=$this->cid($r);$d=$r->validate(['school_enrollment_id'=>['required',Rule::exists('school_enrollments','id')->where('company_id',$cid)]]);
  DB::transaction(function()use($cid,$d){
   $en=SchoolEnrollment::where('company_id',$cid)->lockForUpdate()->findOrFail($d['school_enrollment_id']);
   if(SchoolStudentFee::where('company_id',$cid)->where('school_enrollment_id',$en->id)->exists())throw ValidationException::withMessages(['school_enrollment_id'=>'Frais déjà affectés à cette inscription.']);
   $class=$en->schoolClass;
   $fee=SchoolFeeStructure::where('company_id',$cid)->where('school_year_id',$en->school_year_id)->where('school_level_id',$class->school_level_id)->first();
   if(!$fee)throw ValidationException::withMessages(['school_enrollment_id'=>'Configurez d’abord les frais de ce niveau et de cette année.']);
   SchoolStudentFee::create(['company_id'=>$cid,'school_enrollment_id'=>$en->id,'school_fee_structure_id'=>$fee->id,'registration_fee'=>$fee->registration_fee,'installment_1'=>$fee->installment_1,'installment_2'=>$fee->installment_2,'installment_3'=>$fee->installment_3]);
  });
  return back()->with('success','Frais affectés à l’inscription.');
 }
 public function pay(Request $r,int $id) {
  $cid=$this->cid($r);
  $d=$r->validate(['amount'=>['required','integer','min:1','max:1000000000'],'fee_type'=>['required',Rule::in(['registration_fee','installment_1','installment_2','installment_3'])],'method'=>['required',Rule::in(['cash','bank_transfer','orange_money','mtn_momo'])],'paid_on'=>['required','date'],'external_reference'=>['nullable','string','max:120'],'notes'=>['nullable','string','max:1000']]);
  DB::transaction(function()use($cid,$id,$d){
   $account=SchoolStudentFee::where('company_id',$cid)->lockForUpdate()->findOrFail($id);
   $paid=(int)$account->payments()->where('company_id',$cid)->where('fee_type',$d['fee_type'])->sum('amount');
   $remaining=(int)$account->{$d['fee_type']}-$paid;
   if($d['amount']>$remaining)throw ValidationException::withMessages(['amount'=>'Montant supérieur au solde restant pour cette tranche ('.number_format(max(0,$remaining),0,',',' ').' FCFA).']);
   $payment=$account->payments()->create($d+['company_id'=>$cid,'reference'=>'PENDING-'.\Illuminate\Support\Str::uuid()]);
   $payment->update(['reference'=>'SC-PAY-'.$cid.'-'.str_pad((string)$payment->id,8,'0',STR_PAD_LEFT)]);
  });
  return back()->with('success','Paiement enregistré.');
 }
}
