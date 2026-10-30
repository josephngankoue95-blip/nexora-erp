<?php
namespace App\Http\Controllers\CompanyAdmin\School;
use App\Http\Controllers\Controller;
use App\Models\SchoolTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolTeacherController extends Controller
{
    private function companyId(): int
    {
        return (int) auth()->user()->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();

        $query = SchoolTeacher::query()
            ->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('speciality', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $items = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('company.school.pedagogy.index', [
            'items' => $items,
            'type' => 'teachers',
        ]);
    }

    public function create()
    {
        return view('company.school.pedagogy.form', [
            'item' => new SchoolTeacher(),
            'type' => 'teachers',
        ]);
    }

    public function edit(int $id)
    {
        $item = SchoolTeacher::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        return view('company.school.pedagogy.form', [
            'item' => $item,
            'type' => 'teachers',
        ]);
    }

    private function validated(
        Request $request,
        ?int $id = null
    ): array {
        $companyId = $this->companyId();

        return $request->validate([
            'matricule' => [
                'required',
                'string',
                'max:50',
                Rule::unique('school_teachers', 'matricule')
                    ->where('company_id', $companyId)
                    ->ignore($id),
            ],

            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'speciality' => [
                'nullable',
                'string',
                'max:255',
            ],

            'qualification' => [
                'nullable',
                'string',
                'max:255',
            ],

            'recruitment_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'suspended',
                ]),
            ],

            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('company_id', $companyId),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['company_id'] = $this->companyId();

        DB::transaction(function () use ($data) {
            SchoolTeacher::create($data);
        });

        return redirect()
            ->route('company.school.teachers.index')
            ->with(
                'success',
                'Enseignant enregistré avec succès.'
            );
    }

    public function update(Request $request, int $id)
    {
        $item = SchoolTeacher::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        $data = $this->validated($request, $id);

        DB::transaction(function () use ($item, $data) {
            $item->update($data);
        });

        return redirect()
            ->route('company.school.teachers.index')
            ->with(
                'success',
                'Enseignant modifié avec succès.'
            );
    }

    public function destroy(int $id)
    {
        $companyId = $this->companyId();

        $item = SchoolTeacher::query()
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $hasAssignments = DB::table(
            'school_teacher_assignments'
        )
            ->where('company_id', $companyId)
            ->where('teacher_id', $item->id)
            ->exists();

        if ($hasAssignments) {
            return redirect()
                ->route('company.school.teachers.index')
                ->withErrors([
                    'teacher' =>
                        'Cet enseignant possède des affectations. '
                        . 'Désactivez son compte plutôt que de le supprimer.',
                ]);
        }

        $item->delete();

        return redirect()
            ->route('company.school.teachers.index')
            ->with(
                'success',
                'Enseignant supprimé avec succès.'
            );
    }
}