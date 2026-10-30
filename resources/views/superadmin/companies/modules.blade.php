@extends('layouts.superadmin')

@section('title', 'Modules — ' . $company->name)

@section('content')

<div class="company-modules-page">

    {{-- ============================================================
         HEADER
    ============================================================ --}}

    <div class="modules-header">

        <div class="modules-header-content">

            <a
                href="{{ route('superadmin.companies.index') }}"
                class="modules-back"
            >
                ← Retour aux entreprises
            </a>

            <span class="modules-kicker">
                CONFIGURATION ENTREPRISE
            </span>

            <h1>
                Modules de l'entreprise
            </h1>

            <p>
                Activez ou désactivez les fonctionnalités disponibles
                pour cette entreprise.
            </p>

        </div>


        <div class="company-identity">

            <div class="company-identity-icon">
                🏢
            </div>

            <div>

                <strong>
                    {{ $company->name }}
                </strong>

                <span>
                    {{ $company->email ?? 'Aucune adresse email' }}
                </span>

            </div>

        </div>

    </div>


    {{-- ============================================================
         ALERT SUCCESS
    ============================================================ --}}

    @if(session('success'))

        <div class="modules-alert modules-alert-success">

            <span class="modules-alert-icon">
                ✓
            </span>

            <div>

                <strong>
                    Modification enregistrée
                </strong>

                <p>
                    {{ session('success') }}
                </p>

            </div>

        </div>

    @endif


    {{-- ============================================================
         ERREURS
    ============================================================ --}}

    @if($errors->any())

        <div class="modules-alert modules-alert-danger">

            <span class="modules-alert-icon">
                !
            </span>

            <div>

                <strong>
                    Impossible d'enregistrer les modifications
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- ============================================================
         FORMULAIRE
    ============================================================ --}}

    <form
        method="POST"
        action="{{ route(
            'superadmin.companies.modules.update',
            $company
        ) }}"
        id="company-modules-form"
    >

        @csrf

        @method('PUT')


        {{-- ========================================================
             TOOLBAR
        ======================================================== --}}

        <div class="modules-toolbar">

            <div>

                <span class="modules-toolbar-label">
                    MODULES DISPONIBLES
                </span>

                <strong>
                    {{ $modules->count() }} modules
                </strong>

            </div>


            <div class="modules-toolbar-actions">

                <button
                    type="button"
                    class="modules-tool-btn"
                    id="activate-all"
                >
                    Tout activer
                </button>

                <button
                    type="button"
                    class="modules-tool-btn"
                    id="deactivate-all"
                >
                    Tout désactiver
                </button>

            </div>

        </div>


        {{-- ========================================================
             MODULES
        ======================================================== --}}

        <div class="modules-grid">

            @forelse($modules as $module)

                @php
                    $enabled = in_array(
                        $module->id,
                        $enabledModuleIds
                    );
                @endphp


                <label
                    class="module-card {{ $enabled ? 'is-active' : '' }}"
                    data-module-card
                >

                    <input
                        type="checkbox"
                        name="modules[]"
                        value="{{ $module->id }}"
                        class="module-checkbox"
                        {{ $enabled ? 'checked' : '' }}
                    >


                    <div class="module-card-icon">

                        {{ $module->icon ?? '⚙️' }}

                    </div>


                    <div class="module-card-content">

                        <strong>
                            {{ $module->name }}
                        </strong>

                        <p>
                            {{ $module->description }}
                        </p>

                    </div>


                    <div class="module-card-status">

                        <span class="module-status-dot"></span>

                        <span
                            class="module-status-text"
                        >
                            {{ $enabled ? 'Activé' : 'Désactivé' }}
                        </span>

                    </div>


                    <div class="module-switch">

                        <span></span>

                    </div>

                </label>

            @empty

                <div class="modules-empty">

                    <div>
                        ⚙️
                    </div>

                    <strong>
                        Aucun module disponible
                    </strong>

                    <p>
                        Aucun module actif n'est actuellement disponible
                        dans le catalogue.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- ========================================================
             FOOTER
        ======================================================== --}}

        <div class="modules-form-footer">

            <div class="modules-footer-info">

                <span>
                    Entreprise
                </span>

                <strong>
                    {{ $company->name }}
                </strong>

            </div>


            <div class="modules-footer-actions">

                <a
                    href="{{ route(
                        'superadmin.companies.index'
                    ) }}"
                    class="modules-btn modules-btn-secondary"
                >
                    Annuler
                </a>


                <button
                    type="submit"
                    class="modules-btn modules-btn-primary"
                >
                    <span>
                        ✓
                    </span>

                    Enregistrer les modules
                </button>

            </div>

        </div>

    </form>

</div>


{{-- ================================================================
     JAVASCRIPT
================================================================ --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById(
        'company-modules-form'
    );

    const cards = document.querySelectorAll(
        '[data-module-card]'
    );

    const activateAll = document.getElementById(
        'activate-all'
    );

    const deactivateAll = document.getElementById(
        'deactivate-all'
    );


    /*
    |--------------------------------------------------------------------------
    | ACTUALISATION DES CARTES
    |--------------------------------------------------------------------------
    */

    function refreshCards() {

        cards.forEach(function (card) {

            const checkbox = card.querySelector(
                '.module-checkbox'
            );

            const statusText = card.querySelector(
                '.module-status-text'
            );


            if (checkbox.checked) {

                card.classList.add(
                    'is-active'
                );

                statusText.textContent =
                    'Activé';

            } else {

                card.classList.remove(
                    'is-active'
                );

                statusText.textContent =
                    'Désactivé';
            }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | CHANGEMENT D'UN MODULE
    |--------------------------------------------------------------------------
    */

    cards.forEach(function (card) {

        const checkbox = card.querySelector(
            '.module-checkbox'
        );

        checkbox.addEventListener(
            'change',
            refreshCards
        );

    });


    /*
    |--------------------------------------------------------------------------
    | TOUT ACTIVER
    |--------------------------------------------------------------------------
    */

    activateAll.addEventListener(
        'click',
        function () {

            cards.forEach(function (card) {

                const checkbox =
                    card.querySelector(
                        '.module-checkbox'
                    );

                checkbox.checked = true;

            });

            refreshCards();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TOUT DÉSACTIVER
    |--------------------------------------------------------------------------
    */

    deactivateAll.addEventListener(
        'click',
        function () {

            cards.forEach(function (card) {

                const checkbox =
                    card.querySelector(
                        '.module-checkbox'
                    );

                checkbox.checked = false;

            });

            refreshCards();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ANTI DOUBLE SOUMISSION
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        function () {

            const button =
                form.querySelector(
                    'button[type="submit"]'
                );

            if (!button) {
                return;
            }

            button.disabled = true;

            button.innerHTML =
                '<span>✓</span> Enregistrement...';

        }
    );

});

</script>


{{-- ================================================================
     CSS
================================================================ --}}

<style>

.company-modules-page {

    --modules-primary: #315bea;
    --modules-primary-dark: #2448c9;

    --modules-success: #16a34a;
    --modules-danger: #dc2626;

    --modules-text: #172033;
    --modules-muted: #64748b;

    --modules-border: #e2e8f0;
    --modules-bg: #f8fafc;

    --modules-radius: 16px;

    max-width: 1400px;

    margin: 0 auto;

    padding: 30px;

}


/* ================================================================
   HEADER
================================================================ */

.modules-header {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 30px;

    margin-bottom: 28px;

}


.modules-header-content {

    min-width: 0;

}


.modules-back {

    display: inline-flex;

    align-items: center;

    margin-bottom: 18px;

    color: var(--modules-primary);

    font-size: 13px;

    font-weight: 700;

    text-decoration: none;

}


.modules-back:hover {

    color: var(--modules-primary-dark);

}


.modules-kicker {

    display: block;

    margin-bottom: 7px;

    color: var(--modules-primary);

    font-size: 11px;

    font-weight: 800;

    letter-spacing: .12em;

}


.modules-header h1 {

    margin: 0;

    color: var(--modules-text);

    font-size: 30px;

    line-height: 1.2;

}


.modules-header p {

    max-width: 650px;

    margin: 10px 0 0;

    color: var(--modules-muted);

    font-size: 14px;

    line-height: 1.7;

}


/* ================================================================
   COMPANY IDENTITY
================================================================ */

.company-identity {

    display: flex;

    align-items: center;

    gap: 13px;

    min-width: 280px;

    padding: 15px 18px;

    background: #fff;

    border: 1px solid var(--modules-border);

    border-radius: 14px;

    box-shadow: 0 8px 25px rgba(15, 23, 42, .05);

}


.company-identity-icon {

    display: grid;

    place-items: center;

    width: 46px;

    height: 46px;

    flex: 0 0 46px;

    background: #eff6ff;

    border-radius: 12px;

    font-size: 21px;

}


.company-identity div:last-child {

    display: flex;

    flex-direction: column;

    gap: 3px;

    min-width: 0;

}


.company-identity strong {

    overflow: hidden;

    color: var(--modules-text);

    font-size: 14px;

    text-overflow: ellipsis;

    white-space: nowrap;

}


.company-identity span {

    color: var(--modules-muted);

    font-size: 12px;

}


/* ================================================================
   ALERTS
================================================================ */

.modules-alert {

    display: flex;

    align-items: flex-start;

    gap: 12px;

    margin-bottom: 22px;

    padding: 15px 17px;

    border-radius: 13px;

}


.modules-alert-icon {

    display: grid;

    place-items: center;

    width: 28px;

    height: 28px;

    flex: 0 0 28px;

    border-radius: 50%;

    font-weight: 800;

}


.modules-alert strong {

    display: block;

    margin-bottom: 3px;

    font-size: 13px;

}


.modules-alert p {

    margin: 0;

    font-size: 13px;

}


.modules-alert ul {

    margin: 5px 0 0;

    padding-left: 18px;

    font-size: 13px;

}


.modules-alert-success {

    background: #f0fdf4;

    border: 1px solid #bbf7d0;

    color: #166534;

}


.modules-alert-success .modules-alert-icon {

    background: #dcfce7;

}


.modules-alert-danger {

    background: #fef2f2;

    border: 1px solid #fecaca;

    color: #991b1b;

}


.modules-alert-danger .modules-alert-icon {

    background: #fee2e2;

}


/* ================================================================
   TOOLBAR
================================================================ */

.modules-toolbar {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 16px;

    padding: 16px 18px;

    background: #fff;

    border: 1px solid var(--modules-border);

    border-radius: 14px;

}


.modules-toolbar > div:first-child {

    display: flex;

    flex-direction: column;

    gap: 3px;

}


.modules-toolbar-label {

    color: var(--modules-muted);

    font-size: 10px;

    font-weight: 800;

    letter-spacing: .1em;

}


.modules-toolbar strong {

    color: var(--modules-text);

    font-size: 14px;

}


.modules-toolbar-actions {

    display: flex;

    gap: 8px;

}


.modules-tool-btn {

    padding: 9px 13px;

    color: #334155;

    background: #fff;

    border: 1px solid var(--modules-border);

    border-radius: 9px;

    font: inherit;

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

}


.modules-tool-btn:hover {

    color: var(--modules-primary);

    border-color: #bfdbfe;

    background: #eff6ff;

}


/* ================================================================
   MODULE GRID
================================================================ */

.modules-grid {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 15px;

}


.module-card {

    position: relative;

    display: grid;

    grid-template-columns: auto minmax(0, 1fr) auto;

    grid-template-rows: auto auto;

    align-items: center;

    column-gap: 13px;

    row-gap: 7px;

    min-height: 130px;

    padding: 18px;

    background: #fff;

    border: 1px solid var(--modules-border);

    border-radius: var(--modules-radius);

    cursor: pointer;

    transition:
        border-color .2s ease,
        box-shadow .2s ease,
        transform .2s ease;

}


.module-card:hover {

    border-color: #bfdbfe;

    box-shadow: 0 10px 28px rgba(15, 23, 42, .07);

    transform: translateY(-1px);

}


.module-card.is-active {

    background: #f8fbff;

    border-color: #93c5fd;

    box-shadow:
        0 0 0 1px rgba(49, 91, 234, .04),
        0 10px 28px rgba(49, 91, 234, .06);

}


.module-checkbox {

    position: absolute;

    width: 1px;

    height: 1px;

    opacity: 0;

}


.module-card-icon {

    display: grid;

    place-items: center;

    width: 50px;

    height: 50px;

    grid-row: 1 / span 2;

    background: #f1f5f9;

    border-radius: 13px;

    font-size: 23px;

}


.module-card.is-active .module-card-icon {

    background: #dbeafe;

}


.module-card-content {

    min-width: 0;

}


.module-card-content strong {

    display: block;

    margin-bottom: 5px;

    color: var(--modules-text);

    font-size: 14px;

}


.module-card-content p {

    margin: 0;

    color: var(--modules-muted);

    font-size: 11px;

    line-height: 1.55;

}


.module-card-status {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    grid-column: 2;

    color: #94a3b8;

    font-size: 10px;

    font-weight: 700;

}


.module-card.is-active .module-card-status {

    color: var(--modules-success);

}


.module-status-dot {

    width: 6px;

    height: 6px;

    border-radius: 50%;

    background: #cbd5e1;

}


.module-card.is-active .module-status-dot {

    background: var(--modules-success);

}


.module-switch {

    position: absolute;

    top: 18px;

    right: 18px;

    width: 38px;

    height: 22px;

    padding: 3px;

    background: #cbd5e1;

    border-radius: 999px;

    transition: background .2s ease;

}


.module-switch span {

    display: block;

    width: 16px;

    height: 16px;

    background: #fff;

    border-radius: 50%;

    box-shadow: 0 1px 4px rgba(15, 23, 42, .2);

    transition: transform .2s ease;

}


.module-card.is-active .module-switch {

    background: var(--modules-primary);

}


.module-card.is-active .module-switch span {

    transform: translateX(16px);

}


/* ================================================================
   EMPTY
================================================================ */

.modules-empty {

    grid-column: 1 / -1;

    padding: 60px 20px;

    text-align: center;

    background: #fff;

    border: 1px solid var(--modules-border);

    border-radius: var(--modules-radius);

}


.modules-empty > div {

    margin-bottom: 12px;

    font-size: 35px;

}


.modules-empty strong {

    display: block;

    color: var(--modules-text);

    font-size: 15px;

}


.modules-empty p {

    margin: 7px 0 0;

    color: var(--modules-muted);

    font-size: 13px;

}


/* ================================================================
   FOOTER
================================================================ */

.modules-form-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-top: 22px;

    padding: 18px;

    background: #fff;

    border: 1px solid var(--modules-border);

    border-radius: 14px;

}


.modules-footer-info {

    display: flex;

    flex-direction: column;

    gap: 3px;

}


.modules-footer-info span {

    color: var(--modules-muted);

    font-size: 10px;

    font-weight: 700;

    text-transform: uppercase;

}


.modules-footer-info strong {

    color: var(--modules-text);

    font-size: 13px;

}


.modules-footer-actions {

    display: flex;

    align-items: center;

    gap: 9px;

}


.modules-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    min-height: 42px;

    padding: 0 17px;

    border-radius: 9px;

    font-size: 12px;

    font-weight: 800;

    text-decoration: none;

    cursor: pointer;

}


.modules-btn-secondary {

    color: #475569;

    background: #fff;

    border: 1px solid var(--modules-border);

}


.modules-btn-secondary:hover {

    background: #f8fafc;

}


.modules-btn-primary {

    color: #fff;

    background: var(--modules-primary);

    border: 1px solid var(--modules-primary);

}


.modules-btn-primary:hover {

    background: var(--modules-primary-dark);

}


.modules-btn-primary:disabled {

    opacity: .65;

    cursor: wait;

}


/* ================================================================
   RESPONSIVE
================================================================ */

@media (max-width: 1100px) {

    .modules-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }

}


@media (max-width: 850px) {

    .company-modules-page {

        padding: 22px;

    }


    .modules-header {

        align-items: stretch;

        flex-direction: column;

    }


    .company-identity {

        min-width: 0;

    }

}


@media (max-width: 650px) {

    .company-modules-page {

        padding: 16px;

    }


    .modules-header h1 {

        font-size: 25px;

    }


    .modules-toolbar {

        align-items: stretch;

        flex-direction: column;

    }


    .modules-toolbar-actions {

        width: 100%;

    }


    .modules-tool-btn {

        flex: 1;

    }


    .modules-grid {

        grid-template-columns: 1fr;

    }


    .modules-form-footer {

        align-items: stretch;

        flex-direction: column;

    }


    .modules-footer-actions {

        width: 100%;

        flex-direction: column-reverse;

    }


    .modules-btn {

        width: 100%;

    }

}

</style>

@endsection