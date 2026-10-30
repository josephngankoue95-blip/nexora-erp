<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LevelController extends Controller
{
    private function companyId(): int { return (int) auth()->user()->company_id; }
    private function records() { return SchoolLevel::query()->where('company_id', $this->companyId()); }
    private function owned(int $id): SchoolLevel { return $this->records()->findOrFail($id); }
    private function extras(): array { return ['sections'=>\App\Models\SchoolSection::where('company_id',$this->companyId())->orderBy('name')->get()]; }
    public function index(\Illuminate\Http\Request $request) {
        $query = $this->records()->with('section');
        if ($request->filled('q')) $query->where('name','like','%'.trim((string)$request->input('q')).'%');
        if ($request->filled('section_id')) $query->where('school_section_id',$request->input('section_id'));
        $sort = $request->input('sort');
        if ($sort === 'recent') $query->orderByDesc('id');
        elseif ($sort === 'order') $query->orderBy('sort_order')->orderBy('name');
        else $query->orderBy('name');
        $records = $query->paginate(15)->withQueryString();
        return view('company.school.foundation.index', array_merge($this->extras(), ['records'=>$records,'kind'=>'levels']));
    }
    public function create() {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>new SchoolLevel,'kind'=>'levels']));
    }
    public function edit(int $id) {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>$this->owned($id),'kind'=>'levels']));
    }
    private function rules(?int $id=null): array {
        return ['school_section_id'=>['required',Rule::exists('school_sections','id')->where('company_id',$this->companyId())],'name'=>['required','string','max:80',Rule::unique('school_levels','name')->where('company_id',$this->companyId())->where('school_section_id',request('school_section_id'))->ignore($id)],'sort_order'=>['required','integer','min:0','max:65535'],'is_exam_class'=>['required','boolean']];
    }
    public function store(Request $request) {
        $data = $request->validate($this->rules());

        SchoolLevel::create(['company_id'=>$this->companyId()] + $data);
        return redirect()->route('company.school.levels.index')->with('success','Enregistrement créé.');
    }
    public function update(Request $request,int $id) {
        $record=$this->owned($id);
        $data=$request->validate($this->rules($id));

        $record->update($data);
        return redirect()->route('company.school.levels.index')->with('success','Enregistrement modifié.');
    }
    public function destroy(int $id) {
        $record=$this->owned($id);
        if ($record->classes()->exists()) { throw ValidationException::withMessages(['level'=>'Ce niveau contient des classes.']); }
        $record->delete();
        return redirect()->route('company.school.levels.index')->with('success','Enregistrement supprimé.');
    }

}
