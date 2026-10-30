@extends('company.medical.patients.layout')

@section('title', 'Nouvelle consultation')
@section('page-title', 'Nouvelle consultation')

@section('content')

<div class="mp-toolbar">
    <a href="{{ route('company.medical.consultations.index') }}"
       class="mp-btn mp-btn-light">
        ← Retour aux consultations
    </a>
</div>

<div class="mp-panel mp-form-panel">

    <div class="mp-panel-heading">
        <div>
            <h2>Enregistrer une consultation</h2>
            <p>Renseignez les informations médicales du patient.</p>
        </div>
    </div>

    <form method="POST"
          action="{{ route('company.medical.consultations.store') }}">

        @csrf

        <div class="mp-form-grid">

            <div class="mp-field">
                <label for="patient_id">Patient *</label>

                <select name="patient_id" id="patient_id" required>
                    <option value="">Sélectionner un patient</option>

                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}"
                            @selected(old('patient_id', request('patient_id')) == $patient->id)>
                            {{ $patient->patient_number }}
                            — {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>

                @error('patient_id')
                    <small class="mp-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="mp-field">
                <label for="practitioner_id">Professionnel de santé</label>

                <select name="practitioner_id" id="practitioner_id">
                    <option value="">Non affecté</option>

                    @foreach($practitioners as $practitioner)
                        <option value="{{ $practitioner->id }}"
                            @selected(old('practitioner_id') == $practitioner->id)>
                            {{ $practitioner->full_name }}
                            — {{ $practitioner->specialty ?: $practitioner->profession }}
                        </option>
                    @endforeach
                </select>

                @error('practitioner_id')
                    <small class="mp-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="mp-field">
                <label for="appointment_id">Rendez-vous associé</label>

                <select name="appointment_id" id="appointment_id">
                    <option value="">Sans rendez-vous</option>

                    @foreach($appointments as $appointment)
                        <option value="{{ $appointment->id }}"
                            data-patient-id="{{ $appointment->patient_id }}"
                            @selected(old('appointment_id', request('appointment_id')) == $appointment->id)>
                            {{ $appointment->scheduled_at->format('d/m/Y H:i') }}
                            — RDV #{{ $appointment->id }}
                        </option>
                    @endforeach
                </select>

                @error('appointment_id')
                    <small class="mp-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="mp-field">
                <label for="consultation_date">Date de consultation *</label>

                <input type="datetime-local"
                       name="consultation_date"
                       id="consultation_date"
                       value="{{ old('consultation_date', now()->format('Y-m-d\TH:i')) }}"
                       required>

                @error('consultation_date')
                    <small class="mp-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="mp-field mp-field-full">
                <label for="reason">Motif de consultation</label>

                <input type="text"
                       name="reason"
                       id="reason"
                       maxlength="255"
                       value="{{ old('reason') }}"
                       placeholder="Motif de la consultation">
            </div>

            <div class="mp-field mp-field-full">
                <label for="symptoms">Symptômes</label>

                <textarea name="symptoms"
                          id="symptoms"
                          rows="4"
                          maxlength="10000"
                          placeholder="Symptômes déclarés par le patient...">{{ old('symptoms') }}</textarea>
            </div>

            <div class="mp-field mp-field-full">
                <label for="clinical_observations">Observations cliniques</label>

                <textarea name="clinical_observations"
                          id="clinical_observations"
                          rows="4"
                          maxlength="10000"
                          placeholder="Observations du professionnel de santé...">{{ old('clinical_observations') }}</textarea>
            </div>

            <div class="mp-field mp-field-full">
                <label for="diagnosis">Diagnostic</label>

                <textarea name="diagnosis"
                          id="diagnosis"
                          rows="4"
                          maxlength="10000"
                          placeholder="Diagnostic médical...">{{ old('diagnosis') }}</textarea>
            </div>

            <div class="mp-field mp-field-full">
                <label for="treatment_plan">Plan de traitement</label>

                <textarea name="treatment_plan"
                          id="treatment_plan"
                          rows="4"
                          maxlength="10000"
                          placeholder="Traitement ou prise en charge...">{{ old('treatment_plan') }}</textarea>
            </div>

        </div>

        <div class="mp-form-actions">

            <a href="{{ route('company.medical.consultations.index') }}"
               class="mp-btn mp-btn-light">
                Annuler
            </a>

            <button type="submit" class="mp-btn mp-btn-primary">
                Enregistrer le brouillon
            </button>

        </div>

    </form>

</div>

@endsection