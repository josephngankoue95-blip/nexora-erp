<?php
namespace App\Http\Controllers\CompanyAdmin\School;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class SchoolGradeController extends Controller {
 private function cid():int{return (int)auth()->user()->company_id;}
 private function year(int $id):object{return DB::table('school_years')->where('company_id',$this->cid())->where('id',$id)->firstOrFail();}
 private function cls(int $id,int $year):object{return DB::table('school_classes')->where('company_id',$this->cid())->where('school_year_id',$year)->where('id',$id)->firstOrFail();}
 private function writable(object $year):void{if($year->is_archived)throw ValidationException::withMessages(['year'=>'Année scolaire archivée.']);}
 private function students(int $class,int $year){return DB::table('school_enrollments as e')->join('school_students as s',function($j){$j->on('s.id','=','e.school_student_id')->on('s.company_id','=','e.company_id');})->where('e.company_id',$this->cid())->where('e.school_class_id',$class)->where('e.school_year_id',$year)->where('e.status','enrolled')->select('s.id','s.student_number','s.last_name','s.first_name')->distinct()->orderBy('s.last_name')->get();}
 public function index(Request $r){
  $years=DB::table('school_years')->where('company_id',$this->cid())->orderByDesc('starts_on')->get();
  $yearId=(int)$r->query('year_id',$years->firstWhere('is_active',1)->id??$years->first()->id??0);
  $year=$yearId?$this->year($yearId):null;
  $classes=$year?DB::table('school_classes')->where('company_id',$this->cid())->where('school_year_id',$yearId)->orderBy('name')->get():collect();
  $classId=(int)$r->query('class_id',0);$class=$classId?$this->cls($classId,$yearId):null;
  $subjects=$class?DB::table('school_class_subjects as cs')->join('school_subjects as s',function($j){$j->on('s.id','=','cs.school_subject_id')->on('s.company_id','=','cs.company_id');})->where('cs.company_id',$this->cid())->where('cs.school_class_id',$classId)->where('cs.is_active',1)->select('cs.id','s.name','cs.coefficient','cs.group_number')->orderBy('cs.sort_order')->get():collect();
  $periods=$year?DB::table('school_evaluation_periods')->where('company_id',$this->cid())->where('school_year_id',$yearId)->orderBy('number')->get():collect();
  $config=$year?DB::table('school_grading_configs')->where('company_id',$this->cid())->where('school_year_id',$yearId)->first():null;
  $evaluations=$class?DB::table('school_evaluations as v')->join('school_class_subjects as cs','cs.id','=','v.school_class_subject_id')->join('school_subjects as s','s.id','=','cs.school_subject_id')->join('school_evaluation_periods as p','p.id','=','v.school_evaluation_period_id')->where('v.company_id',$this->cid())->where('v.school_class_id',$classId)->select('v.*','s.name as subject_name','p.name as period_name')->orderByDesc('v.id')->get():collect();
  return view('company.school.grades.index',compact('years','year','classes','class','subjects','periods','config','evaluations'));
 }
 public function configure(Request $r){
  $d=$r->validate(['school_year_id'=>['required','integer',Rule::exists('school_years','id')->where('company_id',$this->cid())],'mode'=>['required',Rule::in(['sequences','trimesters'])]]);
  $year=$this->year((int)$d['school_year_id']);$this->writable($year);
  DB::transaction(function()use($year,$d){
   $key=['company_id'=>$this->cid(),'school_year_id'=>$year->id];$old=DB::table('school_grading_configs')->where($key)->lockForUpdate()->first();
   if($old && $old->mode!==$d['mode'] && DB::table('school_evaluations')->where($key)->exists())throw ValidationException::withMessages(['mode'=>'Des évaluations existent : changement de mode interdit.']);
   if($old && $old->mode===$d['mode'])return;
   DB::table('school_grading_configs')->updateOrInsert($key,['mode'=>$d['mode'],'created_at'=>$old->created_at??now(),'updated_at'=>now()]);
   DB::table('school_evaluation_periods')->where($key)->delete();
   $n=$d['mode']==='sequences'?6:3;
   for($i=1;$i<=$n;$i++)DB::table('school_evaluation_periods')->insert($key+['name'=>($d['mode']==='sequences'?'Séquence ':'Trimestre ').$i,'kind'=>$d['mode']==='sequences'?'sequence':'trimester','number'=>$i,'trimester_number'=>$d['mode']==='sequences'?(int)ceil($i/2):$i,'created_at'=>now(),'updated_at'=>now()]);
  });
  return redirect()->route('company.school.grades.index',['year_id'=>$year->id])->with('success','Configuration enregistrée.');
 }
 public function storeEvaluation(Request $r){
  $d=$r->validate(['school_year_id'=>'required|integer','school_class_id'=>'required|integer','school_class_subject_id'=>'required|integer','school_evaluation_period_id'=>'required|integer','title'=>'required|string|max:120','max_score'=>'required|numeric|gt:0|max:9999','weight'=>'required|numeric|gt:0|max:9999']);
  $year=$this->year((int)$d['school_year_id']);$this->writable($year);$this->cls((int)$d['school_class_id'],$year->id);
  $subject=DB::table('school_class_subjects')->where('company_id',$this->cid())->where('school_class_id',$d['school_class_id'])->where('id',$d['school_class_subject_id'])->where('is_active',1)->exists();
  $period=DB::table('school_evaluation_periods')->where('company_id',$this->cid())->where('school_year_id',$year->id)->where('id',$d['school_evaluation_period_id'])->exists();
  if(!$subject||!$period)throw ValidationException::withMessages(['reference'=>'Matière ou période invalide.']);
  DB::table('school_evaluations')->insert($d+['company_id'=>$this->cid(),'created_at'=>now(),'updated_at'=>now()]);
  return back()->with('success','Évaluation créée.');
 }
 private function evaluation(int $id):object{return DB::table('school_evaluations')->where('company_id',$this->cid())->where('id',$id)->firstOrFail();}
 public function edit(int $id){$evaluation=$this->evaluation($id);$year=$this->year((int)$evaluation->school_year_id);$class=$this->cls((int)$evaluation->school_class_id,$year->id);$students=$this->students($class->id,$year->id);$grades=DB::table('school_grades')->where('company_id',$this->cid())->where('school_evaluation_id',$id)->pluck('score','school_student_id');return view('company.school.grades.edit',compact('evaluation','year','class','students','grades'));}
 public function save(Request $r,int $id){
  $evaluation=$this->evaluation($id);$year=$this->year((int)$evaluation->school_year_id);$this->writable($year);
  if($evaluation->is_locked)throw ValidationException::withMessages(['notes'=>'Évaluation verrouillée.']);
  $d=$r->validate(['grades'=>'required|array','grades.*'=>'nullable|numeric|min:0|max:9999']);
  $allowed=$this->students((int)$evaluation->school_class_id,(int)$evaluation->school_year_id)->pluck('id')->map(fn($v)=>(string)$v)->all();
  foreach($d['grades'] as $student=>$score){if(!in_array((string)$student,$allowed,true))throw ValidationException::withMessages(['grades'=>'Élève non inscrit dans cette classe.']);if($score!==null&&$score!==''&&(float)$score>(float)$evaluation->max_score)throw ValidationException::withMessages(['grades'=>'Une note dépasse le barème.']);}
  DB::transaction(function()use($d,$evaluation){foreach($d['grades'] as $student=>$score){$key=['company_id'=>$this->cid(),'school_evaluation_id'=>$evaluation->id,'school_student_id'=>(int)$student];if($score===null||$score===''){DB::table('school_grades')->where($key)->delete();continue;}DB::table('school_grades')->updateOrInsert($key,['score'=>$score,'entered_by'=>auth()->id(),'created_at'=>DB::table('school_grades')->where($key)->value('created_at')??now(),'updated_at'=>now()]);}});
  return back()->with('success','Notes enregistrées.');
 }
}
