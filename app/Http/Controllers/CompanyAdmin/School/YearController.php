<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class YearController extends Controller
{
    private function companyId(): int { return (int) auth()->user()->company_id; }
    private function records() { return SchoolYear::query()->where('company_id', $this->companyId()); }
    private function owned(int $id): SchoolYear { return $this->records()->findOrFail($id); }
    private function extras(): array { return []; }
    public function index() {
        $records = $this->records()->orderBy('id','desc')->paginate(15);
        return view('company.school.foundation.index', array_merge($this->extras(), ['records'=>$records,'kind'=>'years']));
    }
    public function create() {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>new SchoolYear,'kind'=>'years']));
    }
    public function edit(int $id) {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>$this->owned($id),'kind'=>'years']));
    }
    private function rules(?int $id=null): array {
        return ['name'=>['required','string','max:30',Rule::unique('school_years','name')->where('company_id',$this->companyId())->ignore($id)],'starts_on'=>['required','date'],'ends_on'=>['required','date','after:starts_on']];
    }
    public function store(Request $request) {
        $data = $request->validate($this->rules());
        $data['is_active']=false; $data['is_archived']=false;
        SchoolYear::create(['company_id'=>$this->companyId()] + $data);
        return redirect()->route('company.school.years.index')->with('success','Enregistrement créé.');
    }
    public function update(Request $request,int $id) {
        $record=$this->owned($id);
        $data=$request->validate($this->rules($id));
        unset($data['is_active'],$data['is_archived']);
        $record->update($data);
        return redirect()->route('company.school.years.index')->with('success','Enregistrement modifié.');
    }
    public function destroy(int $id) {
        $record=$this->owned($id);
        if ($record->is_active || $record->is_archived || $record->classes()->exists()) { throw ValidationException::withMessages(['year'=>'Impossible de supprimer une année active, archivée ou contenant des classes.']); }
        $record->delete();
        return redirect()->route('company.school.years.index')->with('success','Enregistrement supprimé.');
    }
    public function activate(int $id) {
        $year=$this->owned($id);
        if ($year->is_archived) { throw ValidationException::withMessages(['year'=>'Une année archivée ne peut pas être activée.']); }
        DB::transaction(function () use ($year) {
            $this->records()->where('id','!=',$year->id)->where('is_active',true)->update(['is_active'=>false]);
            $year->update(['is_active'=>true]);
        });
        return back()->with('success','Année scolaire activée.');
    }
    public function archive(int $id) {
        $year=$this->owned($id);
        if ($year->is_active) { throw ValidationException::withMessages(['year'=>'Désactivez cette année en activant une autre année avant de l’archiver.']); }
        $year->update(['is_archived'=>true]);
        return back()->with('success','Année scolaire archivée.');
    }
}
