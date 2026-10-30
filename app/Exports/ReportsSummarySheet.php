<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsSummarySheet implements
    FromCollection,
    WithHeadings,
    WithTitle,
    ShouldAutoSize
{
    protected int $companyId;
    protected $startDate;
    protected $endDate;

    public function __construct(int $companyId, $startDate, $endDate)
    {
        $this->companyId = $companyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Synthèse';
    }

    public function collection(): Collection
    {
        $sales = Sale::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        $purchases = Purchase::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        $salePayments = SalePayment::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        $purchasePayments = PurchasePayment::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        $salesTotal = (float) $sales->sum('total');
        $purchasesTotal = (float) $purchases->sum('total');

        $paymentsTotal = (float) $salePayments->sum('amount');
        $purchasePaymentsTotal = (float) $purchasePayments->sum('amount');

        $receivables = $this->calculateReceivables();
        $payables = $this->calculatePayables();

        $cash = (float) (clone $salePayments)
            ->where('method', 'cash')
            ->sum('amount');

        $mobileMoney = (float) (clone $salePayments)
            ->where('method', 'mobile_money')
            ->sum('amount');

        $card = (float) (clone $salePayments)
            ->where('method', 'card')
            ->sum('amount');

        $bankTransfer = (float) (clone $salePayments)
            ->where('method', 'bank_transfer')
            ->sum('amount');

        $other = (float) (clone $salePayments)
            ->where('method', 'other')
            ->sum('amount');

        $orangeMoney = (float) (clone $salePayments)
            ->where('method', 'mobile_money')
            ->where('provider', 'orange_money')
            ->sum('amount');

        $mtnMomo = (float) (clone $salePayments)
            ->where('method', 'mobile_money')
            ->where('provider', 'mtn_momo')
            ->sum('amount');

        $netCashFlow = $paymentsTotal - $purchasePaymentsTotal;

        return collect([
            [
                'Indicateur' => 'Période',
                'Valeur' => $this->startDate->format('d/m/Y')
                    . ' - '
                    . $this->endDate->format('d/m/Y'),
            ],
            [
                'Indicateur' => 'Nombre de ventes',
                'Valeur' => $sales->count(),
            ],
            [
                'Indicateur' => 'Chiffre d’affaires',
                'Valeur' => $salesTotal,
            ],
            [
                'Indicateur' => 'Nombre d’achats',
                'Valeur' => $purchases->count(),
            ],
            [
                'Indicateur' => 'Total achats',
                'Valeur' => $purchasesTotal,
            ],
            [
                'Indicateur' => 'Encaissements clients',
                'Valeur' => $paymentsTotal,
            ],
            [
                'Indicateur' => 'Règlements fournisseurs',
                'Valeur' => $purchasePaymentsTotal,
            ],
            [
                'Indicateur' => 'Flux de trésorerie net',
                'Valeur' => $netCashFlow,
            ],
            [
                'Indicateur' => 'Créances clients',
                'Valeur' => $receivables,
            ],
            [
                'Indicateur' => 'Dettes fournisseurs',
                'Valeur' => $payables,
            ],
            [
                'Indicateur' => 'Paiements espèces',
                'Valeur' => $cash,
            ],
            [
                'Indicateur' => 'Paiements Mobile Money',
                'Valeur' => $mobileMoney,
            ],
            [
                'Indicateur' => 'Paiements carte',
                'Valeur' => $card,
            ],
            [
                'Indicateur' => 'Virements bancaires',
                'Valeur' => $bankTransfer,
            ],
            [
                'Indicateur' => 'Autres paiements',
                'Valeur' => $other,
            ],
            [
                'Indicateur' => 'Orange Money',
                'Valeur' => $orangeMoney,
            ],
            [
                'Indicateur' => 'MTN MoMo',
                'Valeur' => $mtnMomo,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Indicateur',
            'Valeur',
        ];
    }

    protected function calculateReceivables(): float
    {
        $sales = Sale::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->get(['id', 'total']);

        $payments = SalePayment::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->get(['sale_id', 'amount'])
            ->groupBy('sale_id');

        $total = 0;

        foreach ($sales as $sale) {
            $paid = $payments->get($sale->id)?->sum('amount') ?? 0;

            $total += max(
                0,
                (float) $sale->total - (float) $paid
            );
        }

        return (float) $total;
    }

    protected function calculatePayables(): float
    {
        $purchases = Purchase::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->get(['id', 'total']);

        $payments = PurchasePayment::where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->get(['purchase_id', 'amount'])
            ->groupBy('purchase_id');

        $total = 0;

        foreach ($purchases as $purchase) {
            $paid = $payments->get($purchase->id)?->sum('amount') ?? 0;

            $total += max(
                0,
                (float) $purchase->total - (float) $paid
            );
        }

        return (float) $total;
    }
}