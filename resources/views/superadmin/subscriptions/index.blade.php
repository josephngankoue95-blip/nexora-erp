@extends('layouts.superadmin')

@section('title', 'Abonnements')

@section('content')

<div class="subscription-plans-page">

    <div class="plans-page-header">

        <div>

            <div class="plans-breadcrumb">

                <span>Super Admin</span>

                <i data-lucide="chevron-right"></i>

                <span>Abonnements</span>

                <i data-lucide="chevron-right"></i>

                <strong>Abonnements actifs</strong>

            </div>

            <h1>Abonnements</h1>

            <p>
                Gérez les abonnements des entreprises.
            </p>

        </div>


        <a
            href="{{ route('superadmin.subscriptions.create') }}"
            class="plans-primary-button"
        >
            <i data-lucide="plus"></i>
            Nouvel abonnement
        </a>

    </div>


    @if(session('success'))

        <div class="plans-alert plans-alert-success">

            <i data-lucide="check-circle"></i>

            <span>{{ session('success') }}</span>

        </div>

    @endif


    <div class="plans-stats-grid">

        <div class="plans-stat-card">

            <div class="plans-stat-icon blue">
                <i data-lucide="credit-card"></i>
            </div>

            <div class="plans-stat-content">

                <span>Total</span>

                <strong>
                    {{ $stats['total'] }}
                </strong>

            </div>

        </div>


        <div class="plans-stat-card">

            <div class="plans-stat-icon green">
                <i data-lucide="circle-check"></i>
            </div>

            <div class="plans-stat-content">

                <span>Actifs</span>

                <strong>
                    {{ $stats['active'] }}
                </strong>

            </div>

        </div>


        <div class="plans-stat-card">

            <div class="plans-stat-icon orange">
                <i data-lucide="clock-3"></i>
            </div>

            <div class="plans-stat-content">

                <span>Expirés</span>

                <strong>
                    {{ $stats['expired'] }}
                </strong>

            </div>

        </div>


        <div class="plans-stat-card">

            <div class="plans-stat-icon purple">
                <i data-lucide="ban"></i>
            </div>

            <div class="plans-stat-content">

                <span>Annulés</span>

                <strong>
                    {{ $stats['cancelled'] }}
                </strong>

            </div>

        </div>

    </div>


    <div class="plans-filter-card">

        <form
            method="GET"
            action="{{ route('superadmin.subscriptions.index') }}"
            class="plans-filter-form"
        >

            <div class="plans-search">

                <i data-lucide="search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Entreprise ou plan..."
                >

            </div>


            <select
                name="status"
                class="plans-filter-select"
            >

                <option value="">
                    Tous les statuts
                </option>

                <option
                    value="active"
                    @selected(request('status') === 'active')
                >
                    Actifs
                </option>

                <option
                    value="expired"
                    @selected(request('status') === 'expired')
                >
                    Expirés
                </option>

                <option
                    value="cancelled"
                    @selected(request('status') === 'cancelled')
                >
                    Annulés
                </option>

            </select>


            <button
                type="submit"
                class="plans-filter-button"
            >
                <i data-lucide="filter"></i>
                Filtrer
            </button>

        </form>

    </div>


    <div class="plans-list-card">

        <div class="plans-list-header">

            <div>

                <h2>Abonnements des entreprises</h2>

                <p>
                    {{ $subscriptions->total() }}
                    abonnement(s)
                </p>

            </div>

        </div>


        @if($subscriptions->count())

            <div class="plans-table-wrapper">

                <table class="plans-table">

                    <thead>

                        <tr>

                            <th>Entreprise</th>

                            <th>Plan</th>

                            <th>Prix</th>

                            <th>Période</th>

                            <th>Expiration</th>

                            <th>Statut</th>

                            <th></th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($subscriptions as $subscription)

                            <tr>

                                <td>

                                    <div class="plan-name-cell">

                                        <div class="plan-icon">

                                            <i data-lucide="building-2"></i>

                                        </div>

                                        <div>

                                            <strong>
                                                {{ $subscription->company->name ?? '—' }}
                                            </strong>

                                            <span>
                                                {{ $subscription->company->email ?? '—' }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <strong>
                                        {{ $subscription->plan->name ?? $subscription->name }}
                                    </strong>

                                </td>


                                <td>

                                    <strong>
                                        {{ $subscription->formatted_price }}
                                    </strong>

                                </td>


                                <td>

                                    <div class="plan-limits">

                                        <span>
                                            <i data-lucide="calendar"></i>
                                            {{ $subscription->starts_at?->format('d/m/Y') }}
                                        </span>

                                        <span>
                                            <i data-lucide="arrow-right"></i>
                                            {{ $subscription->ends_at?->format('d/m/Y') }}
                                        </span>

                                    </div>

                                </td>


                                <td>

                                    @if($subscription->is_active)

                                        <strong>
                                            {{ $subscription->days_remaining }}
                                            jour(s)
                                        </strong>

                                    @else

                                        <span class="plan-status inactive">
                                            Expiré
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($subscription->status === 'active' && $subscription->is_active)

                                        <span class="plan-status active">
                                            <span></span>
                                            Actif
                                        </span>

                                    @elseif($subscription->status === 'cancelled')

                                        <span class="plan-status inactive">
                                            <span></span>
                                            Annulé
                                        </span>

                                    @else

                                        <span class="plan-status inactive">
                                            <span></span>
                                            Expiré
                                        </span>

                                    @endif

                                </td>


                                <td>

                                <!-- Actions -->
                                    <div class="plan-actions">

                                        <a
                                            href="{{ route(
                                                'superadmin.subscriptions.show',
                                                $subscription
                                            ) }}"
                                            class="plan-action edit"
                                            title="Voir"
                                        >
                                            <i data-lucide="eye"></i>
                                        </a>

                                        <a
                                            href="{{ route(
                                                'superadmin.subscriptions.edit',
                                                $subscription
                                            ) }}"
                                            class="plan-action edit"
                                            title="Modifier"
                                        >
                                            <i data-lucide="pencil"></i>
                                        </a>

                                        @if($subscription->status === 'active')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'superadmin.subscriptions.cancel',
                                                    $subscription
                                                ) }}"
                                                onsubmit="return confirm(
                                                    'Voulez-vous vraiment annuler cet abonnement ?'
                                                )"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="plan-action delete"
                                                    title="Annuler"
                                                >
                                                    <i data-lucide="ban"></i>
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            @if($subscriptions->hasPages())

                <div class="plans-pagination">

                    {{ $subscriptions->links() }}

                </div>

            @endif

        @else

            <div class="plans-empty">

                <div class="plans-empty-icon">
                    <i data-lucide="credit-card"></i>
                </div>

                <h3>Aucun abonnement</h3>

                <p>
                    Aucune entreprise ne possède encore d’abonnement.
                </p>

                <a
                    href="{{ route('superadmin.subscriptions.create') }}"
                    class="plans-primary-button"
                >
                    <i data-lucide="plus"></i>
                    Attribuer un abonnement
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

});

</script>

@endpush