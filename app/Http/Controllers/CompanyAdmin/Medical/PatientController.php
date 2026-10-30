<?php

namespace App\Http\Controllers\CompanyAdmin\Medical;

use App\Http\Controllers\Controller;
use App\Models\Medical\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    private function companyId(Request $request): int
    {
        $companyId = $request->user()?->company_id;

        abort_unless($companyId, 403);

        return (int) $companyId;
    }

    private function validatePatient(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);

        $search = trim((string) $request->query('search', ''));

        $patients = Patient::forCompany($companyId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('patient_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('company.medical.patients.index', compact(
            'patients',
            'search'
        ));
    }

    public function create()
    {
        return view('company.medical.patients.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId($request);

        $data = $this->validatePatient($request);

        $patient = DB::transaction(function () use ($companyId, $data) {

            // Verrouillage de l'entreprise pour sérialiser
            // la génération des numéros de patients.
            DB::table('companies')
                ->where('id', $companyId)
                ->lockForUpdate()
                ->first();

            $patient = Patient::create(array_merge($data, [
                'company_id' => $companyId,
                'patient_number' => 'PENDING-' . bin2hex(random_bytes(8)),
            ]));

            $patient->update([
                'patient_number' => 'PAT-' . str_pad(
                    (string) $patient->id,
                    8,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            return $patient;
        });

        return redirect()
            ->route('company.medical.patients.show', $patient)
            ->with('success', 'Patient enregistré avec succès.');
    }

    public function show(Request $request, Patient $patient)
    {
        $this->ensureCompany($request, $patient);

        return view('company.medical.patients.show', compact('patient'));
    }

    public function edit(Request $request, Patient $patient)
    {
        $this->ensureCompany($request, $patient);

        return view('company.medical.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $this->ensureCompany($request, $patient);

        $patient->update($this->validatePatient($request));

        return redirect()
            ->route('company.medical.patients.show', $patient)
            ->with('success', 'Patient modifié avec succès.');
    }

    private function ensureCompany(Request $request, Patient $patient): void
    {
        abort_unless(
            $patient->company_id === $this->companyId($request),
            404
        );
    }
}