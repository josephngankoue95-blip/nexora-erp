<?php

namespace App\Http\Controllers\CompanyAdmin\Medical;

use App\Http\Controllers\Controller;
use App\Models\Medical\Patient;
use App\Models\Medical\Appointment;
use Illuminate\Http\Request;

class ClinicDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user && $user->company_id, 403);

        $company = $user->company;

        session([
            'medical_dashboard' => 'company.clinic.dashboard'
        ]);

        $companyId = $company->id;

        $stats = [
            'patients' => Patient::where('company_id', $companyId)->count(),

            'appointments' => Appointment::where('company_id', $companyId)
                ->whereDate('scheduled_at', today())
                ->count(),

            'upcoming' => Appointment::where('company_id', $companyId)
                ->where('scheduled_at', '>=', now())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count(),

            'completed' => Appointment::where('company_id', $companyId)
                ->where('status', 'completed')
                ->count(),
        ];

        $recentAppointments = Appointment::where('company_id', $companyId)
            ->with(['patient', 'practitioner'])
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->limit(6)
            ->get();

        return view('company.medical.clinic.dashboard', compact(
            'company',
            'stats',
            'recentAppointments'
        ));
    }
}