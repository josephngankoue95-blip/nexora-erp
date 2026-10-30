@extends('layouts.superadmin')

@section('title', 'Modifier le plan')

@section('content')

<div class="subscription-plan-form-page">

    <div class="plans-page-header">

        <div>

            <div class="plans-breadcrumb">

                <a href="{{ route('superadmin.subscription-plans.index') }}">
                    Plans d’abonnement
                </a>

                <i data-lucide="chevron-right"></i>

                <strong>Modifier</strong>

            </div>

            <h1>Modifier {{ $subscriptionPlan->name }}</h1>

            <p>
                Modifiez les paramètres et fonctionnalités de ce plan.
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
        action="{{ route(
            'superadmin.subscription-plans.update',
            $subscriptionPlan
        ) }}"
        class="plan-form"
    >

        @csrf
        @method('PUT')


        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon blue">
                    <i data-lucide="layers"></i>
                </div>

                <div>

                    <h2>Informations générales</h2>

                    <p>
                        Informations principales du plan.
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
                        value="{{ old(
                            'name',
                            $subscriptionPlan->name
                        ) }}"
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
                        value="{{ old(
                            'slug',
                            $subscriptionPlan->slug
                        ) }}"
                    >

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
                            value="{{ old(
                                'price',
                                $subscriptionPlan->price
                            ) }}"
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
                            value="{{ old(
                                'duration_days',
                                $subscriptionPlan->duration_days
                            ) }}"
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
                    >{{ old(
                        'description',
                        $subscriptionPlan->description
                    ) }}</textarea>

                </div>

            </div>

        </div>


        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon purple">
                    <i data-lucide="gauge"></i>
                </div>

                <div>

                    <h2>Limites du plan</h2>

                    <p>
                        Ressources autorisées pour les entreprises.
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
                        value="{{ old(
                            'max_users',
                            $subscriptionPlan->max_users
                        ) }}"
                        min="1"
                    >

                    <small>
                        Vide = illimité.
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
                        value="{{ old(
                            'max_products',
                            $subscriptionPlan->max_products
                        ) }}"
                        min="1"
                    >

                    <small>
                        Vide = illimité.
                    </small>

                </div>

            </div>

        </div>


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
                    placeholder="Gestion des ventes&#10;Gestion du stock&#10;Gestion des clients"
                >{{ old(
                    'features',
                    is_array($subscriptionPlan->features)
                        ? implode(
                            PHP_EOL,
                            $subscriptionPlan->features
                        )
                        : ''
                ) }}</textarea>

            </div>

        </div>


        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon orange">
                    <i data-lucide="settings-2"></i>
                </div>

                <div>

                    <h2>Options</h2>

                    <p>
                        Visibilité et disponibilité du plan.
                    </p>

                </div>

            </div>


            <div class="plan-switches">

                <label class="plan-switch">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(
                            old(
                                'is_active',
                                $subscriptionPlan->is_active
                            )
                        )
                    >

                    <span class="plan-switch-slider"></span>

                    <span class="plan-switch-text">

                        <strong>Plan actif</strong>

                        <small>
                            Le plan peut être attribué aux entreprises.
                        </small>

                    </span>

                </label>


                <label class="plan-switch">

                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        @checked(
                            old(
                                'is_featured',
                                $subscriptionPlan->is_featured
                            )
                        )
                    >

                    <span class="plan-switch-slider"></span>

                    <span class="plan-switch-text">

                        <strong>Plan à la une</strong>

                        <small>
                            Mettre ce plan en évidence.
                        </small>

                    </span>

                </label>

            </div>

        </div>


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
                Enregistrer les modifications
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