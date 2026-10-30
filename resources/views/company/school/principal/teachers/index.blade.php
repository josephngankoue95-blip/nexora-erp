
@extends('company.school.principal.layouts.app')

@section('title', 'Suivi des enseignants')

@section('content')

<section class="welcome">
    <div>
        <span class="eyebrow">SUIVI PÉDAGOGIQUE</span>
        <h1>Enseignants</h1>
        <p>Consultation des enseignants de l'établissement.</p>
    </div>

    <div class="year">
        {{ $year->name ?? 'Aucune année active' }}
    </div>
</section>

<section class="stats">
    <article class="stat">
        <span>Total enseignants</span>
        <strong>{{ $stats['total'] }}</strong>
    </article>

    <article class="stat">
        <span>Enseignants actifs</span>
        <strong>{{ $stats['active'] }}</strong>
    </article>

    <article class="stat">
        <span>Enseignants inactifs</span>
        <strong>{{ $stats['inactive'] }}</strong>
    </article>
</section>

<section class="panel">

    <h2>Liste des enseignants</h2>

    <form method="GET"
          action="{{ route('company.school.principal.teachers.index') }}"
          class="teacher-filters">

        <input
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="Nom, matricule, spécialité..."
        >

        <select name="status">
            <option value="">Tous les statuts</option>
            <option value="active" @selected($status === 'active')>
                Actifs
            </option>
            <option value="inactive" @selected($status === 'inactive')>
                Inactifs
            </option>
        </select>

        <button type="submit">Rechercher</button>
    </form>

    <div class="academic-table-wrap">
        <table class="academic-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Enseignant</th>
                    <th>Spécialité</th>
                    <th>Statut</th>
                    <th>Affectations</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($teachers as $teacher)
                    <tr>
                        <td>{{ $teacher->matricule ?: '—' }}</td>

                        <td>
                            {{ $teacher->last_name }}
                            {{ $teacher->first_name }}
                        </td>

                        <td>{{ $teacher->speciality ?: '—' }}</td>

                        <td>
                            <span class="teacher-status {{ $teacher->status === 'active' ? 'is-active' : 'is-inactive' }}">
                                {{ $teacher->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>

                        <td>{{ $teacher->assignments_count }}</td>

                        <td>
                            <a class="teacher-detail-link"
                               href="{{ route('company.school.principal.teachers.show', $teacher->id) }}">
                                Consulter
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            Aucun enseignant trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $teachers->links() }}
    </div>

</section>

@endsection
