<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyPaymentSetting;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalePaymentController extends Controller
{
    /**
     * Enregistrer un paiement
     */
    public function store(Request $request, Sale $sale)
    {
        $companyId = auth()->user()->company_id;

        /*
        |--------------------------------------------------------------------------
        | Validation
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
        | Vérifier Mobile Money
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


            $settings = CompanyPaymentSetting::where(
                'company_id',
                $companyId
            )->first();


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

            /*
            | Les autres méthodes n'utilisent pas d'opérateur.
            */

            $validated['provider'] = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction sécurisée
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(
            function () use (
                $validated,
                $sale,
                $companyId
            ) {

                /*
                |--------------------------------------------------------------------------
                | Verrouiller la vente
                |--------------------------------------------------------------------------
                */

                $lockedSale = Sale::where(
                    'company_id',
                    $companyId
                )
                    ->where(
                        'id',
                        $sale->id
                    )
                    ->lockForUpdate()
                    ->first();


                if (!$lockedSale) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Cette vente est introuvable.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Vérifier statut vente
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedSale->status !==
                    'completed'
                ) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Impossible d’effectuer un paiement sur une vente annulée.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Paiements déjà encaissés
                |--------------------------------------------------------------------------
                */

                $alreadyPaid =
                    SalePayment::where(
                        'sale_id',
                        $lockedSale->id
                    )
                        ->where(
                            'status',
                            'completed'
                        )
                        ->sum('amount');


                /*
                |--------------------------------------------------------------------------
                | Reste
                |--------------------------------------------------------------------------
                */

                $remaining = max(
                    0,
                    (float) $lockedSale->total
                    -
                    (float) $alreadyPaid
                );


                /*
                |--------------------------------------------------------------------------
                | Vérifier montant
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
                | Déterminer statut
                |--------------------------------------------------------------------------
                */

                $status =
                    $validated['method'] ===
                    'mobile_money'
                        ? 'pending'
                        : 'completed';


                /*
                |--------------------------------------------------------------------------
                | Création paiement
                |--------------------------------------------------------------------------
                */

                $payment = SalePayment::create([

                    'company_id' =>
                        $companyId,

                    'sale_id' =>
                        $lockedSale->id,

                    'user_id' =>
                        auth()->id(),

                    'amount' =>
                        $validated['amount'],

                    'method' =>
                        $validated['method'],

                    'provider' =>
                        $validated['provider'] ?? null,

                    'transaction_reference' =>
                        $validated[
                            'transaction_reference'
                        ] ?? null,

                    'phone' =>
                        $validated['phone'] ?? null,

                    'note' =>
                        $validated['note'] ?? null,

                    'status' =>
                        $status,

                ]);


                return [
                    'payment' => $payment,
                    'status' => $status,
                ];
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Message
        |--------------------------------------------------------------------------
        */

        if (
            $result['status'] ===
            'pending'
        ) {

            return back()->with(
                'success',
                'Demande de paiement Mobile Money enregistrée. Le paiement est en attente de confirmation.'
            );
        }


        return back()->with(
            'success',
            'Paiement enregistré avec succès.'
        );
    }


    /**
     * Annuler un paiement
     */
    public function destroy(SalePayment $payment)
    {
        $companyId =
            auth()->user()->company_id;


        $payment = SalePayment::where(
            'company_id',
            $companyId
        )
            ->findOrFail(
                $payment->id
            );


        if (
            $payment->status ===
            'cancelled'
        ) {

            return back()->withErrors([
                'payment' =>
                    'Ce paiement est déjà annulé.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Paiement en attente
        |--------------------------------------------------------------------------
        */

        $payment->update([
            'status' => 'cancelled',
        ]);


        return back()->with(
            'success',
            'Paiement annulé avec succès.'
        );
    }
}