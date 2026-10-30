<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EcommerceProduct;
use App\Models\EcommerceReview;
use App\Models\EcommerceStoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StorefrontReviewController extends Controller
{
    public function store(Request $request, Company $company, string $slug): RedirectResponse
    {
        abort_unless($company->is_active, 404);

        EcommerceStoreSetting::query()
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->firstOrFail();

        $ecommerceProduct = EcommerceProduct::query()
            ->where('company_id', $company->id)
            ->where('slug', $slug)
            ->published()
            ->whereHas('product', function ($query) use ($company) {
                $query->where('company_id', $company->id)->where('is_active', true);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:100'],
            'comment' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        EcommerceReview::create([
            'company_id' => $company->id,
            'product_id' => $ecommerceProduct->product_id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'status' => 'pending',
            'published_at' => null,
        ]);

        return redirect()
            ->route('storefront.products.show', ['company' => $company->slug, 'slug' => $slug])
            ->with('review_success', 'Merci ! Votre avis sera visible après validation.');
    }
}
