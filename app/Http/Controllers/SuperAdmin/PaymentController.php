<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES PAIEMENTS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Payment::query()
            ->with([
                'company',
                'subscription.plan',
            ]);

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subscription', function ($subscriptionQuery) use ($search) {
                        $subscriptionQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE STATUT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE MÉTHODE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('method')) {
            $query->where(
                'method',
                $request->method
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE DATE DE DÉBUT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'paid_at',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE DATE DE FIN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {
            $query->whereDate(
                'paid_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'total' => Payment::count(),

            'completed' => Payment::where(
                'status',
                'completed'
            )->count(),

            'pending' => Payment::where(
                'status',
                'pending'
            )->count(),

            'failed' => Payment::where(
                'status',
                'failed'
            )->count(),

            'refunded' => Payment::where(
                'status',
                'refunded'
            )->count(),

            'revenue' => Payment::where(
                'status',
                'completed'
            )->sum('amount'),
        ];

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $payments = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'superadmin.payments.index',
            compact(
                'payments',
                'statistics'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE DE CRÉATION
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subscriptions = Subscription::query()
            ->with([
                'company',
                'plan',
            ])
            ->whereIn('status', [
                'active',
                'expired',
            ])
            ->orderByDesc('starts_at')
            ->get();

        return view(
            'superadmin.payments.create',
            compact(
                'companies',
                'subscriptions'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER UN PAIEMENT
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        SubscriptionService $subscriptionService
    ) {
        $validated = $request->validate([
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
            ],

            'subscription_id' => [
                'nullable',
                'integer',
                'exists:subscriptions,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'method' => [
                'required',
                Rule::in([
                    'cash',
                    'mobile_money',
                    'bank_transfer',
                    'card',
                    'other',
                ]),
            ],

            'provider' => [
                'nullable',
                'string',
                'max:100',
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:150',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'completed',
                    'failed',
                    'refunded',
                ]),
            ],

            'paid_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | ENTREPRISE
        |--------------------------------------------------------------------------
        */

        $company = Company::findOrFail(
            $validated['company_id']
        );

        /*
        |--------------------------------------------------------------------------
        | ABONNEMENT
        |--------------------------------------------------------------------------
        */

        $subscription = null;

        if (!empty($validated['subscription_id'])) {
            $subscription = Subscription::query()
                ->with('plan')
                ->findOrFail(
                    $validated['subscription_id']
                );

            /*
            | L'abonnement doit appartenir
            | à l'entreprise sélectionnée.
            */

            if (
                (int) $subscription->company_id
                !== (int) $company->id
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subscription_id' =>
                            'L’abonnement sélectionné n’appartient pas à cette entreprise.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UN PAIEMENT TERMINÉ DOIT AVOIR UN ABONNEMENT
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'completed'
            && !$subscription
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'subscription_id' =>
                        'Un paiement terminé doit être associé à un abonnement.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION DU MONTANT
        |--------------------------------------------------------------------------
        |
        | Un paiement terminé doit couvrir au minimum
        | le prix de l'abonnement.
        |
        */

        if (
            $validated['status'] === 'completed'
            && $subscription
            && (float) $validated['amount']
                < (float) $subscription->price
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'amount' =>
                        'Le montant du paiement doit être au moins égal au prix de l’abonnement, soit '
                        . number_format(
                            (float) $subscription->price,
                            0,
                            ',',
                            ' '
                        )
                        . ' FCFA.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GÉNÉRER LA RÉFÉRENCE
        |--------------------------------------------------------------------------
        */

        $reference = 'PAY-'
            . now()->format('YmdHis')
            . '-'
            . strtoupper(
                substr(
                    uniqid(),
                    -5
                )
            );

        /*
        |--------------------------------------------------------------------------
        | DATE DE PAIEMENT
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'completed'
            && empty($validated['paid_at'])
        ) {
            $validated['paid_at'] = now();
        }

        /*
        |--------------------------------------------------------------------------
        | LES AUTRES STATUTS N'ONT PAS DE DATE DE PAIEMENT
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] !== 'completed'
        ) {
            $validated['paid_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $payment = DB::transaction(function () use (
            $validated,
            $reference,
            $subscription,
            $subscriptionService
        ) {
            /*
            |--------------------------------------------------------------------------
            | CRÉER LE PAIEMENT
            |--------------------------------------------------------------------------
            */

            $payment = Payment::create([
                'company_id' =>
                    $validated['company_id'],

                'subscription_id' =>
                    $validated['subscription_id'] ?? null,

                'reference' =>
                    $reference,

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

                'status' =>
                    $validated['status'],

                'paid_at' =>
                    $validated['paid_at'] ?? null,

                'notes' =>
                    $validated['notes'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | ACTIVER / PROLONGER L'ABONNEMENT
            |--------------------------------------------------------------------------
            */

            if (
                $validated['status'] === 'completed'
                && $subscription
            ) {
                $subscriptionService->activateFromPayment(
                    $subscription
                );
            }

            return $payment;
        });

        /*
        |--------------------------------------------------------------------------
        | REDIRECTION
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'superadmin.payments.show',
                $payment
            )
            ->with(
                'success',
                'Le paiement a été enregistré avec succès et l’abonnement a été mis à jour.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | AFFICHER UN PAIEMENT
    |--------------------------------------------------------------------------
    */

    public function show(Payment $payment)
    {
        $payment->load([
            'company',
            'subscription.plan',
        ]);

        return view(
            'superadmin.payments.show',
            compact('payment')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE DE MODIFICATION
    |--------------------------------------------------------------------------
    */

    public function edit(Payment $payment)
    {
        $payment->load([
            'company',
            'subscription.plan',
        ]);

        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subscriptions = Subscription::query()
            ->with([
                'company',
                'plan',
            ])
            ->whereIn('status', [
                'active',
                'expired',
            ])
            ->orderByDesc('starts_at')
            ->get();

        return view(
            'superadmin.payments.edit',
            compact(
                'payment',
                'companies',
                'subscriptions'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MODIFIER UN PAIEMENT
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Payment $payment,
        SubscriptionService $subscriptionService
    ) {
        $validated = $request->validate([
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
            ],

            'subscription_id' => [
                'nullable',
                'integer',
                'exists:subscriptions,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'method' => [
                'required',
                Rule::in([
                    'cash',
                    'mobile_money',
                    'bank_transfer',
                    'card',
                    'other',
                ]),
            ],

            'provider' => [
                'nullable',
                'string',
                'max:100',
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:150',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'completed',
                    'failed',
                    'refunded',
                ]),
            ],

            'paid_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | ANCIEN STATUT
        |--------------------------------------------------------------------------
        |
        | Sert à empêcher une double activation.
        |
        | pending → completed = activation
        | failed → completed = activation
        | refunded → completed = activation
        | completed → completed = aucune nouvelle activation
        |
        */

        $oldStatus = $payment->status;

        /*
        |--------------------------------------------------------------------------
        | ENTREPRISE
        |--------------------------------------------------------------------------
        */

        $company = Company::findOrFail(
            $validated['company_id']
        );

        /*
        |--------------------------------------------------------------------------
        | ABONNEMENT
        |--------------------------------------------------------------------------
        */

        $subscription = null;

        if (!empty($validated['subscription_id'])) {
            $subscription = Subscription::query()
                ->with('plan')
                ->findOrFail(
                    $validated['subscription_id']
                );

            /*
            | L'abonnement doit appartenir
            | à l'entreprise sélectionnée.
            */

            if (
                (int) $subscription->company_id
                !== (int) $company->id
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subscription_id' =>
                            'L’abonnement sélectionné n’appartient pas à cette entreprise.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PAIEMENT TERMINÉ = ABONNEMENT OBLIGATOIRE
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'completed'
            && !$subscription
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'subscription_id' =>
                        'Un paiement terminé doit être associé à un abonnement.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION DU MONTANT
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'completed'
            && $subscription
            && (float) $validated['amount']
                < (float) $subscription->price
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'amount' =>
                        'Le montant du paiement doit être au moins égal au prix de l’abonnement, soit '
                        . number_format(
                            (float) $subscription->price,
                            0,
                            ',',
                            ' '
                        )
                        . ' FCFA.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DATE DE PAIEMENT
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'completed'
        ) {
            if (empty($validated['paid_at'])) {
                $validated['paid_at'] =
                    $payment->paid_at ?? now();
            }
        } else {
            $validated['paid_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | DÉTERMINER SI L'ABONNEMENT DOIT ÊTRE ACTIVÉ
        |--------------------------------------------------------------------------
        */

        $shouldActivateSubscription =
            $validated['status'] === 'completed'
            && $oldStatus !== 'completed'
            && $subscription !== null;

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $payment,
            $validated,
            $subscription,
            $shouldActivateSubscription,
            $subscriptionService
        ) {
            /*
            |--------------------------------------------------------------------------
            | METTRE À JOUR LE PAIEMENT
            |--------------------------------------------------------------------------
            */

            $payment->update([
                'company_id' =>
                    $validated['company_id'],

                'subscription_id' =>
                    $validated['subscription_id'] ?? null,

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

                'status' =>
                    $validated['status'],

                'paid_at' =>
                    $validated['paid_at'] ?? null,

                'notes' =>
                    $validated['notes'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | ACTIVER / PROLONGER L'ABONNEMENT
            |--------------------------------------------------------------------------
            */

            if (
                $shouldActivateSubscription
                && $subscription
            ) {
                $subscriptionService->activateFromPayment(
                    $subscription
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */

        if ($shouldActivateSubscription) {
            $message =
                'Le paiement a été modifié et l’abonnement a été activé avec succès.';
        } else {
            $message =
                'Le paiement a été modifié avec succès.';
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECTION
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'superadmin.payments.show',
                $payment
            )
            ->with(
                'success',
                $message
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REÇU
    |--------------------------------------------------------------------------
    */

    public function receipt(Payment $payment)
    {
        $payment->load([
            'company',
            'subscription.plan',
        ]);

        return view(
            'superadmin.payments.receipt',
            compact('payment')
        );
    }
}