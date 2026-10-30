@php
    $editing = isset($patient);
@endphp

<div class="mp-form-grid">

    <div class="mp-field">
        <label for="last_name">Nom *</label>
        <input id="last_name"
               type="text"
               name="last_name"
               maxlength="100"
               value="{{ old('last_name', $patient->last_name ?? '') }}"
               required>
    </div>

    <div class="mp-field">
        <label for="first_name">Prénom *</label>
        <input id="first_name"
               type="text"
               name="first_name"
               maxlength="100"
               value="{{ old('first_name', $patient->first_name ?? '') }}"
               required>
    </div>

    <div class="mp-field">
        <label for="date_of_birth">Date de naissance</label>
        <input id="date_of_birth"
               type="date"
               name="date_of_birth"
               max="{{ now()->toDateString() }}"
               value="{{ old('date_of_birth', isset($patient) && $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}">
    </div>

    <div class="mp-field">
        <label for="gender">Sexe</label>
        <select id="gender" name="gender">
            <option value="">Sélectionner</option>
            @foreach(['male' => 'Masculin', 'female' => 'Féminin', 'other' => 'Autre'] as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('gender', $patient->gender ?? '') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mp-field">
        <label for="phone">Téléphone</label>
        <input id="phone"
               type="tel"
               name="phone"
               maxlength="30"
               value="{{ old('phone', $patient->phone ?? '') }}">
    </div>

    <div class="mp-field">
        <label for="email">Adresse e-mail</label>
        <input id="email"
               type="email"
               name="email"
               value="{{ old('email', $patient->email ?? '') }}">
    </div>

    <div class="mp-field">
        <label for="city">Ville</label>
        <input id="city"
               type="text"
               name="city"
               maxlength="100"
               value="{{ old('city', $patient->city ?? '') }}">
    </div>

    <div class="mp-field mp-field-full">
        <label for="address">Adresse</label>
        <input id="address"
               type="text"
               name="address"
               maxlength="255"
               value="{{ old('address', $patient->address ?? '') }}">
    </div>

    <div class="mp-section-title">
        <h3>Contact d'urgence</h3>
    </div>

    <div class="mp-field">
        <label for="emergency_contact_name">Nom du contact</label>
        <input id="emergency_contact_name"
               type="text"
               name="emergency_contact_name"
               value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
    </div>

    <div class="mp-field">
        <label for="emergency_contact_phone">Téléphone du contact</label>
        <input id="emergency_contact_phone"
               type="tel"
               name="emergency_contact_phone"
               maxlength="30"
               value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}">
    </div>

    @if($editing)
        <div class="mp-field">
            <label for="status">Statut</label>
            <select id="status" name="status">
                <option value="active"
                    @selected(old('status', $patient->status) === 'active')>
                    Actif
                </option>
                <option value="inactive"
                    @selected(old('status', $patient->status) === 'inactive')>
                    Inactif
                </option>
            </select>
        </div>
    @endif

</div>

<div class="mp-form-actions">
    <a href="{{ route('company.medical.patients.index') }}"
       class="mp-btn mp-btn-light">
        Annuler
    </a>

    <button type="submit" class="mp-btn mp-btn-primary">
        {{ $editing ? 'Enregistrer les modifications' : 'Enregistrer le patient' }}
    </button>
</div>