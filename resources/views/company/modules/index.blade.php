
@extends('layouts.module-launcher')

@section('title', 'Applications')

@section('content')

@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $companyName = $company->name ?? 'Mon entreprise';
    $userName = $user->name ?? 'Utilisateur';

    $initials = collect(explode(' ', trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');

    $palette = [
        ['bg' => '#eeebff', 'color' => '#6554d9'],
        ['bg' => '#dcf8e9', 'color' => '#16845b'],
        ['bg' => '#fff0dc', 'color' => '#c47721'],
        ['bg' => '#ffe5eb', 'color' => '#d54c70'],
        ['bg' => '#e1efff', 'color' => '#3479cf'],
        ['bg' => '#f0e5ff', 'color' => '#8a52cf'],
        ['bg' => '#dff7f5', 'color' => '#178b83'],
        ['bg' => '#fff4d5', 'color' => '#b08018'],
        ['bg' => '#e9edf4', 'color' => '#536174'],
        ['bg' => '#ffe8df', 'color' => '#cf6848'],
        ['bg' => '#e4edff', 'color' => '#4267bd'],
        ['bg' => '#f0e7ff', 'color' => '#7955bd'],
    ];
@endphp

<div class="nexora-launcher">

    {{-- BARRE SUPERIEURE --}}
    <header class="nexora-topbar">

        <a href="{{ url('/') }}" class="nexora-brand">
            <span class="nexora-brand-mark">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3 21 8 12 13 3 8 12 3Z"
                          stroke="currentColor" stroke-width="1.8"
                          stroke-linejoin="round"/>
                    <path d="m3 12 9 5 9-5M3 16l9 5 9-5"
                          stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>

            <span class="nexora-brand-text">
                <strong>NEXORA</strong>
                <small>ESPACE DE TRAVAIL</small>
            </span>
        </a>

        <div class="nexora-topbar-right">

            <div class="nexora-company">
                <span class="nexora-company-dot"></span>
                <span>{{ $companyName }}</span>
            </div>

            <div class="nexora-profile" title="{{ $userName }}">
                <span>{{ $initials ?: 'U' }}</span>
            </div>

        </div>

    </header>

    {{-- CONTENU PRINCIPAL --}}
    <main class="nexora-main">

        <section class="nexora-heading">

            <div class="nexora-heading-copy">
                <!-- <span class="nexora-eyebrow">
                    <span class="nexora-eyebrow-dot"></span>
                    VOTRE ENVIRONNEMENT
                </span> -->

                <h1>Vos applications</h1>

                <!-- <p>
                    Tout votre environnement de travail,
                    au même endroit.
                </p> -->
            </div>

            <div class="nexora-app-count">
                <span class="nexora-app-count-number">
                    {{ count($availableWorkspaces ?? []) }}
                </span>
                <span class="nexora-app-count-label">
                    applications
                </span>
            </div>

        </section>

        {{-- RECHERCHE --}}
        <section class="nexora-search-section">

            <label class="nexora-search" for="nexoraAppSearch">

                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="6.8"
                            stroke="currentColor" stroke-width="1.8"/>
                    <path d="m16 16 4.2 4.2"
                          stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round"/>
                </svg>

                <input
                    type="search"
                    id="nexoraAppSearch"
                    placeholder="Rechercher une application..."
                    autocomplete="off"
                    aria-label="Rechercher une application"
                >

                <kbd class="nexora-search-shortcut">
                    <span>Ctrl</span><span>K</span>
                </kbd>

            </label>

            <!-- <span class="nexora-search-hint">
                Retrouvez rapidement votre application
            </span> -->

        </section>

        {{-- GRILLE DES APPLICATIONS --}}
        <section class="nexora-app-section">

            <div class="nexora-section-header">
                <h2>Applications disponibles</h2>

                <span class="nexora-section-caption">
                    Sélectionnez un espace pour continuer
                </span>
            </div>

            <div class="nexora-app-grid" id="nexoraAppGrid">

                @forelse(array_values($availableWorkspaces ?? []) as $index => $workspace)

                    @php
                        $appName = $workspace['name']
                            ?? $workspace['label']
                            ?? $workspace['title']
                            ?? 'Application';

                        $appDescription = $workspace['description']
                            ?? $workspace['subtitle']
                            ?? '';

                        $appIcon = $workspace['icon'] ?? '📦';
                        $appRoute = $workspace['route'] ?? null;

                        $hasRoute = $appRoute
                            && Route::has($appRoute);

                        $appUrl = $hasRoute
                            ? route($appRoute)
                            : '#';

                        $customColor = $workspace['color'] ?? null;

                        $appColor = $palette[$index % count($palette)];

                        if ($customColor) {
                            $appColor['color'] = $customColor;
                        }

                        $isImage = is_string($appIcon)
                            && (
                                Str::startsWith($appIcon, 'http://')
                                || Str::startsWith($appIcon, 'https://')
                                || Str::startsWith($appIcon, '/')
                                || Str::startsWith($appIcon, 'storage/')
                            );

                        $searchText = Str::lower(
                            $appName . ' ' . $appDescription
                        );
                    @endphp

                    <a
                        href="{{ $appUrl }}"
                        class="nexora-app-card {{ $hasRoute ? '' : 'is-disabled' }}"
                        data-app-card
                        data-search="{{ $searchText }}"
                        @if(!$hasRoute)
                            aria-disabled="true"
                            title="Cette application n'est pas encore disponible"
                        @endif
                    >

                        <div class="nexora-app-icon-wrap">

                            <div
                                class="nexora-app-icon"
                                style="
                                    --app-icon-bg: {{ $customColor ? $appColor['color'] . '18' : $appColor['bg'] }};
                                    --app-icon-color: {{ $appColor['color'] }};
                                "
                            >

                                @if($isImage)
                                    <img
                                        src="{{ Str::startsWith($appIcon, ['http://', 'https://', '/']) ? $appIcon : asset($appIcon) }}"
                                        alt=""
                                        loading="lazy"
                                    >
                                @else
                                    <span class="nexora-app-emoji">
                                        {{ $appIcon }}
                                    </span>
                                @endif

                            </div>

                            @if(!$hasRoute)
                                <span class="nexora-coming-dot"
                                      title="Bientôt disponible"></span>
                            @endif

                        </div>

                        <div class="nexora-app-info">
                            <h3>{{ $appName }}</h3>

                            @if($appDescription)
                                <p>{{ $appDescription }}</p>
                            @else
                                <p class="nexora-app-default-description">
                                    Accéder à votre espace
                                </p>
                            @endif
                        </div>

                        <span class="nexora-app-arrow" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5 12h14m-6-6 6 6-6 6"
                                      stroke="currentColor"
                                      stroke-width="1.8"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </span>

                    </a>

                @empty

                    <div class="nexora-empty">
                        <div class="nexora-empty-icon">📂</div>
                        <h3>Aucune application disponible</h3>
                        <p>
                            Les applications activées pour votre entreprise
                            apparaîtront ici.
                        </p>
                    </div>

                @endforelse

            </div>

            {{-- AUCUN RESULTAT DE RECHERCHE --}}
            <div class="nexora-no-results" id="nexoraNoResults" hidden>
                <div class="nexora-no-results-icon">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="10.8" cy="10.8" r="6.8"
                                stroke="currentColor" stroke-width="1.7"/>
                        <path d="m16 16 4.2 4.2"
                              stroke="currentColor" stroke-width="1.7"
                              stroke-linecap="round"/>
                    </svg>
                </div>

                <h3>Aucune application trouvée</h3>
                <p>Essayez un autre nom ou un autre mot-clé.</p>

                <button type="button" id="nexoraResetSearch">
                    Effacer la recherche
                </button>
            </div>

        </section>

        {{-- PIED DE PAGE --}}
        <footer class="nexora-footer">
            <span>
                <span class="nexora-footer-mark">N</span>
                NEXORA
            </span>

            <span class="nexora-footer-separator"></span>

            <span>Votre entreprise, un seul espace.</span>

            <span class="nexora-footer-version">
                Espace professionnel
            </span>
        </footer>

    </main>

</div>

@endsection

@push('styles')
<style>
/* ==========================================================
   NEXORA — APPLICATION LAUNCHER
   CSS natif / Responsive / Interface Odoo-inspired
========================================================== */

.nexora-launcher {
    --nx-primary: #635bdb;
    --nx-primary-dark: #5148c5;
    --nx-ink: #20283b;
    --nx-muted: #7e8799;
    --nx-border: #e8ebf2;
    --nx-surface: #ffffff;
    --nx-background: #f5f6fa;

    min-height: 100vh;
    background: var(--nx-background);
    color: var(--nx-ink);
    font-family: Inter, -apple-system, BlinkMacSystemFont,
        "Segoe UI", sans-serif;
    font-size: 14px;
    line-height: 1.5;
}

.nexora-launcher *,
.nexora-launcher *::before,
.nexora-launcher *::after {
    box-sizing: border-box;
}

.nexora-launcher button,
.nexora-launcher input {
    font: inherit;
}

.nexora-launcher a {
    color: inherit;
    text-decoration: none;
}

/* TOPBAR */

.nexora-topbar {
    min-height: 76px;
    padding: 0 5.5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    background: #fff;
    border-bottom: 1px solid #eaecf2;
}

.nexora-brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.nexora-brand-mark {
    width: 43px;
    height: 43px;
    border-radius: 13px;
    display: grid;
    place-items: center;
    background: linear-gradient(145deg, #756bea, #554bc9);
    color: #fff;
    box-shadow: 0 5px 13px rgba(99, 91, 219, .19);
}

.nexora-brand-mark svg {
    width: 26px;
    height: 26px;
}

.nexora-brand-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.nexora-brand-text strong {
    font-size: 17px;
    line-height: 1.15;
    font-weight: 800;
    letter-spacing: 1.1px;
    color: #252b40;
}

.nexora-brand-text small {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1.25px;
    color: #9299aa;
}

.nexora-topbar-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 19px;
    min-width: 0;
}

.nexora-company {
    display: flex;
    align-items: center;
    gap: 9px;
    max-width: 300px;
    color: #626c80;
    font-size: 12px;
    font-weight: 600;
    min-width: 0;
}

.nexora-company > span:last-child {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.nexora-company-dot {
    width: 7px;
    height: 7px;
    background: #39b982;
    border-radius: 50%;
    box-shadow: 0 0 0 4px #e5f8ef;
    flex-shrink: 0;
}

.nexora-profile {
    width: 39px;
    height: 39px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    border: 1px solid #e2dcff;
    border-radius: 50%;
    background: #efedff;
    color: #5d52c9;
    font-size: 12px;
    font-weight: 800;
}

/* MAIN */

.nexora-main {
    width: min(1180px, 89%);
    margin: 0 auto;
    padding: 55px 0 24px;
}

.nexora-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 30px;
}

.nexora-heading-copy {
    min-width: 0;
}

.nexora-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 13px;
    color: #8b91a2;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.nexora-eyebrow-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--nx-primary);
}

.nexora-heading h1 {
    margin: 0;
    color: #222a3d;
    font-size: clamp(29px, 3vw, 36px);
    font-weight: 750;
    letter-spacing: -1.2px;
    line-height: 1.2;
}

.nexora-heading p {
    margin: 11px 0 0;
    color: #7b8497;
    font-size: 14px;
    line-height: 1.7;
}

.nexora-app-count {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 13px;
    border: 1px solid #e7e9f0;
    border-radius: 10px;
    background: rgba(255, 255, 255, .8);
    flex-shrink: 0;
}

.nexora-app-count-number {
    color: var(--nx-primary);
    font-size: 15px;
    font-weight: 800;
}

.nexora-app-count-label {
    color: #7d8596;
    font-size: 11px;
    font-weight: 600;
}

/* SEARCH */

.nexora-search-section {
    margin-bottom: 43px;
}

.nexora-search {
    width: min(100%, 590px);
    min-height: 54px;
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 0 15px;
    background: #fff;
    border: 1px solid #e6e9f1;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(29, 39, 70, .025);
    transition: border-color .2s ease, box-shadow .2s ease;
}

.nexora-search:focus-within {
    border-color: #a39cf0;
    box-shadow: 0 0 0 4px rgba(99, 91, 219, .09);
}

.nexora-search > svg {
    width: 21px;
    height: 21px;
    flex-shrink: 0;
    color: #9199aa;
}

.nexora-search input {
    flex: 1;
    min-width: 0;
    width: 100%;
    border: 0;
    outline: 0;
    background: transparent;
    color: #283147;
    font-size: 13px;
}

.nexora-search input::placeholder {
    color: #a0a7b6;
}

.nexora-search input::-webkit-search-cancel-button {
    cursor: pointer;
}

.nexora-search-shortcut {
    display: flex;
    align-items: center;
    gap: 3px;
    padding: 4px 7px;
    border: 1px solid #e6e8ef;
    border-radius: 6px;
    background: #f8f9fc;
    color: #939aab;
    font-family: inherit;
    font-size: 10px;
    white-space: nowrap;
}

.nexora-search-hint {
    display: block;
    margin: 9px 0 0 3px;
    color: #a0a6b4;
    font-size: 11px;
}

/* SECTION */

.nexora-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px 18px;
    margin-bottom: 18px;
}

.nexora-section-header h2 {
    margin: 0;
    color: #333b4e;
    font-size: 14px;
    font-weight: 750;
}

.nexora-section-caption {
    color: #9aa1b0;
    font-size: 11px;
}

/* APPLICATION GRID */

.nexora-app-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
}

.nexora-app-card {
    position: relative;
    min-width: 0;
    min-height: 143px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 19px 18px 16px;
    overflow: hidden;
    border: 1px solid #e8eaf1;
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 2px 7px rgba(35, 43, 70, .018);
    transition:
        transform .2s ease,
        border-color .2s ease,
        box-shadow .2s ease,
        background .2s ease;
}

.nexora-app-card:hover {
    transform: translateY(-3px);
    border-color: #dcd9fa;
    background: #fff;
    box-shadow: 0 9px 24px rgba(40, 45, 90, .065);
}

.nexora-app-card:focus-visible {
    outline: 3px solid rgba(99, 91, 219, .32);
    outline-offset: 3px;
}

.nexora-app-icon-wrap {
    position: relative;
    margin-bottom: 15px;
}

.nexora-app-icon {
    width: 49px;
    height: 49px;
    display: grid;
    place-items: center;
    overflow: hidden;
    border-radius: 14px;
    background: var(--app-icon-bg, #eeebff);
    color: var(--app-icon-color, #635bdb);
    transition: transform .2s ease;
}

.nexora-app-card:hover .nexora-app-icon {
    transform: scale(1.055);
}

.nexora-app-icon img {
    width: 27px;
    height: 27px;
    object-fit: contain;
}

.nexora-app-emoji {
    font-size: 25px;
    line-height: 1;
}

.nexora-app-info {
    width: 100%;
    padding-right: 8px;
}

.nexora-app-info h3 {
    margin: 0;
    color: #30384b;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.nexora-app-info p {
    margin: 4px 0 0;
    color: #9299a9;
    font-size: 10.5px;
    line-height: 1.5;
}

.nexora-app-default-description {
    opacity: .85;
}

.nexora-app-arrow {
    position: absolute;
    top: 21px;
    right: 17px;
    width: 18px;
    height: 18px;
    display: grid;
    place-items: center;
    color: #c1c5d1;
    opacity: 0;
    transform: translateX(-4px);
    transition: opacity .2s ease, transform .2s ease;
}

.nexora-app-arrow svg {
    width: 17px;
    height: 17px;
}

.nexora-app-card:hover .nexora-app-arrow {
    opacity: 1;
    transform: translateX(0);
}

.nexora-app-card.is-disabled {
    background: #fafbfc;
    border-style: dashed;
    border-color: #e3e6ed;
}

.nexora-app-card.is-disabled .nexora-app-info h3 {
    color: #7f8798;
}

.nexora-app-card.is-disabled .nexora-app-icon {
    filter: saturate(.65);
}

.nexora-app-card.is-disabled:hover {
    transform: none;
    box-shadow: none;
    border-color: #d9dce5;
    cursor: default;
}

.nexora-coming-dot {
    position: absolute;
    right: -2px;
    bottom: -2px;
    width: 11px;
    height: 11px;
    border: 2px solid #fff;
    border-radius: 50%;
    background: #e7ad48;
}

/* EMPTY STATES */

.nexora-empty,
.nexora-no-results {
    grid-column: 1 / -1;
    padding: 52px 20px;
    text-align: center;
    border: 1px dashed #e0e4ed;
    border-radius: 14px;
    background: rgba(255, 255, 255, .7);
}

.nexora-empty-icon,
.nexora-no-results-icon {
    width: 53px;
    height: 53px;
    margin: 0 auto 14px;
    display: grid;
    place-items: center;
    border-radius: 16px;
    background: #eeebff;
    color: var(--nx-primary);
    font-size: 24px;
}

.nexora-no-results-icon svg {
    width: 24px;
    height: 24px;
}

.nexora-empty h3,
.nexora-no-results h3 {
    margin: 0;
    color: #30384b;
    font-size: 15px;
}

.nexora-empty p,
.nexora-no-results p {
    margin: 7px 0 0;
    color: #858da0;
    font-size: 12px;
}

.nexora-no-results[hidden] {
    display: none;
}

.nexora-no-results button {
    margin-top: 17px;
    padding: 9px 14px;
    border: 0;
    border-radius: 8px;
    background: #eeebff;
    color: #5b50c7;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}

.nexora-no-results button:hover {
    background: #e3dfff;
}

/* FOOTER */

.nexora-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 57px;
    padding-top: 19px;
    border-top: 1px solid #e7eaf1;
    color: #9aa1af;
    font-size: 10px;
}

.nexora-footer > span:first-child {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #70798d;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .7px;
}

.nexora-footer-mark {
    width: 21px;
    height: 21px;
    display: grid;
    place-items: center;
    border-radius: 6px;
    background: #eae7ff;
    color: #6156d0;
    font-size: 11px;
}

.nexora-footer-separator {
    width: 1px;
    height: 14px;
    background: #dfe2e9;
}

.nexora-footer-version {
    margin-left: auto;
}

/* RESPONSIVE */

@media (min-width: 1450px) {
    .nexora-main {
        padding-top: 65px;
    }

    .nexora-app-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 17px;
    }

    .nexora-app-card {
        min-height: 150px;
        padding: 21px;
    }
}

@media (max-width: 1050px) {
    .nexora-main {
        width: 91%;
        padding-top: 43px;
    }

    .nexora-app-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 13px;
    }

    .nexora-topbar {
        padding: 0 4.5%;
    }
}

@media (max-width: 650px) {
    .nexora-topbar {
        min-height: 68px;
        padding: 0 5%;
    }

    .nexora-brand-mark {
        width: 38px;
        height: 38px;
        border-radius: 11px;
    }

    .nexora-brand-mark svg {
        width: 23px;
        height: 23px;
    }

    .nexora-brand-text strong {
        font-size: 15px;
    }

    .nexora-brand-text small {
        font-size: 8px;
        letter-spacing: .8px;
    }

    .nexora-topbar-right {
        gap: 10px;
    }

    .nexora-company {
        max-width: 125px;
        font-size: 10px;
    }

    .nexora-profile {
        width: 35px;
        height: 35px;
    }

    .nexora-main {
        width: 90%;
        padding-top: 35px;
    }

    .nexora-heading {
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .nexora-heading h1 {
        font-size: 29px;
        letter-spacing: -.8px;
    }

    .nexora-heading p {
        max-width: 280px;
        font-size: 12px;
    }

    .nexora-app-count {
        gap: 5px;
        padding: 7px 9px;
    }

    .nexora-app-count-number {
        font-size: 13px;
    }

    .nexora-app-count-label {
        font-size: 10px;
    }

    .nexora-search-section {
        margin-bottom: 33px;
    }

    .nexora-search {
        min-height: 49px;
        padding: 0 12px;
    }

    .nexora-search input {
        font-size: 12px;
    }

    .nexora-section-caption {
        display: none;
    }

    .nexora-app-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 11px;
    }

    .nexora-app-card {
        min-height: 137px;
        padding: 15px 13px;
        border-radius: 12px;
    }

    .nexora-app-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
    }

    .nexora-app-emoji {
        font-size: 23px;
    }

    .nexora-app-icon-wrap {
        margin-bottom: 12px;
    }

    .nexora-app-info h3 {
        font-size: 12px;
    }

    .nexora-app-info p {
        font-size: 10px;
    }

    .nexora-app-arrow {
        top: 14px;
        right: 11px;
    }

    .nexora-footer {
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 39px;
    }

    .nexora-footer-version {
        width: 100%;
        margin: 4px 0 0;
    }
}

@media (max-width: 360px) {
    .nexora-company {
        max-width: 82px;
    }

    .nexora-heading h1 {
        font-size: 25px;
    }

    .nexora-app-card {
        padding: 13px 10px;
    }

    .nexora-search-shortcut {
        display: none;
    }
}

@media (prefers-reduced-motion: reduce) {
    .nexora-launcher *,
    .nexora-launcher *::before,
    .nexora-launcher *::after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        transition-duration: .01ms !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    const searchInput = document.getElementById('nexoraAppSearch');
    const appGrid = document.getElementById('nexoraAppGrid');
    const noResults = document.getElementById('nexoraNoResults');
    const resetButton = document.getElementById('nexoraResetSearch');

    if (!searchInput || !appGrid) {
        return;
    }

    const cards = Array.from(
        appGrid.querySelectorAll('[data-app-card]')
    );

    function filterApps() {
        const query = searchInput.value
            .trim()
            .toLocaleLowerCase('fr');

        let visibleCount = 0;

        cards.forEach(function (card) {
            const content = (
                card.dataset.search || ''
            ).toLocaleLowerCase('fr');

            const matches = !query || content.includes(query);

            card.hidden = !matches;

            if (matches) {
                visibleCount++;
            }
        });

        if (noResults) {
            noResults.hidden = visibleCount !== 0;
        }
    }

    searchInput.addEventListener('input', filterApps);

    if (resetButton) {
        resetButton.addEventListener('click', function () {
            searchInput.value = '';
            filterApps();
            searchInput.focus();
        });
    }

    document.addEventListener('keydown', function (event) {
        const isShortcut = (event.ctrlKey || event.metaKey)
            && event.key.toLowerCase() === 'k';

        if (isShortcut) {
            event.preventDefault();
            searchInput.focus();
            searchInput.select();
        }

        if (
            event.key === 'Escape'
            && document.activeElement === searchInput
        ) {
            searchInput.value = '';
            filterApps();
            searchInput.blur();
        }
    });

    // Les modules indisponibles restent non cliquables.
    cards.forEach(function (card) {
        if (card.getAttribute('aria-disabled') === 'true') {
            card.addEventListener('click', function (event) {
                event.preventDefault();
            });
        }
    });

    filterApps();
})();
</script>
@endpush
