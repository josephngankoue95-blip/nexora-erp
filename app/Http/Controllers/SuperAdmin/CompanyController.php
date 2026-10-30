<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Liste des entreprises.
     */
    public function index(Request $request)
    {
        $query = Company::query();

        /*
        |--------------------------------------------------------------------------
        | Recherche
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filtre statut
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            if ($request->status === 'active') {

                $query->where('is_active', true);

            } elseif ($request->status === 'inactive') {

                $query->where('is_active', false);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $companies = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'superadmin.companies.index',
            compact('companies')
        );
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('superadmin.companies.create');
    }

    /**
     * Enregistrer une entreprise.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | ENTREPRISE
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:companies,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | ADMINISTRATEUR
            |--------------------------------------------------------------------------
            */

            'admin_name' => [
                'required',
                'string',
                'max:255',
            ],

            'admin_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'admin_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Création entreprise
            |--------------------------------------------------------------------------
            */

            $company = Company::create([

                'name' => $validated['name'],

                'slug' => Str::slug(
                    $validated['name']
                ),

                'email' => $validated['email'] ?? null,

                'phone' => $validated['phone'] ?? null,

                'address' => $validated['address'] ?? null,

                'city' => $validated['city'] ?? null,

                'country' => $validated['country'] ?? 'Cameroun',

                'is_active' => $validated['is_active'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Création administrateur
            |--------------------------------------------------------------------------
            */

            User::create([

                'name' => $validated['admin_name'],

                'email' => $validated['admin_email'],

                'password' => Hash::make(
                    $validated['admin_password']
                ),

                'role' => 'companyadmin',

                'company_id' => $company->id,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Redirection
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('superadmin.companies.index')
            ->with(
                'success',
                'Entreprise et administrateur créés avec succès.'
            );
    }

    /**
     * Afficher une entreprise.
     */
    public function show(Company $company)
    {
        $company->load('users');

        return view(
            'superadmin.companies.show',
            compact('company')
        );
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Company $company)
    {
        return view(
            'superadmin.companies.edit',
            compact('company')
        );
    }

    /**
     * Modifier une entreprise.
     */
    public function update(
        Request $request,
        Company $company
    ) {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:companies,email,' . $company->id,
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Génération du slug
        |--------------------------------------------------------------------------
        */

        $validated['slug'] = Str::slug(
            $validated['name']
        );

        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */

        $company->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Redirection
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('superadmin.companies.index')
            ->with(
                'success',
                'Entreprise modifiée avec succès.'
            );
    }

    /**
     * Supprimer une entreprise.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()
            ->route('superadmin.companies.index')
            ->with(
                'success',
                'Entreprise supprimée avec succès.'
            );
    }

    /**
     * Activer / désactiver une entreprise.
     */
    public function toggleStatus(Company $company)
    {
        $company->update([
            'is_active' => !$company->is_active,
        ]);

        return back()->with(
            'success',
            $company->is_active
                ? 'Entreprise activée.'
                : 'Entreprise désactivée.'
        );
    }
}