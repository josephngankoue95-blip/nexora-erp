<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Liste des prestations.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Service::where('company_id', $companyId);

        /*
        |--------------------------------------------------------------------------
        | Recherche
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Catégorie
        |--------------------------------------------------------------------------
        */
        if ($request->filled('category')) {
            $query->where(
                'category',
                $request->category
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Statut
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

        /*
        |--------------------------------------------------------------------------
        | Statistiques
        |--------------------------------------------------------------------------
        */
        $statistics = [
            'total' => Service::where(
                'company_id',
                $companyId
            )->count(),

            'active' => Service::where(
                'company_id',
                $companyId
            )
                ->where('is_active', true)
                ->count(),

            'inactive' => Service::where(
                'company_id',
                $companyId
            )
                ->where('is_active', false)
                ->count(),

            'categories' => Service::where(
                'company_id',
                $companyId
            )
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct('category')
                ->count('category'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Catégories disponibles
        |--------------------------------------------------------------------------
        */
        $categories = Service::where(
            'company_id',
            $companyId
        )
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        /*
        |--------------------------------------------------------------------------
        | Prestations
        |--------------------------------------------------------------------------
        */
        $services = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'company.services.index',
            compact(
                'services',
                'statistics',
                'categories'
            )
        );
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view(
            'company.services.create'
        );
    }

    /**
     * Enregistrement.
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('services', 'reference')
                    ->where(function ($query) use ($companyId) {
                        return $query->where(
                            'company_id',
                            $companyId
                        );
                    }),
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['company_id'] = $companyId;

        $validated['tax'] = $validated['tax'] ?? 0;

        $validated['is_active'] =
            $request->boolean('is_active');

        Service::create($validated);

        return redirect()
            ->route('company.services.index')
            ->with(
                'success',
                'La prestation a été créée avec succès.'
            );
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Service $service)
    {
        $this->authorizeCompany($service);

        return view(
            'company.services.edit',
            compact('service')
        );
    }

    /**
     * Mise à jour.
     */
    public function update(
        Request $request,
        Service $service
    ) {
        $this->authorizeCompany($service);

        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('services', 'reference')
                    ->ignore($service->id)
                    ->where(function ($query) use ($companyId) {
                        return $query->where(
                            'company_id',
                            $companyId
                        );
                    }),
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['tax'] = $validated['tax'] ?? 0;

        $validated['is_active'] =
            $request->boolean('is_active');

        $service->update($validated);

        return redirect()
            ->route('company.services.index')
            ->with(
                'success',
                'La prestation a été modifiée avec succès.'
            );
    }

    /**
     * Activation / désactivation.
     */
    public function toggleStatus(Service $service)
    {
        $this->authorizeCompany($service);

        $service->update([
            'is_active' => !$service->is_active,
        ]);

        return back()->with(
            'success',
            $service->is_active
                ? 'La prestation a été activée.'
                : 'La prestation a été désactivée.'
        );
    }

    /**
     * Suppression.
     */
    public function destroy(Service $service)
    {
        $this->authorizeCompany($service);

        $service->delete();

        return redirect()
            ->route('company.services.index')
            ->with(
                'success',
                'La prestation a été supprimée avec succès.'
            );
    }

    /**
     * Vérifie que la prestation appartient
     * bien à l'entreprise connectée.
     */
    private function authorizeCompany(Service $service): void
    {
        if (
            (int) $service->company_id !==
            (int) auth()->user()->company_id
        ) {
            abort(403);
        }
    }
}