@extends('layouts.superadmin')

@section('title', 'Paiements')

@section('content')

<div class="payment-page">

    {{-- ==========================================================
         EN-TÊTE
    =========================================================== --}}

    <div class="payment-page-header">

        <div>
            <div class="payment-breadcrumb">
                <span>Administration</span>
                <i data-lucide="chevron-right"></i>
                <strong>Paiements</strong>
            </div>

            <h1>
                <i data-lucide="wallet-cards"></i>
                Paiements
            </h1>

            <p>
                Gérez les paiements et les transactions des abonnements.
            </p>
        </div>

        <a
            href="{{ route('superadmin.payments.create') }}"
            class="payment-primary-btn"
        >
            <i data-lucide="plus"></i>
            Nouveau paiement
        </a>

    </div>


    {{-- ==========================================================
         MESSAGE DE SUCCÈS
    =========================================================== --}}

    @if(session('success'))
        <div class="payment-alert payment-alert-success">
            <div class="payment-alert-icon">
                <i data-lucide="check-circle"></i>
            </div>

            <div>
                <strong>Opération réussie</strong>
                <span>{{ session('success') }}</span>
            </div>

            <button
                type="button"
                onclick="this.parentElement.remove()"
                class="payment-alert-close"
            >
                <i data-lucide="x"></i>
            </button>
        </div>
    @endif


    {{-- ==========================================================
         STATISTIQUES
    =========================================================== --}}

    <div class="payment-stat-grid">

        {{-- Total --}}
        <div class="payment-stat-card">

            <div class="payment-stat-icon payment-stat-blue">
                <i data-lucide="wallet"></i>
            </div>

            <div class="payment-stat-content">
                <span>Total paiements</span>

                <strong>
                    {{ number_format($statistics['total'], 0, ',', ' ') }}
                </strong>

                <small>
                    Transactions enregistrées
                </small>
            </div>

        </div>


        {{-- Terminés --}}
        <div class="payment-stat-card">

            <div class="payment-stat-icon payment-stat-green">
                <i data-lucide="circle-check"></i>
            </div>

            <div class="payment-stat-content">
                <span>Paiements terminés</span>

                <strong>
                    {{ number_format($statistics['completed'], 0, ',', ' ') }}
                </strong>

                <small>
                    Transactions confirmées
                </small>
            </div>

        </div>


        {{-- En attente --}}
        <div class="payment-stat-card">

            <div class="payment-stat-icon payment-stat-orange">
                <i data-lucide="clock-3"></i>
            </div>

            <div class="payment-stat-content">
                <span>En attente</span>

                <strong>
                    {{ number_format($statistics['pending'], 0, ',', ' ') }}
                </strong>

                <small>
                    Paiements à vérifier
                </small>
            </div>

        </div>


        {{-- Échoués --}}
        <div class="payment-stat-card">

            <div class="payment-stat-icon payment-stat-red">
                <i data-lucide="circle-x"></i>
            </div>

            <div class="payment-stat-content">
                <span>Échoués</span>

                <strong>
                    {{ number_format($statistics['failed'], 0, ',', ' ') }}
                </strong>

                <small>
                    Transactions échouées
                </small>
            </div>

        </div>


        {{-- Chiffre d'affaires --}}
        <div class="payment-stat-card payment-stat-revenue">

            <div class="payment-stat-icon payment-stat-purple">
                <i data-lucide="banknote"></i>
            </div>

            <div class="payment-stat-content">
                <span>Revenus encaissés</span>

                <strong>
                    {{ number_format($statistics['revenue'], 0, ',', ' ') }}
                    FCFA
                </strong>

                <small>
                    Paiements terminés
                </small>
            </div>

        </div>

    </div>


    {{-- ==========================================================
         FILTRES
    =========================================================== --}}

    <div class="payment-filter-card">

        <div class="payment-filter-header">

            <div>
                <h2>
                    <i data-lucide="filter"></i>
                    Rechercher et filtrer
                </h2>

                <p>
                    Affinez la liste des transactions.
                </p>
            </div>

            @if(
                request()->filled('search') ||
                request()->filled('status') ||
                request()->filled('method') ||
                request()->filled('date_from') ||
                request()->filled('date_to')
            )

                <a
                    href="{{ route('superadmin.payments.index') }}"
                    class="payment-reset-btn"
                >
                    <i data-lucide="rotate-ccw"></i>
                    Réinitialiser
                </a>

            @endif

        </div>


        <form
            method="GET"
            action="{{ route('superadmin.payments.index') }}"
            class="payment-filter-form"
        >

            {{-- Recherche --}}
            <div class="payment-form-group payment-search-group">

                <label for="search">
                    Recherche
                </label>

                <div class="payment-input-icon">

                    <i data-lucide="search"></i>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Référence, entreprise, téléphone..."
                    >

                </div>

            </div>


            {{-- Statut --}}
            <div class="payment-form-group">

                <label for="status">
                    Statut
                </label>

                <select
                    name="status"
                    id="status"
                >
                    <option value="">
                        Tous les statuts
                    </option>

                    <option
                        value="completed"
                        {{ request('status') === 'completed' ? 'selected' : '' }}
                    >
                        Terminé
                    </option>

                    <option
                        value="pending"
                        {{ request('status') === 'pending' ? 'selected' : '' }}
                    >
                        En attente
                    </option>

                    <option
                        value="failed"
                        {{ request('status') === 'failed' ? 'selected' : '' }}
                    >
                        Échoué
                    </option>

                    <option
                        value="refunded"
                        {{ request('status') === 'refunded' ? 'selected' : '' }}
                    >
                        Remboursé
                    </option>

                </select>

            </div>


            {{-- Méthode --}}
            <div class="payment-form-group">

                <label for="method">
                    Méthode
                </label>

                <select
                    name="method"
                    id="method"
                >

                    <option value="">
                        Toutes les méthodes
                    </option>

                    <option
                        value="cash"
                        {{ request('method') === 'cash' ? 'selected' : '' }}
                    >
                        Espèces
                    </option>

                    <option
                        value="mobile_money"
                        {{ request('method') === 'mobile_money' ? 'selected' : '' }}
                    >
                        Mobile Money
                    </option>

                    <option
                        value="bank_transfer"
                        {{ request('method') === 'bank_transfer' ? 'selected' : '' }}
                    >
                        Virement bancaire
                    </option>

                    <option
                        value="card"
                        {{ request('method') === 'card' ? 'selected' : '' }}
                    >
                        Carte bancaire
                    </option>

                    <option
                        value="other"
                        {{ request('method') === 'other' ? 'selected' : '' }}
                    >
                        Autre
                    </option>

                </select>

            </div>


            {{-- Date début --}}
            <div class="payment-form-group">

                <label for="date_from">
                    Du
                </label>

                <input
                    type="date"
                    id="date_from"
                    name="date_from"
                    value="{{ request('date_from') }}"
                >

            </div>


            {{-- Date fin --}}
            <div class="payment-form-group">

                <label for="date_to">
                    Au
                </label>

                <input
                    type="date"
                    id="date_to"
                    name="date_to"
                    value="{{ request('date_to') }}"
                >

            </div>


            {{-- Bouton --}}
            <div class="payment-filter-action">

                <button
                    type="submit"
                    class="payment-filter-btn"
                >
                    <i data-lucide="search"></i>
                    Rechercher
                </button>

            </div>

        </form>

    </div>


    {{-- ==========================================================
         TABLEAU
    =========================================================== --}}

    <div class="payment-table-card">

        <div class="payment-table-header">

            <div>
                <h2>
                    Transactions
                </h2>

                <p>
                    {{ $payments->total() }}
                    paiement{{ $payments->total() > 1 ? 's' : '' }}
                    trouvé{{ $payments->total() > 1 ? 's' : '' }}
                </p>
            </div>

            <div class="payment-table-count">
                Page {{ $payments->currentPage() }}
                / {{ $payments->lastPage() }}
            </div>

        </div>


        @if($payments->count())

            <div class="payment-table-wrapper">

                <table class="payment-table">

                    <thead>
                        <tr>

                            <th>Référence</th>

                            <th>Entreprise</th>

                            <th>Abonnement</th>

                            <th>Montant</th>

                            <th>Méthode</th>

                            <th>Statut</th>

                            <th>Date</th>

                            <th class="payment-actions-column">
                                Actions
                            </th>

                        </tr>
                    </thead>


                    <tbody>

                        @foreach($payments as $payment)

                            <tr>

                                {{-- Référence --}}
                                <td>

                                    <div class="payment-reference">

                                        <div class="payment-reference-icon">
                                            <i data-lucide="receipt"></i>
                                        </div>

                                        <div>
                                            <strong>
                                                {{ $payment->reference }}
                                            </strong>

                                            @if($payment->transaction_reference)
                                                <small>
                                                    {{ $payment->transaction_reference }}
                                                </small>
                                            @endif
                                        </div>

                                    </div>

                                </td>


                                {{-- Entreprise --}}
                                <td>

                                    @if($payment->company)

                                        <div class="payment-company">

                                            <div class="payment-company-logo">

                                                @if($payment->company->logo)
                                                    <img
                                                        src="{{ asset('storage/' . $payment->company->logo) }}"
                                                        alt="{{ $payment->company->name }}"
                                                    >
                                                @else
                                                    <i data-lucide="building-2"></i>
                                                @endif

                                            </div>

                                            <div>

                                                <strong>
                                                    {{ $payment->company->name }}
                                                </strong>

                                                @if($payment->company->email)
                                                    <small>
                                                        {{ $payment->company->email }}
                                                    </small>
                                                @endif

                                            </div>

                                        </div>

                                    @else

                                        <span class="payment-muted">
                                            Entreprise supprimée
                                        </span>

                                    @endif

                                </td>


                                {{-- Abonnement --}}
                                <td>

                                    @if($payment->subscription)

                                        <div class="payment-subscription">

                                            <strong>
                                                {{ $payment->subscription->name }}
                                            </strong>

                                            @if($payment->subscription->plan)
                                                <small>
                                                    Plan :
                                                    {{ $payment->subscription->plan->name }}
                                                </small>
                                            @endif

                                        </div>

                                    @else

                                        <span class="payment-no-subscription">
                                            Aucun abonnement
                                        </span>

                                    @endif

                                </td>


                                {{-- Montant --}}
                                <td>

                                    <div class="payment-amount">

                                        <strong>
                                            {{ number_format($payment->amount, 0, ',', ' ') }}
                                            FCFA
                                        </strong>

                                        @if($payment->paid_at)
                                            <small>
                                                Payé le
                                                {{ $payment->paid_at->format('d/m/Y H:i') }}
                                            </small>
                                        @endif

                                    </div>

                                </td>


                                {{-- Méthode --}}
                                <td>

                                    <div class="payment-method">

                                        @switch($payment->method)

                                            @case('mobile_money')
                                                <i data-lucide="smartphone"></i>
                                                <span>Mobile Money</span>
                                                @break

                                            @case('cash')
                                                <i data-lucide="banknote"></i>
                                                <span>Espèces</span>
                                                @break

                                            @case('bank_transfer')
                                                <i data-lucide="landmark"></i>
                                                <span>Virement</span>
                                                @break

                                            @case('card')
                                                <i data-lucide="credit-card"></i>
                                                <span>Carte</span>
                                                @break

                                            @default
                                                <i data-lucide="circle-dollar-sign"></i>
                                                <span>Autre</span>

                                        @endswitch

                                    </div>

                                </td>


                                {{-- Statut --}}
                                <td>

                                    @switch($payment->status)

                                        @case('completed')

                                            <span class="payment-status payment-status-success">
                                                <i data-lucide="check-circle"></i>
                                                Terminé
                                            </span>

                                            @break

                                        @case('pending')

                                            <span class="payment-status payment-status-warning">
                                                <i data-lucide="clock-3"></i>
                                                En attente
                                            </span>

                                            @break

                                        @case('failed')

                                            <span class="payment-status payment-status-danger">
                                                <i data-lucide="x-circle"></i>
                                                Échoué
                                            </span>

                                            @break

                                        @case('refunded')

                                            <span class="payment-status payment-status-info">
                                                <i data-lucide="rotate-ccw"></i>
                                                Remboursé
                                            </span>

                                            @break

                                        @default

                                            <span class="payment-status payment-status-secondary">
                                                {{ $payment->status }}
                                            </span>

                                    @endswitch

                                </td>


                                {{-- Date --}}
                                <td>

                                    <div class="payment-date">

                                        <strong>
                                            {{ $payment->created_at->format('d/m/Y') }}
                                        </strong>

                                        <small>
                                            {{ $payment->created_at->format('H:i') }}
                                        </small>

                                    </div>

                                </td>


                                {{-- Actions --}}
                                <td>

                                    <div class="payment-actions">

                                        <a
                                            href="{{ route('superadmin.payments.show', $payment) }}"
                                            class="payment-action-btn payment-action-view"
                                            title="Voir"
                                        >
                                            <i data-lucide="eye"></i>
                                        </a>

                                        <a
                                            href="{{ route('superadmin.payments.edit', $payment) }}"
                                            class="payment-action-btn payment-action-edit"
                                            title="Modifier"
                                        >
                                            <i data-lucide="pencil"></i>
                                        </a>

                                        <a
                                            href="{{ route('superadmin.payments.receipt', $payment) }}"
                                            class="payment-action-btn payment-action-receipt"
                                            title="Reçu"
                                            target="_blank"
                                        >
                                            <i data-lucide="printer"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- ==================================================
                 PAGINATION
            =================================================== --}}

            <div class="payment-pagination">

                {{ $payments->links() }}

            </div>

        @else

            {{-- ==================================================
                 AUCUN RÉSULTAT
            =================================================== --}}

            <div class="payment-empty">

                <div class="payment-empty-icon">
                    <i data-lucide="wallet-cards"></i>
                </div>

                <h3>
                    Aucun paiement trouvé
                </h3>

                <p>
                    Aucun paiement ne correspond aux critères sélectionnés.
                </p>

                @if(
                    request()->filled('search') ||
                    request()->filled('status') ||
                    request()->filled('method') ||
                    request()->filled('date_from') ||
                    request()->filled('date_to')
                )

                    <a
                        href="{{ route('superadmin.payments.index') }}"
                        class="payment-reset-btn"
                    >
                        <i data-lucide="rotate-ccw"></i>
                        Réinitialiser les filtres
                    </a>

                @else

                    <a
                        href="{{ route('superadmin.payments.create') }}"
                        class="payment-primary-btn"
                    >
                        <i data-lucide="plus"></i>
                        Enregistrer un paiement
                    </a>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection