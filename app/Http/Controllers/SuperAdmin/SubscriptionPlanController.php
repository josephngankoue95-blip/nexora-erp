<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller
{
    /**
     * Liste des plans.
     */
    public function index(Request $request): View
    {
        $query = SubscriptionPlan::query()
            ->withCount('subscriptions');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        }

        if ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($request->status === 'featured') {
            $query->where('is_featured', true);
        }

        $plans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => SubscriptionPlan::count(),

            'active' => SubscriptionPlan::where(
                'is_active',
                true
            )->count(),

            'inactive' => SubscriptionPlan::where(
                'is_active',
                false
            )->count(),

            'featured' => SubscriptionPlan::where(
                'is_featured',
                true
            )->count(),
        ];

        return view(
            'superadmin.subscription-plans.index',
            compact('plans', 'stats')
        );
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        return view(
            'superadmin.subscription-plans.create'
        );
    }

    /**
     * Enregistrer un nouveau plan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:120',
                'unique:subscription_plans,slug',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'duration_days' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_users' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'max_products' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'features' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['slug'] = $validated['slug']
            ?? Str::slug($validated['name']);

        $validated['features'] = $this->parseFeatures(
            $validated['features'] ?? null
        );

        $validated['is_active'] = $request->boolean(
            'is_active'
        );

        $validated['is_featured'] = $request->boolean(
            'is_featured'
        );

        SubscriptionPlan::create($validated);

        return redirect()
            ->route('superadmin.subscription-plans.index')
            ->with(
                'success',
                'Le plan d’abonnement a été créé avec succès.'
            );
    }

    /**
     * Formulaire de modification.
     */
    public function edit(
        SubscriptionPlan $subscriptionPlan
    ): View {
        return view(
            'superadmin.subscription-plans.edit',
            compact('subscriptionPlan')
        );
    }

    /**
     * Modifier un plan.
     */
    public function update(
        Request $request,
        SubscriptionPlan $subscriptionPlan
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:120',
                'unique:subscription_plans,slug,'
                    . $subscriptionPlan->id,
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'duration_days' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_users' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'max_products' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'features' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['slug'] = $validated['slug']
            ?? Str::slug($validated['name']);

        $validated['features'] = $this->parseFeatures(
            $validated['features'] ?? null
        );

        $validated['is_active'] = $request->boolean(
            'is_active'
        );

        $validated['is_featured'] = $request->boolean(
            'is_featured'
        );

        $subscriptionPlan->update($validated);

        return redirect()
            ->route('superadmin.subscription-plans.index')
            ->with(
                'success',
                'Le plan a été modifié avec succès.'
            );
    }

    /**
     * Activer / désactiver un plan.
     */
    public function toggleStatus(
        SubscriptionPlan $subscriptionPlan
    ): RedirectResponse {
        $subscriptionPlan->update([
            'is_active' => !$subscriptionPlan->is_active,
        ]);

        $message = $subscriptionPlan->is_active
            ? 'Le plan a été activé.'
            : 'Le plan a été désactivé.';

        return back()->with('success', $message);
    }

    /**
     * Mettre / retirer un plan à la une.
     */
    public function toggleFeatured(
        SubscriptionPlan $subscriptionPlan
    ): RedirectResponse {
        $subscriptionPlan->update([
            'is_featured' => !$subscriptionPlan->is_featured,
        ]);

        $message = $subscriptionPlan->is_featured
            ? 'Le plan est maintenant mis à la une.'
            : 'Le plan n’est plus mis à la une.';

        return back()->with('success', $message);
    }

    /**
     * Supprimer un plan.
     */
    public function destroy(
        SubscriptionPlan $subscriptionPlan
    ): RedirectResponse {
        if ($subscriptionPlan->subscriptions()->exists()) {
            return back()->with(
                'error',
                'Ce plan ne peut pas être supprimé car il est utilisé par un ou plusieurs abonnements.'
            );
        }

        $subscriptionPlan->delete();

        return redirect()
            ->route('superadmin.subscription-plans.index')
            ->with(
                'success',
                'Le plan a été supprimé avec succès.'
            );
    }

    /**
     * Transformer les fonctionnalités saisies
     * en tableau JSON.
     */
    private function parseFeatures(
        ?string $features
    ): array {
        if (!$features) {
            return [];
        }

        return collect(
            preg_split(
                '/\r\n|\r|\n/',
                $features
            )
        )
            ->map(fn ($feature) => trim($feature))
            ->filter()
            ->values()
            ->toArray();
    }
}