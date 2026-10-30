<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommercePromotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EcommercePromotionController extends Controller
{
    /**
     * Récupère l'identifiant de l'entreprise connectée.
     */
    private function companyId(): int
    {
        return (int) (
            auth()->user()->company_id
            ?? session('company_id')
        );
    }

    /**
     * Afficher les promotions de l'entreprise.
     */
    public function index()
    {
        $companyId = $this->companyId();

        abort_unless($companyId > 0, 403);

        $promotions = EcommercePromotion::where(
            'company_id',
            $companyId
        )
            ->latest()
            ->paginate(15);

        return view(
            'company.ecommerce.promotions.index',
            compact('promotions')
        );
    }

    /**
     * Enregistrer une nouvelle promotion.
     */
    public function store(Request $request)
    {
        $companyId = $this->companyId();

        abort_unless($companyId > 0, 403);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:80',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('ecommerce_promotions', 'code')
                    ->where('company_id', $companyId),
            ],

            'type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],

            'value' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'minimum_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
        ]);

        if (
            $data['type'] === 'percentage'
            && $data['value'] > 100
        ) {
            return back()
                ->withErrors([
                    'value' => 'Le pourcentage ne peut pas dépasser 100 %.',
                ])
                ->withInput();
        }

        $data['company_id'] = $companyId;
        $data['code'] = isset($data['code'])
            ? strtoupper(trim($data['code']))
            : null;

        $data['minimum_order_amount']
            = $data['minimum_order_amount'] ?? 0;

        EcommercePromotion::create($data);

        return redirect()
            ->route('company.ecommerce.promotions.index')
            ->with('success', 'Promotion créée avec succès.');
    }

    
public function create()
{
    return view('company.ecommerce.promotions.create');
}

public function edit(EcommercePromotion $promotion)
{
    abort_unless(
        $promotion->company_id === $this->companyId(),
        404
    );

    return view(
        'company.ecommerce.promotions.edit',
        compact('promotion')
    );
}

public function update(Request $request, EcommercePromotion $promotion)
{
    abort_unless(
        $promotion->company_id === $this->companyId(),
        404
    );

    $companyId = $this->companyId();

    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'code' => [
            'nullable',
            'string',
            'max:80',
            'regex:/^[A-Za-z0-9_-]+$/',
            \Illuminate\Validation\Rule::unique(
                'ecommerce_promotions',
                'code'
            )
                ->where('company_id', $companyId)
                ->ignore($promotion->id),
        ],
        'type' => ['required', \Illuminate\Validation\Rule::in(['percentage', 'fixed'])],
        'value' => ['required', 'numeric', 'min:0.01'],
        'minimum_order_amount' => ['nullable', 'numeric', 'min:0'],
        'usage_limit' => ['nullable', 'integer', 'min:1'],
        'starts_at' => ['nullable', 'date'],
        'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
    ]);

    if ($data['type'] === 'percentage' && $data['value'] > 100) {
        return back()
            ->withErrors(['value' => 'Le pourcentage ne peut pas dépasser 100 %.'])
            ->withInput();
    }

    $data['code'] = isset($data['code'])
        ? strtoupper(trim($data['code']))
        : null;

    $data['minimum_order_amount'] = $data['minimum_order_amount'] ?? 0;

    $promotion->update($data);

    return redirect()
        ->route('company.ecommerce.promotions.index')
        ->with('success', 'Promotion modifiée avec succès.');
}


    /**
     * Activer ou désactiver une promotion.
     */
    public function toggle(EcommercePromotion $promotion)
    {
        abort_unless(
            $promotion->company_id === $this->companyId(),
            404
        );

        $promotion->update([
            'is_active' => ! $promotion->is_active,
        ]);

        return back()->with(
            'success',
            'Statut de la promotion mis à jour.'
        );
    }

    /**
     * Supprimer une promotion.
     */
    public function destroy(EcommercePromotion $promotion)
    {
        abort_unless(
            $promotion->company_id === $this->companyId(),
            404
        );

        $promotion->delete();

        return back()->with(
            'success',
            'Promotion supprimée avec succès.'
        );
    }
}
