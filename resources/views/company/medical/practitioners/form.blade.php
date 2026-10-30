@php
    $isEdit = isset($practitioner);

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

<form method="POST"
      action="{{ $isEdit
          ? route('company.medical.practitioners.update', $practitioner)
          : route('company.medical.practitioners.store') }}">

    @csrf

    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mp-form-grid">

        <div class="mp-field">
            <label for="first_name">Prénom *</label>

            <input type="text"
                   name="first_name"
                   id="first_name"
                   value="{{ old('first_name', $practitioner->first_name ?? '') }}"
                   maxlength="100"
                   required>

            @error('first_name')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="last_name">Nom *</label>

            <input type="text"
                   name="last_name"
                   id="last_name"
                   value="{{ old('last_name', $practitioner->last_name ?? '') }}"
                   maxlength="100"
                   required>

            @error('last_name')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="registration_number">
                Matricule professionnel
            </label>

            <input type="text"
                   name="registration_number"
                   id="registration_number"
                   value="{{ old('registration_number', $practitioner->registration_number ?? '') }}"
                   maxlength="50"
                   placeholder="Ex. MED-001">

            @error('registration_number')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="profession">Profession *</label>

            <select name="profession"
                    id="profession"
                    required>

                <option value="">Sélectionner une profession</option>

                @foreach($professions as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('profession', $practitioner->profession ?? '') === $value)>
                        {{ $label }}
                    </option>
                @endforeach

            </select>

            @error('profession')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="specialty">Spécialité</label>

            <input type="text"
                   name="specialty"
                   id="specialty"
                   value="{{ old('specialty', $practitioner->specialty ?? '') }}"
                   maxlength="150"
                   placeholder="Ex. Cardiologie, pédiatrie...">

            @error('specialty')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="phone">Téléphone</label>

            <input type="tel"
                   name="phone"
                   id="phone"
                   value="{{ old('phone', $practitioner->phone ?? '') }}"
                   maxlength="30"
                   placeholder="Ex. 6XXXXXXXX">

            @error('phone')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="email">Adresse e-mail</label>

            <input type="email"
                   name="email"
                   id="email"
                   value="{{ old('email', $practitioner->email ?? '') }}">

            @error('email')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="hire_date">Date de recrutement</label>

            <input type="date"
                   name="hire_date"
                   id="hire_date"
                   value="{{ old('hire_date', isset($practitioner) ? $practitioner->hire_date?->format('Y-m-d') : '') }}">

            @error('hire_date')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="mp-field">
            <label for="status">Statut *</label>

            <select name="status" id="status" required>

                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('status', $practitioner->status ?? 'active') === $value)>
                        {{ $label }}
                    </option>
                @endforeach

            </select>

            @error('status')
                <small class="mp-field-error">{{ $message }}</small>
            @enderror
        </div>

    </div>

    <div class="mp-form-actions">

        <a href="{{ route('company.medical.practitioners.index') }}"
           class="mp-btn mp-btn-light">
            Annuler
        </a>

        <button type="submit" class="mp-btn mp-btn-primary">
            {{ $isEdit ? 'Enregistrer les modifications' : 'Créer le professionnel' }}
        </button>

    </div>

</form>