<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EcommerceOrder;
use App\Models\EcommerceProduct;
use App\Models\EcommerceStoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StorefrontCheckoutController extends Controller
{
    private function getStore(Company $company): EcommerceStoreSetting
    {
        abort_unless($company->is_active, 404);

        return EcommerceStoreSetting::query()
            ->where('company_id', $company->getKey())
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function cartKey(Company $company): string
    {
        return 'storefront_cart_' . $company->getKey();
    }

    private function prepareItems(Company $company, array $cart): array
    {
        if (empty($cart)) {
            throw ValidationException::withMessages(['cart' => 'Votre panier est vide.']);
        }

        $products = EcommerceProduct::query()
            ->with('product')
            ->where('company_id', $company->getKey())
            ->published()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        $subtotal = 0;

        foreach ($cart as $id => $rawQuantity) {
            $entry = $products->get((int) $id);
            if (!$entry || !$entry->product || (int) $entry->product->company_id !== (int) $company->getKey()) {
                throw ValidationException::withMessages(['cart' => 'Un produit du panier n’est plus disponible.']);
            }

            $quantity = (int) $rawQuantity;
            $stock = (int) ($entry->product->stock_quantity ?? 0);
            if ($quantity < 1 || $quantity > 100 || $quantity > $stock) {
                throw ValidationException::withMessages(['cart' => 'Quantité indisponible pour : ' . $entry->product->name]);
            }

            $price = (float) $entry->sale_price > 0
                ? (float) $entry->sale_price
                : (float) ($entry->product->selling_price ?? 0);

            if ($price <= 0) {
                throw ValidationException::withMessages(['cart' => 'Prix invalide pour : ' . $entry->product->name]);
            }

            $lineTotal = round($price * $quantity, 2);
            $subtotal += $lineTotal;
            $items[] = [
                'entry' => $entry,
                'quantity' => $quantity,
                'unit_price' => $price,
                'total' => $lineTotal,
            ];
        }

        return ['items' => $items, 'subtotal' => round($subtotal, 2)];
    }

    public function index(Request $request, Company $company): View|RedirectResponse
    {
        $settings = $this->getStore($company);
        $cart = $request->session()->get($this->cartKey($company), []);
        if (!$cart) {
            return redirect()->route('storefront.cart.index', ['company' => $company->slug])
                ->withErrors(['cart' => 'Ajoutez un produit avant de commander.']);
        }

        try {
            $summary = $this->prepareItems($company, $cart);
        } catch (ValidationException $e) {
            return redirect()->route('storefront.cart.index', ['company' => $company->slug])
                ->withErrors($e->errors());
        }

        return view('storefront.checkout.index', [
            'company' => $company,
            'settings' => $settings,
            'items' => $summary['items'],
            'subtotal' => $summary['subtotal'],
        ]);
    }

    public function store(Request $request, Company $company): RedirectResponse
    {
        $this->getStore($company);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:150'],
            'shipping_region' => ['nullable', 'string', 'max:150'],
            'shipping_country' => ['required', 'string', 'max:150'],
            'payment_method' => ['required', 'in:cash_on_delivery,bank_transfer,orange_money,mtn_momo'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $cartKey = $this->cartKey($company);
        $cart = $request->session()->get($cartKey, []);

        // Relecture du panier dans une transaction. Les lignes de stock sont verrouillées
        // pour éviter qu'une autre commande simultanée ne dépasse le stock affiché.
        $order = DB::transaction(function () use ($company, $data, $cart) {
            $productIds = EcommerceProduct::query()
                ->where('company_id', $company->getKey())
                ->whereIn('id', array_keys($cart))
                ->pluck('product_id')
                ->filter()
                ->all();

            \App\Models\Product::query()
                ->where('company_id', $company->getKey())
                ->whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $summary = $this->prepareItems($company, $cart);

            $order = EcommerceOrder::create([
                'company_id' => $company->getKey(),
                'client_id' => null,
                'reference' => 'WEB-' . strtoupper(Str::random(16)),
                'status' => 'pending',
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_region' => $data['shipping_region'] ?? null,
                'shipping_country' => $data['shipping_country'],
                'subtotal' => $summary['subtotal'],
                'discount' => 0,
                'shipping_amount' => 0,
                'tax' => 0,
                'total' => $summary['subtotal'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($summary['items'] as $line) {
                $product = $line['entry']->product;
                $order->items()->create([
                    'company_id' => $company->getKey(),
                    'product_id' => $product->getKey(),
                    'ecommerce_product_id' => $line['entry']->getKey(),
                    'product_name' => $product->name,
                    'product_reference' => $product->sku,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount' => 0,
                    'tax' => 0,
                    'total' => $line['total'],
                ]);
            }

            return $order;
        });

        $request->session()->forget($cartKey);
        // Référence liée à la session : elle ne sert pas de secret d'accès public.
        $request->session()->put('storefront_last_order_' . $company->getKey(), $order->reference);

        return redirect()->route('storefront.checkout.success', [
            'company' => $company->slug,
            'reference' => $order->reference,
        ]);
    }

    public function success(Request $request, Company $company, string $reference): View
    {
        $settings = $this->getStore($company);
        abort_unless(
            hash_equals((string) $request->session()->get('storefront_last_order_' . $company->getKey(), ''), $reference),
            404
        );

        $order = EcommerceOrder::query()
            ->where('company_id', $company->getKey())
            ->where('reference', $reference)
            ->with('items')
            ->firstOrFail();

        return view('storefront.checkout.success', compact('company', 'settings', 'order'));
    }
}
