
@extends('layouts.school')

@php
    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION DES MODULES
    |--------------------------------------------------------------------------
    */

    $names = [
        'years'    => 'Années scolaires',
        'sections' => 'Sections',
        'levels'   => 'Niveaux',
        'classes'  => 'Classes',
    ];

    $singular = [
        'years'    => 'année scolaire',
        'sections' => 'section',
        'levels'   => 'niveau',
        'classes'  => 'classe',
    ];

    $label = $names[$kind] ?? 'Gestion scolaire';
    $base = 'company.school.' . $kind;

    $article = in_array($kind, ['years', 'sections', 'classes'], true)
        ? 'une'
        : 'un';

    $colspan = match ($kind) {
        'years', 'sections' => 5,
        'levels', 'classes' => 5,
        default => 5,
    };
@endphp

@section('title', $label)
@section('page-title', $label)
@section('page-description', 'Configuration et suivi de votre établissement')

@section('content')

<div class="foundation-page">

    {{-- ========================================================
         EN-TÊTE
    ======================================================== --}}

    <header class="foundation-heading">

        <div class="foundation-heading-text">

            <span class="foundation-eyebrow">
                ADMINISTRATION SCOLAIRE
            </span>

            <h2>{{ $label }}</h2>

            <p>
                Consultez et organisez les
                {{ mb_strtolower($label) }}
                de votre établissement.
            </p>

        </div>

        <a
            class="foundation-button foundation-primary"
            href="{{ route($base . '.create') }}"
        >
            <span aria-hidden="true">＋</span>
            Ajouter {{ $article }} {{ $singular[$kind] ?? 'élément' }}
        </a>

    </header>


    {{-- ========================================================
         RECHERCHE ET FILTRES
    ======================================================== --}}

    <section
        class="foundation-panel"
        aria-label="Recherche et filtres"
    >

        <div class="foundation-panel-title">

            <div>
                <h3>Rechercher et filtrer</h3>

                <p>
                    Retrouvez rapidement les informations souhaitées.
                </p>
            </div>

            <span class="foundation-count">
                {{ $records->total() }} résultat(s)
            </span>

        </div>

        <form
            method="GET"
            action="{{ route($base . '.index') }}"
        >

            <div class="foundation-filters">

                {{-- RECHERCHE --}}
                <label class="foundation-field">

                    <span>Recherche</span>

                    <input
                        name="q"
                        type="search"
                        value="{{ request('q') }}"
                        placeholder="{{ $kind === 'sections' ? 'Nom ou code' : 'Rechercher par nom' }}"
                    >

                </label>


                {{-- FILTRE SECTION --}}
                @if($kind === 'levels' || $kind === 'classes')

                    <label class="foundation-field">

                        <span>Section</span>

                        <select
                            name="section_id"
                            id="foundationSection"
                        >

                            <option value="">
                                Toutes les sections
                            </option>

                            @foreach(($sections ?? collect()) as $section)

                                <option
                                    value="{{ $section->id }}"
                                    @selected(
                                        (string) request('section_id') ===
                                        (string) $section->id
                                    )
                                >
                                    {{ $section->name }}
                                </option>

                            @endforeach

                        </select>

                    </label>

                @endif


                {{-- FILTRES CLASSES --}}
                @if($kind === 'classes')

                    <label class="foundation-field">

                        <span>Année scolaire</span>

                        <select
                            name="year_id"
                            id="foundationYear"
                        >

                            <option value="">
                                Toutes les années
                            </option>

                            @foreach(($years ?? collect()) as $year)

                                <option
                                    value="{{ $year->id }}"
                                    @selected(
                                        (string) request('year_id') ===
                                        (string) $year->id
                                    )
                                >
                                    {{ $year->name }}
                                </option>

                            @endforeach

                        </select>

                    </label>


                    <label class="foundation-field">

                        <span>Niveau</span>

                        <select
                            name="level_id"
                            id="foundationLevel"
                        >

                            <option value="">
                                Tous les niveaux
                            </option>

                            @foreach(($levels ?? collect()) as $level)

                                <option
                                    value="{{ $level->id }}"
                                    data-section="{{ $level->school_section_id }}"
                                    @selected(
                                        (string) request('level_id') ===
                                        (string) $level->id
                                    )
                                >
                                    {{ $level->name }}
                                    —
                                    {{ $level->section?->name ?? 'Section non définie' }}
                                </option>

                            @endforeach

                        </select>

                    </label>

                @endif


                {{-- TRI --}}
                <label class="foundation-field">

                    <span>Trier par</span>

                    <select name="sort">

                        <option
                            value="name"
                            @selected(request('sort', 'name') === 'name')
                        >
                            Nom (A–Z)
                        </option>

                        <option
                            value="recent"
                            @selected(request('sort') === 'recent')
                        >
                            Plus récents
                        </option>

                        @if($kind === 'levels')

                            <option
                                value="order"
                                @selected(request('sort') === 'order')
                            >
                                Ordre pédagogique
                            </option>

                        @endif

                    </select>

                </label>

            </div>


            <div class="foundation-filter-actions">

                <button
                    class="foundation-button foundation-primary"
                    type="submit"
                >
                    Appliquer les filtres
                </button>

                <a
                    class="foundation-button foundation-outline"
                    href="{{ route($base . '.index') }}"
                >
                    Réinitialiser
                </a>

            </div>

        </form>

    </section>


    {{-- ========================================================
         LISTE DES ENREGISTREMENTS
    ======================================================== --}}

    <section class="foundation-panel foundation-list-panel">

        <div class="foundation-panel-title">

            <div>

                <h3>
                    Liste des {{ mb_strtolower($label) }}
                </h3>

                <p>
                    {{ $records->total() }} enregistrement(s) au total
                </p>

            </div>

        </div>


        <div class="foundation-table-scroll">

            <table class="foundation-table">

                <thead>

                    <tr>

                        @if($kind === 'years')

                            <th>Année</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>

                        @elseif($kind === 'sections')

                            <th>Code</th>
                            <th>Section</th>
                            <th>Statut</th>

                        @elseif($kind === 'levels')

                            <th>Niveau</th>
                            <th>Section</th>
                            <th>Ordre</th>
                            <th>Examen</th>

                        @else

                            <th>Classe</th>
                            <th>Année</th>
                            <th>Niveau / Section</th>
                            <th>Capacité</th>

                        @endif

                        <th class="foundation-actions-col">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($records as $record)

                        <tr>

                            {{-- ANNÉES SCOLAIRES --}}
                            @if($kind === 'years')

                                <td>
                                    <strong>{{ $record->name }}</strong>
                                </td>

                                <td>
                                    {{ $record->starts_on?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td>
                                    {{ $record->ends_on?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td>

                                    @if($record->is_active)

                                        <span class="foundation-tag is-good">
                                            Active
                                        </span>

                                    @elseif($record->is_archived)

                                        <span class="foundation-tag">
                                            Archivée
                                        </span>

                                    @else

                                        <span class="foundation-tag">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                            {{-- SECTIONS --}}
                            @elseif($kind === 'sections')

                                <td>
                                    <span class="foundation-pill">
                                        {{ $record->code }}
                                    </span>
                                </td>

                                <td>
                                    <strong>{{ $record->name }}</strong>
                                </td>

                                <td>
                                    <span class="foundation-tag {{ $record->is_active ? 'is-good' : '' }}">
                                        {{ $record->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>


                            {{-- NIVEAUX --}}
                            @elseif($kind === 'levels')

                                <td>
                                    <strong>{{ $record->name }}</strong>
                                </td>

                                <td>
                                    {{ $record->section?->name ?? '—' }}
                                </td>

                                <td>
                                    <span class="foundation-pill">
                                        {{ $record->sort_order }}
                                    </span>
                                </td>

                                <td>
                                    <span class="foundation-tag {{ $record->is_exam_class ? 'is-good' : '' }}">
                                        {{ $record->is_exam_class ? 'Oui' : 'Non' }}
                                    </span>
                                </td>


                            {{-- CLASSES --}}
                            @else

                                <td>
                                    <strong>{{ $record->name }}</strong>
                                </td>

                                <td>
                                    {{ $record->year?->name ?? '—' }}
                                </td>

                                <td>

                                    {{ $record->level?->name ?? '—' }}

                                    <small class="foundation-muted">
                                        {{ $record->level?->section?->name ?? '—' }}
                                    </small>

                                </td>

                                <td>
                                    {{ $record->capacity ?? '—' }}
                                </td>

                            @endif


                            {{-- =================================================
                                 ACTIONS
                            ================================================= --}}

                            <td>

                                <div class="foundation-row-actions">

                                    {{-- CONFIGURATION DES MATIÈRES --}}
                                    @if($kind === 'classes')

                                        <a
                                            href="{{ route('company.school.classes.subjects.index', $record->id) }}"
                                            class="foundation-config-subjects"
                                            title="Configurer les matières de cette classe"
                                        >
                                            <span aria-hidden="true">📚</span>
                                            Configurer les matières
                                        </a>

                                    @endif


                                    {{-- MODIFIER --}}
                                    <a
                                        class="foundation-edit"
                                        href="{{ route($base . '.edit', $record->id) }}"
                                    >
                                        Modifier
                                    </a>


                                    {{-- ACTIVATION / ARCHIVAGE ANNÉE --}}
                                    @if(
                                        $kind === 'years'
                                        && !$record->is_active
                                        && !$record->is_archived
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route('company.school.years.activate', $record->id) }}"
                                        >
                                            @csrf

                                            <button
                                                class="foundation-edit"
                                                type="submit"
                                            >
                                                Activer
                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route('company.school.years.archive', $record->id) }}"
                                            onsubmit="return confirm('Archiver cette année scolaire ?')"
                                        >
                                            @csrf

                                            <button
                                                class="foundation-edit"
                                                type="submit"
                                            >
                                                Archiver
                                            </button>

                                        </form>

                                    @endif


                                    {{-- SUPPRIMER --}}
                                    <form
                                        method="POST"
                                        action="{{ route($base . '.destroy', $record->id) }}"
                                        onsubmit="return confirm('Voulez-vous vraiment supprimer cet élément ?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="foundation-delete"
                                            type="submit"
                                        >
                                            Supprimer
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="{{ $colspan }}">

                                <div class="foundation-empty">

                                    Aucun résultat.

                                    Modifiez les filtres ou ajoutez
                                    un enregistrement.

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($records->hasPages())

            <div class="foundation-pagination">
                {{ $records->links() }}
            </div>

        @endif

    </section>

</div>


{{-- ============================================================
     CSS NATIF — RESPONSIVE
============================================================ --}}

<style>
.foundation-page {
    max-width: 1380px;
    margin: 0 auto;
    color: #17223b;
}

.foundation-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}

.foundation-eyebrow {
    font-size: 11px;
    letter-spacing: 1.2px;
    font-weight: 800;
    color: #4f46e5;
}

.foundation-heading h2 {
    font-size: 26px;
    margin: 6px 0 5px;
    letter-spacing: -0.6px;
}

.foundation-heading p,
.foundation-panel-title p {
    color: #66738b;
    font-size: 13px;
    margin: 5px 0 0;
}

.foundation-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border-radius: 9px;
    padding: 11px 17px;
    font-size: 13px;
    font-weight: 700;
    border: 1px solid transparent;
    white-space: nowrap;
    cursor: pointer;
    text-decoration: none;
}

.foundation-primary {
    background: #4f46e5;
    color: #fff;
}

.foundation-primary:hover {
    background: #3730a3;
    color: #fff;
}

.foundation-outline {
    background: #fff;
    color: #3730a3;
    border-color: #d9def0;
}

.foundation-outline:hover {
    background: #f5f4ff;
}

.foundation-panel {
    background: #fff;
    border: 1px solid #e5eaf3;
    border-radius: 15px;
    padding: 22px;
    margin-bottom: 20px;
    box-shadow: 0 3px 16px rgba(23, 34, 59, .03);
}

.foundation-panel-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 19px;
}

.foundation-panel-title h3 {
    font-size: 16px;
    margin: 0;
}

.foundation-count {
    background: #eef2ff;
    color: #4338ca;
    border-radius: 20px;
    padding: 7px 11px;
    font-size: 12px;
    font-weight: 800;
}

.foundation-filters {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.foundation-field {
    display: flex;
    flex-direction: column;
    gap: 7px;
    min-width: 0;
}

.foundation-field > span {
    font-size: 12px;
    font-weight: 700;
    color: #42516c;
}

.foundation-field input,
.foundation-field select {
    width: 100%;
    min-width: 0;
    min-height: 43px;
    padding: 10px 12px;
    border: 1px solid #dbe2ee;
    border-radius: 9px;
    background: #fff;
    color: #17223b;
    font-size: 13px;
    outline: none;
    box-sizing: border-box;
}

.foundation-field input:focus,
.foundation-field select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .1);
}

.foundation-filter-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 19px;
}

.foundation-table-scroll {
    overflow-x: auto;
}

.foundation-list-panel {
    padding-bottom: 12px;
}

.foundation-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 720px;
    text-align: left;
}

.foundation-table th {
    background: #f4f6fb;
    color: #52617b;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-size: 11px;
    padding: 13px 15px;
    font-weight: 800;
}

.foundation-table td {
    padding: 15px;
    border-bottom: 1px solid #edf0f5;
    font-size: 13px;
    vertical-align: middle;
}

.foundation-table tr:last-child td {
    border-bottom: 0;
}

.foundation-tag {
    display: inline-block;
    background: #f1f5f9;
    color: #475569;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.foundation-tag.is-good {
    background: #ecfdf5;
    color: #047857;
}

.foundation-pill {
    display: inline-block;
    background: #eef2ff;
    color: #4338ca;
    padding: 6px 9px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 800;
}

.foundation-muted {
    display: block;
    color: #8190a7;
    font-size: 11px;
    margin-top: 4px;
}

.foundation-actions-col {
    text-align: right;
}

.foundation-row-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
}

.foundation-row-actions form {
    margin: 0;
}

.foundation-edit,
.foundation-delete,
.foundation-config-subjects {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-radius: 7px;
    padding: 8px 11px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.4;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    font-family: inherit;
}

.foundation-edit {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #dbeafe;
}

.foundation-delete {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #ffe4e6;
}

.foundation-config-subjects {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
}

.foundation-config-subjects:hover {
    background: #e0e7ff;
    color: #3730a3;
    border-color: #a5b4fc;
}

.foundation-empty {
    text-align: center;
    padding: 32px 10px;
    color: #66738b;
}

.foundation-pagination {
    padding-top: 16px;
    border-top: 1px solid #edf0f5;
    margin-top: 8px;
}

@media (max-width: 1050px) {
    .foundation-filters {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .foundation-heading h2 {
        font-size: 22px;
    }

    .foundation-panel {
        padding: 15px;
    }

    .foundation-filters {
        grid-template-columns: 1fr;
    }

    .foundation-filter-actions .foundation-button {
        flex: 1;
    }

    .foundation-row-actions {
        justify-content: flex-start;
    }

    .foundation-heading > .foundation-button {
        width: 100%;
    }

    .foundation-config-subjects {
        padding: 9px 11px;
    }
}
</style>


{{-- ============================================================
     JAVASCRIPT — FILTRE DYNAMIQUE DES NIVEAUX PAR SECTION
============================================================ --}}

@if($kind === 'classes')

<script>
(function () {
    'use strict';

    const section = document.getElementById('foundationSection');
    const level = document.getElementById('foundationLevel');

    if (!section || !level) {
        return;
    }

    function refreshLevels() {
        for (const option of level.options) {
            if (!option.value) {
                continue;
            }

            option.hidden = Boolean(section.value) &&
                option.dataset.section !== section.value;
        }

        if (level.selectedOptions[0]?.hidden) {
            level.value = '';
        }
    }

    section.addEventListener('change', refreshLevels);

    refreshLevels();
})();
</script>

@endif

@endsection
