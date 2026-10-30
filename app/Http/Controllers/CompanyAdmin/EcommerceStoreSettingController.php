<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EcommerceStoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EcommerceStoreSettingController extends Controller
{
    private function getCompanyId(): int
    {
        $companyId = Auth::user()?->company_id;
        abort_unless($companyId, 403, 'Aucune entreprise associée à cet utilisateur.');
        return (int) $companyId;
    }

    public function edit()
    {
        $companyId = $this->getCompanyId();
        $company = Company::query()->findOrFail($companyId);

        $settings = EcommerceStoreSetting::firstOrCreate(
            ['company_id' => $companyId],
            [
                'store_name' => 'Ma boutique',
                'currency' => 'XAF',
                'allow_guest_checkout' => true,
                'reviews_enabled' => true,
                'is_active' => false,
            ]
        );

        $storefrontUrl = route('storefront.index', ['company' => $company->slug]);

        return view('company.ecommerce.settings.edit', compact('settings', 'storefrontUrl'));
    }

    public function update(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:5000'],
            'currency' => ['required', Rule::in(['XAF', 'XOF', 'EUR', 'USD', 'GBP'])],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'allow_guest_checkout' => ['sometimes', 'boolean'],
            'reviews_enabled' => ['sometimes', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['allow_guest_checkout'] = $request->boolean('allow_guest_checkout');
        $validated['reviews_enabled'] = $request->boolean('reviews_enabled');
        $validated['is_active'] = $request->boolean('is_active');

        EcommerceStoreSetting::updateOrCreate(
            ['company_id' => $companyId],
            $validated
        );

        return redirect()
            ->route('company.ecommerce.settings.edit')
            ->with('success', 'Les paramètres de votre boutique ont été enregistrés avec succès.');
    }
}
