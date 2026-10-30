<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Liste des catégories.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Category::where('company_id', $companyId);

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUT
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
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $categories = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'company.categories.index',
            compact('categories')
        );
    }


    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('company.categories.create');
    }


    /**
     * Enregistrer une catégorie.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | ENTREPRISE CONNECTÉE
        |--------------------------------------------------------------------------
        */

        $companyId = auth()->user()->company_id;

        /*
        |--------------------------------------------------------------------------
        | CRÉATION
        |--------------------------------------------------------------------------
        */

        Category::create([

            'company_id' => $companyId,

            'name' => $validated['name'],

            'slug' => Str::slug(
                $validated['name']
            ),

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $validated['is_active'],
        ]);

        return redirect()
            ->route('company.categories.index')
            ->with(
                'success',
                'Catégorie créée avec succès.'
            );
    }


    /**
     * Afficher une catégorie.
     */
    public function show(Category $category)
    {
        $this->checkCompany($category);

        return view(
            'company.categories.show',
            compact('category')
        );
    }


    /**
     * Formulaire de modification.
     */
    public function edit(Category $category)
    {
        $this->checkCompany($category);

        return view(
            'company.categories.edit',
            compact('category')
        );
    }


    /**
     * Modifier une catégorie.
     */
    public function update(
        Request $request,
        Category $category
    ) {

        $this->checkCompany($category);

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $category->update([

            'name' => $validated['name'],

            'slug' => Str::slug(
                $validated['name']
            ),

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $validated['is_active'],
        ]);

        return redirect()
            ->route('company.categories.index')
            ->with(
                'success',
                'Catégorie modifiée avec succès.'
            );
    }


    /**
     * Supprimer une catégorie.
     */
    public function destroy(Category $category)
    {
        $this->checkCompany($category);

        $category->delete();

        return redirect()
            ->route('company.categories.index')
            ->with(
                'success',
                'Catégorie supprimée avec succès.'
            );
    }


    /**
     * Activer / désactiver.
     */
    public function toggleStatus(Category $category)
    {
        $this->checkCompany($category);

        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return back()->with(
            'success',
            $category->is_active
                ? 'Catégorie activée.'
                : 'Catégorie désactivée.'
        );
    }


    /**
     * Vérifier que la catégorie appartient
     * à l'entreprise connectée.
     */
    private function checkCompany(Category $category): void
    {
        if (
            $category->company_id !==
            auth()->user()->company_id
        ) {
            abort(
                403,
                'Vous n’êtes pas autorisé à accéder à cette catégorie.'
            );
        }
    }
}