@extends('layouts.website')

@section('title', $page->exists ? 'Modifier une page' : 'Créer une page')
@section('page-title', $page->exists ? 'Modifier une page' : 'Nouvelle page')
@section('page-description', 'Rédigez le contenu et choisissez le statut de publication.')

@section('content')
    @include('company.website.pages.styles')

    <div class="wp-wrap">
        @if ($errors->any())
            <div class="wp-alert wp-alert-error" role="alert">
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="wp-form" method="POST"
              action="{{ $page->exists ? route('company.website.pages.update', $page->id) : route('company.website.pages.store') }}">
            @csrf
            @if ($page->exists)
                @method('PUT')
            @endif

            <div class="wp-form-grid">
                <section class="wp-panel" aria-labelledby="wp-content-heading">
                    <div class="wp-panel-heading">
                        <h2 id="wp-content-heading">Contenu de la page</h2>
                        <p>Définissez le titre, l'adresse et le contenu visible par vos visiteurs.</p>
                    </div>

                    <label for="title">Titre <span class="wp-required">*</span></label>
                    <input type="text" id="title" name="title" maxlength="180" required
                           value="{{ old('title', $page->title) }}" placeholder="Ex. : À propos de nous">
                    @error('title') <small class="wp-field-error">{{ $message }}</small> @enderror

                    <label for="slug">Adresse de la page (slug)</label>
                    <input type="text" id="slug" name="slug" maxlength="180"
                           value="{{ old('slug', $page->slug) }}" placeholder="Ex. : a-propos">
                    <p class="wp-hint">Laissez vide pour générer automatiquement l'adresse à partir du titre.</p>
                    @error('slug') <small class="wp-field-error">{{ $message }}</small> @enderror

                    <label for="excerpt">Résumé</label>
                    <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"
                              placeholder="Courte présentation de la page...">{{ old('excerpt', $page->excerpt) }}</textarea>
                    @error('excerpt') <small class="wp-field-error">{{ $message }}</small> @enderror

                    {{-- Ce composant fournit la barre d'outils ET l'unique champ name="content". --}}
                    @include('company.website.pages.editor')
                </section>

                <aside class="wp-panel" aria-labelledby="wp-publication-heading">
                    <div class="wp-panel-heading">
                        <h2 id="wp-publication-heading">Publication</h2>
                        <p>Contrôlez la visibilité de votre page.</p>
                    </div>

                    <label for="status">Statut <span class="wp-required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="draft" @selected(old('status', $page->status ?: 'draft') === 'draft')>Brouillon</option>
                        <option value="published" @selected(old('status', $page->status) === 'published')>Publiée</option>
                    </select>
                    @error('status') <small class="wp-field-error">{{ $message }}</small> @enderror

                    <label class="wp-check" for="show_in_menu">
                        <input type="checkbox" id="show_in_menu" name="show_in_menu" value="1"
                               @checked((bool) old('show_in_menu', $page->show_in_menu))>
                        <span>Afficher dans le menu du site</span>
                    </label>

                    <label for="sort_order">Ordre d'affichage</label>
                    <input type="number" id="sort_order" name="sort_order" min="0"
                           value="{{ old('sort_order', $page->sort_order ?? 0) }}">
                    @error('sort_order') <small class="wp-field-error">{{ $message }}</small> @enderror

                    <div class="wp-form-actions">
                        <button class="wp-btn" type="submit">
                            {{ $page->exists ? 'Enregistrer les modifications' : 'Créer la page' }}
                        </button>

                        @if ($page->exists)
                            <a href="{{ route('company.website.pages.preview', $page->id) }}"
                               target="_blank" rel="noopener noreferrer" class="website-preview-button">
                                👁 Aperçu de la page
                            </a>
                        @endif

                        <a class="wp-cancel" href="{{ route('company.website.pages.index') }}">Annuler</a>
                    </div>
                </aside>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    @vite('resources/css/website-editor.css')
    @vite('resources/css/website-media-picker.css')
    @vite('resources/css/website-buttons.css')
@endpush

@push('scripts')
    @vite('resources/js/website-editor.js')
@endpush
