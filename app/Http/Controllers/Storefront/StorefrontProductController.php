<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EcommerceProduct;
use App\Models\EcommerceReview;
use App\Models\EcommerceStoreSetting;
use Illuminate\Http\Request;

class StorefrontProductController extends Controller
{
    private function getSettings(Company $company): EcommerceStoreSetting
    {
        abort_unless($company->is_active, 404);

        return EcommerceStoreSetting::query()
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function index(Request $request, Company $company)
    {
        $settings = $this->getSettings($company);
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'recent');
        $allowedSorts = ['recent', 'price_asc', 'price_desc', 'name'];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'recent';
        }

        $query = EcommerceProduct::query()
            ->with(['product', 'category'])
            ->where('company_id', $company->id)
            ->published()
            ->whereHas('product', function ($query) use ($company) {
                $query->where('company_id', $company->id)->where('is_active', true);
            });

        if ($search !== '') {
            $query->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('sale_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('sale_price', 'desc');
                break;
            case 'name':
                $query->orderBy(
                    \App\Models\Product::query()
                        ->select('name')
                        ->whereColumn('products.id', 'ecommerce_products.product_id')
                        ->limit(1)
                );
                break;
            default:
                $query->latest('id');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('storefront.products.index', compact(
            'company', 'settings', 'products', 'search', 'sort'
        ));
    }

    public function show(Company $company, string $slug)
    {
        $settings = $this->getSettings($company);

        $ecommerceProduct = EcommerceProduct::query()
            ->with(['product', 'category', 'collections', 'primaryImage'])
            ->where('company_id', $company->id)
            ->where('slug', $slug)
            ->published()
            ->whereHas('product', function ($query) use ($company) {
                $query->where('company_id', $company->id)->where('is_active', true);
            })
            ->firstOrFail();

        $relatedProducts = EcommerceProduct::query()
            ->with(['product', 'primaryImage'])
            ->where('company_id', $company->id)
            ->where('id', '!=', $ecommerceProduct->id)
            ->where('is_published', true)
            ->where('ecommerce_category_id', $ecommerceProduct->ecommerce_category_id)
            ->whereHas('product', function ($query) use ($company) {
                $query->where('company_id', $company->id)->where('is_active', true);
            })
            ->limit(4)
            ->get();

        $reviewQuery = EcommerceReview::query()
            ->forCompany($company->id)
            ->where('product_id', $ecommerceProduct->product_id)
            ->where('status', 'published');

        $reviewCount = (clone $reviewQuery)->count();
        $averageRating = $reviewCount > 0
            ? round((float) (clone $reviewQuery)->avg('rating'), 1)
            : 0;
        $reviews = (clone $reviewQuery)->latest('published_at')->paginate(5, ['*'], 'reviews_page');

        return view('storefront.products.show', compact(
            'company', 'settings', 'ecommerceProduct', 'relatedProducts',
            'reviews', 'reviewCount', 'averageRating'
        ));
    }
}
