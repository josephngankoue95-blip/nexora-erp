<?php
namespace App\Http\Controllers\CompanyAdmin\School;
use App\Http\Controllers\Controller;
use App\Models\{SchoolFeeStructure,SchoolYear,SchoolLevel};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class FeeStructureController extends Controller {
 private function cid(Request $r): int {return (int)$r->user()->company_id;}
 public function index(Request $r) {
  $cid=$this->cid($r);
  $q=SchoolFeeStructure::where('company_id',$cid)->with(['year','level.section']);
  if($r->filled('year_id')) $q->where('school_year_id',$r->integer('year_id'));
  if($r->filled('section_id')) $q->whereHas('level',fn($x)=>$x->where('school_section_id',$r->integer('section_id')));
  if($r->filled('level_id')) $q->where('school_level_id',$r->integer('level_id'));
  $fees=$q->orderByDesc('school_year_id')->orderBy('school_level_id')->paginate(20)->withQueryString();
  return view('company.school.finance.fees-index',['fees'=>$fees,'years'=>SchoolYear::where('company_id',$cid)->orderByDesc('starts_on')->get(),'levels'=>SchoolLevel::with('section')->where('company_id',$cid)->orderBy('sort_order')->get()]);
 }
 public function create(Request $r) {return $this->form($r,new SchoolFeeStructure());}
 public function edit(Request $r,int $id) {return $this->form($r,SchoolFeeStructure::where('company_id',$this->cid($r))->findOrFail($id));}
 private function form(Request $r,SchoolFeeStructure $fee) {
  $cid=$this->cid($r);
  return view('company.school.finance.fees-form',compact('fee')+['years'=>SchoolYear::where('company_id',$cid)->where('is_archived',false)->orderByDesc('starts_on')->get(),'levels'=>SchoolLevel::with('section')->where('company_id',$cid)->orderBy('sort_order')->get()]);
 }
 private function data(Request $r): array {
  $cid=$this->cid($r);
  return $r->validate([
   'school_year_id'=>['required',Rule::exists('school_years','id')->where('company_id',$cid)->where('is_archived',false)],
   'school_level_id'=>['required',Rule::exists('school_levels','id')->where('company_id',$cid)],
   'registration_fee'=>['required','integer','min:0','max:1000000000'],
   'installment_1'=>['required','integer','min:0','max:1000000000'],
   'installment_2'=>['required','integer','min:0','max:1000000000'],
   'installment_3'=>['required','integer','min:0','max:1000000000'],
  ]);
 }
 public function store(Request $r) {
  $cid=$this->cid($r);$data=$this->data($r);
  $r->validate(['school_level_id'=>[Rule::unique('school_fee_structures')->where('company_id',$cid)->where('school_year_id',$data['school_year_id'])]]);
  SchoolFeeStructure::create($data+['company_id'=>$cid]);
  return redirect()->route('company.school.fees.index')->with('success','Tarif scolaire enregistré.');
 }
 public function update(Request $r,int $id) {
  $cid=$this->cid($r);$fee=SchoolFeeStructure::where('company_id',$cid)->findOrFail($id);$data=$this->data($r);
  $r->validate(['school_level_id'=>[Rule::unique('school_fee_structures')->ignore($fee->id)->where('company_id',$cid)->where('school_year_id',$data['school_year_id'])]]);
  $fee->update($data);
  return redirect()->route('company.school.fees.index')->with('success','Tarif mis à jour. Les frais déjà affectés aux élèves restent inchangés.');
 }
 public function destroy(Request $r,int $id) {
  $fee=SchoolFeeStructure::where('company_id',$this->cid($r))->findOrFail($id);
  if(\App\Models\SchoolStudentFee::where('school_fee_structure_id',$fee->id)->exists())return back()->withErrors(['fee'=>'Tarif déjà utilisé : suppression impossible.']);
  $fee->delete();return back()->with('success','Tarif supprimé.');
 }
}
