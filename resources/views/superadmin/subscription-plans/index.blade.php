@extends('layouts.superadmin')

@section('title', 'Plans d’abonnement')

@section('content')

<div class="subscription-plans-page">

    {{-- ==========================================================
         HEADER
    =========================================================== --}}

    <div class="plans-page-header">

        <div>
            <div class="plans-breadcrumb">
                <span>Super Admin</span>
                <i data-lucide="chevron-right"></i>
                <span>Abonnements</span>
                <i data-lucide="chevron-right"></i>
                <strong>Plans</strong>
            </div>

            <h1>Plans d’abonnement</h1>

            <p>
                Gérez les offres commerciales proposées aux entreprises.
            </p>
        </div>

        <a
            href="{{ route('superadmin.subscription-plans.create') }}"
            class="plans-primary-button"
        >
            <i data-lucide="plus"></i>
            <span>Nouveau plan</span>
        </a>

    </div>


    {{-- ==========================================================
         MESSAGES
    =========================================================== --}}

    @if(session('success'))

        <div class="plans-alert plans-alert-success">

            <i data-lucide="check-circle"></i>

            <span>{{ session('success') }}</span>

            <button type="button" class="plans-alert-close">
                <i data-lucide="x"></i>
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="plans-alert plans-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>{{ session('error') }}</span>

            <button type="button" class="plans-alert-close">
                <i data-lucide="x"></i>
            </button>

        </div>

    @endif


    {{-- ==========================================================
         STATISTIQUES
    =========================================================== --}}

    <div class="plans-stats-grid">

        <div class="plans-stat-card">

            <div class="plans-stat-icon blue">
                <i data-lucide="layers"></i>
            </div>

            <div class="plans-stat-content">
                <span>Total des plans</span>
                <strong>{{ $stats['total'] }}</strong>
            </div>

        </div>


        <div class="plans-stat-card">

            <div class="plans-stat-icon green">
                <i data-lucide="circle-check"></i>
            </div>

            <div class="plans-stat-content">
                <span>Plans actifs</span>
                <strong>{{ $stats['active'] }}</strong>
            </div>

        </div>


        <div class="plans-stat-card">

            <div class="plans-stat-icon orange">
                <i data-lucide="pause-circle"></i>
            </div>

            <div class="plans-stat-content">
                <span>Plans inactifs</span>
                <strong>{{ $stats['inactive'] }}</strong>
            </div>

        </div>


        <div class="plans-stat-card">

            <div class="plans-stat-icon purple">
                <i data-lucide="star"></i>
            </div>

            <div class="plans-stat-content">
                <span>Plans à la une</span>
                <strong>{{ $stats['featured'] }}</strong>
            </div>

        </div>

    </div>


    {{-- ==========================================================
         FILTRES
    =========================================================== --}}

    <div class="plans-filter-card">

        <form
            method="GET"
            action="{{ route('superadmin.subscription-plans.index') }}"
            class="plans-filter-form"
        >

            <div class="plans-search">

                <i data-lucide="search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher un plan..."
                >

            </div>


            <select name="status" class="plans-filter-select">

                <option value="">
                    Tous les plans
                </option>

                <option
                    value="active"
                    @selected(request('status') === 'active')
                >
                    Actifs
                </option>

                <option
                    value="inactive"
                    @selected(request('status') === 'inactive')
                >
                    Inactifs
                </option>

                <option
                    value="featured"
                    @selected(request('status') === 'featured')
                >
                    À la une
                </option>

            </select>


            <button
                type="submit"
                class="plans-filter-button"
            >
                <i data-lucide="filter"></i>
                Filtrer
            </button>


            @if(request()->hasAny(['search', 'status']))

                <a
                    href="{{ route('superadmin.subscription-plans.index') }}"
                    class="plans-reset-button"
                >
                    Réinitialiser
                </a>

            @endif

        </form>

    </div>


    {{-- ==========================================================
         PLANS
    =========================================================== --}}

    <div class="plans-list-card">

        <div class="plans-list-header">

            <div>
                <h2>Vos plans</h2>

                <p>
                    {{ $plans->total() }}
                    plan(s) enregistré(s)
                </p>
            </div>

        </div>


        @if($plans->count())

            <div class="plans-table-wrapper">

                <table class="plans-table">

                    <thead>

                        <tr>

                            <th>Plan</th>

                            <th>Tarif</th>

                            <th>Durée</th>

                            <th>Limites</th>

                            <th>Abonnements</th>

                            <th>Statut</th>

                            <th></th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($plans as $plan)

                            <tr>

                                {{-- PLAN --}}

                                <td>

                                    <div class="plan-name-cell">

                                        <div class="plan-icon">

                                            @if($plan->is_featured)

                                                <i data-lucide="star"></i>

                                            @else

                                                <i data-lucide="layers"></i>

                                            @endif

                                        </div>

                                        <div>

                                            <strong>
                                                {{ $plan->name }}
                                            </strong>

                                            <span>
                                                {{ $plan->slug }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                {{-- PRIX --}}

                                <td>

                                    <div class="plan-price">

                                        {{ $plan->formatted_price }}

                                        <small>
                                            / {{ $plan->duration_label }}
                                        </small>

                                    </div>

                                </td>


                                {{-- DURÉE --}}

                                <td>

                                    <span class="plan-duration">

                                        <i data-lucide="calendar-days"></i>

                                        {{ $plan->duration_label }}

                                    </span>

                                </td>


                                {{-- LIMITES --}}

                                <td>

                                    <div class="plan-limits">

                                        <span>

                                            <i data-lucide="users"></i>

                                            {{ $plan->users_limit_label }}

                                        </span>

                                        <span>

                                            <i data-lucide="package"></i>

                                            {{ $plan->products_limit_label }}

                                        </span>

                                    </div>

                                </td>


                                {{-- ABONNEMENTS --}}

                                <td>

                                    <span class="plan-subscriptions-count">

                                        {{ $plan->subscriptions_count }}

                                    </span>

                                </td>


                                {{-- STATUT --}}

                                <td>

                                    @if($plan->is_active)

                                        <span class="plan-status active">
                                            <span></span>
                                            Actif
                                        </span>

                                    @else

                                        <span class="plan-status inactive">
                                            <span></span>
                                            Inactif
                                        </span>

                                    @endif

                                    @if($plan->is_featured)

                                        <span class="plan-featured-badge">
                                            <i data-lucide="star"></i>
                                            À la une
                                        </span>

                                    @endif

                                </td>


                                {{-- ACTIONS --}}

                                <td>

                                    <div class="plan-actions">

                                        <a
                                            href="{{ route(
                                                'superadmin.subscription-plans.edit',
                                                $plan
                                            ) }}"
                                            class="plan-action edit"
                                            title="Modifier"
                                        >
                                            <i data-lucide="pencil"></i>
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'superadmin.subscription-plans.toggle-status',
                                                $plan
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="plan-action status"
                                                title="{{ $plan->is_active ? 'Désactiver' : 'Activer' }}"
                                            >

                                                @if($plan->is_active)

                                                    <i data-lucide="pause"></i>

                                                @else

                                                    <i data-lucide="play"></i>

                                                @endif

                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'superadmin.subscription-plans.toggle-featured',
                                                $plan
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="plan-action featured"
                                                title="À la une"
                                            >
                                                <i data-lucide="star"></i>
                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'superadmin.subscription-plans.destroy',
                                                $plan
                                            ) }}"
                                            onsubmit="return confirm(
                                                'Voulez-vous vraiment supprimer ce plan ?'
                                            )"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="plan-action delete"
                                                title="Supprimer"
                                            >
                                                <i data-lucide="trash-2"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}

            @if($plans->hasPages())

                <div class="plans-pagination">

                    {{ $plans->links() }}

                </div>

            @endif

        @else

            <div class="plans-empty">

                <div class="plans-empty-icon">
                    <i data-lucide="layers"></i>
                </div>

                <h3>Aucun plan trouvé</h3>

                <p>
                    Aucun plan ne correspond à vos critères.
                </p>

                <a
                    href="{{ route('superadmin.subscription-plans.create') }}"
                    class="plans-primary-button"
                >
                    <i data-lucide="plus"></i>
                    Créer un plan
                </a>

            </div>

        @endif

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    document.querySelectorAll('.plans-alert-close')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const alert = button.closest('.plans-alert');

                if (alert) {
                    alert.remove();
                }

            });

        });

});

</script>

@endpush