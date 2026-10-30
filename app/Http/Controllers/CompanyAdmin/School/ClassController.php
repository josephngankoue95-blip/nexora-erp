<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClassController extends Controller
{
    private function companyId(): int { return (int) auth()->user()->company_id; }
    private function records() { return SchoolClass::query()->where('company_id', $this->companyId()); }
    private function owned(int $id): SchoolClass { return $this->records()->findOrFail($id); }
    private function extras(): array { return ['years'=>\App\Models\SchoolYear::where('company_id',$this->companyId())->orderByDesc('starts_on')->get(),'levels'=>\App\Models\SchoolLevel::where('company_id',$this->companyId())->with('section')->orderBy('sort_order')->get()]; }
    public function index(\Illuminate\Http\Request $request) {
        $query = $this->records()->with(['year','level.section']);
        if ($request->filled('q')) $query->where('name','like','%'.trim((string)$request->input('q')).'%');
        if ($request->filled('year_id')) $query->where('school_year_id',$request->input('year_id'));
        if ($request->filled('level_id')) $query->where('school_level_id',$request->input('level_id'));
        if ($request->filled('section_id')) {
            $sectionId = $request->input('section_id');
            $query->whereHas('level', function ($builder) use ($sectionId) { $builder->where('school_section_id',$sectionId); });
        }
        $query->orderBy($request->input('sort') === 'recent' ? 'id' : 'name', $request->input('sort') === 'recent' ? 'desc' : 'asc');
        $records = $query->paginate(15)->withQueryString();
        $extras = $this->extras();
        $extras['sections'] = \App\Models\SchoolSection::where('company_id',$this->companyId())->orderBy('name')->get();
        return view('company.school.foundation.index', array_merge($extras, ['records'=>$records,'kind'=>'classes']));
    }
    public function create() {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>new SchoolClass,'kind'=>'classes']));
    }
    public function edit(int $id) {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>$this->owned($id),'kind'=>'classes']));
    }
    private function rules(?int $id=null): array {
        return ['school_year_id'=>['required',Rule::exists('school_years','id')->where('company_id',$this->companyId())],'school_level_id'=>['required',Rule::exists('school_levels','id')->where('company_id',$this->companyId())],'name'=>['required','string','max:100',Rule::unique('school_classes','name')->where('company_id',$this->companyId())->where('school_year_id',request('school_year_id'))->ignore($id)],'capacity'=>['nullable','integer','min:1','max:65535']];
    }
    public function store(Request $request) {
        $data = $request->validate($this->rules());
        if (\App\Models\SchoolYear::where('company_id',$this->companyId())->whereKey($data['school_year_id'])->where('is_archived',true)->exists()) { throw ValidationException::withMessages(['school_year_id'=>'Une année archivée ne peut pas recevoir de nouvelles classes.']); }
        SchoolClass::create(['company_id'=>$this->companyId()] + $data);
        return redirect()->route('company.school.classes.index')->with('success','Enregistrement créé.');
    }
    public function update(Request $request,int $id) {
        $record=$this->owned($id);
        $data=$request->validate($this->rules($id));
        if (\App\Models\SchoolYear::where('company_id',$this->companyId())->whereKey($data['school_year_id'])->where('is_archived',true)->exists()) { throw ValidationException::withMessages(['school_year_id'=>'Une année archivée ne peut pas être modifiée.']); }
        $record->update($data);
        return redirect()->route('company.school.classes.index')->with('success','Enregistrement modifié.');
    }
    public function destroy(int $id) {
        $record=$this->owned($id);
        if ($record->year->is_archived) { throw ValidationException::withMessages(['class'=>'Une classe d’une année archivée ne peut pas être supprimée.']); }
        $record->delete();
        return redirect()->route('company.school.classes.index')->with('success','Enregistrement supprimé.');
    }

}
