<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Liste des produits
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Product::with('category')
            ->where('company_id', $companyId);

        // Recherche
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filtre catégorie
        if ($request->filled('category_id')) {

            $query->where(
                'category_id',
                $request->category_id
            );
        }

        // Filtre statut
        if ($request->filled('status')) {

            if ($request->status === 'active') {

                $query->where('is_active', true);

            } elseif ($request->status === 'inactive') {

                $query->where('is_active', false);

            } elseif ($request->status === 'low_stock') {

                $query->whereColumn(
                    'stock_quantity',
                    '<=',
                    'min_stock'
                );

            } elseif ($request->status === 'out_of_stock') {

                $query->where(
                    'stock_quantity',
                    '<=',
                    0
                );
            }
        }

        $products = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where(
            'company_id',
            $companyId
        )
            ->orderBy('name')
            ->get();

        return view(
            'company.products.index',
            compact(
                'products',
                'categories'
            )
        );
    }

    /**
     * Formulaire création
     */
    public function create()
    {
        $companyId = auth()->user()->company_id;

        $categories = Category::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'company.products.create',
            compact('categories')
        );
    }

    /**
     * Enregistrement
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

            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'company_id',
                            $companyId
                        )
                    ),
            ],

            'barcode' => [
                'nullable',
                'string',
                'max:100',
            ],

            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'company_id',
                            $companyId
                        )
                    ),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'min_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $validated['company_id'] = $companyId;

        Product::create($validated);

        return redirect()
            ->route('company.products.index')
            ->with(
                'success',
                'Produit ajouté avec succès.'
            );
    }

    /**
     * Affichage
     */
    public function show($id)
    {
        $companyId = auth()->user()->company_id;

        $product = Product::with('category')
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return view(
            'company.products.show',
            compact('product')
        );
    }

    /**
     * Formulaire modification
     */
    public function edit($id)
    {
        $companyId = auth()->user()->company_id;

        $product = Product::where(
            'company_id',
            $companyId
        )->findOrFail($id);

        $categories = Category::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'company.products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }

    /**
     * Modification
     */
    public function update(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;

        $product = Product::where(
            'company_id',
            $companyId
        )->findOrFail($id);

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'sku' => [
                'required',
                'string',
                'max:100',

                Rule::unique('products', 'sku')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'company_id',
                            $companyId
                        )
                    )
                    ->ignore($product->id),
            ],

            'barcode' => [
                'nullable',
                'string',
                'max:100',
            ],

            'category_id' => [
                'nullable',
                'integer',

                Rule::exists('categories', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'company_id',
                            $companyId
                        )
                    ),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'min_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $product->update($validated);

        return redirect()
            ->route('company.products.index')
            ->with(
                'success',
                'Produit modifié avec succès.'
            );
    }

    /**
     * Suppression
     */
    public function destroy($id)
    {
        $companyId = auth()->user()->company_id;

        $product = Product::where(
            'company_id',
            $companyId
        )->findOrFail($id);

        $product->delete();

        return redirect()
            ->route('company.products.index')
            ->with(
                'success',
                'Produit supprimé avec succès.'
            );
    }
}