<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\EcommerceOrder;
use App\Models\EcommerceProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EcommerceOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $company = auth()->user()->company;

        $query = EcommerceOrder::query()
            ->where('company_id', $company->id)
            ->with(['client', 'items'])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | PAIEMENT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        $baseQuery = EcommerceOrder::query()
            ->where('company_id', $company->id);

        $stats = [
            'total' => (clone $baseQuery)->count(),

            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'confirmed' => (clone $baseQuery)
                ->where('status', 'confirmed')
                ->count(),

            'preparing' => (clone $baseQuery)
                ->where('status', 'preparing')
                ->count(),

            'shipped' => (clone $baseQuery)
                ->where('status', 'shipped')
                ->count(),

            'delivered' => (clone $baseQuery)
                ->where('status', 'delivered')
                ->count(),

            'cancelled' => (clone $baseQuery)
                ->where('status', 'cancelled')
                ->count(),

            'revenue' => (clone $baseQuery)
                ->whereIn('status', [
                    'confirmed',
                    'preparing',
                    'shipped',
                    'delivered',
                ])
                ->sum('total'),
        ];

        $orders = $query
            ->paginate(15)
            ->withQueryString();

        return view('company.ecommerce.orders.index', compact(
            'orders',
            'stats'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $company = auth()->user()->company;

        /*
        |--------------------------------------------------------------------------
        | PRODUITS DISPONIBLES DANS LE CATALOGUE E-COMMERCE
        |--------------------------------------------------------------------------
        */

        $products = EcommerceProduct::query()
            ->where('company_id', $company->id)
            ->where('is_published', true)
            ->with('product')
            ->whereHas('product', function ($query) use ($company) {
                $query->where('company_id', $company->id)
                    ->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CLIENTS ERP
        |--------------------------------------------------------------------------
        */

        $clients = Client::query()
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.ecommerce.orders.create', compact(
            'products',
            'clients'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([

            'client_id' => [
                'nullable',
                'integer',
                Rule::exists('clients', 'id')
                    ->where(fn ($query) =>
                        $query->where('company_id', $company->id)
                    ),
            ],

            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'customer_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'shipping_address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'shipping_city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'shipping_region' => [
                'nullable',
                'string',
                'max:100',
            ],

            'shipping_country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payment_method' => [
                'nullable',
                'string',
                'max:100',
            ],

            'customer_note' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'internal_note' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'shipping_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.ecommerce_product_id' => [
                'required',
                'integer',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.001',
            ],

            'items.*.unit_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $order = DB::transaction(function () use (
            $validated,
            $company
        ) {

            $subtotal = 0;

            $itemsData = [];


            foreach ($validated['items'] as $item) {

                $ecommerceProduct = EcommerceProduct::query()
                    ->where('company_id', $company->id)
                    ->where('id', $item['ecommerce_product_id'])
                    ->where('is_published', true)
                    ->with('product')
                    ->firstOrFail();

                $product = $ecommerceProduct->product;

                if (!$product) {
                    abort(422, 'Le produit sélectionné est introuvable.');
                }

                $unitPrice = isset($item['unit_price'])
                    ? (float) $item['unit_price']
                    : (float) $product->selling_price;

                $quantity = (float) $item['quantity'];

                $lineTotal = $unitPrice * $quantity;

                $subtotal += $lineTotal;

                $itemsData[] = [
                    'ecommerce_product_id' => $ecommerceProduct->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_reference' => $product->reference,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => $lineTotal,
                ];
            }


            $discount = (float) ($validated['discount'] ?? 0);
            $shipping = (float) ($validated['shipping_amount'] ?? 0);
            $tax = (float) ($validated['tax'] ?? 0);

            $total = max(
                0,
                $subtotal - $discount + $shipping + $tax
            );


            /*
            |--------------------------------------------------------------------------
            | RÉFÉRENCE
            |--------------------------------------------------------------------------
            */

            $reference = $this->generateReference($company->id);


            /*
            |--------------------------------------------------------------------------
            | COMMANDE
            |--------------------------------------------------------------------------
            */

            $order = EcommerceOrder::create([

                'company_id' => $company->id,

                'client_id' => $validated['client_id'] ?? null,

                'reference' => $reference,

                'status' => 'pending',

                'customer_name' => $validated['customer_name'],

                'customer_email' =>
                    $validated['customer_email'] ?? null,

                'customer_phone' =>
                    $validated['customer_phone'] ?? null,

                'shipping_address' =>
                    $validated['shipping_address'] ?? null,

                'shipping_city' =>
                    $validated['shipping_city'] ?? null,

                'shipping_region' =>
                    $validated['shipping_region'] ?? null,

                'shipping_country' =>
                    $validated['shipping_country'] ?? null,

                'subtotal' => $subtotal,

                'discount' => $discount,

                'shipping_amount' => $shipping,

                'tax' => $tax,

                'total' => $total,

                'payment_method' =>
                    $validated['payment_method'] ?? null,

                'payment_status' => 'pending',

                'customer_note' =>
                    $validated['customer_note'] ?? null,

                'internal_note' =>
                    $validated['internal_note'] ?? null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | LIGNES
            |--------------------------------------------------------------------------
            */

            foreach ($itemsData as $item) {

                $order->items()->create([
                    'company_id' => $company->id,
                    ...$item,
                ]);
            }


            return $order;
        });


        return redirect()
            ->route('company.ecommerce.orders.show', $order)
            ->with(
                'success',
                "La commande {$order->reference} a été créée avec succès."
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(EcommerceOrder $order)
    {
        $this->authorizeCompany($order);

        $order->load([
            'client',
            'items.product',
            'items.ecommerceProduct',
        ]);

        return view(
            'company.ecommerce.orders.show',
            compact('order')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        EcommerceOrder $order
    ) {
        $this->authorizeCompany($order);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'preparing',
                    'shipped',
                    'delivered',
                    'cancelled',
                ]),
            ],
        ]);

        $newStatus = $validated['status'];

        if ($order->status === 'cancelled') {
            return back()->with(
                'error',
                'Une commande annulée ne peut plus être modifiée.'
            );
        }

        if ($order->status === 'delivered') {
            return back()->with(
                'error',
                'Une commande livrée ne peut plus être modifiée.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HORODATAGE
        |--------------------------------------------------------------------------
        */

        $data = [
            'status' => $newStatus,
        ];

        if ($newStatus === 'confirmed') {
            $data['confirmed_at'] = now();
        }

        if ($newStatus === 'shipped') {
            $data['shipped_at'] = now();
        }

        if ($newStatus === 'delivered') {
            $data['delivered_at'] = now();
        }

        if ($newStatus === 'cancelled') {
            $data['cancelled_at'] = now();
        }

        $order->update($data);

        return back()->with(
            'success',
            "Le statut de la commande {$order->reference} a été mis à jour."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY / ANNULATION
    |--------------------------------------------------------------------------
    */

    public function destroy(EcommerceOrder $order)
    {
        $this->authorizeCompany($order);

        if ($order->status === 'delivered') {
            return back()->with(
                'error',
                'Une commande livrée ne peut pas être annulée.'
            );
        }

        if ($order->status === 'cancelled') {
            return back()->with(
                'error',
                'Cette commande est déjà annulée.'
            );
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()
            ->route('company.ecommerce.orders.index')
            ->with(
                'success',
                "La commande {$order->reference} a été annulée."
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GÉNÉRATION RÉFÉRENCE
    |--------------------------------------------------------------------------
    */

    private function generateReference(int $companyId): string
    {
        do {

            $reference =
                'WEB-' .
                now()->format('Ym') .
                '-' .
                strtoupper(
                    substr(
                        md5(uniqid('', true)),
                        0,
                        6
                    )
                );

        } while (
            EcommerceOrder::where('company_id', $companyId)
                ->where('reference', $reference)
                ->exists()
        );

        return $reference;
    }


    /*
    |--------------------------------------------------------------------------
    | ISOLATION ENTREPRISE
    |--------------------------------------------------------------------------
    */

    private function authorizeCompany(EcommerceOrder $order): void
    {
        if (
            !$order->company_id ||
            $order->company_id !== auth()->user()->company_id
        ) {
            abort(404);
        }
    }
}