@extends('layouts.website')
@section('title', 'Personnaliser : ' . $page->title)
@section('page-title', 'WebStudio · ' . $page->title)
@section('page-description', 'Créez une expérience web unique, section par section.')
@section('content')
<div class="wb2" id="wb-builder" data-media-list="{{ route('company.website.media.index') }}" data-media-upload="{{ route('company.website.media.store') }}">
    @if(session('success'))<div class="wb2-alert" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="wb2-error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <form id="wb-form" method="POST" action="{{ route('company.website.builder.update', $page->id) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="builder_content" id="wb-json">
        <header class="wb2-bar">
            <div class="wb2-brand"><a class="wb2-back" href="{{ route('company.website.pages.index') }}" aria-label="Retour aux pages">←</a><span class="wb2-brand-icon">✦</span><div><strong>WebStudio</strong><small>{{ $page->title }}</small></div></div>
            <div class="wb2-devices" role="group" aria-label="Format de prévisualisation">
                <button type="button" class="active" data-device="desktop" title="Ordinateur">▣ <span>Desktop</span></button>
                <button type="button" data-device="tablet" title="Tablette">▤</button>
                <button type="button" data-device="mobile" title="Mobile">▯</button>
            </div>
            <div class="wb2-actions"><button type="button" id="wb-undo" title="Annuler">↶</button><button type="button" id="wb-redo" title="Rétablir">↷</button><select name="status" aria-label="Statut"><option value="draft" @selected($page->status === 'draft')>Brouillon</option><option value="published" @selected($page->status === 'published')>Publiée</option></select><button type="submit" class="wb2-save">✓ Enregistrer</button></div>
        </header>
        <div class="wb2-layout">
            <aside class="wb2-library" aria-label="Bibliothèque de composants"><div class="wb2-panel-head"><span class="wb2-eyebrow">COMPOSANTS</span><h2>Bibliothèque</h2><p>Composez votre site en quelques clics.</p></div><label class="wb2-search-wrap"><span>⌕</span><input type="search" id="wb-search" placeholder="Rechercher un bloc…" aria-label="Rechercher un élément"></label><div id="wb-categories"></div><p class="wb2-tip">✦ Cliquez ou glissez un bloc dans la page. Utilisez ⠿ pour le déplacer.</p></aside>
            <main class="wb2-workspace"><div class="wb2-workspace-top"><span><span class="wb2-live-dot"></span> ÉDITEUR VISUEL</span><span id="wb-device-label">100% · Ordinateur</span></div><div class="wb2-stage"><div id="wb-canvas" class="wb2-canvas" aria-label="Aperçu des blocs"></div></div><div class="wb2-foot">Astuce : sélectionnez un bloc pour modifier ses propriétés · ⠿ déplacer · ↑ ↓ réorganiser</div></main>
            <aside class="wb2-inspector" id="wb-properties" aria-label="Propriétés du bloc"><div class="wb2-panel-head"><span class="wb2-eyebrow">PERSONNALISATION</span><h2>Propriétés</h2><p>Sélectionnez un élément dans la page.</p></div></aside>
        </div>
    </form>
</div>
<div class="wb2-media-modal" id="wb-media-modal" hidden role="dialog" aria-modal="true" aria-label="Médiathèque">
  <div class="wb2-media-dialog"><header><div><strong>Médiathèque NEXORA</strong><p>Choisissez une image ou importez-la depuis votre ordinateur.</p></div><button type="button" id="wb-media-close" aria-label="Fermer">✕</button></header>
  <label class="wb2-upload-zone">＋ Importer une image <input type="file" id="wb-media-upload" accept="image/jpeg,image/png,image/webp,image/gif" hidden></label>
  <p id="wb-media-message" role="status"></p><div id="wb-media-grid" class="wb2-media-grid"></div></div>
</div>
<script type="application/json" id="wb-initial">@json($page->builder_content ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>
@vite(['resources/css/website-builder.css', 'resources/js/website-builder.js'])
@endsection
