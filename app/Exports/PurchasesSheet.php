<?php

namespace App\Exports;

use App\Models\Purchase;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PurchasesSheet implements
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
        return 'Achats';
    }

    public function collection(): Collection
    {
        return Purchase::with(['supplier', 'user'])
            ->where('company_id', $this->companyId)
            ->where('status', 'completed')
            ->whereBetween(
                'created_at',
                [$this->startDate, $this->endDate]
            )
            ->latest('created_at')
            ->get()
            ->map(function ($purchase) {
                return [
                    'Référence' => $purchase->reference ?? '—',

                    'Date' => $purchase->created_at
                        ? $purchase->created_at->format('d/m/Y H:i')
                        : '—',

                    'Fournisseur' => $purchase->supplier?->name
                        ?? 'Fournisseur',

                    'Utilisateur' => $purchase->user?->name ?? '—',

                    'Sous-total' => (float) ($purchase->subtotal ?? 0),

                    'Remise' => (float) ($purchase->discount ?? 0),

                    'Taxe' => (float) ($purchase->tax ?? 0),

                    'Total' => (float) ($purchase->total ?? 0),

                    'Statut' => 'Terminé',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Date',
            'Fournisseur',
            'Utilisateur',
            'Sous-total',
            'Remise',
            'Taxe',
            'Total',
            'Statut',
        ];
    }
}