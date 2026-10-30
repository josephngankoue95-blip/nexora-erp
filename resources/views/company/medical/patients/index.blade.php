@extends('company.medical.patients.layout')

@section('title', 'Liste des patients')
@section('page-title', 'Patients')

@section('content')

<div class="mp-toolbar">
    <form method="GET"
          action="{{ route('company.medical.patients.index') }}"
          class="mp-search">

        <input type="search"
               name="search"
               value="{{ $search }}"
               placeholder="Nom, matricule ou téléphone...">

        <button type="submit" class="mp-btn mp-btn-secondary">
            Rechercher
        </button>

        @if($search !== '')
            <a href="{{ route('company.medical.patients.index') }}"
               class="mp-btn mp-btn-light">
                Effacer
            </a>
        @endif
    </form>

    <a href="{{ route('company.medical.patients.create') }}"
       class="mp-btn mp-btn-primary">
        + Nouveau patient
    </a>
</div>

<div class="mp-panel">

    <div class="mp-panel-heading">
        <h2>Répertoire des patients</h2>
        <span>{{ $patients->total() }} patient(s)</span>
    </div>

    <div class="mp-table-wrap">
        <table class="mp-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom complet</th>
                    <th>Sexe</th>
                    <th>Téléphone</th>
                    <th>Ville</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>
                            <strong>{{ $patient->patient_number }}</strong>
                        </td>

                        <td>{{ $patient->full_name }}</td>

                        <td>
                            @switch($patient->gender)
                                @case('male') Masculin @break
                                @case('female') Féminin @break
                                @case('other') Autre @break
                                @default —
                            @endswitch
                        </td>

                        <td>{{ $patient->phone ?: '—' }}</td>
                        <td>{{ $patient->city ?: '—' }}</td>

                        <td>
                            <span class="mp-status {{ $patient->status === 'active' ? 'is-active' : 'is-inactive' }}">
                                {{ $patient->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>

                        <td>
                            <div class="mp-actions">
                                <a href="{{ route('company.medical.patients.show', $patient) }}">
                                    Voir
                                </a>

                                <a href="{{ route('company.medical.patients.edit', $patient) }}">
                                    Modifier
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="mp-empty">
                            Aucun patient trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
        <div class="mp-pagination">
            {{ $patients->links() }}
        </div>
    @endif

</div>

@endsection