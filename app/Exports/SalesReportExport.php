<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected int $companyId;

    protected $startDate;

    protected $endDate;

    public function __construct(
        int $companyId,
        $startDate,
        $endDate
    ) {
        $this->companyId = $companyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Récupération des ventes et des achats.
     */
    public function collection(): Collection
    {
        /*
        |--------------------------------------------------------------------------
        | VENTES
        |--------------------------------------------------------------------------
        */

        $sales = Sale::query()
            ->with([
                'client',
                'user',
            ])
            ->where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $this->startDate,
                $this->endDate,
            ])
            ->get()
            ->map(function ($sale) {
                return [
                    'type' => 'Vente',
                    'reference' => $sale->reference,
                    'date' => $sale->created_at,
                    'third_party' => $sale->client?->name ?? 'Client comptoir',
                    'user' => $sale->user?->name ?? '—',
                    'subtotal' => (float) ($sale->subtotal ?? 0),
                    'discount' => (float) ($sale->discount ?? 0),
                    'tax' => (float) ($sale->tax ?? 0),
                    'total' => (float) ($sale->total ?? 0),
                    'status' => 'Terminée',
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | ACHATS
        |--------------------------------------------------------------------------
        */

        $purchases = Purchase::query()
            ->with([
                'supplier',
                'user',
            ])
            ->where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $this->startDate,
                $this->endDate,
            ])
            ->get()
            ->map(function ($purchase) {
                return [
                    'type' => 'Achat',
                    'reference' => $purchase->reference,
                    'date' => $purchase->created_at,
                    'third_party' => $purchase->supplier?->name ?? 'Fournisseur',
                    'user' => $purchase->user?->name ?? '—',
                    'subtotal' => (float) ($purchase->subtotal ?? 0),
                    'discount' => (float) ($purchase->discount ?? 0),
                    'tax' => (float) ($purchase->tax ?? 0),
                    'total' => (float) ($purchase->total ?? 0),
                    'status' => 'Terminé',
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | FUSION VENTES + ACHATS
        |--------------------------------------------------------------------------
        */

        return $sales
            ->concat($purchases)
            ->sortByDesc(function ($operation) {
                return $operation['date'];
            })
            ->values();
    }

    /**
     * En-têtes du fichier Excel.
     */
    public function headings(): array
    {
        return [
            'Type',
            'Référence',
            'Date',
            'Client / Fournisseur',
            'Utilisateur',
            'Sous-total',
            'Remise',
            'Taxe',
            'Total',
            'Statut',
        ];
    }

    /**
     * Transformation d'une opération en ligne Excel.
     */
    public function map($operation): array
    {
        return [
            $operation['type'],

            $operation['reference'] ?? '—',

            $operation['date']
                ? $operation['date']->format('d/m/Y H:i')
                : '—',

            $operation['third_party'],

            $operation['user'],

            $operation['subtotal'],

            $operation['discount'],

            $operation['tax'],

            $operation['total'],

            $operation['status'],
        ];
    }
}