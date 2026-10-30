@extends('company.medical.patients.layout')

@section('title', 'Nouveau rendez-vous')
@section('page-title', 'Programmer un rendez-vous')

@section('content')

<div class="mp-panel mp-form-panel">

    <h2>Nouveau rendez-vous médical</h2>

    <p class="mp-muted">
        Sélectionnez un patient et renseignez les informations du rendez-vous.
    </p>

    <form method="POST"
          action="{{ route('company.medical.appointments.store') }}">

        @csrf

        <div class="mp-form-grid">

            <div class="mp-field">
                <label for="patient_id">Patient *</label>

                <select name="patient_id"
                        id="patient_id"
                        required>
                    <option value="">Sélectionner un patient</option>

                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}"
                            @selected(old('patient_id', request('patient_id')) == $patient->id)>
                            {{ $patient->patient_number }}
                            — {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mp-field">
                <label for="practitioner_id">
                    Professionnel de santé
                </label>

                <select name="practitioner_id" id="practitioner_id">
                    <option value="">Non affecté</option>

                    @foreach($practitioners as $practitioner)
                        <option value="{{ $practitioner->id }}"
                            @selected(old('practitioner_id') == $practitioner->id)>
                            {{ $practitioner->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mp-field">
                <label for="scheduled_at">Date et heure *</label>

                <input type="datetime-local"
                       name="scheduled_at"
                       id="scheduled_at"
                       value="{{ old('scheduled_at') }}"
                       required>
            </div>

            <div class="mp-field">
                <label for="duration_minutes">
                    Durée estimée
                </label>

                <select name="duration_minutes"
                        id="duration_minutes">
                    @foreach([15, 20, 30, 45, 60, 90, 120] as $duration)
                        <option value="{{ $duration }}"
                            @selected((int) old('duration_minutes', 30) === $duration)>
                            {{ $duration }} minutes
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mp-field mp-field-full">
                <label for="reason">Motif du rendez-vous</label>

                <input type="text"
                       name="reason"
                       id="reason"
                       maxlength="255"
                       value="{{ old('reason') }}"
                       placeholder="Ex. : Consultation générale">
            </div>

            <div class="mp-field mp-field-full">
                <label for="notes">Notes administratives</label>

                <textarea name="notes"
                          id="notes"
                          rows="4"
                          maxlength="5000"
                          placeholder="Informations administratives utiles...">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="mp-form-actions">

            <a href="{{ route('company.medical.appointments.index') }}"
               class="mp-btn mp-btn-light">
                Annuler
            </a>

            <button type="submit"
                    class="mp-btn mp-btn-primary">
                Enregistrer le rendez-vous
            </button>

        </div>

    </form>

</div>

@endsection