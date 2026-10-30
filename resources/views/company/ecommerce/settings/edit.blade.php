

@extends('layouts.ecommerce')



@section('title', 'Paramètres Boutique')



@section('content')



<div class="store-settings-page">



    {{-- EN-TÊTE --}}

    <div class="store-settings-header">

        <div>

            <span class="store-settings-eyebrow">

                E-commerce / Configuration

            </span>



            <h1>Paramètres de la boutique</h1>



            <p>

                Personnalisez les informations, les coordonnées

                et les options de fonctionnement de votre boutique.

            </p>

        </div>



        <div class="store-settings-header-icon">

            ⚙

        </div>

    </div>



    {{-- MESSAGE DE SUCCÈS --}}

    @if(session('success'))

        <div class="store-settings-alert success" role="status">

            <span>✓</span>

            <span>{{ session('success') }}</span>

        </div>

    @endif



    {{-- ERREURS DE VALIDATION --}}

    @if($errors->any())

        <div class="store-settings-alert error" role="alert">

            <strong>Veuillez corriger les erreurs suivantes :</strong>



            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif



    <form

        method="POST"

        action="{{ route('company.ecommerce.settings.update') }}"

        class="store-settings-form"

    >

        @csrf

        @method('PUT')



        {{-- INFORMATIONS GÉNÉRALES --}}

        <section class="store-settings-card">



            <div class="store-settings-card-heading">

                <div class="store-settings-section-icon">

                    🏬

                </div>



                <div>

                    <h2>Informations générales</h2>

                    <p>

                        Définissez l'identité et la présentation

                        de votre boutique.

                    </p>

                </div>

            </div>



            <div class="store-settings-grid">



                <div class="store-settings-field full">

                    <label for="store_name">

                        Nom de la boutique

                        <span class="required">*</span>

                    </label>



                    <input

                        type="text"

                        id="store_name"

                        name="store_name"

                        value="{{ old('store_name', $settings->store_name) }}"

                        placeholder="Ex. : KF Boutique"

                        maxlength="255"

                        required

                    >



                    @error('store_name')

                        <small class="field-error">{{ $message }}</small>

                    @enderror

                </div>



                <div class="store-settings-field full">

                    <label for="description">

                        Description de la boutique

                    </label>



                    <textarea

                        id="description"

                        name="description"

                        rows="4"

                        maxlength="5000"

                        placeholder="Présentez votre boutique, ses produits et ses services..."

                    >{{ old('description', $settings->description) }}</textarea>



                    @error('description')

                        <small class="field-error">{{ $message }}</small>

                    @enderror

                </div>



            </div>

        </section>



        {{-- CONTACTS --}}

        <section class="store-settings-card">



            <div class="store-settings-card-heading">

                <div class="store-settings-section-icon">

                    ☎

                </div>



                <div>

                    <h2>Coordonnées et assistance</h2>

                    <p>

                        Indiquez les contacts permettant à vos clients

                        de joindre votre boutique.

                    </p>

                </div>

            </div>



            <div class="store-settings-grid">



                <div class="store-settings-field">

                    <label for="support_email">

                        Adresse e-mail

                    </label>



                    <input

                        type="email"

                        id="support_email"

                        name="support_email"

                        value="{{ old('support_email', $settings->support_email) }}"

                        placeholder="contact@boutique.com"

                    >



                    @error('support_email')

                        <small class="field-error">{{ $message }}</small>

                    @enderror

                </div>



                <div class="store-settings-field">

                    <label for="support_phone">

                        Numéro de téléphone

                    </label>



                    <input

                        type="tel"

                        id="support_phone"

                        name="support_phone"

                        value="{{ old('support_phone', $settings->support_phone) }}"

                        placeholder="+237 6XX XXX XXX"

                    >



                    @error('support_phone')

                        <small class="field-error">{{ $message }}</small>

                    @enderror

                </div>



            </div>

        </section>



        {{-- DEVISE ET LIVRAISON --}}

        <section class="store-settings-card">



            <div class="store-settings-card-heading">

                <div class="store-settings-section-icon">

                    💳

                </div>



                <div>

                    <h2>Devise et livraison</h2>

                    <p>

                        Configurez la monnaie et le seuil

                        de livraison gratuite.

                    </p>

                </div>

            </div>



            <div class="store-settings-grid">



                <div class="store-settings-field">

                    <label for="currency">

                        Devise de la boutique

                        <span class="required">*</span>

                    </label>



                    <select

                        id="currency"

                        name="currency"

                        required

                    >

                        @foreach([

                            'XAF' => 'Franc CFA (XAF)',

                            'XOF' => 'Franc CFA (XOF)',

                            'EUR' => 'Euro (EUR)',

                            'USD' => 'Dollar américain (USD)',

                            'GBP' => 'Livre sterling (GBP)',

                        ] as $code => $label)



                            <option

                                value="{{ $code }}"

                                @selected(

                                    old('currency', $settings->currency) === $code

                                )

                            >

                                {{ $label }}

                            </option>



                        @endforeach

                    </select>



                    @error('currency')

                        <small class="field-error">{{ $message }}</small>

                    @enderror

                </div>



                <div class="store-settings-field">

                    <label for="free_shipping_threshold">

                        Seuil de livraison gratuite

                    </label>



                    <input

                        type="number"

                        id="free_shipping_threshold"

                        name="free_shipping_threshold"

                        min="0"

                        max="9999999999.99"

                        step="0.01"

                        value="{{ old('free_shipping_threshold', $settings->free_shipping_threshold) }}"

                        placeholder="Ex. : 50000"

                    >



                    <small class="field-help">

                        Laissez vide si aucun seuil n'est défini.

                    </small>



                    @error('free_shipping_threshold')

                        <small class="field-error">{{ $message }}</small>

                    @enderror

                </div>



            </div>

        </section>



        {{-- OPTIONS DE LA BOUTIQUE --}}

        <section class="store-settings-card">



            <div class="store-settings-card-heading">

                <div class="store-settings-section-icon">

                    ⚙

                </div>



                <div>

                    <h2>Options de fonctionnement</h2>

                    <p>

                        Activez ou désactivez certaines fonctionnalités

                        de votre boutique.

                    </p>

                </div>

            </div>



            <div class="store-settings-options">



                <label class="store-settings-option">

                    <span class="store-settings-option-info">

                        <strong>Commandes sans compte</strong>

                        <small>

                            Autoriser les visiteurs à commander

                            sans créer de compte client.

                        </small>

                    </span>



                    <span class="store-settings-switch">

                        <input

                            type="checkbox"

                            name="allow_guest_checkout"

                            value="1"

                            @checked(

                                old(

                                    'allow_guest_checkout',

                                    $settings->allow_guest_checkout

                                )

                            )

                        >

                        <span class="store-settings-switch-slider"></span>

                    </span>

                </label>



                <label class="store-settings-option">

                    <span class="store-settings-option-info">

                        <strong>Avis clients</strong>

                        <small>

                            Autoriser l'affichage et la gestion

                            des avis sur les produits.

                        </small>

                    </span>



                    <span class="store-settings-switch">

                        <input

                            type="checkbox"

                            name="reviews_enabled"

                            value="1"

                            @checked(

                                old(

                                    'reviews_enabled',

                                    $settings->reviews_enabled

                                )

                            )

                        >

                        <span class="store-settings-switch-slider"></span>

                    </span>

                </label>



            </div>

        </section>





<div class="form-group">

    <label for="is_active">

        Statut de la boutique publique

    </label>



    <select

        name="is_active"

        id="is_active"

        class="form-control"

    >

        <option value="1"

            @selected(old('is_active', $settings->is_active ?? false) == 1)>

            Boutique active

        </option>



        <option value="0"

            @selected(old('is_active', $settings->is_active ?? false) == 0)>

            Boutique désactivée

        </option>

    </select>

</div>





        {{-- ACTIONS --}}

        <div class="store-settings-actions">



            <p>

                Les modifications seront appliquées

                après leur enregistrement.

            </p>



            <button

                type="submit"

                class="store-settings-save"

            >

                <span>✓</span>

                Enregistrer les paramètres

            </button>



        </div>



    </form>

    {{-- URL PUBLIQUE : hors du formulaire de sauvegarde --}}
    <section class="store-settings-card store-public-link">
        <div class="store-settings-card-heading">
            <div class="store-settings-section-icon">🔗</div>
            <div><h2>Adresse publique de la boutique</h2><p>Copiez ou partagez le lien de votre boutique avec vos clients.</p></div>
        </div>
        @if($settings->is_active)
            <p class="store-public-status active">● Boutique activée</p>
        @else
            <p class="store-public-status inactive">● Boutique désactivée : le lien peut ne pas être accessible aux visiteurs.</p>
        @endif
        <div class="store-settings-field full">
            <label for="storefront_public_url">Lien public de la boutique</label>
            <input type="url" id="storefront_public_url" value="{{ $storefrontUrl }}" readonly>
        </div>
        <div class="store-public-actions">
            <button type="button" id="copy_storefront_url" class="store-public-button primary">Copier le lien</button>
            <a href="{{ $storefrontUrl }}" target="_blank" rel="noopener noreferrer" class="store-public-button secondary">Visiter ma boutique ↗</a>
            <a href="https://wa.me/?text={{ rawurlencode('Découvrez notre boutique : ' . $storefrontUrl) }}" target="_blank" rel="noopener noreferrer" class="store-public-button whatsapp">Partager sur WhatsApp</a>
        </div>
        <p id="store_public_copy_status" role="status" aria-live="polite" class="store-public-feedback"></p>
    </section>




</div>



@endsection



@vite('resources/css/ecommerce-store-settings.css')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('copy_storefront_url');
    const input = document.getElementById('storefront_public_url');
    const status = document.getElementById('store_public_copy_status');
    if (!button || !input || !status) return;
    button.addEventListener('click', async function () {
        try {
            await navigator.clipboard.writeText(input.value);
            status.textContent = 'Lien copié avec succès !';
        } catch (error) {
            input.focus(); input.select();
            status.textContent = 'Sélectionnez le lien pour le copier manuellement.';
        }
    });
});
</script>
@endpush
