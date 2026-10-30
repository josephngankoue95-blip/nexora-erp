<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolSettingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeSchoolSettings($request);
        $company = $this->company($request);

        $settings = SchoolSetting::firstOrNew([
            'company_id' => $company->id,
        ]);

        $settings->school_name ??= $company->name;
        $settings->country ??= 'Cameroun';
        $settings->academic_system ??= 'both';
        $settings->grading_scale ??= 20;

        return view('company.school.settings.index', compact(
            'company',
            'settings'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeSchoolSettings($request);
        $company = $this->company($request);

        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_code' => ['nullable', 'string', 'max:100'],
            'school_type' => [
                'nullable',
                Rule::in(['public', 'private', 'other']),
            ],
            'authorization_number' => [
                'nullable', 'string', 'max:150',
            ],
            'motto' => ['nullable', 'string', 'max:255'],

            'country' => ['required', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],

            'principal_name' => ['nullable', 'string', 'max:255'],
            'director_name' => ['nullable', 'string', 'max:255'],

            'academic_system' => [
                'required',
                Rule::in(['francophone', 'anglophone', 'both']),
            ],
            'grading_scale' => [
                'required', 'integer', Rule::in([20, 100]),
            ],
            'bulletin_footer' => [
                'nullable', 'string', 'max:2000',
            ],

            'logo' => [
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'stamp' => [
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        unset($data['logo'], $data['stamp']);

        $settings = SchoolSetting::firstOrNew([
            'company_id' => $company->id,
        ]);

        $newFiles = [];
        $oldFiles = [];

        try {
            foreach (['logo', 'stamp'] as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store(
                        'school/' . $company->id . '/settings',
                        'public'
                    );

                    if (!$path) {
                        throw new \RuntimeException(
                            'Impossible de stocker le fichier : ' . $field
                        );
                    }

                    $newFiles[$field] = $path;
                    $oldFiles[$field] = $settings->{$field};
                    $data[$field] = $path;
                }
            }

            DB::transaction(function () use (
                $settings,
                $company,
                $data
            ) {
                $settings->fill($data);
                $settings->company_id = $company->id;
                $settings->save();
            });

        } catch (\Throwable $e) {
            foreach ($newFiles as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        foreach ($oldFiles as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        return redirect()
            ->route('company.school.settings.index')
            ->with(
                'success',
                'Les paramètres de l’établissement ont été enregistrés.'
            );
    }

    private function company(Request $request): Company
    {
        $companyId = $request->user()?->company_id;

        abort_unless($companyId, 403);

        return Company::query()
            ->whereKey($companyId)
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function authorizeSchoolSettings(Request $request): void
{
    abort_unless(
        in_array($request->user()?->role, [
            'companyadmin',
            'school_admin',
            'school_principal',
            'school_director',
        ], true),
        403,
        'Accès aux paramètres de l’établissement non autorisé.'
    );
}
}