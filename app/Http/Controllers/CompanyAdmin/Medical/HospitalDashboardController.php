<?php

namespace App\Http\Controllers\CompanyAdmin\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HospitalDashboardController extends Controller
{
public function index(Request $request)
{
    $company = $request->user()->company;

    abort_unless($company && $company->is_active, 403);

    session(['medical_dashboard' => 'company.hospital.dashboard']);
    return view('company.medical.hospital.dashboard', [
        'company' => $company,
    ]);
}
}
