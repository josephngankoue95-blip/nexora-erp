@extends('layouts.company')

@section('title', 'Paramètres de l’entreprise')

@section('page-title', 'Paramètres')

@section('page-description')
    Configurez les informations et le fonctionnement de votre entreprise.
@endsection


@section('content')

<div class="company-settings">


    {{-- =========================================================
        EN-TÊTE
    ========================================================== --}}

    <div class="settings-header">

        <div>

            <h2>
                Paramètres de l’entreprise
            </h2>

            <p>
                Gérez l’identité, les coordonnées et les paramètres
                généraux de votre entreprise.
            </p>

        </div>

    </div>


    {{-- =========================================================
        FORMULAIRE
    ========================================================== --}}

    <form
        action="{{ route('company.settings.update') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')


        {{-- =====================================================
            IDENTITÉ
        ====================================================== --}}

        <section class="settings-card">

            <div class="settings-card-header">

                <div>

                    <h3>
                        Identité de l’entreprise
                    </h3>

                    <p>
                        Les informations principales affichées
                        dans votre ERP.
                    </p>

                </div>

            </div>


            <div class="settings-card-body">


                {{-- LOGO --}}

                <div class="settings-logo-section">

                    <div class="settings-logo-preview">

                        @if($company->logo)

                            <img
                                src="{{ asset('storage/' . $company->logo) }}"
                                alt="Logo de {{ $company->name }}">

                        @else

                            <span>
                                {{ strtoupper(
                                    substr(
                                        $company->name ?? 'E',
                                        0,
                                        1
                                    )
                                ) }}
                            </span>

                        @endif

                    </div>


                    <div class="settings-logo-content">

                        <h4>
                            Logo de l’entreprise
                        </h4>

                        <p>
                            JPG, PNG ou WEBP. Taille maximale : 2 Mo.
                        </p>


                        <div class="settings-logo-actions">

                            <label
                                for="company-logo"
                                class="settings-button settings-button-secondary">

                                Choisir un logo

                            </label>

                            <input
                                type="file"
                                name="logo"
                                id="company-logo"
                                accept=".jpg,.jpeg,.png,.webp"
                                hidden>


                            @if($company->logo)

                                <button
                                    type="submit"
                                    form="remove-logo-form"
                                    class="settings-button settings-button-danger">

                                    Supprimer

                                </button>

                            @endif

                        </div>

                    </div>

                </div>


                <div class="settings-grid">


                    {{-- NOM --}}

                    <div class="settings-field">

                        <label for="name">
                            Nom de l’entreprise
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $company->name) }}"
                            required>

                        @error('name')

                            <small class="settings-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- SLOGAN --}}

                    <div class="settings-field">

                        <label for="slogan">
                            Slogan
                        </label>

                        <input
                            type="text"
                            id="slogan"
                            name="slogan"
                            value="{{ old('slogan', $company->slogan) }}"
                            placeholder="Votre slogan">

                    </div>


                </div>

            </div>

        </section>



        {{-- =====================================================
            CONTACT
        ====================================================== --}}

        <section class="settings-card">

            <div class="settings-card-header">

                <div>

                    <h3>
                        Coordonnées
                    </h3>

                    <p>
                        Les moyens de contacter votre entreprise.
                    </p>

                </div>

            </div>


            <div class="settings-card-body">

                <div class="settings-grid">


                    <div class="settings-field">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $company->email) }}">

                    </div>


                    <div class="settings-field">

                        <label for="phone">
                            Téléphone
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $company->phone) }}">

                    </div>


                    <div class="settings-field">

                        <label for="whatsapp">
                            WhatsApp
                        </label>

                        <input
                            type="text"
                            id="whatsapp"
                            name="whatsapp"
                            value="{{ old('whatsapp', $company->whatsapp) }}">

                    </div>


                    <div class="settings-field">

                        <label for="website">
                            Site web
                        </label>

                        <input
                            type="url"
                            id="website"
                            name="website"
                            value="{{ old('website', $company->website) }}"
                            placeholder="https://...">

                    </div>


                </div>

            </div>

        </section>



        {{-- =====================================================
            ADRESSE
        ====================================================== --}}

        <section class="settings-card">

            <div class="settings-card-header">

                <div>

                    <h3>
                        Adresse
                    </h3>

                    <p>
                        Localisation physique de l’entreprise.
                    </p>

                </div>

            </div>


            <div class="settings-card-body">

                <div class="settings-grid">


                    <div class="settings-field settings-field-full">

                        <label for="address">
                            Adresse
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="3">{{ old('address', $company->address) }}</textarea>

                    </div>


                    <div class="settings-field">

                        <label for="city">
                            Ville
                        </label>

                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="{{ old('city', $company->city) }}">

                    </div>


                    <div class="settings-field">

                        <label for="country">
                            Pays
                        </label>

                        <input
                            type="text"
                            id="country"
                            name="country"
                            value="{{ old('country', $company->country ?? 'Cameroun') }}">

                    </div>


                </div>

            </div>

        </section>



        {{-- =====================================================
            INFORMATIONS LÉGALES
        ====================================================== --}}

        <section class="settings-card">

            <div class="settings-card-header">

                <div>

                    <h3>
                        Informations légales
                    </h3>

                    <p>
                        Informations utilisées notamment sur les documents
                        commerciaux.
                    </p>

                </div>

            </div>


            <div class="settings-card-body">

                <div class="settings-grid">


                    <div class="settings-field">

                        <label for="tax_number">
                            Numéro fiscal
                        </label>

                        <input
                            type="text"
                            id="tax_number"
                            name="tax_number"
                            value="{{ old('tax_number', $company->tax_number) }}">

                    </div>


                    <div class="settings-field">

                        <label for="rccm">
                            RCCM
                        </label>

                        <input
                            type="text"
                            id="rccm"
                            name="rccm"
                            value="{{ old('rccm', $company->rccm) }}">

                    </div>


                </div>

            </div>

        </section>



        {{-- =====================================================
            CONFIGURATION COMMERCIALE
        ====================================================== --}}

        <section class="settings-card">

            <div class="settings-card-header">

                <div>

                    <h3>
                        Configuration commerciale
                    </h3>

                    <p>
                        Paramètres utilisés pour les ventes et les factures.
                    </p>

                </div>

            </div>


            <div class="settings-card-body">

                <div class="settings-grid">


                  {{-- Couleur principale --}}
                    <div class="settings-field">
                        <label for="primary_color">
                            Couleur principale
                        </label>

                        <div class="settings-color-wrapper">
                            <input
                                type="color"
                                id="primary_color"
                                name="primary_color"
                                value="{{ old('primary_color', $company->primary_color ?? '#2563eb') }}"
                                class="settings-color-input"
                            >

                            <input
                                type="text"
                                value="{{ old('primary_color', $company->primary_color ?? '#2563eb') }}"
                                class="settings-input settings-color-text"
                                id="primary_color_text"
                                maxlength="7"
                                readonly
                            >
                        </div>

                        <small class="settings-help">
                            Cette couleur pourra être utilisée pour personnaliser votre ERP,
                            les boutons et certains éléments de vos documents.
                        </small>

                        @error('primary_color')
                            <div class="settings-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                    <div class="settings-field">

                        <label for="currency">
                            Devise
                        </label>

                        <input
                            type="text"
                            id="currency"
                            name="currency"
                            value="{{ old('currency', $company->currency ?? 'XAF') }}">

                    </div>


                    <div class="settings-field">

                        <label for="currency_symbol">
                            Symbole
                        </label>

                        <input
                            type="text"
                            id="currency_symbol"
                            name="currency_symbol"
                            value="{{ old('currency_symbol', $company->currency_symbol ?? 'FCFA') }}">

                    </div>


                    <div class="settings-field">

                        <label for="invoice_prefix">
                            Préfixe des factures
                        </label>

                        <input
                            type="text"
                            id="invoice_prefix"
                            name="invoice_prefix"
                            value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'FAC-') }}">

                    </div>


                    <div class="settings-field">

                        <label for="date_format">
                            Format de date
                        </label>

                        <select
                            id="date_format"
                            name="date_format">

                            <option
                                value="d/m/Y"
                                @selected(old('date_format', $company->date_format ?? 'd/m/Y') === 'd/m/Y')>
                                31/12/2026
                            </option>

                            <option
                                value="Y-m-d"
                                @selected(old('date_format', $company->date_format ?? 'd/m/Y') === 'Y-m-d')>
                                2026-12-31
                            </option>

                            <option
                                value="d-m-Y"
                                @selected(old('date_format', $company->date_format ?? 'd/m/Y') === 'd-m-Y')>
                                31-12-2026
                            </option>

                        </select>

                    </div>


                    <div class="settings-field settings-field-full">

                        <label for="invoice_footer">
                            Pied de facture
                        </label>

                        <textarea
                            id="invoice_footer"
                            name="invoice_footer"
                            rows="4"
                            placeholder="Merci pour votre confiance...">{{ old('invoice_footer', $company->invoice_footer) }}</textarea>

                    </div>


                </div>

            </div>

        </section>



        {{-- =====================================================
            STOCK
        ====================================================== --}}

        <section class="settings-card">

            <div class="settings-card-header">

                <div>

                    <h3>
                        Configuration du stock
                    </h3>

                    <p>
                        Configurez le seuil d’alerte automatique.
                    </p>

                </div>

            </div>


            <div class="settings-card-body">

                <div class="settings-grid">


                    <div class="settings-field">

                        <label for="stock_alert_threshold">
                            Seuil d’alerte
                        </label>

                        <input
                            type="number"
                            id="stock_alert_threshold"
                            name="stock_alert_threshold"
                            min="0"
                            value="{{ old(
                                'stock_alert_threshold',
                                $company->stock_alert_threshold ?? 5
                            ) }}">

                        <small class="settings-help">
                            Une alerte sera affichée lorsque le stock
                            atteindra ou passera sous ce seuil.
                        </small>

                    </div>


                </div>

            </div>

        </section>



        {{-- =====================================================
            BOUTON SAUVEGARDER
        ====================================================== --}}

        <div class="settings-submit">

            <button
                type="submit"
                class="settings-button settings-button-primary">

                Enregistrer les paramètres

            </button>

        </div>

    </form>


    {{-- =========================================================
        FORMULAIRE SUPPRESSION LOGO
    ========================================================== --}}

    @if($company->logo)

        <form
            id="remove-logo-form"
            action="{{ route('company.settings.logo.remove') }}"
            method="POST">

            @csrf
            @method('DELETE')

        </form>

    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const colorInput = document.getElementById('primary_color');
    const colorText = document.getElementById('primary_color_text');

    if (colorInput && colorText) {

        colorInput.addEventListener('input', function () {
            colorText.value = this.value;
        });

    }

});
</script>
@endpush

@endsection