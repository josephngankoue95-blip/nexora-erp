@extends('layouts.company')

@section('title', 'Paiements')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>
            <div class="module-breadcrumb">
                Paramètres / Paiements
            </div>

            <h1>Paiements</h1>

            <p>
                Configurez les moyens de paiement de votre entreprise.
            </p>
        </div>

    </div>


    @if(session('success'))
        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>
    @endif


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


    <form
        action="{{ route('company.settings.payments.update') }}"
        method="POST"
    >

        @csrf
        @method('PUT')


        {{-- ENVIRONNEMENT --}}

        <div class="module-card payment-settings-card">

            <div class="payment-settings-header">

                <div>
                    <h2>Environnement</h2>

                    <p>
                        Utilisez le mode sandbox pour vos tests.
                    </p>
                </div>

            </div>


            <div class="module-form-group">

                <label for="environment">
                    Environnement
                </label>

                <select
                    id="environment"
                    name="environment"
                >

                    <option
                        value="sandbox"
                        {{ old('environment', $settings->environment) === 'sandbox' ? 'selected' : '' }}
                    >
                        Sandbox / Test
                    </option>

                    <option
                        value="production"
                        {{ old('environment', $settings->environment) === 'production' ? 'selected' : '' }}
                    >
                        Production
                    </option>

                </select>

            </div>

        </div>


        {{-- ORANGE MONEY --}}

        <div class="module-card payment-settings-card">

            <div class="payment-provider-header">

                <!-- <div class="payment-provider-icon">
                    OM
                </div> -->
                <div class="payment-provider-icon">
                    <img
                        src="{{ asset('images/orange-money.png') }}"
                        alt="Orange Money"
                    >
                </div>

                <div class="payment-provider-title">

                    <h2>Orange Money</h2>

                    <p>
                        Paiements Orange Money des clients.
                    </p>

                </div>

                <label class="payment-toggle">

                    <input
                        type="checkbox"
                        name="orange_enabled"
                        value="1"
                        {{ old('orange_enabled', $settings->orange_enabled) ? 'checked' : '' }}
                    >

                    <span></span>

                </label>

            </div>


            <div class="module-form-grid">

                <div class="module-form-group">

                    <label for="orange_client_id">
                        Client ID
                    </label>

                    <input
                        type="text"
                        id="orange_client_id"
                        name="orange_client_id"
                        value="{{ old('orange_client_id', $settings->orange_client_id) }}"
                        autocomplete="off"
                    >

                </div>


                <div class="module-form-group">

                    <label for="orange_merchant_id">
                        Merchant ID
                    </label>

                    <input
                        type="text"
                        id="orange_merchant_id"
                        name="orange_merchant_id"
                        value="{{ old('orange_merchant_id', $settings->orange_merchant_id) }}"
                        autocomplete="off"
                    >

                </div>


                <div class="module-form-group">

                    <label for="orange_client_secret">
                        Client Secret
                    </label>

                    <input
                        type="password"
                        id="orange_client_secret"
                        name="orange_client_secret"
                        placeholder="{{ $settings->orange_client_secret ? 'Secret déjà enregistré — laisser vide pour conserver' : 'Votre Client Secret' }}"
                        autocomplete="new-password"
                    >

                </div>

            </div>

        </div>


        {{-- MTN MOMO --}}

        <div class="module-card payment-settings-card">

            <div class="payment-provider-header">

                <!-- <div class="payment-provider-icon">
                    MTN
                </div> -->

                <div class="payment-provider-icon">
                    <img
                        src="{{ asset('images/mtn-momo.png') }}"
                        alt="MTN Mobile Money"
                    >
                </div>

                <div class="payment-provider-title">

                    <h2>MTN Mobile Money</h2>

                    <p>
                        Paiements MTN Mobile Money des clients.
                    </p>

                </div>

                <label class="payment-toggle">

                    <input
                        type="checkbox"
                        name="mtn_enabled"
                        value="1"
                        {{ old('mtn_enabled', $settings->mtn_enabled) ? 'checked' : '' }}
                    >

                    <span></span>

                </label>

            </div>


            <div class="module-form-grid">

                <div class="module-form-group">

                    <label for="mtn_api_user">
                        API User
                    </label>

                    <input
                        type="text"
                        id="mtn_api_user"
                        name="mtn_api_user"
                        value="{{ old('mtn_api_user', $settings->mtn_api_user) }}"
                        autocomplete="off"
                    >

                </div>


                <div class="module-form-group">

                    <label for="mtn_api_key">
                        API Key
                    </label>

                    <input
                        type="password"
                        id="mtn_api_key"
                        name="mtn_api_key"
                        placeholder="{{ $settings->mtn_api_key ? 'Clé déjà enregistrée — laisser vide pour conserver' : 'Votre API Key' }}"
                        autocomplete="new-password"
                    >

                </div>


                <div class="module-form-group">

                    <label for="mtn_subscription_key">
                        Subscription Key
                    </label>

                    <input
                        type="password"
                        id="mtn_subscription_key"
                        name="mtn_subscription_key"
                        value="{{ old('mtn_subscription_key', $settings->mtn_subscription_key) }}"
                        autocomplete="off"
                    >

                </div>

            </div>

        </div>


        <div class="payment-settings-actions">

            <button
                type="submit"
                class="module-btn module-btn-primary"
            >
                Enregistrer les paramètres
            </button>

        </div>

    </form>

</div>

@endsection