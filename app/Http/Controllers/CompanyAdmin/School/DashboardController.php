<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\SchoolLevel;
use App\Models\SchoolSection;
use App\Models\SchoolSetting;
use App\Models\SchoolYear;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $companyId = (int) auth()->user()->company_id;
        abort_unless($companyId > 0, 403);
        $company = auth()->user()->company;
        $year = SchoolYear::where('company_id', $companyId)->where('is_active', true)->first();
        return view('company.school.dashboard', [
            'company' => $company,
            'settings' => SchoolSetting::where('company_id', $companyId)->first(),
            'year' => $year,
            'yearsCount' => SchoolYear::where('company_id', $companyId)->count(),
            'sectionsCount' => SchoolSection::where('company_id', $companyId)->count(),
            'levelsCount' => SchoolLevel::where('company_id', $companyId)->count(),
            'classesCount' => $year ? SchoolClass::where('company_id', $companyId)->where('school_year_id', $year->id)->count() : 0,
        ]);
    }
}
