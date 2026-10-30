@extends('company.medical.patients.layout')

@section('title', 'Rendez-vous médicaux')
@section('page-title', 'Rendez-vous médicaux')

@section('content')

<div class="mp-toolbar">

    <form method="GET"
          action="{{ route('company.medical.appointments.index') }}"
          class="mp-search">

        <input type="date"
               name="date"
               value="{{ request('date') }}"
               aria-label="Filtrer par date">

        <select name="status" aria-label="Filtrer par statut">
            <option value="">Tous les statuts</option>

            @foreach([
                'scheduled' => 'Programmé',
                'confirmed' => 'Confirmé',
                'completed' => 'Terminé',
                'cancelled' => 'Annulé',
                'no_show' => 'Absent'
            ] as $value => $label)
                <option value="{{ $value }}"
                    @selected(request('status') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="mp-btn mp-btn-secondary">
            Filtrer
        </button>

        <a href="{{ route('company.medical.appointments.index') }}"
           class="mp-btn mp-btn-light">
            Réinitialiser
        </a>

    </form>

    <a href="{{ route('company.medical.appointments.create') }}"
       class="mp-btn mp-btn-primary">
        + Nouveau rendez-vous
    </a>

</div>

<div class="mp-panel">

    <div class="mp-panel-heading">
        <h2>Liste des rendez-vous</h2>
        <span>{{ $appointments->total() }} résultat(s)</span>
    </div>

    <div class="mp-table-wrap">
        <table class="mp-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Patient</th>
                    <th>Professionnel</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($appointments as $appointment)
                    <tr>
                        <td>
                            {{ $appointment->scheduled_at->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $appointment->scheduled_at->format('H:i') }}
                        </td>

                        <td>
                            <strong>
                                {{ $appointment->patient?->full_name ?? '—' }}
                            </strong>
                        </td>

                        <td>
                            {{ $appointment->practitioner?->name ?? 'Non affecté' }}
                        </td>

                        <td>
                            {{ $appointment->reason ?: '—' }}
                        </td>

                        <td>
                            @php
                                $statusLabels = [
                                    'scheduled' => 'Programmé',
                                    'confirmed' => 'Confirmé',
                                    'completed' => 'Terminé',
                                    'cancelled' => 'Annulé',
                                    'no_show' => 'Absent'
                                ];
                            @endphp

                            <span class="mp-appointment-status mp-appointment-status-{{ $appointment->status }}">
                                {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('company.medical.appointments.show', $appointment) }}"
                               class="mp-action-link">
                                Consulter
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="mp-empty">
                            Aucun rendez-vous trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($appointments->hasPages())
        <div class="mp-pagination">
            {{ $appointments->links() }}
        </div>
    @endif

</div>

@endsection