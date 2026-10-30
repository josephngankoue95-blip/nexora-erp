<?php

namespace App\Http\Controllers\CompanyAdmin\Medical;

use App\Http\Controllers\Controller;
use App\Models\Medical\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PractitionerController extends Controller
{
    private function companyId(Request $request): int
    {
        $companyId = $request->user()?->company_id;

        abort_unless($companyId, 403);

        return (int) $companyId;
    }

    private function findPractitioner(
        Request $request,
        Practitioner $practitioner
    ): Practitioner {
        abort_unless(
            (int) $practitioner->company_id === $this->companyId($request),
            404
        );

        return $practitioner;
    }

    private function validatedData(Request $request): array
    {
        $companyId = $this->companyId($request);

        return $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'registration_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('medical_practitioners', 'registration_number')
                    ->where('company_id', $companyId)
                    ->ignore($request->route('practitioner')?->id),
            ],

            'profession' => [
                'required',
                Rule::in([
                    'doctor',
                    'nurse',
                    'midwife',
                    'lab_technician',
                    'pharmacist',
                    'other',
                ]),
            ],

            'specialty' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'hire_date' => ['nullable', 'date'],

            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'on_leave']),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);

        $search = trim((string) $request->query('search', ''));

        $practitioners = Practitioner::forCompany($companyId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%")
                        ->orWhere('specialty', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'company.medical.practitioners.index',
            compact('practitioners', 'search')
        );
    }

    public function create()
    {
        return view('company.medical.practitioners.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        Practitioner::create([
            ...$data,
            'company_id' => $this->companyId($request),
        ]);

        return redirect()
            ->route('company.medical.practitioners.index')
            ->with('success', 'Professionnel enregistré avec succès.');
    }

    public function edit(Request $request, Practitioner $practitioner)
    {
        $this->findPractitioner($request, $practitioner);

        return view(
            'company.medical.practitioners.edit',
            compact('practitioner')
        );
    }

    public function update(Request $request, Practitioner $practitioner)
    {
        $this->findPractitioner($request, $practitioner);

        $practitioner->update($this->validatedData($request));

        return redirect()
            ->route('company.medical.practitioners.index')
            ->with('success', 'Professionnel modifié avec succès.');
    }
}