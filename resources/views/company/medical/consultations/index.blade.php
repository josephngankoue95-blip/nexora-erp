@extends('company.medical.patients.layout')

@section('title', 'Consultations médicales')
@section('page-title', 'Consultations médicales')

@section('content')

@php
    $statuses = [
        'draft' => 'Brouillon',
        'in_progress' => 'En cours',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ];
@endphp

<div class="mp-toolbar">

    <form method="GET"
          action="{{ route('company.medical.consultations.index') }}"
          class="mp-search">

        <input type="date"
               name="date"
               value="{{ request('date') }}"
               aria-label="Date de consultation">

        <select name="status" aria-label="Statut">
            <option value="">Tous les statuts</option>

            @foreach($statuses as $value => $label)
                <option value="{{ $value }}"
                    @selected(request('status') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="mp-btn mp-btn-secondary">
            Filtrer
        </button>

        <a href="{{ route('company.medical.consultations.index') }}"
           class="mp-btn mp-btn-light">
            Réinitialiser
        </a>
    </form>

    <a href="{{ route('company.medical.consultations.create') }}"
       class="mp-btn mp-btn-primary">
        + Nouvelle consultation
    </a>

</div>

<div class="mp-panel">

    <div class="mp-panel-heading">
        <h2>Historique des consultations</h2>
        <span>{{ $consultations->total() }} consultation(s)</span>
    </div>

    <div class="mp-table-wrap">
        <table class="mp-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Professionnel</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($consultations as $consultation)
                    <tr>
                        <td>
                            {{ $consultation->consultation_date->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <strong>
                                {{ $consultation->patient?->full_name ?? '—' }}
                            </strong>
                        </td>

                        <td>
                            {{ $consultation->practitioner?->full_name ?? 'Non affecté' }}
                        </td>

                        <td>{{ $consultation->reason ?: '—' }}</td>

                        <td>
                            <span class="mp-consultation-status status-{{ $consultation->status }}">
                                {{ $statuses[$consultation->status] ?? $consultation->status }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('company.medical.consultations.show', $consultation) }}"
                               class="mp-action-link">
                                Consulter
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="mp-empty">
                            Aucune consultation trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($consultations->hasPages())
        <div class="mp-pagination">
            {{ $consultations->links() }}
        </div>
    @endif

</div>

@endsection