<?php
namespace App\Http\Controllers\CompanyAdmin\School;
use App\Http\Controllers\Controller;
use App\Models\SchoolSubjectGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchoolSubjectGroupController extends Controller {
    private function companyId(): int { return (int) auth()->user()->company_id; }
    private function assertOwned(string $table, int $id, int $companyId): void {
        if (!DB::table($table)->where('company_id', $companyId)->where('id', $id)->exists())
            throw ValidationException::withMessages(['reference' => 'La référence sélectionnée est invalide pour cette entreprise.']);
    }
    private function options(): array {
        $companyId = $this->companyId();
        return [
            'sections' => DB::table('school_sections')->where('company_id',$companyId)->orderBy('name')->get(['id','name']),
            'groups' => DB::table('school_subject_groups')->where('company_id',$companyId)->orderBy('name')->get(['id','name','section_id']),
            'teachers' => DB::table('school_teachers')->where('company_id',$companyId)->orderBy('last_name')->get(['id','first_name','last_name']),
            'subjects' => DB::table('school_subjects')->where('company_id',$companyId)->orderBy('name')->get(['id','name','section_id']),
            'classes' => DB::table('school_classes')->where('company_id',$companyId)->orderBy('name')->get(['id','name','section_id']),
            'years' => DB::table('school_years')->where('company_id',$companyId)->orderByDesc('id')->get(['id','name']),
        ];
    }
    public function index(Request $request) {
        $items = SchoolSubjectGroup::where('company_id',$this->companyId())->latest('id')->paginate(20);
        return view('company.school.pedagogy.index', ['items'=>$items,'type'=>'subject-groups']);
    }
    public function create() {
        return view('company.school.pedagogy.form', ['item'=>new SchoolSubjectGroup,'type'=>'subject-groups'] + $this->options());
    }
    public function edit(int $id) {
        $item = SchoolSubjectGroup::where('company_id',$this->companyId())->findOrFail($id);
        return view('company.school.pedagogy.form', ['item'=>$item,'type'=>'subject-groups'] + $this->options());
    }
    private function validated(Request $request, ?int $id = null): array {
        $companyId = $this->companyId();
        $rules = ['section_id'=>'required|integer','name'=>'required|string|max:255','code'=>'nullable|string|max:30','description'=>'nullable|string'];
        
        $data = $request->validate($rules);
        $this->assertOwned('school_sections', $data['section_id'], $companyId);
        return $data;
    }
    public function store(Request $request) {
        $data = $this->validated($request);
        SchoolSubjectGroup::create($data + ['company_id'=>$this->companyId()]);
        return redirect()->route('company.school.subject-groups.index')->with('success','Enregistrement effectué.');
    }
    public function update(Request $request, int $id) {
        $item = SchoolSubjectGroup::where('company_id',$this->companyId())->findOrFail($id);
        $item->update($this->validated($request, $id));
        return redirect()->route('company.school.subject-groups.index')->with('success','Modification effectuée.');
    }
    public function destroy(int $id) {
        SchoolSubjectGroup::where('company_id',$this->companyId())->findOrFail($id)->delete();
        return redirect()->route('company.school.subject-groups.index')->with('success','Suppression effectuée.');
    }
}
