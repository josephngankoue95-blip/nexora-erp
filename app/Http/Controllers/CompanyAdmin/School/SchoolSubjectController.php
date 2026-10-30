<?php
namespace App\Http\Controllers\CompanyAdmin\School;
use App\Http\Controllers\Controller;
use App\Models\SchoolSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchoolSubjectController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ENTREPRISE CONNECTÉE
    |--------------------------------------------------------------------------
    */

    private function companyId(): int
    {
        return (int) auth()->user()->company_id;
    }

    /*
    |--------------------------------------------------------------------------
    | VÉRIFICATION DES RÉFÉRENCES
    |--------------------------------------------------------------------------
    */

    private function assertOwned(
        string $table,
        int $id,
        int $companyId
    ): void {
        $exists = DB::table($table)
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->exists();

        if (!$exists) {
            throw ValidationException::withMessages([
                'section_id' =>
                    'La section sélectionnée est invalide pour cette entreprise.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONS DU FORMULAIRE
    |--------------------------------------------------------------------------
    |
    | Une matière appartient à une section scolaire.
    |
    | Le groupe et le coefficient seront définis
    | lors de la configuration des matières par classe.
    |
    */

    private function options(): array
    {
        $companyId = $this->companyId();

        return [
            'sections' => DB::table('school_sections')
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE DES MATIÈRES
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $items = SchoolSubject::query()
            ->where('company_id', $this->companyId())
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('company.school.pedagogy.index', [
            'items' => $items,
            'type'  => 'subjects',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE D'AJOUT
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'company.school.pedagogy.form',
            array_merge(
                [
                    'item' => new SchoolSubject(),
                    'type' => 'subjects',
                ],
                $this->options()
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE DE MODIFICATION
    |--------------------------------------------------------------------------
    */

    public function edit(int $id)
    {
        $item = SchoolSubject::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        return view(
            'company.school.pedagogy.form',
            array_merge(
                [
                    'item' => $item,
                    'type' => 'subjects',
                ],
                $this->options()
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validated(Request $request): array
    {
        $companyId = $this->companyId();

        $data = $request->validate([
            'section_id' => [
                'required',
                'integer',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VÉRIFIER LA SECTION
        |--------------------------------------------------------------------------
        */

        $this->assertOwned(
            'school_sections',
            (int) $data['section_id'],
            $companyId
        );

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER UNE MATIÈRE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['company_id'] = $this->companyId();

        SchoolSubject::create($data);

        return redirect()
            ->route('company.school.subjects.index')
            ->with(
                'success',
                'La matière a été enregistrée avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFIER UNE MATIÈRE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, int $id)
    {
        $item = SchoolSubject::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        $data = $this->validated($request);

        $item->update($data);

        return redirect()
            ->route('company.school.subjects.index')
            ->with(
                'success',
                'La matière a été modifiée avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER UNE MATIÈRE
    |--------------------------------------------------------------------------
    */

    public function destroy(int $id)
    {
        $item = SchoolSubject::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        $item->delete();

        return redirect()
            ->route('company.school.subjects.index')
            ->with(
                'success',
                'La matière a été supprimée avec succès.'
            );
    }
}