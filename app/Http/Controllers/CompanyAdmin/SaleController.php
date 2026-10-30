<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use App\Models\CompanyPaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    /**
     * Liste des ventes
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Sale::query()
            ->with([
                'client',
                'user',
                'payments' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->where('company_id', $companyId);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'reference',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas('client', function ($clientQuery) use ($search) {

                    $clientQuery->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                });
            });
        }

        if ($request->filled('status')) {

            if (in_array(
                $request->status,
                ['completed', 'cancelled'],
                true
            )) {
                $query->where(
                    'status',
                    $request->status
                );
            }
        }

        if ($request->filled('payment_status')) {

            $paymentStatus = $request->payment_status;

            if ($paymentStatus === 'paid') {

                $query->whereRaw(
                    '(
                        SELECT COALESCE(SUM(sp.amount), 0)
                        FROM sale_payments sp
                        WHERE sp.sale_id = sales.id
                        AND sp.status = ?
                    ) >= sales.total',
                    ['completed']
                );
            }

            if ($paymentStatus === 'partial') {

                $query->whereRaw(
                    '(
                        SELECT COALESCE(SUM(sp.amount), 0)
                        FROM sale_payments sp
                        WHERE sp.sale_id = sales.id
                        AND sp.status = ?
                    ) > 0
                    AND
                    (
                        SELECT COALESCE(SUM(sp2.amount), 0)
                        FROM sale_payments sp2
                        WHERE sp2.sale_id = sales.id
                        AND sp2.status = ?
                    ) < sales.total',
                    ['completed', 'completed']
                );
            }

            if ($paymentStatus === 'unpaid') {

                $query->whereRaw(
                    '(
                        SELECT COALESCE(SUM(sp.amount), 0)
                        FROM sale_payments sp
                        WHERE sp.sale_id = sales.id
                        AND sp.status = ?
                    ) = 0',
                    ['completed']
                );
            }
        }

        $sales = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalSales = Sale::where(
            'company_id',
            $companyId
        )->count();

        $completedSales = Sale::where(
            'company_id',
            $companyId
        )
            ->where('status', 'completed')
            ->count();

        $cancelledSales = Sale::where(
            'company_id',
            $companyId
        )
            ->where('status', 'cancelled')
            ->count();

        $totalRevenue = Sale::where(
            'company_id',
            $companyId
        )
            ->where('status', 'completed')
            ->sum('total');

        $totalPaid = SalePayment::where(
            'company_id',
            $companyId
        )
            ->where('status', 'completed')
            ->sum('amount');

        $totalRemaining = max(
            0,
            (float) $totalRevenue - (float) $totalPaid
        );

        return view(
            'company.sales.index',
            compact(
                'sales',
                'totalSales',
                'completedSales',
                'cancelledSales',
                'totalRevenue',
                'totalPaid',
                'totalRemaining'
            )
        );
    }

    /**
     * Formulaire nouvelle vente
     */
    public function create()
    {
        $companyId = auth()->user()->company_id;

        $clients = Client::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $services = Service::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'company.sales.create',
            compact(
                'clients',
                'products',
                'services'
            )
        );
    }

    /**
     * Enregistrement d'une vente
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([

            'client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.type' => [
                'required',
                'in:product,service',
            ],

            'items.*.product_id' => [
                'nullable',
                'integer',
                'exists:products,id',
            ],

            'items.*.service_id' => [
                'nullable',
                'integer',
                'exists:services,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.discount' => [
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

            'notes' => [
                'nullable',
                'string',
            ],

            'initial_payment_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'initial_payment_method' => [
                'nullable',
                'in:cash,mobile_money,card,bank_transfer,other',
            ],

            'initial_payment_provider' => [
                'nullable',
                'in:orange_money,mtn_momo',
            ],

            'initial_payment_transaction_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'initial_payment_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'initial_payment_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vérification client
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['client_id'])) {

            $clientExists = Client::where(
                'company_id',
                $companyId
            )
                ->where(
                    'id',
                    $validated['client_id']
                )
                ->exists();

            if (!$clientExists) {

                throw ValidationException::withMessages([
                    'client_id' =>
                        'Ce client n’appartient pas à votre entreprise.',
                ]);
            }
        }

        return DB::transaction(function () use (
            $validated,
            $companyId
        ) {

            $company = auth()->user()
                ->company()
                ->lockForUpdate()
                ->first();

            if (!$company) {

                throw ValidationException::withMessages([
                    'sale' =>
                        'Votre entreprise est introuvable.',
                ]);
            }

            $subtotal = 0;

            $preparedItems = [];

            /*
            |--------------------------------------------------------------------------
            | Préparation des lignes
            |--------------------------------------------------------------------------
            */

            foreach ($validated['items'] as $index => $item) {

                $type = $item['type'];

                $quantity = (int) $item['quantity'];

                $unitPrice = (float) $item['unit_price'];

                $lineDiscount = (float) (
                    $item['discount'] ?? 0
                );

                if ($type === 'product') {

                    if (empty($item['product_id'])) {

                        throw ValidationException::withMessages([
                            "items.{$index}.product_id" =>
                                'Veuillez sélectionner un produit.',
                        ]);
                    }

                    $product = Product::where(
                        'company_id',
                        $companyId
                    )
                        ->where(
                            'id',
                            $item['product_id']
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {

                        throw ValidationException::withMessages([
                            'items' =>
                                'Un des produits sélectionnés est invalide.',
                        ]);
                    }

                    if (!$product->is_active) {

                        throw ValidationException::withMessages([
                            'items' =>
                                "Le produit {$product->name} est désactivé.",
                        ]);
                    }

                    if (
                        $quantity >
                        $product->stock_quantity
                    ) {

                        throw ValidationException::withMessages([
                            'items' =>
                                "Stock insuffisant pour {$product->name}. " .
                                "Stock disponible : {$product->stock_quantity}.",
                        ]);
                    }

                    $lineSubtotal =
                        $quantity * $unitPrice;

                    if ($lineDiscount > $lineSubtotal) {

                        throw ValidationException::withMessages([
                            "items.{$index}.discount" =>
                                "La remise de la ligne {$product->name} " .
                                "ne peut pas dépasser son montant.",
                        ]);
                    }

                    $lineTotal =
                        $lineSubtotal - $lineDiscount;

                    $subtotal += $lineTotal;

                    $preparedItems[] = [
                        'type' => 'product',
                        'product' => $product,
                        'service' => null,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount' => $lineDiscount,
                        'total' => $lineTotal,
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Prestation
                |--------------------------------------------------------------------------
                */

                if (empty($item['service_id'])) {

                    throw ValidationException::withMessages([
                        "items.{$index}.service_id" =>
                            'Veuillez sélectionner une prestation.',
                    ]);
                }

                $service = Service::where(
                    'company_id',
                    $companyId
                )
                    ->where(
                        'id',
                        $item['service_id']
                    )
                    ->first();

                if (!$service) {

                    throw ValidationException::withMessages([
                        'items' =>
                            'Une des prestations sélectionnées est invalide.',
                    ]);
                }

                if (!$service->is_active) {

                    throw ValidationException::withMessages([
                        'items' =>
                            "La prestation {$service->name} est désactivée.",
                    ]);
                }

                $lineSubtotal =
                    $quantity * $unitPrice;

                if ($lineDiscount > $lineSubtotal) {

                    throw ValidationException::withMessages([
                        "items.{$index}.discount" =>
                            "La remise de la prestation {$service->name} " .
                            "ne peut pas dépasser son montant.",
                    ]);
                }

                $lineTotal =
                    $lineSubtotal - $lineDiscount;

                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'type' => 'service',
                    'product' => null,
                    'service' => $service,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $lineDiscount,
                    'total' => $lineTotal,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Remise / taxe globale
            |--------------------------------------------------------------------------
            */

            $discount = (float) (
                $validated['discount'] ?? 0
            );

            $tax = (float) (
                $validated['tax'] ?? 0
            );

            if ($discount > $subtotal) {

                throw ValidationException::withMessages([
                    'discount' =>
                        'La remise ne peut pas être supérieure au sous-total.',
                ]);
            }

            $total =
                $subtotal -
                $discount +
                $tax;

            $total = max(0, $total);

            /*
            |--------------------------------------------------------------------------
            | Référence
            |--------------------------------------------------------------------------
            */

            $prefix = $company->invoice_prefix ?: 'FAC-';

            $reference =
                $prefix .
                now()->format('YmdHis');

            $counter = 1;

            while (
                Sale::where(
                    'company_id',
                    $companyId
                )
                    ->where(
                        'reference',
                        $reference
                    )
                    ->exists()
            ) {

                $reference =
                    $prefix .
                    now()->format('YmdHis') .
                    '-' .
                    $counter;

                $counter++;
            }

            /*
            |--------------------------------------------------------------------------
            | Création vente
            |--------------------------------------------------------------------------
            */

            $sale = Sale::create([
                'company_id' => $companyId,

                'client_id' =>
                    $validated['client_id'] ?? null,

                'user_id' =>
                    auth()->id(),

                'reference' =>
                    $reference,

                'subtotal' =>
                    $subtotal,

                'discount' =>
                    $discount,

                'tax' =>
                    $tax,

                'total' =>
                    $total,

                'status' =>
                    'completed',

                'notes' =>
                    $validated['notes'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Création des lignes
            |--------------------------------------------------------------------------
            */

            foreach ($preparedItems as $item) {

                /*
                | Prestation
                */

                if ($item['type'] === 'service') {

                    SaleItem::create([
                        'sale_id' =>
                            $sale->id,

                        'type' =>
                            'service',

                        'product_id' =>
                            null,

                        'service_id' =>
                            $item['service']->id,

                        'quantity' =>
                            $item['quantity'],

                        'unit_price' =>
                            $item['unit_price'],

                        'discount' =>
                            $item['discount'],

                        'total' =>
                            $item['total'],
                    ]);

                    continue;
                }

                /*
                | Produit
                */

                $product =
                    $item['product'];

                $stockBefore =
                    $product->stock_quantity;

                $stockAfter =
                    $stockBefore -
                    $item['quantity'];

                SaleItem::create([
                    'sale_id' =>
                        $sale->id,

                    'type' =>
                        'product',

                    'product_id' =>
                        $product->id,

                    'service_id' =>
                        null,

                    'quantity' =>
                        $item['quantity'],

                    'unit_price' =>
                        $item['unit_price'],

                    'discount' =>
                        $item['discount'],

                    'total' =>
                        $item['total'],
                ]);

                $product->update([
                    'stock_quantity' =>
                        $stockAfter,
                ]);

                StockMovement::create([
                    'company_id' =>
                        $companyId,

                    'product_id' =>
                        $product->id,

                    'user_id' =>
                        auth()->id(),

                    'type' =>
                        'exit',

                    'quantity' =>
                        $item['quantity'],

                    'stock_before' =>
                        $stockBefore,

                    'stock_after' =>
                        $stockAfter,

                    'reason' =>
                        'Vente ' .
                        $sale->reference,

                    'note' =>
                        'Sortie automatique liée à une vente.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Paiement initial
            |--------------------------------------------------------------------------
            */

            $initialPaymentAmount =
                (float) (
                    $validated['initial_payment_amount'] ?? 0
                );

            if ($initialPaymentAmount > 0) {

                if ($initialPaymentAmount > $total) {

                    throw ValidationException::withMessages([
                        'initial_payment_amount' =>
                            'Le montant payé ne peut pas être supérieur au total de la vente.',
                    ]);
                }

                $method =
                    $validated['initial_payment_method']
                    ?? 'cash';

                if ($method === 'mobile_money') {

                    $provider =
                        $validated['initial_payment_provider']
                        ?? null;

                    $phone =
                        $validated['initial_payment_phone']
                        ?? null;

                    if (!$provider) {

                        throw ValidationException::withMessages([
                            'initial_payment_provider' =>
                                'Veuillez sélectionner le fournisseur Mobile Money.',
                        ]);
                    }

                    if (!$phone) {

                        throw ValidationException::withMessages([
                            'initial_payment_phone' =>
                                'Le numéro de téléphone est obligatoire pour un paiement Mobile Money.',
                        ]);
                    }

                    $paymentSetting =
                        CompanyPaymentSetting::where(
                            'company_id',
                            $companyId
                        )->first();

                    if (!$paymentSetting) {

                        throw ValidationException::withMessages([
                            'initial_payment_provider' =>
                                'Aucune configuration de paiement Mobile Money n’est disponible pour votre entreprise.',
                        ]);
                    }

                    if (
                        $provider === 'orange_money' &&
                        !$paymentSetting->orange_enabled
                    ) {

                        throw ValidationException::withMessages([
                            'initial_payment_provider' =>
                                'Orange Money n’est pas activé dans les paramètres de paiement.',
                        ]);
                    }

                    if (
                        $provider === 'mtn_momo' &&
                        !$paymentSetting->mtn_enabled
                    ) {

                        throw ValidationException::withMessages([
                            'initial_payment_provider' =>
                                'MTN Mobile Money n’est pas activé dans les paramètres de paiement.',
                        ]);
                    }

                    $paymentStatus = 'pending';

                } else {

                    $provider = null;
                    $phone = null;

                    $paymentStatus = 'completed';
                }

                SalePayment::create([
                    'company_id' =>
                        $companyId,

                    'sale_id' =>
                        $sale->id,

                    'user_id' =>
                        auth()->id(),

                    'amount' =>
                        $initialPaymentAmount,

                    'method' =>
                        $method,

                    'provider' =>
                        $provider,

                    'transaction_reference' =>
                        $validated[
                            'initial_payment_transaction_reference'
                        ] ?? null,

                    'phone' =>
                        $phone,

                    'note' =>
                        $validated[
                            'initial_payment_note'
                        ] ?? null,

                    'status' =>
                        $paymentStatus,
                ]);
            }

            return redirect()
                ->route(
                    'company.sales.show',
                    $sale
                )
                ->with(
                    'success',
                    'Vente enregistrée avec succès.'
                );
        });
    }

    /**
     * Afficher une vente
     */
    public function show(Sale $sale)
    {
        $companyId =
            auth()->user()->company_id;

        $sale = Sale::query()
            ->where(
                'company_id',
                $companyId
            )
            ->with([
                'company',
                'client',
                'user',
                'items.product',
                'items.service',
                'payments' => function ($query) {
                    $query
                        ->with('user')
                        ->latest();
                },
            ])
            ->findOrFail($sale->id);

        $settings =
            CompanyPaymentSetting::where(
                'company_id',
                $companyId
            )->first();

        return view(
            'company.sales.show',
            compact(
                'sale',
                'settings'
            )
        );
    }

    /**
     * Annulation d'une vente
     */
    public function destroy(Sale $sale)
    {
        $sale = Sale::with([
            'items.product',
            'items.service',
            'payments',
        ])
            ->where(
                'company_id',
                auth()->user()->company_id
            )
            ->findOrFail($sale->id);

        if ($sale->status === 'cancelled') {

            return back()->withErrors([
                'sale' =>
                    'Cette vente est déjà annulée.',
            ]);
        }

        $pendingPayments =
            $sale->payments
                ->where('status', 'pending')
                ->count();

        if ($pendingPayments > 0) {

            return back()->withErrors([
                'sale' =>
                    'Cette vente possède un paiement en attente. Veuillez le traiter avant d’annuler la vente.',
            ]);
        }

        $paidAmount =
            $sale->payments
                ->where('status', 'completed')
                ->sum('amount');

        if ((float) $paidAmount > 0) {

            return back()->withErrors([
                'sale' =>
                    'Cette vente possède déjà un paiement de ' .
                    number_format(
                        (float) $paidAmount,
                        0,
                        ',',
                        ' '
                    ) .
                    ' FCFA. Une procédure de remboursement sera nécessaire avant son annulation.',
            ]);
        }

        DB::transaction(function () use ($sale) {

            foreach ($sale->items as $item) {

                /*
                |--------------------------------------------------------------------------
                | Une prestation ne touche jamais au stock
                |--------------------------------------------------------------------------
                */

                if ($item->type === 'service') {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Produit : restauration du stock
                |--------------------------------------------------------------------------
                */

                $product = Product::where(
                    'id',
                    $item->product_id
                )
                    ->where(
                        'company_id',
                        $sale->company_id
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    continue;
                }

                $stockBefore =
                    $product->stock_quantity;

                $stockAfter =
                    $stockBefore +
                    $item->quantity;

                $product->update([
                    'stock_quantity' =>
                        $stockAfter,
                ]);

                StockMovement::create([
                    'company_id' =>
                        $sale->company_id,

                    'product_id' =>
                        $product->id,

                    'user_id' =>
                        auth()->id(),

                    'type' =>
                        'adjustment',

                    'quantity' =>
                        $item->quantity,

                    'stock_before' =>
                        $stockBefore,

                    'stock_after' =>
                        $stockAfter,

                    'reason' =>
                        'Annulation vente ' .
                        $sale->reference,

                    'note' =>
                        'Réintégration automatique du stock.',
                ]);
            }

            $sale->update([
                'status' =>
                    'cancelled',
            ]);
        });

        return redirect()
            ->route(
                'company.sales.index'
            )
            ->with(
                'success',
                'Vente annulée et stock restauré.'
            );
    }

    /**
     * Facture
     */
    public function invoice(Sale $sale)
    {
        $companyId =
            auth()->user()->company_id;

        $sale = Sale::query()
            ->where(
                'company_id',
                $companyId
            )
            ->with([
                'company',
                'client',
                'user',
                'items.product',
                'items.service',
                'payments' => function ($query) {
                    $query
                        ->with('user')
                        ->latest();
                },
            ])
            ->findOrFail($sale->id);

        return view(
            'company.sales.invoice',
            compact('sale')
        );
    }
}