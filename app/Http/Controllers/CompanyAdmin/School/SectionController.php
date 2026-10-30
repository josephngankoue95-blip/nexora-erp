<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SectionController extends Controller
{
    private function companyId(): int { return (int) auth()->user()->company_id; }
    private function records() { return SchoolSection::query()->where('company_id', $this->companyId()); }
    private function owned(int $id): SchoolSection { return $this->records()->findOrFail($id); }
    private function extras(): array { return []; }
    public function index(\Illuminate\Http\Request $request) {
        $query = $this->records();
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%'.$q.'%')->orWhere('code', 'like', '%'.$q.'%');
            });
        }
        $query->orderBy($request->input('sort') === 'recent' ? 'id' : 'name', $request->input('sort') === 'recent' ? 'desc' : 'asc');
        $records = $query->paginate(15)->withQueryString();
        return view('company.school.foundation.index', ['records'=>$records,'kind'=>'sections']);
    }
    public function create() {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>new SchoolSection,'kind'=>'sections']));
    }
    public function edit(int $id) {
        return view('company.school.foundation.form', array_merge($this->extras(), ['record'=>$this->owned($id),'kind'=>'sections']));
    }
    private function rules(?int $id=null): array {
        return ['code'=>['required','string','max:30',Rule::unique('school_sections','code')->where('company_id',$this->companyId())->ignore($id)],'name'=>['required','string','max:80'],'is_active'=>['required','boolean']];
    }
    public function store(Request $request) {
        $data = $request->validate($this->rules());

        SchoolSection::create(['company_id'=>$this->companyId()] + $data);
        return redirect()->route('company.school.sections.index')->with('success','Enregistrement créé.');
    }
    public function update(Request $request,int $id) {
        $record=$this->owned($id);
        $data=$request->validate($this->rules($id));

        $record->update($data);
        return redirect()->route('company.school.sections.index')->with('success','Enregistrement modifié.');
    }
    public function destroy(int $id) {
        $record=$this->owned($id);
        if ($record->levels()->exists()) { throw ValidationException::withMessages(['section'=>'Cette section contient des niveaux.']); }
        $record->delete();
        return redirect()->route('company.school.sections.index')->with('success','Enregistrement supprimé.');
    }

}
