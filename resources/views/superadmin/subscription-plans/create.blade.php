@extends('layouts.superadmin')

@section('title', 'Nouveau plan')

@section('content')

<div class="subscription-plan-form-page">

    <div class="plans-page-header">

        <div>

            <div class="plans-breadcrumb">

                <a href="{{ route('superadmin.subscription-plans.index') }}">
                    Plans d’abonnement
                </a>

                <i data-lucide="chevron-right"></i>

                <strong>Nouveau plan</strong>

            </div>

            <h1>Créer un plan d’abonnement</h1>

            <p>
                Définissez une nouvelle offre commerciale pour vos entreprises.
            </p>

        </div>

        <a
            href="{{ route('superadmin.subscription-plans.index') }}"
            class="plans-secondary-button"
        >
            <i data-lucide="arrow-left"></i>
            Retour
        </a>

    </div>


    @if($errors->any())

        <div class="plans-alert plans-alert-error">

            <i data-lucide="circle-alert"></i>

            <div>

                <strong>
                    Vérifiez les informations saisies.
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('superadmin.subscription-plans.store') }}"
        class="plan-form"
    >

        @csrf


        {{-- ======================================================
             INFORMATIONS
        ======================================================= --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon blue">
                    <i data-lucide="layers"></i>
                </div>

                <div>

                    <h2>Informations générales</h2>

                    <p>
                        Présentez votre offre aux entreprises.
                    </p>

                </div>

            </div>


            <div class="plan-form-grid">

                <div class="plan-form-group full">

                    <label for="name">
                        Nom du plan
                        <span>*</span>
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ex : Plan Pro"
                        required
                    >

                </div>


                <div class="plan-form-group">

                    <label for="slug">
                        Slug
                    </label>

                    <input
                        id="slug"
                        type="text"
                        name="slug"
                        value="{{ old('slug') }}"
                        placeholder="plan-pro"
                    >

                    <small>
                        Laissez vide pour le générer automatiquement.
                    </small>

                </div>


                <div class="plan-form-group">

                    <label for="price">
                        Prix
                        <span>*</span>
                    </label>

                    <div class="plan-input-with-suffix">

                        <input
                            id="price"
                            type="number"
                            name="price"
                            value="{{ old('price', 0) }}"
                            min="0"
                            step="1"
                            required
                        >

                        <span>FCFA</span>

                    </div>

                </div>


                <div class="plan-form-group">

                    <label for="duration_days">
                        Durée
                        <span>*</span>
                    </label>

                    <div class="plan-input-with-suffix">

                        <input
                            id="duration_days"
                            type="number"
                            name="duration_days"
                            value="{{ old('duration_days', 30) }}"
                            min="1"
                            required
                        >

                        <span>jours</span>

                    </div>

                </div>


                <div class="plan-form-group full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Décrivez brièvement ce plan..."
                    >{{ old('description') }}</textarea>

                </div>

            </div>

        </div>


        {{-- ======================================================
             LIMITES
        ======================================================= --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon purple">
                    <i data-lucide="gauge"></i>
                </div>

                <div>

                    <h2>Limites du plan</h2>

                    <p>
                        Définissez les ressources disponibles.
                    </p>

                </div>

            </div>


            <div class="plan-form-grid">

                <div class="plan-form-group">

                    <label for="max_users">
                        Nombre maximum d’utilisateurs
                    </label>

                    <input
                        id="max_users"
                        type="number"
                        name="max_users"
                        value="{{ old('max_users') }}"
                        min="1"
                        placeholder="Ex : 10"
                    >

                    <small>
                        Laissez vide pour illimité.
                    </small>

                </div>


                <div class="plan-form-group">

                    <label for="max_products">
                        Nombre maximum de produits
                    </label>

                    <input
                        id="max_products"
                        type="number"
                        name="max_products"
                        value="{{ old('max_products') }}"
                        min="1"
                        placeholder="Ex : 2000"
                    >

                    <small>
                        Laissez vide pour illimité.
                    </small>

                </div>

            </div>

        </div>


        {{-- ======================================================
             FONCTIONNALITÉS
        ======================================================= --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon green">
                    <i data-lucide="list-checks"></i>
                </div>

                <div>

                    <h2>Fonctionnalités</h2>

                    <p>
                        Une fonctionnalité par ligne.
                    </p>

                </div>

            </div>


            <div class="plan-form-group">

                <label for="features">
                    Fonctionnalités incluses
                </label>

                <textarea
                    id="features"
                    name="features"
                    rows="7"
                    placeholder="Gestion des ventes&#10;Gestion du stock&#10;Gestion des clients&#10;Rapports financiers&#10;Support prioritaire"
                >{{ old('features') }}</textarea>

                <small>
                    Chaque ligne sera enregistrée comme une fonctionnalité distincte.
                </small>

            </div>

        </div>


        {{-- ======================================================
             OPTIONS
        ======================================================= --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon orange">
                    <i data-lucide="settings-2"></i>
                </div>

                <div>

                    <h2>Options</h2>

                    <p>
                        Configurez la visibilité commerciale du plan.
                    </p>

                </div>

            </div>


            <div class="plan-switches">

                <label class="plan-switch">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(old('is_active', true))
                    >

                    <span class="plan-switch-slider"></span>

                    <span class="plan-switch-text">

                        <strong>Plan actif</strong>

                        <small>
                            Le plan pourra être attribué aux entreprises.
                        </small>

                    </span>

                </label>


                <label class="plan-switch">

                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        @checked(old('is_featured'))
                    >

                    <span class="plan-switch-slider"></span>

                    <span class="plan-switch-text">

                        <strong>Mettre à la une</strong>

                        <small>
                            Mettre en évidence ce plan dans l’interface.
                        </small>

                    </span>

                </label>

            </div>

        </div>


        {{-- ======================================================
             ACTIONS
        ======================================================= --}}

        <div class="plan-form-actions">

            <a
                href="{{ route('superadmin.subscription-plans.index') }}"
                class="plans-secondary-button"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="plans-primary-button"
            >
                <i data-lucide="save"></i>
                Créer le plan
            </button>

        </div>

    </form>

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