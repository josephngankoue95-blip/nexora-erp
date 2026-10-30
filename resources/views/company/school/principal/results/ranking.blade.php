
@extends('company.school.principal.layouts.app')

@section('title', 'Classement des élèves')

@section('content')

<section class="welcome">
    <div>
        <span class="eyebrow">RÉSULTATS SCOLAIRES</span>
        <h1>Classement et moyennes</h1>
        <p>
            Consolidation provisoire des résultats
            par classe et période.
        </p>
    </div>

    <div class="year">
        {{ $year->name ?? 'Aucune année active' }}
    </div>
</section>

<section class="panel">
    <h2>Choisir la classe et la période</h2>

    @if($year)
        <form
            method="GET"
            action="{{ route('company.school.principal.results.ranking') }}"
            class="teacher-filters"
        >
            <select name="class_id" required>
                <option value="">Sélectionner une classe</option>

                @foreach($classes as $classe)
                    <option
                        value="{{ $classe->id }}"
                        @selected($selectedClass?->id == $classe->id)
                    >
                        {{ $classe->name }}
                    </option>
                @endforeach
            </select>

            <select name="period_id" required>
                <option value="">Sélectionner une période</option>

                @foreach($periods as $period)
                    <option
                        value="{{ $period->id }}"
                        @selected($selectedPeriod?->id == $period->id)
                    >
                        {{ $period->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Afficher</button>
        </form>
    @else
        <p>Aucune année scolaire active.</p>
    @endif
</section>

@if($selectedClass && $selectedPeriod)

<section class="stats">
    <article class="stat">
        <span>Élèves inscrits</span>
        <strong>{{ $summary['students'] }}</strong>
    </article>

    <article class="stat">
        <span>Dossiers complets</span>
        <strong>{{ $summary['complete'] }}</strong>
    </article>

    <article class="stat">
        <span>Moyenne des dossiers complets</span>
        <strong>
            {{ $summary['class_average'] !== null
                ? number_format($summary['class_average'], 2, ',', ' ')
                : '—' }}
        </strong>
    </article>

    <article class="stat">
        <span>Taux ≥ 10/20 (dossiers complets)</span>
        <strong>
            {{ $summary['success_rate'] !== null
                ? number_format($summary['success_rate'], 2, ',', ' ') . ' %'
                : '—' }}
        </strong>
    </article>
</section>

<section class="panel">

    <h2>
        {{ $selectedClass->name }}
        —
        {{ $selectedPeriod->name }}
    </h2>

    <p class="filter-hint">
        Les rangs sont provisoires et attribués uniquement
        aux élèves ayant un dossier complet.
        Les moyennes partielles ne sont pas des résultats définitifs.
    </p>

    <div class="academic-table-wrap">
        <table class="academic-table results-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Élève</th>

                    @foreach($subjects as $subject)
                        <th>
                            {{ $subject->name }}
                            <small>
                                Coef. {{ $subject->coefficient }}
                            </small>
                        </th>
                    @endforeach

                    <th>Moyenne /20</th>
                    <th>Rang provisoire</th>
                    <th>Notes manquantes</th>
                </tr>
            </thead>

            <tbody>
                @forelse($results as $student)
                    <tr>
                        <td>{{ $student->student_number }}</td>

                        <td>
                            {{ $student->last_name }}
                            {{ $student->first_name }}
                        </td>

                        @foreach($subjects as $subject)
                            @php
                                $average = $student->subject_averages[$subject->id] ?? null;
                            @endphp

                            <td>
                                {{ $average !== null
                                    ? number_format($average, 2, ',', ' ')
                                    : '—' }}
                            </td>
                        @endforeach

                        <td>
                            <strong>
                                {{ $student->general_average !== null
                                    ? number_format($student->general_average, 2, ',', ' ')
                                    : '—' }}
                            </strong>
                        </td>

                        <td>
                            {{ $student->rank !== null
                                ? $student->rank
                                : '—' }}
                        </td>

                        <td>
                            @if($student->is_complete)
                                <span class="teacher-status is-active">
                                    Complet
                                </span>
                            @else
                                <span class="teacher-status is-inactive">
                                    Incomplet ({{ $student->missing }})
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $subjects->count() + 5 }}">
                            Aucun élève inscrit dans cette classe.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>

@endif

@endsection
