@extends('company.medical.patients.layout')

@section('title', 'Détail de consultation')
@section('page-title', 'Détail de consultation')

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
    <a href="{{ route('company.medical.consultations.index') }}"
       class="mp-btn mp-btn-light">
        ← Retour aux consultations
    </a>
</div>

<div class="mp-panel">

    <div class="mp-panel-heading">
        <div>
            <h2>Consultation #{{ $consultation->id }}</h2>
            <p>
                {{ $consultation->consultation_date->format('d/m/Y à H:i') }}
            </p>
        </div>

        <span class="mp-consultation-status status-{{ $consultation->status }}">
            {{ $statuses[$consultation->status] ?? $consultation->status }}
        </span>
    </div>

    <div class="mp-detail-grid">

        <div>
            <span>Patient</span>
            <strong>{{ $consultation->patient?->full_name ?? '—' }}</strong>
        </div>

        <div>
            <span>Matricule</span>
            <strong>{{ $consultation->patient?->patient_number ?? '—' }}</strong>
        </div>

        <div>
            <span>Professionnel</span>
            <strong>{{ $consultation->practitioner?->full_name ?? 'Non affecté' }}</strong>
        </div>

        <div>
            <span>Rendez-vous associé</span>
            <strong>
                {{ $consultation->appointment
                    ? 'RDV #' . $consultation->appointment->id
                    : 'Aucun' }}
            </strong>
        </div>

        <div>
            <span>Motif</span>
            <strong>{{ $consultation->reason ?: 'Non renseigné' }}</strong>
        </div>

        <div>
            <span>Statut</span>
            <strong>{{ $statuses[$consultation->status] ?? '—' }}</strong>
        </div>

    </div>

</div>

<div class="mp-panel mp-consultation-details">

    <h3>Compte rendu médical</h3>

    <div class="mp-medical-section">
        <h4>Symptômes</h4>
        <p>{{ $consultation->symptoms ?: 'Non renseigné' }}</p>
    </div>

    <div class="mp-medical-section">
        <h4>Observations cliniques</h4>
        <p>{{ $consultation->clinical_observations ?: 'Non renseigné' }}</p>
    </div>

    <div class="mp-medical-section">
        <h4>Diagnostic</h4>
        <p>{{ $consultation->diagnosis ?: 'Non renseigné' }}</p>
    </div>

    <div class="mp-medical-section">
        <h4>Plan de traitement</h4>
        <p>{{ $consultation->treatment_plan ?: 'Non renseigné' }}</p>
    </div>

</div>

@endsection