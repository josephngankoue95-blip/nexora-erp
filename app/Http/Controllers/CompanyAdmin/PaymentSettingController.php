<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyPaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;

        $settings = CompanyPaymentSetting::firstOrCreate(
            ['company_id' => $company->id],
            [
                'environment' => 'sandbox',
                'orange_enabled' => false,
                'mtn_enabled' => false,
            ]
        );

        return view('company.settings.payments', compact(
            'company',
            'settings'
        ));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'environment' => [
                'required',
                'in:sandbox,production',
            ],

            'orange_enabled' => [
                'nullable',
                'boolean',
            ],

            'orange_client_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'orange_client_secret' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'orange_merchant_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mtn_enabled' => [
                'nullable',
                'boolean',
            ],

            'mtn_api_user' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mtn_api_key' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'mtn_subscription_key' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $validated['orange_enabled'] =
            $request->boolean('orange_enabled');

        $validated['mtn_enabled'] =
            $request->boolean('mtn_enabled');

        /*
        |--------------------------------------------------------------------------
        | Sécurité : ne pas remplacer un secret existant par une valeur vide
        |--------------------------------------------------------------------------
        */

        $settings = CompanyPaymentSetting::firstOrCreate(
            ['company_id' => $company->id]
        );

        if (
            empty($validated['orange_client_secret'] ?? null)
            && $settings->orange_client_secret
        ) {
            unset($validated['orange_client_secret']);
        }

        if (
            empty($validated['mtn_api_key'] ?? null)
            && $settings->mtn_api_key
        ) {
            unset($validated['mtn_api_key']);
        }

        $settings->update($validated);

        return back()->with(
            'success',
            'Les paramètres de paiement ont été enregistrés.'
        );
    }
}