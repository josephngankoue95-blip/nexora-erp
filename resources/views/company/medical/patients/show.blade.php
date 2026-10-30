@extends('company.medical.patients.layout')

@section('title', 'Fiche patient')
@section('page-title', 'Fiche patient')

@section('content')

<div class="mp-toolbar mp-no-print">

    <a href="{{ route('company.medical.patients.index') }}"
       class="mp-btn mp-btn-light">
        ← Retour
    </a>

    <div class="mp-actions">
        <a href="{{ route('company.medical.patients.edit', $patient) }}"
           class="mp-btn mp-btn-secondary">
            Modifier
        </a>

        <button type="button"
                class="mp-btn mp-btn-primary"
                id="printPatient">
            Imprimer
        </button>
    </div>

</div>

<div class="mp-panel mp-patient-sheet">

    <div class="mp-sheet-header">
        <div>
            <span class="mp-eyebrow">NEXORA MÉDICAL</span>
            <h2>Fiche administrative du patient</h2>
        </div>

        <strong>{{ $patient->patient_number }}</strong>
    </div>

    <div class="mp-detail-grid">

        <div>
            <span>Nom complet</span>
            <strong>{{ $patient->full_name }}</strong>
        </div>

        <div>
            <span>Sexe</span>
            <strong>
                {{ match($patient->gender) {
                    'male' => 'Masculin',
                    'female' => 'Féminin',
                    'other' => 'Autre',
                    default => 'Non renseigné'
                } }}
            </strong>
        </div>

        <div>
            <span>Date de naissance</span>
            <strong>
                {{ $patient->date_of_birth?->format('d/m/Y') ?? 'Non renseignée' }}
            </strong>
        </div>

        <div>
            <span>Téléphone</span>
            <strong>{{ $patient->phone ?: '—' }}</strong>
        </div>

        <div>
            <span>E-mail</span>
            <strong>{{ $patient->email ?: '—' }}</strong>
        </div>

        <div>
            <span>Ville</span>
            <strong>{{ $patient->city ?: '—' }}</strong>
        </div>

        <div class="mp-detail-full">
            <span>Adresse</span>
            <strong>{{ $patient->address ?: '—' }}</strong>
        </div>

    </div>

    <h3>Contact d'urgence</h3>

    <div class="mp-detail-grid">

        <div>
            <span>Nom</span>
            <strong>{{ $patient->emergency_contact_name ?: '—' }}</strong>
        </div>

        <div>
            <span>Téléphone</span>
            <strong>{{ $patient->emergency_contact_phone ?: '—' }}</strong>
        </div>

    </div>

    <div class="mp-sheet-footer">
        <small>
            Date d'enregistrement :
            {{ $patient->created_at->format('d/m/Y à H:i') }}
        </small>
    </div>

</div>

@endsection