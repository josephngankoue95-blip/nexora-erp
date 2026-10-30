<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesSheet implements
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
        return 'Ventes';
    }

    public function collection(): Collection
    {
        return Sale::with(['client', 'user'])
            ->where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween(
                'created_at',
                [$this->startDate, $this->endDate]
            )
            ->latest('created_at')
            ->get()
            ->map(function ($sale) {
                return [
                    'Référence' => $sale->reference ?? '—',

                    'Date' => $sale->created_at
                        ? $sale->created_at->format('d/m/Y H:i')
                        : '—',

                    'Client' => $sale->client?->name
                        ?? 'Client comptoir',

                    'Vendeur' => $sale->user?->name ?? '—',

                    'Sous-total' => (float) ($sale->subtotal ?? 0),

                    'Remise' => (float) ($sale->discount ?? 0),

                    'Taxe' => (float) ($sale->tax ?? 0),

                    'Total' => (float) ($sale->total ?? 0),

                    'Statut' => 'Terminée',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Date',
            'Client',
            'Vendeur',
            'Sous-total',
            'Remise',
            'Taxe',
            'Total',
            'Statut',
        ];
    }
}