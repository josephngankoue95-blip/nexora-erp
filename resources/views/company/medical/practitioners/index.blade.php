@extends('company.medical.patients.layout')

@section('title', 'Professionnels de santé')
@section('page-title', 'Professionnels de santé')

@section('content')

@php
    $professions = [
        'doctor' => 'Médecin',
        'nurse' => 'Infirmier / Infirmière',
        'midwife' => 'Sage-femme',
        'lab_technician' => 'Technicien de laboratoire',
        'pharmacist' => 'Pharmacien',
        'other' => 'Autre',
    ];

    $statuses = [
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'on_leave' => 'En congé',
    ];
@endphp

<div class="mp-toolbar">

    <form method="GET"
          action="{{ route('company.medical.practitioners.index') }}"
          class="mp-search">

        <input type="search"
               name="search"
               value="{{ $search }}"
               placeholder="Nom, matricule ou spécialité...">

        <button type="submit" class="mp-btn mp-btn-secondary">
            Rechercher
        </button>

        @if($search !== '')
            <a href="{{ route('company.medical.practitioners.index') }}"
               class="mp-btn mp-btn-light">
                Effacer
            </a>
        @endif

    </form>

    <a href="{{ route('company.medical.practitioners.create') }}"
       class="mp-btn mp-btn-primary">
        + Nouveau professionnel
    </a>

</div>

<div class="mp-panel">

    <div class="mp-panel-heading">
        <h2>Répertoire des professionnels</h2>
        <span>{{ $practitioners->total() }} professionnel(s)</span>
    </div>

    <div class="mp-table-wrap">
        <table class="mp-table">

            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom complet</th>
                    <th>Profession</th>
                    <th>Spécialité</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($practitioners as $practitioner)
                    <tr>
                        <td>
                            <strong>
                                {{ $practitioner->registration_number ?: '—' }}
                            </strong>
                        </td>

                        <td>{{ $practitioner->full_name }}</td>

                        <td>
                            {{ $professions[$practitioner->profession] ?? 'Autre' }}
                        </td>

                        <td>{{ $practitioner->specialty ?: '—' }}</td>

                        <td>{{ $practitioner->phone ?: '—' }}</td>

                        <td>
                            <span class="mp-practitioner-status status-{{ $practitioner->status }}">
                                {{ $statuses[$practitioner->status] ?? $practitioner->status }}
                            </span>
                        </td>

                        <td>
                            <div class="mp-actions">
                                <a href="{{ route('company.medical.practitioners.edit', $practitioner) }}">
                                    Modifier
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="mp-empty">
                            Aucun professionnel trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    @if($practitioners->hasPages())
        <div class="mp-pagination">
            {{ $practitioners->links() }}
        </div>
    @endif

</div>

@endsection