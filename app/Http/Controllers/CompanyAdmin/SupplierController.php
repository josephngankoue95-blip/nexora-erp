<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Liste des fournisseurs
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Supplier::where('company_id', $companyId);

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
                    ->orWhere('tax_number', 'like', "%{$search}%")
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
            }

            if ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $suppliers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistiques
        |--------------------------------------------------------------------------
        */

        $totalSuppliers = Supplier::where(
            'company_id',
            $companyId
        )->count();

        $activeSuppliers = Supplier::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->count();

        $inactiveSuppliers = Supplier::where(
            'company_id',
            $companyId
        )
            ->where('is_active', false)
            ->count();

        return view('company.suppliers.index', compact(
            'suppliers',
            'totalSuppliers',
            'activeSuppliers',
            'inactiveSuppliers'
        ));
    }

    /**
     * Formulaire création
     */
    public function create()
    {
        return view('company.suppliers.create');
    }

    /**
     * Enregistrement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'tax_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        $validated['is_active'] = $request->boolean('is_active');

        Supplier::create($validated);

        return redirect()
            ->route('company.suppliers.index')
            ->with('success', 'Fournisseur ajouté avec succès.');
    }

    /**
     * Affichage
     */
    public function show(Supplier $supplier)
    {
        $supplier = Supplier::where(
            'company_id',
            auth()->user()->company_id
        )->findOrFail($supplier->id);

        return view(
            'company.suppliers.show',
            compact('supplier')
        );
    }

    /**
     * Formulaire modification
     */
    public function edit(Supplier $supplier)
    {
        $supplier = Supplier::where(
            'company_id',
            auth()->user()->company_id
        )->findOrFail($supplier->id);

        return view(
            'company.suppliers.edit',
            compact('supplier')
        );
    }

    /**
     * Mise à jour
     */
    public function update(
        Request $request,
        Supplier $supplier
    ) {
        $supplier = Supplier::where(
            'company_id',
            auth()->user()->company_id
        )->findOrFail($supplier->id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'tax_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $supplier->update($validated);

        return redirect()
            ->route('company.suppliers.index')
            ->with('success', 'Fournisseur modifié avec succès.');
    }

    /**
     * Suppression
     */
    public function destroy(Supplier $supplier)
    {
        $supplier = Supplier::where(
            'company_id',
            auth()->user()->company_id
        )->findOrFail($supplier->id);

        $supplier->delete();

        return redirect()
            ->route('company.suppliers.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
}