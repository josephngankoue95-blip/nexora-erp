
@extends('layouts.school')

@section('title', 'Paramètres de l’établissement')
@section('page-title', 'Paramètres de l’établissement')
@section('page-description', 'Identité, coordonnées et configuration de votre école')

@push('styles')
    @vite('resources/css/school-settings.css')
@endpush

@section('content')
<div class="school-settings-page">

    @if(session('success'))
        <div class="ss-alert ss-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="ss-alert ss-error" role="alert">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ss-intro">
        <div>
            <span class="ss-eyebrow">CONFIGURATION SCOLAIRE</span>
            <h2>Informations de l'établissement</h2>
            <p>
                Personnalisez les informations officielles utilisées
                dans les documents de votre école.
            </p>
        </div>
        <span class="ss-company">
            Entreprise : {{ $company->name }}
        </span>
    </div>

    <form
        method="POST"
        action="{{ route('company.school.settings.update') }}"
        enctype="multipart/form-data"
        class="ss-form"
    >
        @csrf
        @method('PUT')

        {{-- IDENTITÉ --}}
        <section class="ss-panel">
            <div class="ss-panel-heading">
                <span class="ss-step">01</span>
                <div>
                    <h3>Identité de l'établissement</h3>
                    <p>Informations administratives et officielles</p>
                </div>
            </div>

            <div class="ss-grid">
                <label class="ss-field ss-wide">
                    <span>Nom officiel de l'établissement *</span>
                    <input type="text" name="school_name"
                        value="{{ old('school_name', $settings->school_name) }}"
                        maxlength="255" required>
                </label>

                <label class="ss-field">
                    <span>Code de l'établissement</span>
                    <input type="text" name="school_code"
                        value="{{ old('school_code', $settings->school_code) }}">
                </label>

                <label class="ss-field">
                    <span>Type d'établissement</span>
                    <select name="school_type">
                        <option value="">Sélectionner</option>
                        @foreach([
                            'public' => 'Public',
                            'private' => 'Privé',
                            'other' => 'Autre'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('school_type', $settings->school_type) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="ss-field">
                    <span>Numéro d'autorisation</span>
                    <input type="text" name="authorization_number"
                        value="{{ old('authorization_number', $settings->authorization_number) }}">
                </label>

                <label class="ss-field">
                    <span>Devise de l'établissement</span>
                    <input type="text" name="motto"
                        value="{{ old('motto', $settings->motto) }}">
                </label>
            </div>
        </section>

        {{-- LOGO ET CACHET --}}
        <section class="ss-panel">
            <div class="ss-panel-heading">
                <span class="ss-step">02</span>
                <div>
                    <h3>Logo et cachet</h3>
                    <p>Images utilisées dans les documents scolaires</p>
                </div>
            </div>

            <div class="ss-grid">
                @foreach([
                    'logo' => 'Logo de l’établissement',
                    'stamp' => 'Cachet officiel'
                ] as $field => $label)
                    <div class="ss-upload">
                        <strong>{{ $label }}</strong>

                        <div class="ss-preview">
                            @if($settings->{$field})
                                <img
                                    id="preview-{{ $field }}"
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings->{$field}) }}"
                                    alt="{{ $label }}"
                                >
                            @else
                                <img
                                    id="preview-{{ $field }}"
                                    alt="{{ $label }}"
                                    hidden
                                >
                            @endif
                        </div>

                        <input
                            type="file"
                            name="{{ $field }}"
                            accept=".jpg,.jpeg,.png,.webp"
                            data-preview="preview-{{ $field }}"
                        >

                        <small>JPG, PNG ou WEBP — 2 Mo maximum.</small>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- COORDONNÉES --}}
        <section class="ss-panel">
            <div class="ss-panel-heading">
                <span class="ss-step">03</span>
                <div>
                    <h3>Coordonnées et localisation</h3>
                    <p>Adresse et moyens de contact</p>
                </div>
            </div>

            <div class="ss-grid">
                @foreach([
                    'country' => 'Pays',
                    'region' => 'Région',
                    'city' => 'Ville',
                    'address' => 'Adresse / Quartier',
                    'phone' => 'Téléphone',
                    'email' => 'Adresse email',
                    'website' => 'Site web'
                ] as $field => $label)
                    <label class="ss-field">
                        <span>{{ $label }} {{ $field === 'country' ? '*' : '' }}</span>
                        <input
                            type="{{ $field === 'email' ? 'email' : ($field === 'website' ? 'url' : 'text') }}"
                            name="{{ $field }}"
                            value="{{ old($field, $settings->{$field}) }}"
                            @if($field === 'country') required @endif
                            @if($field === 'website') placeholder="https://exemple.com" @endif
                        >
                    </label>
                @endforeach
            </div>
        </section>

        {{-- RESPONSABLES --}}
        <section class="ss-panel">
            <div class="ss-panel-heading">
                <span class="ss-step">04</span>
                <div>
                    <h3>Responsables de l'établissement</h3>
                    <p>Noms des responsables administratifs</p>
                </div>
            </div>

            <div class="ss-grid">
                <label class="ss-field">
                    <span>Directeur / Directrice</span>
                    <input type="text" name="director_name"
                        value="{{ old('director_name', $settings->director_name) }}">
                </label>

                <label class="ss-field">
                    <span>Proviseur / Principal</span>
                    <input type="text" name="principal_name"
                        value="{{ old('principal_name', $settings->principal_name) }}">
                </label>
            </div>
        </section>

        {{-- PRÉFÉRENCES --}}
        <section class="ss-panel">
            <div class="ss-panel-heading">
                <span class="ss-step">05</span>
                <div>
                    <h3>Configuration pédagogique</h3>
                    <p>Système éducatif et documents scolaires</p>
                </div>
            </div>

            <div class="ss-grid">
                <label class="ss-field">
                    <span>Système éducatif *</span>
                    <select name="academic_system" required>
                        @foreach([
                            'francophone' => 'Francophone',
                            'anglophone' => 'Anglophone',
                            'both' => 'Francophone et anglophone'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('academic_system', $settings->academic_system) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="ss-field">
                    <span>Barème de notation *</span>
                    <select name="grading_scale" required>
                        @foreach([20, 100] as $scale)
                            <option value="{{ $scale }}"
                                @selected((int) old('grading_scale', $settings->grading_scale) === $scale)>
                                Sur {{ $scale }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="ss-field ss-wide">
                    <span>Texte de pied de page des bulletins</span>
                    <textarea name="bulletin_footer" rows="4"
                        maxlength="2000">{{ old('bulletin_footer', $settings->bulletin_footer) }}</textarea>
                </label>
            </div>
        </section>

        <div class="ss-actions">
            <span>Les modifications concernent uniquement votre établissement.</span>
            <button type="submit" class="ss-submit">
                Enregistrer les paramètres
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    @vite('resources/js/school-settings.js')
@endpush
