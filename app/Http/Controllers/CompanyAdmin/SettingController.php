<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Afficher les paramètres de l'entreprise.
     */
    public function index()
    {
        $company = auth()->user()->company;

        return view('company.settings.index', compact('company'));
    }

    /**
     * Mettre à jour les paramètres de l'entreprise.
     */
    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | Identité
            |--------------------------------------------------------------------------
            */
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slogan' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Logo
            |--------------------------------------------------------------------------
            */
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            /*
            |--------------------------------------------------------------------------
            | Coordonnées
            |--------------------------------------------------------------------------
            */
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:50',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Adresse
            |--------------------------------------------------------------------------
            */
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Informations légales
            |--------------------------------------------------------------------------
            */
            'tax_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'rccm' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Paramètres commerciaux
            |--------------------------------------------------------------------------
            */
            'currency' => [
                'required',
                'string',
                'max:10',
            ],

            'currency_symbol' => [
                'required',
                'string',
                'max:20',
            ],

            'invoice_prefix' => [
                'required',
                'string',
                'max:30',
            ],

            'invoice_footer' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'date_format' => [
                'required',
                'string',
                'max:30',
            ],

            /*
            |--------------------------------------------------------------------------
            | Couleur principale
            |--------------------------------------------------------------------------
            */
            'primary_color' => [
                'required',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            /*
            |--------------------------------------------------------------------------
            | Stock
            |--------------------------------------------------------------------------
            */
            'stock_alert_threshold' => [
                'required',
                'integer',
                'min:0',
                'max:1000000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Gestion du logo
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('logo')) {

            // Supprimer l'ancien logo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            // Enregistrer le nouveau logo
            $validated['logo'] = $request
                ->file('logo')
                ->store('companies/logos', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */
        $company->update($validated);

        return redirect()
            ->route('company.settings.index')
            ->with(
                'success',
                'Les paramètres de votre entreprise ont été enregistrés avec succès.'
            );
    }

    /**
     * Supprimer le logo.
     */
    public function removeLogo()
    {
        $company = auth()->user()->company;

        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->update([
            'logo' => null,
        ]);

        return redirect()
            ->route('company.settings.index')
            ->with(
                'success',
                'Le logo de l’entreprise a été supprimé.'
            );
    }
}