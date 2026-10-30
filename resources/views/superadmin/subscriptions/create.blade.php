@extends('layouts.superadmin')

@section('title', 'Nouvel abonnement')

@section('content')

<div class="subscription-plan-form-page">

    <div class="plans-page-header">

        <div>

            <div class="plans-breadcrumb">

                <a href="{{ route('superadmin.subscriptions.index') }}">
                    Abonnements
                </a>

                <i data-lucide="chevron-right"></i>

                <strong>Nouvel abonnement</strong>

            </div>

            <h1>Attribuer un abonnement</h1>

            <p>
                Sélectionnez une entreprise et le plan à lui attribuer.
            </p>

        </div>

        <a
            href="{{ route('superadmin.subscriptions.index') }}"
            class="plans-secondary-button"
        >
            <i data-lucide="arrow-left"></i>
            Retour
        </a>

    </div>


    @if(session('error'))

        <div class="plans-alert plans-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>{{ session('error') }}</span>

        </div>

    @endif


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
        action="{{ route('superadmin.subscriptions.store') }}"
        class="plan-form"
    >

        @csrf


        {{-- ENTREPRISE --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon blue">
                    <i data-lucide="building-2"></i>
                </div>

                <div>

                    <h2>Entreprise</h2>

                    <p>
                        Choisissez l’entreprise qui recevra l’abonnement.
                    </p>

                </div>

            </div>


            <div class="plan-form-group">

                <label for="company_id">
                    Entreprise
                    <span>*</span>
                </label>

                <select
                    id="company_id"
                    name="company_id"
                    class="subscription-select"
                    required
                >

                    <option value="">
                        Sélectionner une entreprise
                    </option>

                    @foreach($companies as $company)

                        <option
                            value="{{ $company->id }}"
                            @selected(
                                old('company_id') == $company->id
                            )
                        >
                            {{ $company->name }}

                            @if($company->email)
                                — {{ $company->email }}
                            @endif
                        </option>

                    @endforeach

                </select>

            </div>

        </div>


        {{-- PLAN --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon purple">
                    <i data-lucide="layers"></i>
                </div>

                <div>

                    <h2>Plan d’abonnement</h2>

                    <p>
                        Le prix et la durée seront récupérés automatiquement.
                    </p>

                </div>

            </div>


            <div class="plan-form-group">

                <label for="plan_id">
                    Plan
                    <span>*</span>
                </label>

                <select
                    id="plan_id"
                    name="plan_id"
                    class="subscription-select"
                    required
                >

                    <option value="">
                        Sélectionner un plan
                    </option>

                    @foreach($plans as $plan)

                        <option
                            value="{{ $plan->id }}"
                            data-price="{{ $plan->price }}"
                            data-duration="{{ $plan->duration_days }}"
                            @selected(
                                old('plan_id') == $plan->id
                            )
                        >
                            {{ $plan->name }}
                            —
                            {{ $plan->formatted_price }}
                            /
                            {{ $plan->duration_label }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div
                class="subscription-plan-preview"
                id="planPreview"
            >

                <div>

                    <span>Prix</span>

                    <strong id="previewPrice">
                        —
                    </strong>

                </div>

                <div>

                    <span>Durée</span>

                    <strong id="previewDuration">
                        —
                    </strong>

                </div>

                <div>

                    <span>Expiration</span>

                    <strong id="previewEnd">
                        —
                    </strong>

                </div>

            </div>

        </div>


        {{-- DATES --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon green">
                    <i data-lucide="calendar-days"></i>
                </div>

                <div>

                    <h2>Période</h2>

                    <p>
                        Définissez la date de début de l’abonnement.
                    </p>

                </div>

            </div>


            <div class="plan-form-group">

                <label for="starts_at">
                    Date de début
                    <span>*</span>
                </label>

                <input
                    id="starts_at"
                    type="date"
                    name="starts_at"
                    value="{{ old(
                        'starts_at',
                        now()->format('Y-m-d')
                    ) }}"
                    required
                >

            </div>

        </div>


        {{-- NOTES --}}

        <div class="plan-form-card">

            <div class="plan-form-card-header">

                <div class="plan-form-icon orange">
                    <i data-lucide="file-text"></i>
                </div>

                <div>

                    <h2>Notes</h2>

                    <p>
                        Informations complémentaires facultatives.
                    </p>

                </div>

            </div>


            <div class="plan-form-group">

                <label for="notes">
                    Notes
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="5"
                    placeholder="Ex : Offre commerciale négociée..."
                >{{ old('notes') }}</textarea>

            </div>

        </div>


        <div class="plan-form-actions">

            <a
                href="{{ route('superadmin.subscriptions.index') }}"
                class="plans-secondary-button"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="plans-primary-button"
            >
                <i data-lucide="check"></i>
                Activer l’abonnement
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

    const planSelect = document.getElementById('plan_id');
    const startInput = document.getElementById('starts_at');

    const priceElement =
        document.getElementById('previewPrice');

    const durationElement =
        document.getElementById('previewDuration');

    const endElement =
        document.getElementById('previewEnd');


    function updatePreview() {

        const option =
            planSelect.options[
                planSelect.selectedIndex
            ];

        if (!option || !option.value) {

            priceElement.textContent = '—';
            durationElement.textContent = '—';
            endElement.textContent = '—';

            return;
        }

        const price =
            Number(option.dataset.price || 0);

        const duration =
            Number(option.dataset.duration || 0);

        priceElement.textContent =
            new Intl.NumberFormat('fr-FR').format(price)
            + ' FCFA';

        durationElement.textContent =
            duration + ' jour(s)';


        if (startInput.value && duration > 0) {

            const start =
                new Date(startInput.value + 'T00:00:00');

            start.setDate(
                start.getDate() + duration
            );

            endElement.textContent =
                start.toLocaleDateString('fr-FR');

        } else {

            endElement.textContent = '—';

        }
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

@endpush