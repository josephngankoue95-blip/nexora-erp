@extends('company.medical.patients.layout')

@section('title', 'Détail du rendez-vous')
@section('page-title', 'Détail du rendez-vous')

@section('content')

@php
    $statusLabels = [
        'scheduled' => 'Programmé',
        'confirmed' => 'Confirmé',
        'completed' => 'Terminé',
        'cancelled' => 'Annulé',
        'no_show' => 'Absent'
    ];
@endphp

<div class="mp-toolbar">

    <a href="{{ route('company.medical.appointments.index') }}"
       class="mp-btn mp-btn-light">
        ← Retour aux rendez-vous
    </a>

    <a href="{{ route('company.medical.appointments.create', [
        'patient_id' => $patient->id
    ]) }}"
    class="mp-btn mp-btn-primary">
        + Programmer un rendez-vous
    </a>

</div>

<div class="mp-panel">

    <div class="mp-sheet-header">
        <div>
            <span class="mp-eyebrow">NEXORA MÉDICAL</span>
            <h2>Rendez-vous #{{ $appointment->id }}</h2>
        </div>

        <span class="mp-appointment-status mp-appointment-status-{{ $appointment->status }}">
            {{ $statusLabels[$appointment->status] ?? $appointment->status }}
        </span>
    </div>

    <div class="mp-detail-grid">

        <div>
            <span>Patient</span>

            <strong>
                {{ $appointment->patient?->full_name ?? '—' }}
            </strong>
        </div>

        <div>
            <span>Matricule du patient</span>

            <strong>
                {{ $appointment->patient?->patient_number ?? '—' }}
            </strong>
        </div>

        <div>
            <span>Date et heure</span>

            <strong>
                {{ $appointment->scheduled_at->format('d/m/Y à H:i') }}
            </strong>
        </div>

        <div>
            <span>Durée estimée</span>

            <strong>
                {{ $appointment->duration_minutes }} minutes
            </strong>
        </div>

        <div>
            <span>Professionnel affecté</span>

            <strong>
                {{ $appointment->practitioner?->name ?? 'Non affecté' }}
            </strong>
        </div>

        <div>
            <span>Motif</span>

            <strong>
                {{ $appointment->reason ?: 'Non renseigné' }}
            </strong>
        </div>

    </div>

    @if($appointment->notes)
        <div class="mp-appointment-notes">
            <h3>Notes administratives</h3>
            <p>{{ $appointment->notes }}</p>
        </div>
    @endif

</div>

<div class="mp-panel mp-status-panel">

    <h3>Modifier le statut</h3>

    <form method="POST"
          action="{{ route('company.medical.appointments.status', $appointment) }}"
          class="mp-status-form">

        @csrf
        @method('PATCH')

        <select name="status" required>
            @foreach($statusLabels as $value => $label)
                <option value="{{ $value }}"
                    @selected($appointment->status === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="mp-btn mp-btn-primary">
            Mettre à jour
        </button>

    </form>

</div>

@endsection