<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyPaymentSetting;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    /**
     * Liste des achats.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Purchase::query()
            ->with([
                'supplier',
                'user',
                'payments' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->where('company_id', $companyId);

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'reference',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'supplier',
                    function ($supplierQuery) use ($search) {

                        $supplierQuery
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'phone',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE STATUT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            if (
                in_array(
                    $request->status,
                    [
                        'completed',
                        'cancelled',
                    ],
                    true
                )
            ) {

                $query->where(
                    'status',
                    $request->status
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE PAIEMENT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_status')) {

            $paymentStatus = $request->payment_status;

            if ($paymentStatus === 'paid') {

                $query->whereRaw(
                    '(
                        SELECT COALESCE(SUM(pp.amount), 0)
                        FROM purchase_payments pp
                        WHERE pp.purchase_id = purchases.id
                        AND pp.status = ?
                    ) >= purchases.total',
                    ['completed']
                );
            }

            if ($paymentStatus === 'partial') {

                $query->whereRaw(
                    '(
                        SELECT COALESCE(SUM(pp.amount), 0)
                        FROM purchase_payments pp
                        WHERE pp.purchase_id = purchases.id
                        AND pp.status = ?
                    ) > 0
                    AND
                    (
                        SELECT COALESCE(SUM(pp2.amount), 0)
                        FROM purchase_payments pp2
                        WHERE pp2.purchase_id = purchases.id
                        AND pp2.status = ?
                    ) < purchases.total',
                    [
                        'completed',
                        'completed',
                    ]
                );
            }

            if ($paymentStatus === 'unpaid') {

                $query->whereRaw(
                    '(
                        SELECT COALESCE(SUM(pp.amount), 0)
                        FROM purchase_payments pp
                        WHERE pp.purchase_id = purchases.id
                        AND pp.status = ?
                    ) = 0',
                    ['completed']
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES ACHATS
        |--------------------------------------------------------------------------
        */

        $totalPurchases = Purchase::query()
            ->where(
                'company_id',
                $companyId
            )
            ->count();

        $completedPurchases = Purchase::query()
            ->where(
                'company_id',
                $companyId
            )
            ->where(
                'status',
                'completed'
            )
            ->count();

        $cancelledPurchases = Purchase::query()
            ->where(
                'company_id',
                $companyId
            )
            ->where(
                'status',
                'cancelled'
            )
            ->count();

        $totalExpenses = Purchase::query()
            ->where(
                'company_id',
                $companyId
            )
            ->where(
                'status',
                'completed'
            )
            ->sum('total');

        /*
        |--------------------------------------------------------------------------
        | TOTAL PAYÉ
        |--------------------------------------------------------------------------
        */

        $totalPaid = PurchasePayment::query()
            ->where(
                'company_id',
                $companyId
            )
            ->where(
                'status',
                'completed'
            )
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | RESTE À PAYER
        |--------------------------------------------------------------------------
        */

        $totalRemaining = max(
            0,
            $totalExpenses - $totalPaid
        );

        /*
        |--------------------------------------------------------------------------
        | LISTE
        |--------------------------------------------------------------------------
        */

        $purchases = $query
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'company.purchases.index',
            compact(
                'purchases',
                'totalPurchases',
                'completedPurchases',
                'cancelledPurchases',
                'totalExpenses',
                'totalPaid',
                'totalRemaining'
            )
        );
    }


    /**
     * Formulaire de création.
     */
    public function create()
    {
        $companyId = auth()->user()->company_id;

        $suppliers = Supplier::query()
            ->where(
                'company_id',
                $companyId
            )
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->where(
                'company_id',
                $companyId
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'reference',
                'purchase_price',
                'stock_quantity',
            ]);

        return view(
            'company.purchases.create',
            compact(
                'suppliers',
                'products'
            )
        );
    }


    /**
     * Enregistrer un achat.
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([

            'supplier_id' => [
                'required',
                'integer',
            ],

            'purchase_date' => [
                'required',
                'date',
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

            /*
            |--------------------------------------------------------------------------
            | PAIEMENT INITIAL
            |--------------------------------------------------------------------------
            */

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
                'max:50',
            ],

            'initial_payment_note' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
            |--------------------------------------------------------------------------
            | COMPATIBILITÉ ANCIEN FORMULAIRE
            |--------------------------------------------------------------------------
            */

            'amount_paid' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | AUTRES
            |--------------------------------------------------------------------------
            */

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.001',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | FOURNISSEUR
        |--------------------------------------------------------------------------
        */

        $supplier = Supplier::query()
            ->where(
                'company_id',
                $companyId
            )
            ->where(
                'id',
                $validated['supplier_id']
            )
            ->first();

        if (!$supplier) {

            throw ValidationException::withMessages([
                'supplier_id' =>
                    'Le fournisseur sélectionné n’appartient pas à votre entreprise.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        return DB::transaction(
            function () use (
                $validated,
                $companyId
            ) {

                $subtotal = 0;

                $preparedItems = [];

                /*
                |--------------------------------------------------------------------------
                | PRODUITS
                |--------------------------------------------------------------------------
                */

                foreach (
                    $validated['items']
                    as $item
                ) {

                    $product = Product::query()
                        ->where(
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

                    $quantity = (float) $item['quantity'];

                    $unitPrice = (float) $item['unit_price'];

                    $lineTotal =
                        $quantity *
                        $unitPrice;

                    $subtotal += $lineTotal;

                    $preparedItems[] = [
                        'product' =>
                            $product,

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $unitPrice,

                        'total' =>
                            $lineTotal,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | TOTAUX
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
                            'La remise ne peut pas dépasser le sous-total.',
                    ]);
                }

                $total =
                    $subtotal -
                    $discount +
                    $tax;

                if ($total < 0) {
                    $total = 0;
                }

                /*
                |--------------------------------------------------------------------------
                | PAIEMENT INITIAL
                |--------------------------------------------------------------------------
                */

                $initialPaymentAmount =
                    array_key_exists(
                        'initial_payment_amount',
                        $validated
                    )
                        ? (float) (
                            $validated['initial_payment_amount']
                            ?? 0
                        )
                        : (float) (
                            $validated['amount_paid']
                            ?? 0
                        );

                if ($initialPaymentAmount > $total) {

                    throw ValidationException::withMessages([
                        'initial_payment_amount' =>
                            'Le montant payé ne peut pas dépasser le total.',
                    ]);
                }

                $paymentMethod =
                    $validated['initial_payment_method']
                    ?? null;

                /*
                |--------------------------------------------------------------------------
                | COMPATIBILITÉ ANCIEN FORMULAIRE
                |--------------------------------------------------------------------------
                */

                if (
                    $initialPaymentAmount > 0 &&
                    !$paymentMethod
                ) {
                    $paymentMethod = 'cash';
                }

                /*
                |--------------------------------------------------------------------------
                | PAIEMENT MOBILE MONEY
                |--------------------------------------------------------------------------
                */

                $paymentStatus = 'completed';

                if (
                    $initialPaymentAmount > 0 &&
                    $paymentMethod === 'mobile_money'
                ) {

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

                    $settings = CompanyPaymentSetting::query()
                        ->where(
                            'company_id',
                            $companyId
                        )
                        ->first();

                    if (!$settings) {

                        throw ValidationException::withMessages([
                            'initial_payment_provider' =>
                                'Les paramètres de paiement de votre entreprise ne sont pas configurés.',
                        ]);
                    }

                    if (
                        $provider === 'orange_money' &&
                        !$settings->orange_enabled
                    ) {

                        throw ValidationException::withMessages([
                            'initial_payment_provider' =>
                                'Orange Money n’est pas activé pour votre entreprise.',
                        ]);
                    }

                    if (
                        $provider === 'mtn_momo' &&
                        !$settings->mtn_enabled
                    ) {

                        throw ValidationException::withMessages([
                            'initial_payment_provider' =>
                                'MTN Mobile Money n’est pas activé pour votre entreprise.',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Pour l'instant, Mobile Money reste en attente.
                    |--------------------------------------------------------------------------
                    */

                    $paymentStatus = 'pending';
                }

                /*
                |--------------------------------------------------------------------------
                | RÉFÉRENCE
                |--------------------------------------------------------------------------
                */

                $reference =
                    'ACH-' .
                    now()->format('YmdHis');

                $counter = 1;

                while (
                    Purchase::query()
                        ->where(
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
                        'ACH-' .
                        now()->format('YmdHis') .
                        '-' .
                        $counter;

                    $counter++;
                }

                /*
                |--------------------------------------------------------------------------
                | ACHAT
                |--------------------------------------------------------------------------
                */

                $purchase = Purchase::create([

                    'company_id' =>
                        $companyId,

                    'supplier_id' =>
                        $validated['supplier_id'],

                    'reference' =>
                        $reference,

                    'purchase_date' =>
                        $validated['purchase_date'],

                    'subtotal' =>
                        $subtotal,

                    'discount' =>
                        $discount,

                    'tax' =>
                        $tax,

                    'total' =>
                        $total,

                    /*
                    | Les montants seront recalculés après
                    | création du paiement.
                    */

                    'amount_paid' =>
                        0,

                    'amount_due' =>
                        $total,

                    'payment_status' =>
                        'unpaid',

                    'status' =>
                        'completed',

                    'notes' =>
                        $validated['notes'] ?? null,

                    'user_id' =>
                        auth()->id(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | LIGNES + ENTRÉE STOCK
                |--------------------------------------------------------------------------
                */

                foreach (
                    $preparedItems
                    as $item
                ) {

                    $product =
                        $item['product'];

                    $stockBefore =
                        (float)
                        $product->stock_quantity;

                    $stockAfter =
                        $stockBefore +
                        $item['quantity'];

                    PurchaseItem::create([

                        'purchase_id' =>
                            $purchase->id,

                        'product_id' =>
                            $product->id,

                        'quantity' =>
                            $item['quantity'],

                        'unit_price' =>
                            $item['unit_price'],

                        'discount' =>
                            0,

                        'total' =>
                            $item['total'],
                    ]);

                    $product->update([

                        'stock_quantity' =>
                            $stockAfter,

                        'purchase_price' =>
                            $item['unit_price'],
                    ]);

                    StockMovement::create([

                        'company_id' =>
                            $companyId,

                        'product_id' =>
                            $product->id,

                        'user_id' =>
                            auth()->id(),

                        'type' =>
                            'entry',

                        'quantity' =>
                            $item['quantity'],

                        'stock_before' =>
                            $stockBefore,

                        'stock_after' =>
                            $stockAfter,

                        'reason' =>
                            'Achat ' .
                            $purchase->reference,

                        'note' =>
                            'Entrée automatique liée à un achat fournisseur.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | CRÉATION DU PAIEMENT INITIAL
                |--------------------------------------------------------------------------
                */

                if ($initialPaymentAmount > 0) {

                    PurchasePayment::create([

                        'company_id' =>
                            $companyId,

                        'purchase_id' =>
                            $purchase->id,

                        'user_id' =>
                            auth()->id(),

                        'amount' =>
                            $initialPaymentAmount,

                        'method' =>
                            $paymentMethod,

                        'provider' =>
                            $validated['initial_payment_provider']
                            ?? null,

                        'transaction_reference' =>
                            $validated['initial_payment_transaction_reference']
                            ?? null,

                        'phone' =>
                            $validated['initial_payment_phone']
                            ?? null,

                        'note' =>
                            $validated['initial_payment_note']
                            ?? null,

                        'status' =>
                            $paymentStatus,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | RECALCUL DU PAIEMENT
                |--------------------------------------------------------------------------
                */

                $amountPaid = PurchasePayment::query()
                    ->where(
                        'purchase_id',
                        $purchase->id
                    )
                    ->where(
                        'status',
                        'completed'
                    )
                    ->sum('amount');

                $amountDue = max(
                    0,
                    $total - $amountPaid
                );

                $purchasePaymentStatus = match (true) {

                    $amountPaid <= 0 =>
                        'unpaid',

                    $amountPaid >= $total =>
                        'paid',

                    default =>
                        'partial',
                };

                $purchase->update([

                    'amount_paid' =>
                        $amountPaid,

                    'amount_due' =>
                        $amountDue,

                    'payment_status' =>
                        $purchasePaymentStatus,
                ]);

                return redirect()
                    ->route(
                        'company.purchases.show',
                        $purchase
                    )
                    ->with(
                        'success',
                        'Achat enregistré avec succès.'
                    );
            }
        );
    }


    /**
     * Afficher un achat.
     */
    /**
 * Afficher un achat.
 */
public function show(Purchase $purchase)
{
    $companyId = auth()->user()->company_id;

    $purchase = Purchase::query()
        ->where('company_id', $companyId)
        ->with([
            'supplier',
            'user',
            'company',
            'items.product',
            'payments.user',
        ])
        ->findOrFail($purchase->id);

    /*
    |--------------------------------------------------------------------------
    | RECALCUL DES PAIEMENTS
    |--------------------------------------------------------------------------
    | On se base sur les paiements réellement terminés.
    | Les paiements "pending" ou "cancelled" ne comptent pas.
    */

    $amountPaid = (float) $purchase->payments
        ->where('status', 'completed')
        ->sum('amount');

    $total = (float) $purchase->total;

    $amountDue = max(
        0,
        $total - $amountPaid
    );

    /*
    |--------------------------------------------------------------------------
    | STATUT DE PAIEMENT
    |--------------------------------------------------------------------------
    */

    $paymentStatus = match (true) {

        $amountPaid <= 0 =>
            'unpaid',

        $amountPaid >= $total =>
            'paid',

        default =>
            'partial',
    };

    /*
    |--------------------------------------------------------------------------
    | SYNCHRONISER L'ACHAT
    |--------------------------------------------------------------------------
    */

    if (
        (float) $purchase->amount_paid !== $amountPaid
        ||
        (float) $purchase->amount_due !== $amountDue
        ||
        $purchase->payment_status !== $paymentStatus
    ) {

        $purchase->update([
            'amount_paid' =>
                $amountPaid,

            'amount_due' =>
                $amountDue,

            'payment_status' =>
                $paymentStatus,
        ]);

        /*
        | On recharge les valeurs mises à jour.
        */
        $purchase->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | PARAMÈTRES MOBILE MONEY
    |--------------------------------------------------------------------------
    */

    $settings = CompanyPaymentSetting::query()
        ->where('company_id', $companyId)
        ->first();

    return view(
        'company.purchases.show',
        compact(
            'purchase',
            'settings'
        )
    );
}

    /**
     * Annuler un achat.
     *
     * L'annulation inverse l'entrée de stock.
     */
    public function destroy(Purchase $purchase)
    {
        $companyId =
            auth()->user()->company_id;

        $purchase = Purchase::query()
            ->with([
                'items',
                'payments',
            ])
            ->where(
                'company_id',
                $companyId
            )
            ->findOrFail(
                $purchase->id
            );

        if (
            $purchase->status ===
            'cancelled'
        ) {

            return back()
                ->withErrors([
                    'purchase' =>
                        'Cet achat est déjà annulé.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS EN ATTENTE
        |--------------------------------------------------------------------------
        */

        $hasPendingPayment = $purchase->payments()
            ->where(
                'status',
                'pending'
            )
            ->exists();

        if ($hasPendingPayment) {

            return back()
                ->withErrors([
                    'purchase' =>
                        'Impossible d’annuler cet achat : un paiement est encore en attente.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS TERMINÉS
        |--------------------------------------------------------------------------
        */

        $hasCompletedPayment = $purchase->payments()
            ->where(
                'status',
                'completed'
            )
            ->exists();

        if ($hasCompletedPayment) {

            return back()
                ->withErrors([
                    'purchase' =>
                        'Impossible d’annuler cet achat : un paiement fournisseur a déjà été enregistré. Un remboursement doit être traité avant l’annulation.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use ($purchase) {

                foreach (
                    $purchase->items
                    as $item
                ) {

                    $product =
                        Product::query()
                            ->where(
                                'company_id',
                                $purchase->company_id
                            )
                            ->where(
                                'id',
                                $item->product_id
                            )
                            ->lockForUpdate()
                            ->first();

                    if (!$product) {
                        continue;
                    }

                    $stockBefore =
                        (float)
                        $product->stock_quantity;

                    $stockAfter = max(
                        0,
                        $stockBefore -
                        (float) $item->quantity
                    );

                    $product->update([

                        'stock_quantity' =>
                            $stockAfter,
                    ]);

                    StockMovement::create([

                        'company_id' =>
                            $purchase->company_id,

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
                            'Annulation achat ' .
                            $purchase->reference,

                        'note' =>
                            'Annulation de l’entrée stock liée à cet achat.',
                    ]);
                }

                $purchase->update([

                    'status' =>
                        'cancelled',

                    'payment_status' =>
                        'unpaid',

                    'amount_paid' =>
                        0,

                    'amount_due' =>
                        $purchase->total,
                ]);
            }
        );

        return redirect()
            ->route(
                'company.purchases.index'
            )
            ->with(
                'success',
                'Achat annulé avec succès.'
            );
    }


    /**
     * Afficher la facture d'achat.
     */
    public function invoice(Purchase $purchase)
    {
        $companyId =
            auth()->user()->company_id;

        $purchase = Purchase::query()
            ->with([
                'company',
                'supplier',
                'user',
                'items.product',
                'payments.user',
            ])
            ->where(
                'company_id',
                $companyId
            )
            ->findOrFail(
                $purchase->id
            );

        return view(
            'company.purchases.invoice',
            compact('purchase')
        );
    }
}