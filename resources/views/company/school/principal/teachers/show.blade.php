
@extends('company.school.principal.layouts.app')

@section('title', 'Fiche enseignant')

@section('content')

<div class="teacher-back">
    <a href="{{ route('company.school.principal.teachers.index') }}">
        ← Retour aux enseignants
    </a>
</div>

<section class="welcome">
    <div>
        <span class="eyebrow">FICHE PÉDAGOGIQUE</span>

        <h1>
            {{ $teacher->last_name }}
            {{ $teacher->first_name }}
        </h1>

        <p>
            Matricule :
            {{ $teacher->matricule ?: 'Non renseigné' }}
        </p>
    </div>

    <div class="year">
        {{ $year->name ?? 'Aucune année active' }}
    </div>
</section>

<section class="stats">
    <article class="stat">
        <span>Classes attribuées</span>
        <strong>{{ $workload['classes'] }}</strong>
    </article>

    <article class="stat">
        <span>Matières enseignées</span>
        <strong>{{ $workload['subjects'] }}</strong>
    </article>

    <article class="stat">
        <span>Affectations actives</span>
        <strong>{{ $workload['assignments'] }}</strong>
    </article>
</section>

<section class="panel">

    <h2>Informations de l'enseignant</h2>

    <div class="teacher-information">
        <p><strong>Email :</strong> {{ $teacher->email ?: '—' }}</p>
        <p><strong>Téléphone :</strong> {{ $teacher->phone ?: '—' }}</p>
        <p><strong>Spécialité :</strong> {{ $teacher->speciality ?: '—' }}</p>
        <p><strong>Qualification :</strong> {{ $teacher->qualification ?: '—' }}</p>

        <p>
            <strong>Recrutement :</strong>
            {{ $teacher->recruitment_date?->format('d/m/Y') ?? '—' }}
        </p>

        <p>
            <strong>Statut :</strong>
            {{ $teacher->status === 'active' ? 'Actif' : 'Inactif' }}
        </p>
    </div>

</section>

<section class="panel">

    <h2>Affectations pédagogiques</h2>

    <div class="academic-table-wrap">
        <table class="academic-table">
            <thead>
                <tr>
                    <th>Classe</th>
                    <th>Matière</th>
                    <th>Coefficient</th>
                </tr>
            </thead>

            <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td>
                            {{ $assignment->schoolClass?->name ?? '—' }}
                        </td>

                        <td>
                            {{ $assignment->subject?->name ?? '—' }}
                        </td>

                        <td>
                            {{ $assignment->subject?->coefficient ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            Aucune affectation pour l'année scolaire active.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>

@endsection
