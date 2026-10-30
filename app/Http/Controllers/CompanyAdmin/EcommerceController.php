<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;
use App\Models\EcommerceCategory;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EcommerceController extends Controller
{
    /**
     * Tableau de bord e-commerce.
     */
    public function index(): View
    {
        $companyId = $this->getCompanyId();

        $query = EcommerceProduct::query()
            ->where('company_id', $companyId);

        $stats = [
            'totalProducts' => (clone $query)->count(),

            'publishedProducts' => (clone $query)
                ->where('is_published', true)
                ->count(),

            'featuredProducts' => (clone $query)
                ->where('is_featured', true)
                ->count(),

            'unpublishedProducts' => (clone $query)
                ->where('is_published', false)
                ->count(),
        ];

        $recentProducts = EcommerceProduct::query()
            ->with(['product', 'primaryImage'])
            ->where('company_id', $companyId)
            ->latest()
            ->take(10)
            ->get();

        return view('company.ecommerce.index', compact(
            'stats',
            'recentProducts'
        ));
    }

    /**
     * Liste des produits du catalogue e-commerce.
     */
    public function products(Request $request): View
    {
        $companyId = $this->getCompanyId();

        $query = EcommerceProduct::query()
            ->with(['product', 'primaryImage'])
            ->where('company_id', $companyId);

        // Recherche par nom ou référence SKU.
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->whereHas('product', function ($productQuery) use ($search) {
                $productQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            });
        }

        // Filtrage par statut.
        switch ($request->input('status')) {
            case 'published':
                $query->where('is_published', true);
                break;

            case 'unpublished':
                $query->where('is_published', false);
                break;

            case 'featured':
                $query->where('is_featured', true);
                break;
        }

        $ecommerceProducts = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view(
            'company.ecommerce.products.index',
            compact('ecommerceProducts')
        );
    }

    /**
     * Formulaire d'ajout d'un produit au catalogue.
     *
     * Le produit doit déjà exister dans le catalogue ERP.
     */
    public function create(): View
    {
        $companyId = $this->getCompanyId();

        $products = Product::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereDoesntHave('ecommerce')
            ->orderBy('name')
            ->get();

            
            $categories = EcommerceCategory::query()
                ->where('company_id', auth()->user()->company_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();


        return view('company.ecommerce.products.create', compact(
            'products','categories'
        ));
    }

    /**
     * Ajouter un produit ERP au catalogue e-commerce.
     *
     * Le prix de vente reste enregistré dans products.selling_price.
     * Aucun champ de prix n'est ajouté à ecommerce_products.
     */
    public function store(Request $request): RedirectResponse
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'seo_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'is_published' => [
                'sometimes',
                'boolean',
            ],

            'is_featured' => [
                'sometimes',
                'boolean',
            ],

            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],


            
            'ecommerce_category_id' => [
                'nullable',
                'integer',
                \Illuminate\Validation\Rule::exists(
                    'ecommerce_categories',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'company_id',
                        auth()->user()->company_id
                    )->where('is_active', true);
                }),
            ],

        ]);

        // Vérifier que le produit appartient bien à l'entreprise.
        $product = Product::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->findOrFail($validated['product_id']);

        // Empêcher l'ajout en double.
        $alreadyExists = EcommerceProduct::query()
            ->where('company_id', $companyId)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyExists) {
            return back()
                ->withInput()
                ->with('error', 'Ce produit est déjà présent dans le catalogue e-commerce.');
        }

        $isPublished = $request->boolean('is_published');

        $slug = $this->makeUniqueSlug($product->name);

        DB::transaction(function () use (
            $validated,
            $companyId,
            $product,
            $isPublished,
            $slug,
            $request
        ) {
            $ecommerceProduct = EcommerceProduct::create([
                'company_id' => $companyId,
                'product_id' => $product->id,

                'slug' => $slug,

                'short_description' =>
                    $validated['short_description'] ?? null,

                'description' =>
                    $validated['description'] ?? null,

                'seo_title' =>
                    $validated['seo_title'] ?? null,

                'seo_description' =>
                    $validated['seo_description'] ?? null,

                'is_published' => $isPublished,

                'is_featured' => $request->boolean('is_featured'),

                'published_at' => $isPublished
                    ? now()
                    : null,

                'sort_order' => 0,
                'ecommerce_category_id' => $validated['ecommerce_category_id'] ?? null,
            ]);

            $this->saveImages($request, $ecommerceProduct, $companyId);
        });

        return redirect()
            ->route('company.ecommerce.products.index')
            ->with('success', 'Le produit a été ajouté au catalogue e-commerce.');
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(EcommerceProduct $ecommerceProduct): View
    {
        $this->authorizeCompany($ecommerceProduct);

        $ecommerceProduct->load(['product', 'images']);

        
        $categories = EcommerceCategory::query()
            ->where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'company.ecommerce.products.edit',
            compact('ecommerceProduct','categories')
        );
    }

    /**
     * Mettre à jour les informations e-commerce.
     *
     * Le prix de vente n'est pas modifié ici :
     * il appartient au modèle Product.
     */
    public function update(
        Request $request,
        EcommerceProduct $ecommerceProduct
    ): RedirectResponse {
        $this->authorizeCompany($ecommerceProduct);

        $validated = $request->validate([
            'short_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'seo_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'is_published' => [
                'sometimes',
                'boolean',
            ],

            'is_featured' => [
                'sometimes',
                'boolean',
            ],

            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],


            
        'ecommerce_category_id' => [
            'nullable',
            'integer',
            \Illuminate\Validation\Rule::exists(
                'ecommerce_categories',
                'id'
            )->where(function ($query) {
                $query->where(
                    'company_id',
                    auth()->user()->company_id
                )->where('is_active', true);
            }),
        ],

        ]);

        $request->validate([
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
            'primary_image_id' => ['nullable', 'integer'],
        ]);

        $wasPublished = (bool) $ecommerceProduct->is_published;
        $isPublished = $request->boolean('is_published');

        $data = [
            'short_description' =>
                $validated['short_description'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'seo_title' =>
                $validated['seo_title'] ?? null,

            'seo_description' =>
                $validated['seo_description'] ?? null,

            'is_published' => $isPublished,

            'is_featured' => $request->boolean('is_featured'),
            'ecommerce_category_id' => $validated['ecommerce_category_id'] ?? null,
        ];

        if ($isPublished && !$wasPublished) {
            $data['published_at'] = now();
        } elseif (!$isPublished) {
            $data['published_at'] = null;
        }

        $removedPaths = [];
        DB::transaction(function () use ($ecommerceProduct, $data, $request, &$removedPaths) {
            $ecommerceProduct->update($data);

            $ids = $request->input('remove_image_ids', []);
            $toDelete = $ecommerceProduct->images()->whereIn('id', $ids)->get();
            foreach ($toDelete as $image) {
                $removedPaths[] = $image->image_path;
                $image->delete();
            }

            $remaining = $ecommerceProduct->images()->count();
            $incoming = count($request->file('images', []));
            if ($remaining + $incoming > 5) {
                throw ValidationException::withMessages([
                    'images' => 'Maximum 5 photos par produit.',
                ]);
            }

            $this->saveImages($request, $ecommerceProduct, $this->getCompanyId());

            $primaryId = $request->input('primary_image_id');
            if ($primaryId) {
                $selected = $ecommerceProduct->images()->whereKey($primaryId)->first();
                if ($selected) {
                    $ecommerceProduct->images()->update(['is_primary' => false]);
                    $selected->update(['is_primary' => true]);
                }
            }
            $this->ensurePrimaryImage($ecommerceProduct);
        });

        foreach ($removedPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('company.ecommerce.products.index')
            ->with('success', 'Le produit e-commerce a été mis à jour.');
    }

    /**
     * Retirer un produit du catalogue e-commerce.
     *
     * Le produit ERP n'est pas supprimé.
     */
    public function destroy(
        EcommerceProduct $ecommerceProduct
    ): RedirectResponse {
        $this->authorizeCompany($ecommerceProduct);

        $paths = $ecommerceProduct->images()->pluck('image_path')->all();
        $ecommerceProduct->delete();
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('company.ecommerce.products.index')
            ->with('success', 'Le produit a été retiré du catalogue e-commerce.');
    }

    /** Stocker les nouvelles photos du produit. */
    private function saveImages(Request $request, EcommerceProduct $item, int $companyId): void
    {
        $files = $request->file('images', []);
        if (!$files) {
            return;
        }

        $existing = $item->images()->count();
        if ($existing + count($files) > 5) {
            throw ValidationException::withMessages([
                'images' => 'Maximum 5 photos par produit.',
            ]);
        }

        foreach ($files as $index => $file) {
            $path = $file->store("ecommerce/{$companyId}/products/{$item->id}", 'public');
            $item->images()->create([
                'company_id' => $companyId,
                'image_path' => $path,
                'alt_text' => $item->product?->name,
                'is_primary' => $existing === 0 && $index === 0,
                'sort_order' => $existing + $index,
            ]);
        }
    }

    /** Assurer une image principale si des images existent. */
    private function ensurePrimaryImage(EcommerceProduct $item): void
    {
        if ($item->images()->where('is_primary', true)->exists()) {
            return;
        }
        $first = $item->images()->orderBy('sort_order')->first();
        if ($first) {
            $first->update(['is_primary' => true]);
        }
    }

    /**
     * Retourner l'identifiant de l'entreprise connectée.
     */
    private function getCompanyId(): int
    {
        $companyId = auth()->user()?->company_id;

        abort_if(
            !$companyId,
            403,
            'Aucune entreprise n’est associée à votre compte.'
        );

        return (int) $companyId;
    }

    /**
     * Vérifier que le produit e-commerce appartient
     * à l'entreprise de l'utilisateur connecté.
     */
    private function authorizeCompany(
        EcommerceProduct $ecommerceProduct
    ): void {
        abort_unless(
            $ecommerceProduct->company_id === $this->getCompanyId(),
            403,
            'Vous n’êtes pas autorisé à accéder à ce produit.'
        );
    }

    /**
     * Générer un slug unique.
     */
    private function makeUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'produit';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            EcommerceProduct::query()
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}