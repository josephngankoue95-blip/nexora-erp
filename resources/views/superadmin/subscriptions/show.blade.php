@extends('layouts.superadmin')

@section('title', 'Détail de l’abonnement')

@section('content')

<div class="subscription-page">

    {{-- ==========================================================
         EN-TÊTE
    =========================================================== --}}

    <div class="page-header">

        <div>
            <div class="breadcrumb">
                <a href="{{ route('superadmin.subscriptions.index') }}">
                    Abonnements
                </a>

                <span>/</span>

                <span>Détail</span>
            </div>

            <h1>Détail de l’abonnement</h1>

            <p>
                Consultez les informations et l’état de cet abonnement.
            </p>
        </div>

        <div class="page-actions">

            <a
                href="{{ route('superadmin.subscriptions.edit', $subscription) }}"
                class="btn btn-primary"
            >
                <i data-lucide="pencil"></i>
                Modifier
            </a>

            <a
                href="{{ route('superadmin.subscriptions.index') }}"
                class="btn btn-secondary"
            >
                <i data-lucide="arrow-left"></i>
                Retour
            </a>

        </div>

    </div>


    {{-- ==========================================================
         ALERTES
    =========================================================== --}}

    @if(session('success'))
        <div class="alert alert-success">
            <i data-lucide="circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif


    {{-- ==========================================================
         GRILLE PRINCIPALE
    =========================================================== --}}

    <div class="subscription-show-grid">

        {{-- ======================================================
             COLONNE PRINCIPALE
        ======================================================= --}}

        <div>

            {{-- --------------------------------------------------
                 CARTE ABONNEMENT
            --------------------------------------------------- --}}

            <div class="subscription-card">

                <div class="subscription-card-header">

                    <div>
                        <span class="subscription-label">
                            Abonnement
                        </span>

                        <h2>
                            {{ $subscription->name }}
                        </h2>
                    </div>

                    @if($subscription->status === 'active')
                        @if($subscription->ends_at && $subscription->ends_at->isFuture())
                            <span class="status-badge status-active">
                                <span></span>
                                Actif
                            </span>
                        @else
                            <span class="status-badge status-expired">
                                <span></span>
                                Expiré
                            </span>
                        @endif
                    @elseif($subscription->status === 'expired')
                        <span class="status-badge status-expired">
                            <span></span>
                            Expiré
                        </span>
                    @else
                        <span class="status-badge status-cancelled">
                            <span></span>
                            Annulé
                        </span>
                    @endif

                </div>


                {{-- --------------------------------------------------
                     INFORMATIONS DU PLAN
                --------------------------------------------------- --}}

                <div class="subscription-info-grid">

                    <div class="info-item">
                        <span class="info-label">
                            Plan
                        </span>

                        <strong>
                            @if($subscription->plan)
                                {{ $subscription->plan->name }}
                            @else
                                {{ $subscription->name }}
                            @endif
                        </strong>
                    </div>


                    <div class="info-item">
                        <span class="info-label">
                            Prix
                        </span>

                        <strong class="info-price">
                            {{ $subscription->formatted_price }}
                        </strong>
                    </div>


                    <div class="info-item">
                        <span class="info-label">
                            Durée
                        </span>

                        <strong>
                            {{ $subscription->duration_days }} jours
                        </strong>
                    </div>


                    <div class="info-item">
                        <span class="info-label">
                            Référence
                        </span>

                        <strong>
                            #{{ $subscription->id }}
                        </strong>
                    </div>

                </div>

            </div>


            {{-- --------------------------------------------------
                 PÉRIODE
            --------------------------------------------------- --}}

            <div class="subscription-card">

                <div class="card-section-title">
                    <div class="section-icon">
                        <i data-lucide="calendar"></i>
                    </div>

                    <div>
                        <h3>Période de validité</h3>

                        <p>
                            Dates de début et de fin de l'abonnement.
                        </p>
                    </div>
                </div>


                <div class="subscription-period">

                    <div class="period-item">

                        <span class="period-label">
                            Date de début
                        </span>

                        <strong>
                            {{ $subscription->starts_at?->format('d/m/Y') ?? '—' }}
                        </strong>

                    </div>


                    <div class="period-line">
                        <span></span>
                    </div>


                    <div class="period-item">

                        <span class="period-label">
                            Date de fin
                        </span>

                        <strong>
                            {{ $subscription->ends_at?->format('d/m/Y') ?? '—' }}
                        </strong>

                    </div>

                </div>


                {{-- --------------------------------------------------
                     JOURS RESTANTS
                --------------------------------------------------- --}}

                @if($subscription->status === 'active')

                    @php
                        $daysRemaining = $subscription->days_remaining;
                        $duration = max((int) $subscription->duration_days, 1);

                        $progress = min(
                            100,
                            max(
                                0,
                                (($duration - $daysRemaining) / $duration) * 100
                            )
                        );
                    @endphp

                    <div class="subscription-progress">

                        <div class="progress-header">

                            <span>
                                Progression de l'abonnement
                            </span>

                            <strong>
                                {{ $daysRemaining }} jour{{ $daysRemaining > 1 ? 's' : '' }} restant{{ $daysRemaining > 1 ? 's' : '' }}
                            </strong>

                        </div>

                        <div class="progress-bar">

                            <div
                                class="progress-value"
                                style="width: {{ $progress }}%;"
                            ></div>

                        </div>

                    </div>

                @elseif($subscription->status === 'expired')

                    <div class="subscription-message expired">
                        <i data-lucide="calendar-x"></i>

                        <div>
                            <strong>Abonnement expiré</strong>

                            <p>
                                Cet abonnement a dépassé sa date de fin.
                            </p>
                        </div>
                    </div>

                @elseif($subscription->status === 'cancelled')

                    <div class="subscription-message cancelled">
                        <i data-lucide="circle-x"></i>

                        <div>
                            <strong>Abonnement annulé</strong>

                            <p>
                                Cet abonnement n'est plus actif.
                            </p>
                        </div>
                    </div>

                @endif

            </div>


            {{-- --------------------------------------------------
                 NOTES
            --------------------------------------------------- --}}

            @if($subscription->notes)

                <div class="subscription-card">

                    <div class="card-section-title">

                        <div class="section-icon">
                            <i data-lucide="file-text"></i>
                        </div>

                        <div>
                            <h3>Notes</h3>

                            <p>
                                Informations complémentaires.
                            </p>
                        </div>

                    </div>

                    <div class="subscription-notes">
                        {!! nl2br(e($subscription->notes)) !!}
                    </div>

                </div>

            @endif

        </div>


        {{-- ======================================================
             COLONNE LATÉRALE
        ======================================================= --}}

        <aside>

            {{-- --------------------------------------------------
                 ENTREPRISE
            --------------------------------------------------- --}}

            <div class="subscription-card">

                <div class="card-section-title">

                    <div class="section-icon">
                        <i data-lucide="building-2"></i>
                    </div>

                    <div>
                        <h3>Entreprise</h3>

                        <p>
                            Société bénéficiaire.
                        </p>
                    </div>

                </div>


                <div class="company-summary">

                    <div class="company-avatar">

                        @if($subscription->company?->logo)

                            <img
                                src="{{ asset('storage/' . $subscription->company->logo) }}"
                                alt="{{ $subscription->company->name }}"
                            >

                        @else

                            {{ strtoupper(substr($subscription->company?->name ?? 'E', 0, 1)) }}

                        @endif

                    </div>


                    <div>

                        <strong>
                            {{ $subscription->company?->name ?? '—' }}
                        </strong>

                        @if($subscription->company?->email)
                            <span>
                                {{ $subscription->company->email }}
                            </span>
                        @endif

                    </div>

                </div>


                @if($subscription->company)

                    <a
                        href="{{ route('superadmin.companies.show', $subscription->company) }}"
                        class="company-link"
                    >
                        Voir l'entreprise
                        <i data-lucide="arrow-up-right"></i>
                    </a>

                @endif

            </div>


            {{-- --------------------------------------------------
                 PLAN
            --------------------------------------------------- --}}

            <div class="subscription-card">

                <div class="card-section-title">

                    <div class="section-icon">
                        <i data-lucide="layers"></i>
                    </div>

                    <div>
                        <h3>Plan souscrit</h3>

                        <p>
                            Configuration actuelle du plan.
                        </p>
                    </div>

                </div>


                <div class="plan-summary">

                    <strong>
                        {{ $subscription->plan?->name ?? $subscription->name }}
                    </strong>

                    <span>
                        {{ $subscription->formatted_price }}
                    </span>

                </div>


                @if($subscription->plan)

                    <div class="plan-features">

                        @if($subscription->plan->max_users !== null)

                            <div>
                                <i data-lucide="users"></i>
                                <span>
                                    {{ $subscription->plan->max_users }}
                                    utilisateurs
                                </span>
                            </div>

                        @else

                            <div>
                                <i data-lucide="users"></i>
                                <span>
                                    Utilisateurs illimités
                                </span>
                            </div>

                        @endif


                        @if($subscription->plan->max_products !== null)

                            <div>
                                <i data-lucide="package"></i>
                                <span>
                                    {{ number_format($subscription->plan->max_products, 0, ',', ' ') }}
                                    produits
                                </span>
                            </div>

                        @else

                            <div>
                                <i data-lucide="package"></i>
                                <span>
                                    Produits illimités
                                </span>
                            </div>

                        @endif

                    </div>

                @endif

            </div>


            {{-- --------------------------------------------------
                 ACTIONS
            --------------------------------------------------- --}}

            @if($subscription->status === 'active')

                <div class="subscription-card danger-card">

                    <div class="card-section-title">

                        <div class="section-icon danger">
                            <i data-lucide="triangle-alert"></i>
                        </div>

                        <div>
                            <h3>Actions</h3>

                            <p>
                                Gestion de l'abonnement.
                            </p>
                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('superadmin.subscriptions.cancel', $subscription) }}"
                        onsubmit="return confirm('Voulez-vous vraiment annuler cet abonnement ?');"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn btn-danger btn-full"
                        >
                            <i data-lucide="ban"></i>
                            Annuler l'abonnement
                        </button>

                    </form>

                </div>

            @endif

        </aside>

    </div>

</div>

@endsection