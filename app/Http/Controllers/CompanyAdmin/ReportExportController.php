<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Sale;
use App\Models\SalePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    /**
     * Retourne les dates correspondant à la période sélectionnée.
     */
    private function period(Request $request): array
    {
        $period = $request->input('period', 'month');

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;

            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;

            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;

            case 'month':
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $period = 'month';
                break;
        }

        return [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    /**
     * ================================================================
     * EXPORT EXCEL
     * ================================================================
     *
     * Le fichier contient :
     *
     * 1. Synthèse
     * 2. Ventes
     * 3. Achats
     * 4. Paiements
     */
    public function excel(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $period = $this->period($request);

        $filename = 'rapport-erp-'
            . $period['period']
            . '-'
            . now()->format('Y-m-d')
            . '.xlsx';

        return Excel::download(
            new ReportsExport(
                $companyId,
                $period['startDate'],
                $period['endDate']
            ),
            $filename
        );
    }

    /**
     * ================================================================
     * EXPORT PDF
     * ================================================================
     */
    public function pdf(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $period = $this->period($request);

        /*
        |--------------------------------------------------------------------------
        | VENTES
        |--------------------------------------------------------------------------
        */

        $sales = Sale::with([
            'client',
            'user',
            'items.product',
        ])
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $period['startDate'],
                $period['endDate'],
            ])
            ->latest()
            ->get();

        $salesCount = $sales->count();

        $salesTotal = (float) $sales->sum('total');
        $salesSubtotal = (float) $sales->sum('subtotal');
        $salesDiscount = (float) $sales->sum('discount');
        $salesTax = (float) $sales->sum('tax');

        /*
        |--------------------------------------------------------------------------
        | ACHATS
        |--------------------------------------------------------------------------
        */

        $purchases = Purchase::with([
            'supplier',
            'user',
            'items.product',
        ])
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $period['startDate'],
                $period['endDate'],
            ])
            ->latest()
            ->get();

        $purchasesCount = $purchases->count();

        $purchasesTotal = (float) $purchases->sum('total');
        $purchasesSubtotal = (float) $purchases->sum('subtotal');
        $purchasesDiscount = (float) $purchases->sum('discount');
        $purchasesTax = (float) $purchases->sum('tax');

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS CLIENTS
        |--------------------------------------------------------------------------
        */

        $payments = SalePayment::with([
            'sale',
            'user',
        ])
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $period['startDate'],
                $period['endDate'],
            ])
            ->latest()
            ->get();

        $paymentsTotal = (float) $payments->sum('amount');

        $cashTotal = (float) $payments
            ->where('method', 'cash')
            ->sum('amount');

        $mobileMoneyTotal = (float) $payments
            ->where('method', 'mobile_money')
            ->sum('amount');

        $cardTotal = (float) $payments
            ->where('method', 'card')
            ->sum('amount');

        $bankTransferTotal = (float) $payments
            ->where('method', 'bank_transfer')
            ->sum('amount');

        $otherPaymentsTotal = (float) $payments
            ->where('method', 'other')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | MOBILE MONEY CLIENTS
        |--------------------------------------------------------------------------
        */

        $orangeMoneyTotal = (float) $payments
            ->where('method', 'mobile_money')
            ->where('provider', 'orange_money')
            ->sum('amount');

        $mtnMomoTotal = (float) $payments
            ->where('method', 'mobile_money')
            ->where('provider', 'mtn_momo')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS FOURNISSEURS
        |--------------------------------------------------------------------------
        */

        $purchasePayments = PurchasePayment::with([
            'purchase',
            'user',
        ])
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $period['startDate'],
                $period['endDate'],
            ])
            ->latest()
            ->get();

        $purchasePaymentsTotal = (float) $purchasePayments->sum('amount');

        $purchaseCashTotal = (float) $purchasePayments
            ->where('method', 'cash')
            ->sum('amount');

        $purchaseMobileMoneyTotal = (float) $purchasePayments
            ->where('method', 'mobile_money')
            ->sum('amount');

        $purchaseCardTotal = (float) $purchasePayments
            ->where('method', 'card')
            ->sum('amount');

        $purchaseBankTransferTotal = (float) $purchasePayments
            ->where('method', 'bank_transfer')
            ->sum('amount');

        $purchaseOtherPaymentsTotal = (float) $purchasePayments
            ->where('method', 'other')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | MOBILE MONEY FOURNISSEURS
        |--------------------------------------------------------------------------
        */

        $purchaseOrangeMoneyTotal = (float) $purchasePayments
            ->where('method', 'mobile_money')
            ->where('provider', 'orange_money')
            ->sum('amount');

        $purchaseMtnMomoTotal = (float) $purchasePayments
            ->where('method', 'mobile_money')
            ->where('provider', 'mtn_momo')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | FLUX DE TRÉSORERIE NET
        |--------------------------------------------------------------------------
        */

        $netCashFlow = $paymentsTotal - $purchasePaymentsTotal;

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS EN ATTENTE
        |--------------------------------------------------------------------------
        */

        $pendingSalePayments = (float) SalePayment::where(
            'company_id',
            $companyId
        )
            ->where('status', 'pending')
            ->whereBetween('created_at', [
                $period['startDate'],
                $period['endDate'],
            ])
            ->sum('amount');

        $pendingPurchasePayments = (float) PurchasePayment::where(
            'company_id',
            $companyId
        )
            ->where('status', 'pending')
            ->whereBetween('created_at', [
                $period['startDate'],
                $period['endDate'],
            ])
            ->sum('amount');

        $pendingPayments =
            $pendingSalePayments
            + $pendingPurchasePayments;

        /*
        |--------------------------------------------------------------------------
        | CRÉANCES CLIENTS
        |--------------------------------------------------------------------------
        */

        $allSales = Sale::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get([
                'id',
                'total',
            ]);

        $allPayments = SalePayment::where(
            'company_id',
            $companyId
        )
            ->where('status', 'completed')
            ->get([
                'sale_id',
                'amount',
            ]);

        $paidBySale = $allPayments
            ->groupBy('sale_id')
            ->map(function ($items) {
                return (float) $items->sum('amount');
            });

        $receivablesTotal = 0;

        foreach ($allSales as $sale) {
            $paid = $paidBySale->get($sale->id, 0);

            $receivablesTotal += max(
                0,
                (float) $sale->total - $paid
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DETTES FOURNISSEURS
        |--------------------------------------------------------------------------
        */

        $allPurchases = Purchase::where(
            'company_id',
            $companyId
        )
            ->where('status', 'completed')
            ->get([
                'id',
                'total',
            ]);

        $allPurchasePayments = PurchasePayment::where(
            'company_id',
            $companyId
        )
            ->where('status', 'completed')
            ->get([
                'purchase_id',
                'amount',
            ]);

        $paidByPurchase = $allPurchasePayments
            ->groupBy('purchase_id')
            ->map(function ($items) {
                return (float) $items->sum('amount');
            });

        $payablesTotal = 0;

        foreach ($allPurchases as $purchase) {
            $paid = $paidByPurchase->get(
                $purchase->id,
                0
            );

            $payablesTotal += max(
                0,
                (float) $purchase->total - $paid
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        */

        $products = Product::where(
            'company_id',
            $companyId
        )->get();

        $productsCount = $products->count();

        $stockQuantity = (int) $products->sum(
            'stock_quantity'
        );

        $stockValuePurchase = (float) $products->sum(
            function ($product) {
                return
                    $product->stock_quantity
                    * $product->purchase_price;
            }
        );

        $stockValueSelling = (float) $products->sum(
            function ($product) {
                return
                    $product->stock_quantity
                    * $product->selling_price;
            }
        );

        $lowStock = $products
            ->filter(function ($product) {
                return $product->isLowStock();
            })
            ->count();

        $outOfStock = $products
            ->filter(function ($product) {
                return $product->isOutOfStock();
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | TOP PRODUITS
        |--------------------------------------------------------------------------
        */

        $topProducts = collect();

        try {
            $topProducts = DB::table('sale_items')
                ->join(
                    'sales',
                    'sales.id',
                    '=',
                    'sale_items.sale_id'
                )
                ->join(
                    'products',
                    'products.id',
                    '=',
                    'sale_items.product_id'
                )
                ->where(
                    'sales.company_id',
                    $companyId
                )
                ->where(
                    'sales.status',
                    'completed'
                )
                ->whereBetween(
                    'sales.created_at',
                    [
                        $period['startDate'],
                        $period['endDate'],
                    ]
                )
                ->select(
                    'products.name',
                    DB::raw(
                        'SUM(sale_items.quantity) as quantity'
                    ),
                    DB::raw(
                        'SUM(sale_items.total) as total'
                    )
                )
                ->groupBy(
                    'products.id',
                    'products.name'
                )
                ->orderByDesc('quantity')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            $topProducts = collect();
        }

        /*
        |--------------------------------------------------------------------------
        | ENTREPRISE
        |--------------------------------------------------------------------------
        */

        $company = auth()->user()->company;

        /*
        |--------------------------------------------------------------------------
        | GÉNÉRATION DU PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'company.reports.pdf',
            compact(
                'company',

                'period',

                // Ventes
                'sales',
                'salesCount',
                'salesTotal',
                'salesSubtotal',
                'salesDiscount',
                'salesTax',

                // Achats
                'purchases',
                'purchasesCount',
                'purchasesTotal',
                'purchasesSubtotal',
                'purchasesDiscount',
                'purchasesTax',

                // Paiements clients
                'payments',
                'paymentsTotal',
                'cashTotal',
                'mobileMoneyTotal',
                'cardTotal',
                'bankTransferTotal',
                'otherPaymentsTotal',

                // Mobile Money clients
                'orangeMoneyTotal',
                'mtnMomoTotal',

                // Paiements fournisseurs
                'purchasePayments',
                'purchasePaymentsTotal',
                'purchaseCashTotal',
                'purchaseMobileMoneyTotal',
                'purchaseCardTotal',
                'purchaseBankTransferTotal',
                'purchaseOtherPaymentsTotal',

                // Mobile Money fournisseurs
                'purchaseOrangeMoneyTotal',
                'purchaseMtnMomoTotal',

                // Paiements en attente
                'pendingPayments',
                'pendingSalePayments',
                'pendingPurchasePayments',

                // Trésorerie
                'netCashFlow',

                // Créances / dettes
                'receivablesTotal',
                'payablesTotal',

                // Stock
                'products',
                'productsCount',
                'stockQuantity',
                'stockValuePurchase',
                'stockValueSelling',
                'lowStock',
                'outOfStock',

                // Top produits
                'topProducts'
            )
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            'rapport-'
            . $period['period']
            . '-'
            . now()->format('Y-m-d')
            . '.pdf'
        );
    }
}


