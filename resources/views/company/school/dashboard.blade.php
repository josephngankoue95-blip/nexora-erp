@extends('layouts.school')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-description', 'Vue générale de votre établissement')

@section('content')
<style>
    .school-dashboard {
        --sd-ink: #17243b;
        --sd-muted: #64748b;
        --sd-line: #e2e8f0;
        --sd-primary: #2563eb;
        --sd-surface: #ffffff;
        width: 100%;
        max-width: 1320px;
        margin: 0 auto;
        padding: 12px 4px 36px;
        color: var(--sd-ink);
    }
    .school-dashboard *, .school-dashboard *::before, .school-dashboard *::after { box-sizing: border-box; }
    .school-dashboard .sd-heading { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; flex-wrap: wrap; margin-bottom: 24px; }
    .school-dashboard .sd-eyebrow { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 750; text-transform: uppercase; letter-spacing: .09em; color: var(--sd-primary); margin-bottom: 9px; }
    .school-dashboard .sd-eyebrow::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .school-dashboard .sd-title { font-size: clamp(24px, 3vw, 32px); line-height: 1.2; letter-spacing: -.03em; margin: 0 0 9px; font-weight: 800; }
    .school-dashboard .sd-subtitle { margin: 0; color: var(--sd-muted); font-size: 14px; line-height: 1.6; }
    .school-dashboard .sd-year { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: var(--sd-surface); border: 1px solid var(--sd-line); border-radius: 14px; box-shadow: 0 5px 18px rgba(15, 23, 42, .035); }
    .school-dashboard .sd-year-icon { display: grid; place-items: center; flex: 0 0 40px; height: 40px; border-radius: 11px; background: #eff6ff; color: #2563eb; font-size: 21px; }
    .school-dashboard .sd-year-label { display: block; color: var(--sd-muted); font-size: 12px; margin-bottom: 4px; }
    .school-dashboard .sd-year-value { display: block; font-size: 14px; font-weight: 800; }
    .school-dashboard .sd-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .school-dashboard .sd-stat { position: relative; overflow: hidden; min-width: 0; padding: 22px; background: var(--sd-surface); border: 1px solid var(--sd-line); border-radius: 18px; box-shadow: 0 6px 20px rgba(15, 23, 42, .035); }
    .school-dashboard .sd-stat-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .school-dashboard .sd-stat-label { display: block; color: var(--sd-muted); font-size: 13px; font-weight: 600; line-height: 1.5; }
    .school-dashboard .sd-stat-icon { flex: 0 0 42px; height: 42px; display: grid; place-items: center; border-radius: 12px; background: #eff6ff; color: #2563eb; font-size: 21px; }
    .school-dashboard .sd-stat:nth-child(2) .sd-stat-icon { background: #f0fdf4; color: #16a34a; }
    .school-dashboard .sd-stat:nth-child(3) .sd-stat-icon { background: #fff7ed; color: #ea580c; }
    .school-dashboard .sd-stat:nth-child(4) .sd-stat-icon { background: #f5f3ff; color: #7c3aed; }
    .school-dashboard .sd-stat-value { display: block; font-size: clamp(27px, 3vw, 36px); font-weight: 800; letter-spacing: -.04em; margin: 10px 0 5px; font-variant-numeric: tabular-nums; }
    .school-dashboard .sd-stat-caption { margin: 0; color: #94a3b8; font-size: 12px; }
    .school-dashboard .sd-content { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(280px, 1fr); gap: 18px; margin-top: 20px; align-items: start; }
    .school-dashboard .sd-panel { min-width: 0; background: var(--sd-surface); border: 1px solid var(--sd-line); border-radius: 18px; padding: 24px; box-shadow: 0 6px 20px rgba(15, 23, 42, .035); }
    .school-dashboard .sd-panel h2 { font-size: 18px; font-weight: 800; margin: 0 0 9px; }
    .school-dashboard .sd-panel-desc { color: var(--sd-muted); line-height: 1.65; font-size: 13px; margin: 0 0 18px; }
    .school-dashboard .sd-steps { display: grid; gap: 11px; }
    .school-dashboard .sd-step { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid #e8edf5; border-radius: 12px; background: #fbfcff; }
    .school-dashboard .sd-step-num { display: grid; place-items: center; flex: 0 0 31px; height: 31px; border-radius: 9px; background: #eff6ff; color: #1d4ed8; font-weight: 800; font-size: 12px; }
    .school-dashboard .sd-step-info { flex: 1; min-width: 0; }
    .school-dashboard .sd-step-title { display: block; font-weight: 750; font-size: 13px; }
    .school-dashboard .sd-step-desc { display: block; font-size: 12px; color: var(--sd-muted); margin-top: 3px; line-height: 1.5; }
    .school-dashboard .sd-step-link { flex-shrink: 0; color: #1d4ed8; text-decoration: none; font-size: 12px; font-weight: 750; }
    .school-dashboard .sd-step-link:hover { text-decoration: underline; }
    .school-dashboard .sd-notice { padding: 15px 16px; border-radius: 12px; border: 1px solid #fde68a; background: #fffbeb; color: #92400e; font-size: 13px; line-height: 1.65; }
    .school-dashboard .sd-notice.is-success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
    .school-dashboard .sd-notice strong { display: block; margin-bottom: 3px; }
    .school-dashboard .sd-tip { margin-top: 16px; color: var(--sd-muted); font-size: 12px; line-height: 1.6; }
    @media (max-width: 1100px) { .school-dashboard .sd-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .school-dashboard .sd-content { grid-template-columns: 1fr; } }
    @media (max-width: 600px) { .school-dashboard { padding: 8px 0 24px; } .school-dashboard .sd-heading { margin-bottom: 17px; } .school-dashboard .sd-year { width: 100%; } .school-dashboard .sd-stats { gap: 11px; } .school-dashboard .sd-stat { padding: 15px; border-radius: 14px; } .school-dashboard .sd-stat-icon { flex-basis: 36px; height: 36px; font-size: 18px; } .school-dashboard .sd-stat-value { font-size: 28px; } .school-dashboard .sd-panel { padding: 18px; } }
    @media (max-width: 380px) { .school-dashboard .sd-stats { grid-template-columns: 1fr; } }
</style>

@php
    $schoolName = $settings?->school_name ?: ($company->name ?? 'Établissement scolaire');
    $activeYearName = $year?->name ?: 'Non configurée';
    $statCards = [
        ['label' => 'Années scolaires', 'value' => $yearsCount ?? 0, 'icon' => '📅', 'caption' => 'Années enregistrées'],
        ['label' => 'Sections', 'value' => $sectionsCount ?? 0, 'icon' => '🌐', 'caption' => 'Francophone / Anglophone'],
        ['label' => 'Niveaux', 'value' => $levelsCount ?? 0, 'icon' => '📚', 'caption' => 'Niveaux configurés'],
        ['label' => 'Classes', 'value' => $classesCount ?? 0, 'icon' => '🏫', 'caption' => 'Pour l’année active'],
    ];
    $setupSteps = [
        ['label' => 'Années scolaires', 'description' => 'Créer une année et définir celle qui est active.', 'route' => 'company.school.years.index'],
        ['label' => 'Sections', 'description' => 'Configurer les sections francophone et anglophone.', 'route' => 'company.school.sections.index'],
        ['label' => 'Niveaux', 'description' => 'Organiser les niveaux selon chaque section.', 'route' => 'company.school.levels.index'],
        ['label' => 'Classes', 'description' => 'Créer les classes de l’année scolaire active.', 'route' => 'company.school.classes.index'],
    ];
@endphp

<div class="school-dashboard">
    <header class="sd-heading">
        <div>
            <span class="sd-eyebrow">Espace administration</span>
            <h1 class="sd-title">Gestion scolaire</h1>
            <p class="sd-subtitle">{{ $schoolName }} — Pilotage et configuration de l’établissement</p>
        </div>
        <div class="sd-year" aria-label="Année scolaire active">
            <span class="sd-year-icon" aria-hidden="true">📅</span>
            <div>
                <span class="sd-year-label">Année scolaire active</span>
                <strong class="sd-year-value">{{ $activeYearName }}</strong>
            </div>
        </div>
    </header>

    <section class="sd-stats" aria-label="Statistiques de configuration">
        @foreach ($statCards as $stat)
            <article class="sd-stat">
                <div class="sd-stat-top">
                    <span class="sd-stat-label">{{ $stat['label'] }}</span>
                    <span class="sd-stat-icon" aria-hidden="true">{{ $stat['icon'] }}</span>
                </div>
                <strong class="sd-stat-value">{{ number_format((int) $stat['value'], 0, ',', ' ') }}</strong>
                <p class="sd-stat-caption">{{ $stat['caption'] }}</p>
            </article>
        @endforeach
    </section>

    <div class="sd-content">
        <section class="sd-panel">
            <h2>Configuration de l’établissement</h2>
            <p class="sd-panel-desc">Préparez la structure scolaire avant de commencer les inscriptions, les affectations des enseignants et la saisie des notes.</p>
            <div class="sd-steps">
                @foreach ($setupSteps as $index => $step)
                    <div class="sd-step">
                        <span class="sd-step-num">{{ $index + 1 }}</span>
                        <div class="sd-step-info">
                            <span class="sd-step-title">{{ $step['label'] }}</span>
                            <span class="sd-step-desc">{{ $step['description'] }}</span>
                        </div>
                        @if (\Illuminate\Support\Facades\Route::has($step['route']))
                            <a class="sd-step-link" href="{{ route($step['route']) }}">Ouvrir →</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="sd-panel">
            <h2>État de la configuration</h2>
            <p class="sd-panel-desc">Vérifiez les éléments indispensables au fonctionnement de votre établissement.</p>
            @if ($year)
                <div class="sd-notice is-success" role="status">
                    <strong>✓ Année scolaire active</strong>
                    L’année {{ $activeYearName }} est sélectionnée. Vous pouvez poursuivre la configuration des classes.
                </div>
            @else
                <div class="sd-notice" role="status">
                    <strong>⚠ Aucune année scolaire active</strong>
                    Créez et activez une année scolaire avant d’inscrire des élèves ou d’organiser les classes.
                </div>
            @endif
            <p class="sd-tip">Les modules Élèves, Enseignants, Scolarité, Notes et Bulletins seront intégrés progressivement à cet espace.</p>
        </aside>
    </div>
</div>
@endsection
