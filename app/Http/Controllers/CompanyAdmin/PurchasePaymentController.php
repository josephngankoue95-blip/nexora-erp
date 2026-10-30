<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyPaymentSetting;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchasePaymentController extends Controller
{
    /**
     * Enregistrer un paiement fournisseur.
     */
    public function store(Request $request, Purchase $purchase)
    {
        $companyId = auth()->user()->company_id;

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'method' => [
                'required',
                'in:cash,mobile_money,card,bank_transfer,other',
            ],

            'provider' => [
                'nullable',
                'in:orange_money,mtn_momo',
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | MOBILE MONEY
        |--------------------------------------------------------------------------
        */

        if ($validated['method'] === 'mobile_money') {

            if (empty($validated['provider'])) {

                throw ValidationException::withMessages([
                    'provider' =>
                        'Veuillez sélectionner un opérateur Mobile Money.',
                ]);
            }

            if (empty($validated['phone'])) {

                throw ValidationException::withMessages([
                    'phone' =>
                        'Veuillez saisir le numéro Mobile Money.',
                ]);
            }

            $settings = CompanyPaymentSetting::query()
                ->where('company_id', $companyId)
                ->first();

            if (!$settings) {

                throw ValidationException::withMessages([
                    'provider' =>
                        'Aucun paramètre Mobile Money n’est configuré pour cette entreprise.',
                ]);
            }

            if (
                $validated['provider'] === 'orange_money'
                &&
                !$settings->orange_enabled
            ) {

                throw ValidationException::withMessages([
                    'provider' =>
                        'Orange Money n’est pas activé pour cette entreprise.',
                ]);
            }

            if (
                $validated['provider'] === 'mtn_momo'
                &&
                !$settings->mtn_enabled
            ) {

                throw ValidationException::withMessages([
                    'provider' =>
                        'MTN Mobile Money n’est pas activé pour cette entreprise.',
                ]);
            }

        } else {

            $validated['provider'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION SÉCURISÉE
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(
            function () use (
                $validated,
                $purchase,
                $companyId
            ) {

                /*
                |--------------------------------------------------------------------------
                | VERROUILLER L'ACHAT
                |--------------------------------------------------------------------------
                */

                $lockedPurchase = Purchase::query()
                    ->where('company_id', $companyId)
                    ->where('id', $purchase->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedPurchase) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Cet achat est introuvable.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | VÉRIFIER LE STATUT DE L'ACHAT
                |--------------------------------------------------------------------------
                */

                if ($lockedPurchase->status !== 'completed') {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Impossible d’effectuer un paiement sur un achat annulé.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | CALCUL DES PAIEMENTS TERMINÉS
                |--------------------------------------------------------------------------
                */

                $alreadyPaid = (float) PurchasePayment::query()
                    ->where('company_id', $companyId)
                    ->where('purchase_id', $lockedPurchase->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                /*
                |--------------------------------------------------------------------------
                | CALCUL DU RESTE
                |--------------------------------------------------------------------------
                */

                $total = (float) $lockedPurchase->total;

                $remaining = max(
                    0,
                    $total - $alreadyPaid
                );

                /*
                |--------------------------------------------------------------------------
                | ACHAT DÉJÀ TOTALEMENT PAYÉ
                |--------------------------------------------------------------------------
                */

                if ($remaining <= 0) {

                    /*
                    | On synchronise également les montants.
                    */

                    $lockedPurchase->update([
                        'amount_paid' =>
                            $total,

                        'amount_due' =>
                            0,

                        'payment_status' =>
                            'paid',
                    ]);

                    throw ValidationException::withMessages([
                        'amount' =>
                            'Cet achat est déjà totalement payé. Aucun paiement supplémentaire n’est nécessaire.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | MONTANT SUPÉRIEUR AU RESTE
                |--------------------------------------------------------------------------
                */

                if (
                    (float) $validated['amount']
                    >
                    $remaining
                ) {

                    throw ValidationException::withMessages([
                        'amount' => sprintf(
                            'Le montant ne peut pas dépasser le reste à payer : %s FCFA.',
                            number_format(
                                $remaining,
                                0,
                                ',',
                                ' '
                            )
                        ),
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | STATUT DU PAIEMENT
                |--------------------------------------------------------------------------
                |
                | Espèces / carte / virement / autre = terminé
                | Mobile Money = en attente
                |
                */

                $status =
                    $validated['method'] === 'mobile_money'
                        ? 'pending'
                        : 'completed';

                /*
                |--------------------------------------------------------------------------
                | CRÉATION DU PAIEMENT
                |--------------------------------------------------------------------------
                */

                $payment = PurchasePayment::create([

                    'company_id' =>
                        $companyId,

                    'purchase_id' =>
                        $lockedPurchase->id,

                    'user_id' =>
                        auth()->id(),

                    'amount' =>
                        $validated['amount'],

                    'method' =>
                        $validated['method'],

                    'provider' =>
                        $validated['provider'] ?? null,

                    'transaction_reference' =>
                        $validated['transaction_reference'] ?? null,

                    'phone' =>
                        $validated['phone'] ?? null,

                    'note' =>
                        $validated['note'] ?? null,

                    'status' =>
                        $status,

                ]);

                /*
                |--------------------------------------------------------------------------
                | RECALCUL IMMÉDIAT
                |--------------------------------------------------------------------------
                */

                $newAmountPaid = (float) PurchasePayment::query()
                    ->where('company_id', $companyId)
                    ->where('purchase_id', $lockedPurchase->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                $newAmountDue = max(
                    0,
                    $total - $newAmountPaid
                );

                $newPaymentStatus = match (true) {

                    $newAmountPaid <= 0 =>
                        'unpaid',

                    $newAmountPaid >= $total =>
                        'paid',

                    default =>
                        'partial',
                };

                $lockedPurchase->update([

                    'amount_paid' =>
                        $newAmountPaid,

                    'amount_due' =>
                        $newAmountDue,

                    'payment_status' =>
                        $newPaymentStatus,

                ]);

                return [
                    'payment' =>
                        $payment,

                    'status' =>
                        $status,

                    'amount_paid' =>
                        $newAmountPaid,

                    'amount_due' =>
                        $newAmountDue,

                    'payment_status' =>
                        $newPaymentStatus,
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */

        if ($result['status'] === 'pending') {

            return back()->with(
                'success',
                'Demande de paiement Mobile Money enregistrée. Le paiement est en attente de confirmation.'
            );
        }

        return back()->with(
            'success',
            'Paiement fournisseur enregistré avec succès.'
        );
    }


    /**
     * Annuler un paiement.
     */
    public function destroy(PurchasePayment $payment)
    {
        $companyId = auth()->user()->company_id;

        /*
        |--------------------------------------------------------------------------
        | RÉCUPÉRER LE PAIEMENT
        |--------------------------------------------------------------------------
        */

        $payment = PurchasePayment::query()
            ->where('company_id', $companyId)
            ->with('purchase')
            ->findOrFail($payment->id);

        /*
        |--------------------------------------------------------------------------
        | DÉJÀ ANNULÉ
        |--------------------------------------------------------------------------
        */

        if ($payment->status === 'cancelled') {

            return back()->withErrors([
                'payment' =>
                    'Ce paiement est déjà annulé.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $payment,
            $companyId
        ) {

            /*
            |--------------------------------------------------------------------------
            | ANNULER LE PAIEMENT
            |--------------------------------------------------------------------------
            */

            $payment->update([
                'status' => 'cancelled',
            ]);

            /*
            |--------------------------------------------------------------------------
            | RECALCULER L'ACHAT
            |--------------------------------------------------------------------------
            */

            $purchase = Purchase::query()
                ->where('company_id', $companyId)
                ->where('id', $payment->purchase_id)
                ->lockForUpdate()
                ->first();

            if (!$purchase) {
                return;
            }

            /*
            | Paiements réellement terminés.
            */

            $amountPaid = (float) PurchasePayment::query()
                ->where('company_id', $companyId)
                ->where('purchase_id', $purchase->id)
                ->where('status', 'completed')
                ->sum('amount');

            $total = (float) $purchase->total;

            $amountDue = max(
                0,
                $total - $amountPaid
            );

            $paymentStatus = match (true) {

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
                    $paymentStatus,

            ]);
        });

        return back()->with(
            'success',
            'Paiement fournisseur annulé avec succès.'
        );
    }
}