<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EcommerceCollection;
use App\Models\EcommerceStoreSetting;

class StorefrontController extends Controller
{
    /**
     * Vérifier que la boutique est publique.
     */
    private function getStore(Company $company): EcommerceStoreSetting
    {
        abort_unless($company->is_active, 404);

        $settings = EcommerceStoreSetting::query()
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->firstOrFail();

        return $settings;
    }

    /**
     * Accueil de la boutique.
     */
    public function index(Company $company)
    {
        $settings = $this->getStore($company);

        $collections = EcommerceCollection::query()
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

            
$reviews = \App\Models\EcommerceReview::query()
    ->where('company_id', $company->id)
    ->where('status', 'published')
    ->whereNotNull('published_at')
    ->orderByDesc('published_at')
    ->limit(6)
    ->get();


        return view('storefront.index', compact(
            'company',
            'settings',
            'collections',
            'reviews'
        ));
    }

    /**
     * Liste des collections publiques.
     */
    public function collections(Company $company)
    {
        $settings = $this->getStore($company);

        $collections = EcommerceCollection::query()
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->withCount([
                'products as published_products_count' => function ($query) use ($company) {
                    $query->where('ecommerce_products.company_id', $company->id)
                        ->where('ecommerce_products.is_published', true);
                }
            ])
            ->orderBy('sort_order')
            ->paginate(12);

        return view('storefront.collections.index', compact(
            'company',
            'settings',
            'collections'
        ));
    }

    /**
     * Détail d'une collection publique.
     */
    public function showCollection(Company $company, string $slug)
    {
        $settings = $this->getStore($company);

        $collection = EcommerceCollection::query()
            ->where('company_id', $company->id)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $collection->products()
            ->with('product')
            ->where('ecommerce_products.company_id', $company->id)
            ->where('ecommerce_products.is_published', true)
            ->orderBy('ecommerce_collection_product.sort_order')
            ->paginate(12);

        return view('storefront.collections.show', compact(
            'company',
            'settings',
            'collection',
            'products'
        ));
    }
}
