<?php

namespace App\Http\Controllers\CompanyAdmin\Medical;

use App\Http\Controllers\Controller;
use App\Models\Medical\Appointment;
use App\Models\Medical\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    private function companyId(Request $request): int
    {
        $companyId = $request->user()?->company_id;

        abort_unless($companyId, 403);

        return (int) $companyId;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);

        $appointments = Appointment::forCompany($companyId)
            ->with(['patient', 'practitioner'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('scheduled_at', $request->date);
            })
            ->orderBy('scheduled_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'company.medical.appointments.index',
            compact('appointments')
        );
    }

    public function create(Request $request)
    {
        $companyId = $this->companyId($request);

        $patients = Patient::forCompany($companyId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'patient_number']);

        $practitioners = User::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            'company.medical.appointments.create',
            compact('patients', 'practitioners')
        );
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId($request);

        $data = $request->validate([
            'patient_id' => [
                'required',
                Rule::exists('medical_patients', 'id')
                    ->where('company_id', $companyId)
                    ->where('status', 'active'),
            ],
            'practitioner_id' => [
                'nullable',
                Rule::exists('users', 'id')
                    ->where('company_id', $companyId)
                    ->where('is_active', true),
            ],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'between:5,480'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $appointment = Appointment::create([
            ...$data,
            'company_id' => $companyId,
            'status' => 'scheduled',
        ]);

        return redirect()
            ->route('company.medical.appointments.show', $appointment)
            ->with('success', 'Rendez-vous enregistré avec succès.');
    }

    public function show(Request $request, Appointment $appointment)
    {
        abort_unless(
            $appointment->company_id === $this->companyId($request),
            404
        );

        $appointment->load(['patient', 'practitioner']);

        return view(
            'company.medical.appointments.show',
            compact('appointment')
        );
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        abort_unless(
            $appointment->company_id === $this->companyId($request),
            404
        );

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'scheduled',
                    'confirmed',
                    'completed',
                    'cancelled',
                    'no_show',
                ]),
            ],
        ]);

        $appointment->update($data);

        return back()->with(
            'success',
            'Statut du rendez-vous mis à jour.'
        );
    }
}