@extends('layouts.school')

@section('title', 'Bulletins scolaires')
@section('page-title', 'Bulletins scolaires')
@section('page-description', 'Consultez et éditez les bulletins des élèves par année scolaire et par classe.')

@push('styles')
    @vite('resources/css/school-grades.css')
@endpush

@section('content')
@php
    $selectedYear = $year?->id;
    $selectedClass = $class?->id;
    $sequencePeriods = $periods->filter(fn ($period) => $period->kind === 'sequence')->unique('number')->sortBy('number');
    $hasSequences = $sequencePeriods->isNotEmpty();
    $studentCount = $class ? $students->count() : 0;
@endphp

<div class="grades-page bulletin-index-page">
    <header class="bulletin-hero">
        <div class="bulletin-hero-content">
            <span class="bulletin-eyebrow"><span class="bulletin-eyebrow-dot"></span> Gestion scolaire · Évaluations</span>
            <h1>Bulletins scolaires</h1>
            <p>Retrouvez les relevés séquentiels, trimestriels et annuels de chaque élève, prêts à être consultés et imprimés.</p>
        </div>
        <div class="bulletin-hero-stat" aria-label="Nombre d'élèves dans la classe sélectionnée">
            <span class="bulletin-stat-icon" aria-hidden="true">▦</span>
            <span class="bulletin-stat-number">{{ $studentCount }}</span>
            <span class="bulletin-stat-label">{{ $studentCount > 1 ? 'Élèves sélectionnés' : 'Élève sélectionné' }}</span>
        </div>
    </header>

    <section class="bulletin-filter-card" aria-labelledby="bulletin-filters-title">
        <div class="bulletin-section-heading">
            <div>
                <span class="bulletin-kicker">RECHERCHE</span>
                <h2 id="bulletin-filters-title">Sélectionner une classe</h2>
                <p>Choisissez une année scolaire, puis une classe pour afficher ses élèves.</p>
            </div>
            <span class="bulletin-heading-icon" aria-hidden="true">⌕</span>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="bulletin-filter-grid">
            <label class="bulletin-field">
                <span>Année scolaire</span>
                <select name="year_id" onchange="this.form.elements['class_id'].value = ''; this.form.submit()">
                    @forelse($years as $y)
                        <option value="{{ $y->id }}" @selected((int) $selectedYear === (int) $y->id)>{{ $y->name }}{{ $y->is_active ? ' · En cours' : '' }}</option>
                    @empty
                        <option value="">Aucune année disponible</option>
                    @endforelse
                </select>
            </label>
            <label class="bulletin-field">
                <span>Classe</span>
                <select name="class_id" onchange="this.form.submit()" @disabled(!$year)>
                    <option value="">Choisir une classe</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected((int) $selectedClass === (int) $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </label>
            <noscript><button type="submit" class="bulletin-action bulletin-action-primary">Afficher les élèves</button></noscript>
        </form>
    </section>

    @if(!$year)
        <section class="bulletin-empty" role="status">
            <span class="bulletin-empty-icon" aria-hidden="true">▤</span>
            <h2>Aucune année scolaire disponible</h2>
            <p>Créez ou activez une année scolaire pour accéder aux bulletins.</p>
        </section>
    @elseif(!$class)
        <section class="bulletin-empty" role="status">
            <span class="bulletin-empty-icon" aria-hidden="true">▦</span>
            <h2>Sélectionnez une classe</h2>
            <p>Les élèves et leurs bulletins apparaîtront ici dès qu'une classe sera choisie.</p>
        </section>
    @else
        <section class="bulletin-list-card" aria-labelledby="bulletin-list-title">
            <div class="bulletin-list-header">
                <div>
                    <span class="bulletin-kicker">LISTE DES ÉLÈVES</span>
                    <h2 id="bulletin-list-title">Classe de {{ $class->name }}</h2>
                    <p>{{ $year->name }} · {{ $studentCount }} {{ $studentCount > 1 ? 'élèves inscrits' : 'élève inscrit' }}</p>
                </div>
                <span class="bulletin-count-pill">{{ $studentCount }} {{ $studentCount > 1 ? 'élèves' : 'élève' }}</span>
            </div>

            <div class="bulletin-table-scroll">
                <table class="bulletin-students-table">
                    <thead>
                        <tr>
                            <th scope="col">Élève</th>
                            <th scope="col">Bulletins disponibles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $s)
                            <tr>
                                <td class="bulletin-student-cell">
                                    <div class="bulletin-student">
                                        <span class="bulletin-student-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($s->last_name ?: $s->first_name, 0, 1)) }}</span>
                                        <div class="bulletin-student-details">
                                            <strong>{{ $s->last_name }} {{ $s->first_name }}</strong>
                                            <span>Matricule : {{ $s->student_number ?: 'Non renseigné' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="bulletin-periods">
                                        @if($hasSequences)
                                            <div class="bulletin-period-group">
                                                <span class="bulletin-period-label">Séquences</span>
                                                <div class="bulletin-period-links">
                                                    @foreach($sequencePeriods as $p)
                                                        <a class="bulletin-period-link bulletin-sequence" target="_blank" rel="noopener noreferrer"
                                                           title="{{ $p->name }} — {{ $s->last_name }} {{ $s->first_name }}"
                                                           href="{{ route('company.school.bulletins.show', [$class->id, $s->id, 'kind' => 'sequence', 'number' => $p->number]) }}">S{{ $p->number }} <span aria-hidden="true">↗</span></a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <div class="bulletin-period-group">
                                            <span class="bulletin-period-label">Trimestres</span>
                                            <div class="bulletin-period-links">
                                                @foreach([1, 2, 3] as $n)
                                                    <a class="bulletin-period-link bulletin-trimester" target="_blank" rel="noopener noreferrer"
                                                       title="Trimestre {{ $n }} — {{ $s->last_name }} {{ $s->first_name }}"
                                                       href="{{ route('company.school.bulletins.show', [$class->id, $s->id, 'kind' => 'trimester', 'number' => $n]) }}">T{{ $n }} <span aria-hidden="true">↗</span></a>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="bulletin-period-group">
                                            <span class="bulletin-period-label">Bilan</span>
                                            <div class="bulletin-period-links">
                                                <a class="bulletin-period-link bulletin-annual" target="_blank" rel="noopener noreferrer"
                                                   title="Bulletin annuel — {{ $s->last_name }} {{ $s->first_name }}"
                                                   href="{{ route('company.school.bulletins.show', [$class->id, $s->id, 'kind' => 'annual']) }}">Annuel <span aria-hidden="true">↗</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2"><div class="bulletin-table-empty">Aucun élève inscrit dans cette classe pour cette année scolaire.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($studentCount > 0)
                <footer class="bulletin-list-footer"><span aria-hidden="true">ⓘ</span> Les bulletins s'ouvrent dans un nouvel onglet. Les notes manquantes ne sont pas remplacées par des valeurs fictives.</footer>
            @endif
        </section>
    @endif
</div>
@endsection
