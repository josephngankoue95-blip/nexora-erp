
@extends('company.school.principal.layouts.app')

@section('title', 'Notes par classe')

@section('content')

<section class="welcome">
    <div>
        <span class="eyebrow">SUIVI DES RÉSULTATS</span>
        <h1>Notes par classe et matière</h1>
        <p>
            Consultation des notes et des moyennes
            de l'année scolaire active.
        </p>
    </div>

    <div class="year">
        {{ $year->name ?? 'Aucune année active' }}
    </div>
</section>

<section class="panel">

    <h2>Filtres pédagogiques</h2>

    @if($year)
        <form
            method="GET"
            action="{{ route('company.school.principal.results.by-class') }}"
            class="teacher-filters"
        >

            <select name="class_id" id="classFilter" required>
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

            <select name="subject_id" required>
                <option value="">Sélectionner une matière</option>

                @foreach($subjects as $subject)
                    <option
                        value="{{ $subject->id }}"
                        @selected($selectedSubject?->id == $subject->id)
                    >
                        {{ $subject->name }}
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

            <button type="submit">
                Afficher les résultats
            </button>
        </form>

        <p class="filter-hint">
            Sélectionnez d'abord une classe pour charger
            ses matières, puis choisissez une période.
        </p>
    @else
        <p>Aucune année scolaire active.</p>
    @endif

</section>

@if($selectedClass && $selectedSubject && $selectedPeriod)

<section class="panel">

    <h2>
        {{ $selectedClass->name }}
        —
        {{ $selectedSubject->name }}
    </h2>

    <p>
        Période : {{ $selectedPeriod->name }}
        |
        Coefficient matière :
        {{ $selectedSubject->coefficient }}
    </p>

    <div class="academic-table-wrap">
        <table class="academic-table results-table">

            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Élève</th>

                    @foreach($evaluations as $evaluation)
                        <th>
                            {{ $evaluation->title }}
                            <small>
                                / {{ $evaluation->max_score }}
                            </small>
                        </th>
                    @endforeach

                    <th>Moyenne /20</th>
                    <th>Notes manquantes</th>
                </tr>
            </thead>

            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->student_number }}</td>

                        <td>
                            {{ $student->last_name }}
                            {{ $student->first_name }}
                        </td>

                        @foreach($evaluations as $evaluation)
                            <td>
                                {{ $student->scores[$evaluation->id] ?? '—' }}
                            </td>
                        @endforeach

                        <td>
                            <strong>
                                {{ $student->average !== null
                                    ? number_format($student->average, 2, ',', ' ')
                                    : '—' }}
                            </strong>
                        </td>

                        <td>
                            {{ $student->missing }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $evaluations->count() + 4 }}">
                            Aucun élève inscrit dans cette classe.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    @if($evaluations->isEmpty())
        <p class="filter-hint">
            Aucune évaluation n'est enregistrée
            pour cette matière et cette période.
        </p>
    @endif

</section>

@endif

@endsection
