
@extends('company.school.principal.layouts.app')

@section('title', 'Suivi des résultats')

@section('content')

<section class="welcome">
    <div>
        <span class="eyebrow">SUPERVISION PÉDAGOGIQUE</span>
        <h1>Suivi des évaluations</h1>
        <p>Consultez les évaluations et les notes enregistrées.</p>
    </div>

    <div class="year">
        {{ $year->name ?? 'Aucune année active' }}
    </div>
</section>

<section class="stats">

    <article class="stat">
        <span>Évaluations</span>
        <strong>{{ $stats['evaluations'] }}</strong>
    </article>

    <article class="stat">
        <span>Évaluations verrouillées</span>
        <strong>{{ $stats['locked'] }}</strong>
    </article>

    <article class="stat">
        <span>Notes enregistrées</span>
        <strong>{{ $stats['grades'] }}</strong>
    </article>

    <article class="stat">
        <span>Élèves inscrits</span>
        <strong>{{ $stats['students'] }}</strong>
    </article>

</section>

<section class="panel">

    <h2>Évaluations de l'année scolaire</h2>

    @if($year)

        <form method="GET"
              action="{{ route('company.school.principal.results.index') }}"
              class="teacher-filters">

            <select name="class_id">
                <option value="">Toutes les classes</option>

                @foreach($classes as $classe)
                    <option value="{{ $classe->id }}"
                        @selected($selectedClass?->id == $classe->id)>
                        {{ $classe->name }}
                    </option>
                @endforeach
            </select>

            <select name="period_id">
                <option value="">Toutes les périodes</option>

                @foreach($periods as $period)
                    <option value="{{ $period->id }}"
                        @selected($selectedPeriod?->id == $period->id)>
                        {{ $period->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Filtrer</button>
        </form>

        <div class="academic-table-wrap">
            <table class="academic-table">

                <thead>
                    <tr>
                        <th>Évaluation</th>
                        <th>Classe</th>
                        <th>Matière</th>
                        <th>Période</th>
                        <th>Barème</th>
                        <th>Notes saisies</th>
                        <th>État</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($evaluations as $evaluation)
                        <tr>
                            <td>{{ $evaluation->title }}</td>
                            <td>{{ $evaluation->class_name }}</td>
                            <td>{{ $evaluation->subject_name }}</td>
                            <td>{{ $evaluation->period_name }}</td>
                            <td>{{ $evaluation->max_score }}</td>
                            <td>{{ $evaluation->grades_count }}</td>
                            <td>
                                @if($evaluation->is_locked)
                                    <span class="teacher-status is-active">
                                        Verrouillée
                                    </span>
                                @else
                                    <span class="teacher-status is-inactive">
                                        Non verrouillée
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                Aucune évaluation disponible.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="pagination-container">
            {{ $evaluations->links() }}
        </div>

    @else
        <p>Aucune année scolaire active.</p>
    @endif

</section>

@endsection
