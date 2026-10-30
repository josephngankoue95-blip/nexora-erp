@extends('layouts.superadmin')

@section('title', 'Modifier l’abonnement')

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

                <a href="{{ route('superadmin.subscriptions.show', $subscription) }}">
                    #{{ $subscription->id }}
                </a>

                <span>/</span>

                <span>Modifier</span>

            </div>

            <h1>Modifier l’abonnement</h1>

            <p>
                Modifiez le plan, la période ou l'état de cet abonnement.
            </p>

        </div>


        <div class="page-actions">

            <a
                href="{{ route('superadmin.subscriptions.show', $subscription) }}"
                class="btn btn-secondary"
            >
                <i data-lucide="arrow-left"></i>
                Retour
            </a>

        </div>

    </div>


    {{-- ==========================================================
         ERREURS
    =========================================================== --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <i data-lucide="circle-alert"></i>

            <div>

                <strong>
                    Impossible d'enregistrer les modifications.
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        </div>

    @endif


    {{-- ==========================================================
         FORMULAIRE
    =========================================================== --}}

    <form
        method="POST"
        action="{{ route('superadmin.subscriptions.update', $subscription) }}"
    >

        @csrf
        @method('PUT')


        <div class="subscription-form-layout">

            {{-- ==================================================
                 COLONNE PRINCIPALE
            =================================================== --}}

            <div>

                {{-- ------------------------------------------------
                     ENTREPRISE
                ------------------------------------------------- --}}

                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-icon">
                            <i data-lucide="building-2"></i>
                        </div>

                        <div>
                            <h2>Entreprise</h2>

                            <p>
                                Entreprise concernée par l'abonnement.
                            </p>
                        </div>

                    </div>


                    <div class="form-group">

                        <label>
                            Entreprise
                        </label>

                        <div class="readonly-field">

                            <div class="readonly-icon">
                                <i data-lucide="building-2"></i>
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

                        <small class="form-help">
                            L'entreprise ne peut pas être modifiée depuis cet écran.
                        </small>

                    </div>

                </div>


                {{-- ------------------------------------------------
                     PLAN
                ------------------------------------------------- --}}

                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-icon">
                            <i data-lucide="layers"></i>
                        </div>

                        <div>
                            <h2>Plan d'abonnement</h2>

                            <p>
                                Choisissez le nouveau plan à appliquer.
                            </p>
                        </div>

                    </div>


                    <div class="form-group">

                        <label for="plan_id">
                            Plan
                            <span class="required">*</span>
                        </label>

                        <select
                            name="plan_id"
                            id="plan_id"
                            class="form-control subscription-select @error('plan_id') is-invalid @enderror"
                            required
                        >

                            @foreach($plans as $plan)

                                <option
                                    value="{{ $plan->id }}"
                                    data-price="{{ $plan->price }}"
                                    data-duration="{{ $plan->duration_days }}"
                                    @selected(old('plan_id', $subscription->plan_id) == $plan->id)
                                >
                                    {{ $plan->name }}
                                    — {{ $plan->formatted_price }}
                                    / {{ $plan->duration_label }}
                                </option>

                            @endforeach

                        </select>

                        @error('plan_id')
                            <div class="field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- ------------------------------------------------
                         APERÇU PLAN
                    ------------------------------------------------- --}}

                    <div
                        class="subscription-plan-preview"
                        id="planPreview"
                    >

                        <div>

                            <span>Prix</span>

                            <strong id="previewPrice">
                                {{ $subscription->formatted_price }}
                            </strong>

                        </div>


                        <div>

                            <span>Durée</span>

                            <strong id="previewDuration">
                                {{ $subscription->duration_days }} jours
                            </strong>

                        </div>


                        <div>

                            <span>Nouvelle date de fin</span>

                            <strong id="previewEndDate">
                                {{ $subscription->ends_at?->format('d/m/Y') ?? '—' }}
                            </strong>

                        </div>

                    </div>

                </div>


                {{-- ------------------------------------------------
                     PÉRIODE
                ------------------------------------------------- --}}

                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-icon">
                            <i data-lucide="calendar"></i>
                        </div>

                        <div>
                            <h2>Période</h2>

                            <p>
                                Définissez la date de début de l'abonnement.
                            </p>
                        </div>

                    </div>


                    <div class="form-group">

                        <label for="starts_at">
                            Date de début
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            name="starts_at"
                            id="starts_at"
                            class="form-control @error('starts_at') is-invalid @enderror"
                            value="{{ old('starts_at', $subscription->starts_at?->format('Y-m-d')) }}"
                            required
                        >

                        @error('starts_at')
                            <div class="field-error">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="form-help">
                            La date de fin sera recalculée automatiquement selon la durée du plan.
                        </small>

                    </div>

                </div>


                {{-- ------------------------------------------------
                     NOTES
                ------------------------------------------------- --}}

                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-icon">
                            <i data-lucide="file-text"></i>
                        </div>

                        <div>
                            <h2>Notes</h2>

                            <p>
                                Ajoutez une information complémentaire.
                            </p>
                        </div>

                    </div>


                    <div class="form-group">

                        <label for="notes">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            id="notes"
                            rows="5"
                            class="form-control"
                            placeholder="Informations complémentaires..."
                        >{{ old('notes', $subscription->notes) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- ==================================================
                 COLONNE LATÉRALE
            =================================================== --}}

            <aside>

                {{-- ------------------------------------------------
                     STATUT
                ------------------------------------------------- --}}

                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-icon">
                            <i data-lucide="activity"></i>
                        </div>

                        <div>
                            <h2>Statut</h2>

                            <p>
                                État actuel de l'abonnement.
                            </p>
                        </div>

                    </div>


                    <div class="form-group">

                        <label for="status">
                            Statut
                            <span class="required">*</span>
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-control @error('status') is-invalid @enderror"
                            required
                        >

                            <option
                                value="active"
                                @selected(old('status', $subscription->status) === 'active')
                            >
                                Actif
                            </option>

                            <option
                                value="expired"
                                @selected(old('status', $subscription->status) === 'expired')
                            >
                                Expiré
                            </option>

                            <option
                                value="cancelled"
                                @selected(old('status', $subscription->status) === 'cancelled')
                            >
                                Annulé
                            </option>

                        </select>

                        @error('status')
                            <div class="field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="status-help">

                        <i data-lucide="info"></i>

                        <p>
                            Un abonnement actif permet à l'entreprise d'utiliser son offre.
                        </p>

                    </div>

                </div>


                {{-- ------------------------------------------------
                     INFORMATIONS ACTUELLES
                ------------------------------------------------- --}}

                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-icon">
                            <i data-lucide="info"></i>
                        </div>

                        <div>
                            <h2>Informations</h2>

                            <p>
                                Données actuelles.
                            </p>
                        </div>

                    </div>


                    <div class="summary-list">

                        <div class="summary-row">

                            <span>
                                ID
                            </span>

                            <strong>
                                #{{ $subscription->id }}
                            </strong>

                        </div>


                        <div class="summary-row">

                            <span>
                                Plan actuel
                            </span>

                            <strong>
                                {{ $subscription->name }}
                            </strong>

                        </div>


                        <div class="summary-row">

                            <span>
                                Prix actuel
                            </span>

                            <strong>
                                {{ $subscription->formatted_price }}
                            </strong>

                        </div>


                        <div class="summary-row">

                            <span>
                                Créé le
                            </span>

                            <strong>
                                {{ $subscription->created_at?->format('d/m/Y') ?? '—' }}
                            </strong>

                        </div>

                    </div>

                </div>


                {{-- ------------------------------------------------
                     ENREGISTRER
                ------------------------------------------------- --}}

                <div class="form-actions-card">

                    <button
                        type="submit"
                        class="btn btn-primary btn-full"
                    >
                        <i data-lucide="save"></i>
                        Enregistrer les modifications
                    </button>


                    <a
                        href="{{ route('superadmin.subscriptions.show', $subscription) }}"
                        class="btn btn-secondary btn-full"
                    >
                        Annuler
                    </a>

                </div>

            </aside>

        </div>

    </form>

</div>


{{-- ==============================================================
     JAVASCRIPT
=============================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const planSelect = document.getElementById('plan_id');
    const startInput = document.getElementById('starts_at');

    const previewPrice = document.getElementById('previewPrice');
    const previewDuration = document.getElementById('previewDuration');
    const previewEndDate = document.getElementById('previewEndDate');


    function formatPrice(value) {

        return new Intl.NumberFormat('fr-FR', {
            maximumFractionDigits: 0
        }).format(Number(value || 0)) + ' FCFA';

    }


    function formatDate(date) {

        return new Intl.DateTimeFormat('fr-FR').format(date);

    }


    function updatePreview() {

        const option = planSelect.options[planSelect.selectedIndex];

        if (!option) {
            return;
        }


        const price = option.dataset.price || 0;
        const duration = Number(option.dataset.duration || 0);


        previewPrice.textContent = formatPrice(price);

        previewDuration.textContent =
            duration === 1
                ? '1 jour'
                : duration + ' jours';


        if (!startInput.value || !duration) {

            previewEndDate.textContent = '—';

            return;

        }


        const startDate = new Date(startInput.value + 'T00:00:00');

        startDate.setDate(
            startDate.getDate() + duration
        );


        previewEndDate.textContent =
            formatDate(startDate);

    }


    planSelect.addEventListener(
        'change',
        updatePreview
    );


    startInput.addEventListener(
        'change',
        updatePreview
    );


    updatePreview();

});

</script>

@endsection