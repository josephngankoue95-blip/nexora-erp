<?php

namespace App\Http\Controllers\CompanyAdmin\Medical;

use App\Http\Controllers\Controller;
use App\Models\Medical\Consultation;
use App\Models\Medical\Patient;
use App\Models\Medical\Appointment;
use App\Models\Medical\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConsultationController extends Controller
{
    private function companyId(Request $request): int
    {
        $companyId = $request->user()?->company_id;

        abort_unless($companyId, 403);

        return (int) $companyId;
    }

    private function checkCompany(
        Request $request,
        Consultation $consultation
    ): void {
        abort_unless(
            (int) $consultation->company_id === $this->companyId($request),
            404
        );
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);

        $consultations = Consultation::forCompany($companyId)
            ->with(['patient', 'practitioner'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('consultation_date', $request->date);
            })
            ->latest('consultation_date')
            ->paginate(15)
            ->withQueryString();

        return view(
            'company.medical.consultations.index',
            compact('consultations')
        );
    }

    public function create(Request $request)
    {
        $companyId = $this->companyId($request);

        $patients = Patient::forCompany($companyId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get();

        $practitioners = Practitioner::forCompany($companyId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get();

        $appointments = Appointment::forCompany($companyId)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderByDesc('scheduled_at')
            ->limit(100)
            ->get();

        return view(
            'company.medical.consultations.create',
            compact('patients', 'practitioners', 'appointments')
        );
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId($request);

        $data = $request->validate([
            'patient_id' => [
                'required',
                Rule::exists('medical_patients', 'id')
                    ->where('company_id', $companyId),
            ],
            'appointment_id' => [
                'nullable',
                Rule::exists('medical_appointments', 'id')
                    ->where('company_id', $companyId)
                    ->where('patient_id', $request->input('patient_id')),
            ],
            'practitioner_id' => [
                'nullable',
                Rule::exists('medical_practitioners', 'id')
                    ->where('company_id', $companyId)
                    ->where('status', 'active'),
            ],
            'consultation_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
            'symptoms' => ['nullable', 'string', 'max:10000'],
            'clinical_observations' => ['nullable', 'string', 'max:10000'],
            'diagnosis' => ['nullable', 'string', 'max:10000'],
            'treatment_plan' => ['nullable', 'string', 'max:10000'],
        ]);

        $consultation = Consultation::create([
            ...$data,
            'company_id' => $companyId,
            'status' => 'draft',
        ]);

        return redirect()
            ->route('company.medical.consultations.show', $consultation)
            ->with('success', 'Consultation créée avec succès.');
    }

    public function show(Request $request, Consultation $consultation)
    {
        $this->checkCompany($request, $consultation);

        $consultation->load([
            'patient',
            'practitioner',
            'appointment'
        ]);

        return view(
            'company.medical.consultations.show',
            compact('consultation')
        );
    }
}