@extends('layouts.website')
@section('title', 'Identité visuelle')
@section('page-title', 'Identité visuelle')
@section('page-description', 'Personnalisez le site public de votre entreprise')
@section('content')
<div class="wst-page">
    @if(session('success')) <p class="wst-success">{{ session('success') }}</p> @endif
    @if($errors->any()) <div class="wst-error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div> @endif
    <form action="{{ route('company.website.settings.update') }}" method="POST" enctype="multipart/form-data" class="wst-form">
        @csrf @method('PUT')
        <section class="wst-card"><h2>Couleurs</h2>
            <label>Couleur principale <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color ?: '#2563eb') }}" required></label>
            <label>Couleur secondaire <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color ?: '#172033') }}" required></label>
        </section>
        <section class="wst-card"><h2>Logo et bannière</h2>
            @if($settings->logo_path)<img class="wst-logo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings->logo_path) }}" alt="Logo actuel">@endif
            <label>Logo (2 Mo max) <input type="file" name="logo" accept="image/jpeg,image/png,image/webp,image/gif"></label>
            <label><input type="checkbox" name="remove_logo" value="1"> Supprimer le logo actuel</label>
            @if($settings->hero_path)<img class="wst-hero" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings->hero_path) }}" alt="Bannière actuelle">@endif
            <label>Bannière (5 Mo max) <input type="file" name="hero" accept="image/jpeg,image/png,image/webp"></label>
            <label><input type="checkbox" name="remove_hero" value="1"> Supprimer la bannière actuelle</label>
        </section>
        <section class="wst-card"><h2>Textes du site</h2>
            <label>Titre de la bannière <input type="text" name="hero_title" maxlength="255" value="{{ old('hero_title', $settings->hero_title) }}"></label>
            <label>Sous-titre <textarea name="hero_subtitle" rows="4" maxlength="2000">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea></label>
            <label>Texte du pied de page <input type="text" name="footer_text" maxlength="255" value="{{ old('footer_text', $settings->footer_text) }}"></label>
        </section>
        <button class="wst-save" type="submit">Enregistrer les modifications</button>
        
{{-- ===================================================
     WEBSTUDIO — ADRESSE PUBLIQUE DU SITE
=================================================== --}}

@php
    $websiteCompany = auth()->user()->company;

    $publicWebsiteUrl = route(
        'website.public.index',
        ['company' => $websiteCompany->slug]
    );
@endphp

<div class="website-public-link-card">

    <div class="website-public-link-header">
        <div>
            <h3>🌐 Adresse publique de votre site</h3>

            <p>
                Copiez ou partagez l'adresse de votre
                site vitrine avec vos visiteurs.
            </p>
        </div>
    </div>

    <div class="website-public-link-field">

        <input
            type="text"
            id="websitePublicUrl"
            value="{{ $publicWebsiteUrl }}"
            readonly
            aria-label="Adresse publique du site"
        >

        <button
            type="button"
            id="websiteCopyUrl"
        >
            Copier le lien
        </button>

    </div>

    <div class="website-public-link-actions">

        <a
            href="{{ $publicWebsiteUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="website-public-open"
        >
            ↗ Ouvrir mon site
        </a>

        <a
            href="https://wa.me/?text={{ rawurlencode('Découvrez notre site web : ' . $publicWebsiteUrl) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="website-public-whatsapp"
        >
            Partager sur WhatsApp
        </a>

    </div>

    <p
        id="websiteCopyFeedback"
        class="website-copy-feedback"
        role="status"
        aria-live="polite"
    ></p>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('websitePublicUrl');
    const button = document.getElementById('websiteCopyUrl');
    const feedback = document.getElementById('websiteCopyFeedback');

    if (!input || !button) {
        return;
    }

    button.addEventListener('click', async function () {

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(input.value);
            } else {
                input.focus();
                input.select();

                const copied = document.execCommand('copy');

                if (!copied) {
                    throw new Error('Copie indisponible');
                }
            }

            button.textContent = '✓ Lien copié';

            if (feedback) {
                feedback.textContent = 'Adresse copiée avec succès.';
            }

        } catch (error) {
            input.focus();
            input.select();

            if (feedback) {
                feedback.textContent =
                    'Sélectionnez le lien puis copiez-le manuellement.';
            }
        }
    });

});
</script>

    </form>
</div>
@endsection
@push('styles')
@vite('resources/css/website-settings.css')
@endpush
