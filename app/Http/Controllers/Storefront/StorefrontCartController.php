<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EcommerceProduct;
use App\Models\EcommerceStoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontCartController extends Controller
{
    private function getStore(Company $company): EcommerceStoreSetting
    {
        abort_unless($company->is_active, 404);

        return EcommerceStoreSetting::query()
            ->where('company_id', $company->getKey())
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function sessionKey(Company $company): string
    {
        return 'storefront_cart_' . $company->getKey();
    }

    private function eligibleProduct(Company $company, int $id): EcommerceProduct
    {
        return EcommerceProduct::query()
            ->with(['product', 'primaryImage'])
            ->where('company_id', $company->getKey())
            ->published()
            ->whereHas('product', fn ($query) => $query->where('company_id', $company->getKey()))
            ->whereKey($id)
            ->firstOrFail();
    }

    public function index(Request $request, Company $company): View
    {
        $settings = $this->getStore($company);
        $cart = $request->session()->get($this->sessionKey($company), []);
        $products = EcommerceProduct::query()
            ->with(['product', 'primaryImage'])
            ->where('company_id', $company->getKey())
            ->published()
            ->whereHas('product', fn ($query) => $query->where('company_id', $company->getKey()))
            ->whereIn('id', array_keys($cart))
            ->get();

        $items = $products->map(function (EcommerceProduct $item) use ($cart) {
            $quantity = max(1, (int) ($cart[$item->id] ?? 1));
            $price = (float) $item->sale_price > 0
                ? (float) $item->sale_price
                : (float) ($item->product?->selling_price ?? 0);

            return [
                'ecommerce_product' => $item,
                'quantity' => $quantity,
                'unit_price' => $price,
                'subtotal' => round($price * $quantity, 2),
            ];
        });

        $total = round($items->sum('subtotal'), 2);

        return view('storefront.cart.index', compact('company', 'settings', 'items', 'total'));
    }

    public function store(Request $request, Company $company): RedirectResponse
    {
        $this->getStore($company);
        $validated = $request->validate([
            'ecommerce_product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $product = $this->eligibleProduct($company, (int) $validated['ecommerce_product_id']);
        $key = $this->sessionKey($company);
        $cart = $request->session()->get($key, []);
        $current = (int) ($cart[$product->id] ?? 0);
        $newQuantity = $current + (int) $validated['quantity'];

        $stock = max(0, (int) ($product->product?->stock_quantity ?? 0));
        if ($newQuantity > $stock) {
            return back()->withErrors(['quantity' => "Stock insuffisant. Disponible : {$stock}."]);
        }

        if ($newQuantity > 100) {
            return back()->withErrors(['quantity' => 'Maximum 100 unités par produit.']);
        }

        $cart[$product->id] = $newQuantity;
        $request->session()->put($key, $cart);

        return redirect()->route('storefront.cart.index', $company)
            ->with('success', 'Produit ajouté au panier.');
    }

    public function update(Request $request, Company $company, int $productId): RedirectResponse
    {
        $this->getStore($company);
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $key = $this->sessionKey($company);
        $cart = $request->session()->get($key, []);
        if (!array_key_exists($productId, $cart)) {
            abort(404);
        }

        $product = $this->eligibleProduct($company, $productId);
        $stock = max(0, (int) ($product->product?->stock_quantity ?? 0));
        if ((int) $validated['quantity'] > $stock) {
            return back()->withErrors(['quantity' => "Stock insuffisant. Disponible : {$stock}."]);
        }
        $cart[$productId] = (int) $validated['quantity'];
        $request->session()->put($key, $cart);

        return back()->with('success', 'Quantité mise à jour.');
    }

    public function destroy(Request $request, Company $company, int $productId): RedirectResponse
    {
        $this->getStore($company);
        $key = $this->sessionKey($company);
        $cart = $request->session()->get($key, []);
        unset($cart[$productId]);
        $request->session()->put($key, $cart);

        return back()->with('success', 'Produit retiré du panier.');
    }

    public function clear(Request $request, Company $company): RedirectResponse
    {
        $this->getStore($company);
        $request->session()->forget($this->sessionKey($company));

        return back()->with('success', 'Panier vidé.');
    }
}
