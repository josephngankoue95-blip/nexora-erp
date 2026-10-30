@extends('layouts.website')

@section('title', 'Modèles de sites — WebStudio')

@section('content')
@php
    $icons = [
        'immobilier' => '🏠', 'ecole' => '🏫', 'services' => '💼',
        'commerce' => '🛍️', 'stock' => '📦', 'hotel' => '🏨',
        'restaurant' => '🍽️', 'clinique' => '🩺', 'hopital' => '🏥',
        'pharmacie' => '💊', 'location-vehicules' => '🚗',
        'ressources-humaines' => '👥', 'projets' => '📋',
        'maintenance' => '🛠️', 'documents' => '📁',
        'site-web' => '🌐', 'elearning' => '🎓',
    ];
    $colors = [
        'immobilier' => '#b48a4c', 'ecole' => '#2563eb', 'services' => '#0d9488',
        'commerce' => '#ea580c', 'stock' => '#475569', 'hotel' => '#a16207',
        'restaurant' => '#b45309', 'clinique' => '#0891b2', 'hopital' => '#2563eb',
        'pharmacie' => '#16a34a', 'location-vehicules' => '#dc2626',
        'ressources-humaines' => '#7c3aed', 'projets' => '#4f46e5',
        'maintenance' => '#ca8a04', 'documents' => '#64748b',
        'site-web' => '#4f46e5', 'elearning' => '#9333ea',
    ];
@endphp

<div class="ws-library" id="wsTemplateLibrary">
    <div class="ws-library-head">
        <div>
            <span class="ws-library-kicker">WEBSTUDIO / BIBLIOTHÈQUE</span>
            <h1>Modèles de sites web</h1>
            <p>Choisissez un modèle adapté à votre activité, prévisualisez-le puis installez ses pages.</p>
        </div>
        <a class="ws-library-back" href="{{ route('company.website.pages.index') }}">← Mes pages</a>
    </div>

    @if(session('success'))
        <div class="ws-library-alert ws-library-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="ws-library-alert ws-library-alert-error" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="ws-library-alert ws-library-alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="ws-library-toolbar">
        <label class="ws-library-search">
            <span aria-hidden="true">⌕</span>
            <input id="wsTemplateSearch" type="search" placeholder="Rechercher un modèle, une activité..." aria-label="Rechercher un modèle" autocomplete="off">
        </label>
        <span class="ws-library-count" id="wsTemplateCount">{{ count($templates) }} modèles disponibles</span>
    </div>

    <div class="ws-library-grid" id="wsTemplateGrid">
        @foreach($templates as $key => $template)
            @php
                $accent = $colors[$key] ?? '#4f46e5';
                $icon = $icons[$key] ?? '🌐';
                $pages = $template['pages'] ?? [];
            @endphp
            <article class="ws-library-card" data-search="{{ mb_strtolower($template['name'].' '.$template['description'].' '.implode(' ', $pages)) }}" style="--card-accent: {{ $accent }}">
                <div class="ws-library-card-top">
                    <span class="ws-library-icon" aria-hidden="true">{{ $icon }}</span>
                    <span class="ws-library-page-count">{{ count($pages) }} pages</span>
                </div>
                <h2>{{ $template['name'] }}</h2>
                <p class="ws-library-description">{{ $template['description'] }}</p>
                <div class="ws-library-page-list">
                    <strong>Pages incluses</strong>
                    <ul>
                        @foreach($pages as $title)
                            <li>{{ $title }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="ws-library-actions">
                    <a class="ws-library-preview" href="{{ route('company.website.templates.preview', ['template' => $key]) }}">👁 Prévisualiser</a>
                    <form method="POST" action="{{ route('company.website.templates.install', ['template' => $key]) }}" onsubmit="return confirm('Installer les pages de ce modèle ? Les pages existantes seront conservées.')">
                        @csrf
                        <button class="ws-library-install" type="submit">Utiliser ce modèle →</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div class="ws-library-empty" id="wsTemplateEmpty" hidden>Aucun modèle ne correspond à votre recherche.</div>
    @if(empty($templates))
        <div class="ws-library-empty">Aucun modèle disponible pour le moment.</div>
    @endif
    <p class="ws-library-footnote">Les modèles créent une structure de pages en brouillon. Ils n'activent pas les modules métiers et n'appliquent pas encore automatiquement un thème au site public.</p>
</div>

<style>
.ws-library{max-width:1440px;margin:0 auto;color:#172033}.ws-library *{box-sizing:border-box}.ws-library-head{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-bottom:26px}.ws-library-kicker{display:block;color:#4f46e5;font-size:11px;letter-spacing:1.7px;font-weight:800;margin-bottom:8px}.ws-library-head h1{font-size:clamp(24px,3vw,32px);margin:0 0 8px;font-weight:800}.ws-library-head p{margin:0;color:#64748b;line-height:1.65}.ws-library-back{display:inline-flex;align-items:center;justify-content:center;min-height:43px;padding:10px 16px;border:1px solid #dbe2ea;border-radius:10px;background:#fff;color:#4338ca;text-decoration:none;font-weight:700;font-size:13px}.ws-library-toolbar{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:22px}.ws-library-search{display:flex;align-items:center;gap:10px;width:min(100%,420px);padding:0 14px;border:1px solid #dbe2ea;background:#fff;border-radius:12px}.ws-library-search span{font-size:25px;color:#64748b}.ws-library-search input{height:45px;width:100%;min-width:0;border:0;background:transparent;outline:none;font:inherit;font-size:14px;color:#172033}.ws-library-search:focus-within{outline:2px solid #c7d2fe}.ws-library-count{font-size:13px;color:#64748b}.ws-library-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.ws-library-card{min-width:0;display:flex;flex-direction:column;padding:23px;background:#fff;border:1px solid #e2e8f0;border-radius:17px;box-shadow:0 5px 20px rgba(15,23,42,.045);transition:transform .2s,box-shadow .2s}.ws-library-card:hover{transform:translateY(-3px);box-shadow:0 12px 26px rgba(15,23,42,.09)}.ws-library-card-top{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px}.ws-library-icon{display:grid;place-items:center;width:58px;height:58px;border-radius:15px;background:color-mix(in srgb,var(--card-accent) 12%,white);font-size:29px}.ws-library-page-count{font-size:11px;font-weight:700;color:var(--card-accent);background:#f8fafc;padding:7px 10px;border-radius:20px}.ws-library-card h2{font-size:19px;margin:0 0 8px;line-height:1.3}.ws-library-description{font-size:13px;color:#64748b;line-height:1.65;margin:0 0 17px;min-height:42px}.ws-library-page-list{padding:16px;background:#f8fafc;border-radius:11px;margin-bottom:18px}.ws-library-page-list strong{font-size:12px}.ws-library-page-list ul{margin:9px 0 0;padding-left:19px;font-size:12px;color:#475569;line-height:1.85}.ws-library-page-list li::marker{color:var(--card-accent)}.ws-library-actions{display:grid;gap:10px;margin-top:auto}.ws-library-actions form{margin:0}.ws-library-preview,.ws-library-install{display:flex;align-items:center;justify-content:center;width:100%;min-height:43px;padding:10px 14px;border-radius:9px;font-size:13px;font-weight:750;text-decoration:none;cursor:pointer;text-align:center}.ws-library-preview{border:1px solid #c7d2fe;background:#eef2ff;color:#4338ca}.ws-library-preview:hover{background:#e0e7ff}.ws-library-install{border:0;background:#4f46e5;color:#fff}.ws-library-install:hover{background:#4338ca}.ws-library-alert{padding:14px 17px;border-radius:10px;margin-bottom:20px;font-size:14px}.ws-library-alert-success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}.ws-library-alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}.ws-library-empty{padding:30px;text-align:center;border:1px dashed #cbd5e1;border-radius:12px;color:#64748b;background:#fff}.ws-library-footnote{font-size:12px;line-height:1.7;color:#64748b;margin-top:22px}[hidden]{display:none!important}@media(max-width:1200px){.ws-library-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:680px){.ws-library-grid{grid-template-columns:1fr}.ws-library-head{align-items:stretch}.ws-library-back{width:100%}.ws-library-search{width:100%}.ws-library-card{padding:19px}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('wsTemplateSearch');
    const cards = Array.from(document.querySelectorAll('#wsTemplateGrid .ws-library-card'));
    const count = document.getElementById('wsTemplateCount');
    const empty = document.getElementById('wsTemplateEmpty');
    if (!search) return;
    search.addEventListener('input', function () {
        const query = search.value.toLocaleLowerCase().trim();
        let visible = 0;
        cards.forEach(function (card) {
            const matches = (card.dataset.search || '').includes(query);
            card.hidden = !matches;
            if (matches) visible++;
        });
        count.textContent = visible + ' modèle' + (visible > 1 ? 's' : '') + ' disponible' + (visible > 1 ? 's' : '');
        empty.hidden = visible !== 0;
    });
});
</script>
@endsection
