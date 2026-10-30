<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Liste des abonnements.
     */
    public function index(Request $request): View
    {
        $query = Subscription::query()
            ->with([
                'company',
                'plan',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->whereHas('company', function ($company) use ($search) {
                    $company
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });

                $q->orWhereHas('plan', function ($plan) use ($search) {
                    $plan->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                });

                $q->orWhere(
                    'name',
                    'like',
                    "%{$search}%"
                );
            });
        }

        if ($request->status === 'active') {
            $query->where('status', 'active');
        }

        if ($request->status === 'expired') {
            $query->where(function ($q) {
                $q->where('status', 'expired')
                    ->orWhereDate(
                        'ends_at',
                        '<',
                        now()->toDateString()
                    );
            });
        }

        if ($request->status === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $subscriptions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Subscription::count(),

            'active' => Subscription::where(
                'status',
                'active'
            )
                ->whereDate(
                    'ends_at',
                    '>=',
                    now()->toDateString()
                )
                ->count(),

            'expired' => Subscription::where(function ($query) {
                $query->where(
                    'status',
                    'expired'
                )->orWhereDate(
                    'ends_at',
                    '<',
                    now()->toDateString()
                );
            })->count(),

            'cancelled' => Subscription::where(
                'status',
                'cancelled'
            )->count(),
        ];

        return view(
            'superadmin.subscriptions.index',
            compact(
                'subscriptions',
                'stats'
            )
        );
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return view(
            'superadmin.subscriptions.create',
            compact(
                'companies',
                'plans'
            )
        );
    }

    /**
     * Attribuer un abonnement à une entreprise.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => [
                'required',
                'exists:companies,id',
            ],

            'plan_id' => [
                'required',
                'exists:subscription_plans,id',
            ],

            'starts_at' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $company = Company::findOrFail(
            $validated['company_id']
        );

        $plan = SubscriptionPlan::findOrFail(
            $validated['plan_id']
        );

        if (!$plan->is_active) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ce plan est actuellement inactif.'
                );
        }

        $startsAt = \Carbon\Carbon::parse(
            $validated['starts_at']
        )->startOfDay();

        $endsAt = $startsAt
            ->copy()
            ->addDays($plan->duration_days);

        DB::transaction(function () use (
            $company,
            $plan,
            $startsAt,
            $endsAt,
            $validated
        ) {

            /*
             * On désactive l'ancien abonnement actif
             * de cette entreprise.
             */
            Subscription::where(
                'company_id',
                $company->id
            )
                ->where('status', 'active')
                ->update([
                    'status' => 'expired',
                ]);

            Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,

                /*
                 * Snapshot du plan.
                 * Cela permet de conserver l'historique
                 * même si le plan est modifié plus tard.
                 */
                'name' => $plan->name,
                'price' => $plan->price,
                'duration_days' => $plan->duration_days,

                'starts_at' => $startsAt,
                'ends_at' => $endsAt,

                'status' => 'active',

                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route(
                'superadmin.subscriptions.index'
            )
            ->with(
                'success',
                'L’abonnement a été créé avec succès.'
            );
    }

    /**
     * Afficher un abonnement.
     */
    public function show(
        Subscription $subscription
    ): View {
        $subscription->load([
            'company',
            'plan',
        ]);

        return view(
            'superadmin.subscriptions.show',
            compact('subscription')
        );
    }

    /**
     * Formulaire de renouvellement/modification.
     */
    public function edit(
        Subscription $subscription
    ): View {
        $subscription->load([
            'company',
            'plan',
        ]);

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return view(
            'superadmin.subscriptions.edit',
            compact(
                'subscription',
                'plans'
            )
        );
    }

    /**
     * Modifier / renouveler un abonnement.
     */
    public function update(
        Request $request,
        Subscription $subscription
    ): RedirectResponse {
        $validated = $request->validate([
            'plan_id' => [
                'required',
                'exists:subscription_plans,id',
            ],

            'starts_at' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:active,expired,cancelled',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $plan = SubscriptionPlan::findOrFail(
            $validated['plan_id']
        );

        $startsAt = \Carbon\Carbon::parse(
            $validated['starts_at']
        )->startOfDay();

        $endsAt = $startsAt
            ->copy()
            ->addDays($plan->duration_days);

            if ($validated['status'] === 'active') {

    Subscription::where('company_id', $subscription->company_id)
        ->where('id', '!=', $subscription->id)
        ->where('status', 'active')
        ->update([
            'status' => 'expired',
        ]);
}

        $subscription->update([
            'plan_id' => $plan->id,

            'name' => $plan->name,
            'price' => $plan->price,
            'duration_days' => $plan->duration_days,

            'starts_at' => $startsAt,
            'ends_at' => $endsAt,

            'status' => $validated['status'],

            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route(
                'superadmin.subscriptions.show',
                $subscription
            )
            ->with(
                'success',
                'L’abonnement a été mis à jour.'
            );
    }

    /**
     * Annuler un abonnement.
     */
    public function cancel(
        Subscription $subscription
    ): RedirectResponse {
        $subscription->update([
            'status' => 'cancelled',
        ]);

        return back()->with(
            'success',
            'L’abonnement a été annulé.'
        );
    }
}