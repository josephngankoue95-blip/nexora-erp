<div class="module-form-grid">

    <div class="module-field module-field-full">

        <label for="name">
            Nom du fournisseur *
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $supplier->name ?? '') }}"
            required
        >

        @error('name')
            <span class="module-error">{{ $message }}</span>
        @enderror

    </div>


    <div class="module-field">

        <label for="phone">
            Téléphone
        </label>

        <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', $supplier->phone ?? '') }}"
        >

        @error('phone')
            <span class="module-error">{{ $message }}</span>
        @enderror

    </div>


    <div class="module-field">

        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $supplier->email ?? '') }}"
        >

        @error('email')
            <span class="module-error">{{ $message }}</span>
        @enderror

    </div>


    <div class="module-field">

        <label for="city">
            Ville
        </label>

        <input
            type="text"
            id="city"
            name="city"
            value="{{ old('city', $supplier->city ?? '') }}"
        >

    </div>


    <div class="module-field">

        <label for="country">
            Pays *
        </label>

        <input
            type="text"
            id="country"
            name="country"
            value="{{ old('country', $supplier->country ?? 'Cameroun') }}"
            required
        >

    </div>


    <div class="module-field module-field-full">

        <label for="address">
            Adresse
        </label>

        <input
            type="text"
            id="address"
            name="address"
            value="{{ old('address', $supplier->address ?? '') }}"
        >

    </div>


    <div class="module-field">

        <label for="tax_number">
            N° contribuable
        </label>

        <input
            type="text"
            id="tax_number"
            name="tax_number"
            value="{{ old('tax_number', $supplier->tax_number ?? '') }}"
        >

    </div>


    <div class="module-field module-field-full">

        <label for="notes">
            Notes
        </label>

        <textarea
            id="notes"
            name="notes"
            rows="5"
        >{{ old('notes', $supplier->notes ?? '') }}</textarea>

    </div>


    <div class="module-field module-field-full">

        <label class="module-checkbox">

            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old(
                    'is_active',
                    isset($supplier)
                        ? $supplier->is_active
                        : true
                ))
            >

            <span>
                Fournisseur actif
            </span>

        </label>

    </div>

</div>


@if($errors->any())

    <div class="module-alert module-alert-danger">

        <strong>
            Veuillez corriger les erreurs suivantes :
        </strong>

        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

    </div>

@endif


<div class="module-form-footer">

    <a href="{{ route('company.suppliers.index') }}"
       class="module-btn module-btn-secondary">
        Annuler
    </a>

    <button type="submit"
            class="module-btn module-btn-primary">
        {{ isset($supplier) ? 'Enregistrer les modifications' : 'Créer le fournisseur' }}
    </button>

</div>