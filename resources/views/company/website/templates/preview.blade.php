@extends('layouts.website')

@section('title', 'Prévisualisation du modèle — WebStudio')

@section('content')
@php
    $themes = [
        'immobilier' => ['#b48a4c','#18263b','🏡','L’adresse de vos nouveaux projets','Découvrez des biens et un accompagnement immobilier sur mesure.','Nos biens à découvrir'],
        'ecole' => ['#2563eb','#142d55','🎓','Grandir, apprendre et réussir','Un établissement tourné vers la réussite et l’épanouissement des élèves.','Notre établissement'],
        'services' => ['#0d9488','#123b40','🤝','Votre partenaire de confiance','Des solutions professionnelles adaptées à vos ambitions.','Notre expertise'],
        'commerce' => ['#ea580c','#292524','🛍️','Des produits qui vous ressemblent','Découvrez notre sélection et nos services pour votre quotidien.','Notre sélection'],
        'stock' => ['#475569','#172033','📦','Des produits, des solutions, de la fiabilité','Découvrez notre catalogue et notre savoir-faire en distribution.','Notre catalogue'],
        'hotel' => ['#a16207','#302517','🏨','Le confort au cœur de votre séjour','Un accueil chaleureux et des espaces pensés pour vous.','Nos chambres et services'],
        'restaurant' => ['#b45309','#3a2418','🍽️','Le plaisir de bien manger','Une cuisine généreuse, des saveurs à partager.','Découvrez notre carte'],
        'clinique' => ['#0891b2','#123c4a','🩺','Votre santé, notre priorité','Une équipe à votre écoute et des soins de proximité.','Nos spécialités'],
        'hopital' => ['#2563eb','#152e52','🏥','Des soins au service de chacun','Découvrez nos départements et nos services médicaux.','Nos services de santé'],
        'pharmacie' => ['#16a34a','#173c2b','💊','Votre bien-être au quotidien','Conseils, accompagnement et produits de santé.','Nos produits et conseils'],
        'location-vehicules' => ['#dc2626','#292524','🚗','Prenez la route en toute liberté','Choisissez le véhicule adapté à chaque déplacement.','Découvrez notre flotte'],
        'ressources-humaines' => ['#7c3aed','#30204c','👥','Le talent au cœur de votre réussite','Recrutement et accompagnement des organisations.','Nos solutions RH'],
        'projets' => ['#4f46e5','#202348','📋','Donnons vie à vos projets','Des idées, des équipes et des réalisations concrètes.','Nos réalisations'],
        'maintenance' => ['#ca8a04','#33301c','🛠️','Des équipements toujours performants','Maintenance, assistance et interventions techniques.','Nos prestations'],
        'documents' => ['#64748b','#1e293b','📁','Organisez et valorisez vos documents','Des solutions fiables pour votre information et vos archives.','Nos solutions'],
        'site-web' => ['#4f46e5','#211b4d','🌐','Votre présence digitale commence ici','Des expériences web modernes pour faire rayonner votre activité.','Nos créations'],
        'elearning' => ['#9333ea','#321a4c','🎓','Apprenez à votre rythme','Des formations accessibles pour développer vos compétences.','Nos formations'],
    ];
    $theme = $themes[$templateKey] ?? ['#4f46e5','#172033','🌐','Bienvenue sur notre site','Découvrez nos activités et nos services.','Nos activités'];
    [$accent, $dark, $icon, $headline, $intro, $sectionTitle] = $theme;
    $pages = $template['pages'] ?? [];
    $secondaryPages = array_slice($pages, 1, 3, true);
@endphp

<div class="ws-preview" style="--preview-accent:{{ $accent }};--preview-dark:{{ $dark }}">
    <div class="ws-preview-toolbar">
        <div>
            <span class="ws-preview-kicker">WEBSTUDIO / APERÇU</span>
            <h1>{{ $template['name'] }}</h1>
            <p>{{ $template['description'] }}</p>
        </div>
        <div class="ws-preview-toolbar-actions">
            <a href="{{ route('company.website.templates.index') }}" class="ws-preview-return">← Tous les modèles</a>
            <form method="POST" action="{{ route('company.website.templates.install', ['template' => $templateKey]) }}" onsubmit="return confirm('Installer les pages de ce modèle ? Les pages existantes seront conservées.')">
                @csrf
                <button type="submit" class="ws-preview-install">Utiliser ce modèle →</button>
            </form>
        </div>
    </div>

    <div class="ws-preview-device-switch" role="group" aria-label="Taille de prévisualisation">
        <button type="button" class="ws-preview-device is-active" data-device="desktop" aria-pressed="true">▣ Ordinateur</button>
        <button type="button" class="ws-preview-device" data-device="tablet" aria-pressed="false">▤ Tablette</button>
        <button type="button" class="ws-preview-device" data-device="mobile" aria-pressed="false">▯ Mobile</button>
    </div>

    <div class="ws-preview-stage">
        <div class="ws-preview-browser" id="wsPreviewBrowser" data-device="desktop">
            <div class="ws-preview-browser-bar" aria-hidden="true">
                <span class="ws-preview-dots"><i></i><i></i><i></i></span>
                <span class="ws-preview-address">Aperçu fictif — {{ $template['name'] }}</span>
            </div>
            <div class="ws-preview-site">
                <header class="ws-preview-site-header">
                    <div class="ws-preview-brand"><span class="ws-preview-brand-icon">{{ $icon }}</span><strong>Votre entreprise</strong></div>
                    <nav class="ws-preview-nav" aria-label="Pages du modèle">
                        @foreach(array_slice($pages, 0, 4) as $label)
                            <span>{{ $label }}</span>
                        @endforeach
                    </nav>
                </header>
                <section class="ws-preview-hero">
                    <div class="ws-preview-hero-content">
                        <span class="ws-preview-eyebrow">{{ mb_strtoupper($template['name']) }}</span>
                        <h2>{{ $headline }}</h2>
                        <p>{{ $intro }}</p>
                        <span class="ws-preview-cta">Découvrir nos services <span aria-hidden="true">→</span></span>
                    </div>
                    <div class="ws-preview-hero-art" aria-hidden="true"><div class="ws-preview-art-circle"><span>{{ $icon }}</span></div><div class="ws-preview-art-badge">★ Qualité & confiance</div></div>
                </section>
                <section class="ws-preview-section">
                    <div class="ws-preview-section-title"><span>DÉCOUVREZ NOTRE UNIVERS</span><h3>{{ $sectionTitle }}</h3><p>Une présentation soignée de votre activité et de vos points forts.</p></div>
                    <div class="ws-preview-cards">
                        @forelse($secondaryPages as $slug => $label)
                            <article class="ws-preview-demo-card"><span class="ws-preview-card-number">{{ str_pad((string)$loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h4>{{ $label }}</h4><p>Présentez ici vos informations, vos réalisations et les atouts de votre activité.</p><span class="ws-preview-card-link">En savoir plus →</span></article>
                        @empty
                            <article class="ws-preview-demo-card"><h4>Votre activité</h4><p>Présentez ici votre entreprise.</p></article>
                        @endforelse
                    </div>
                </section>
                <section class="ws-preview-contact"><div><h3>Parlons de votre projet</h3><p>Un espace pour présenter vos coordonnées et inviter vos visiteurs à vous contacter.</p></div><span>Nous contacter →</span></section>
                <footer class="ws-preview-footer"><strong>Votre entreprise</strong><span>© {{ date('Y') }} · Démonstration WebStudio</span></footer>
            </div>
        </div>
    </div>
    <p class="ws-preview-note">Cette maquette est une démonstration visuelle. Le bouton « Utiliser ce modèle » crée les pages en brouillon sans appliquer automatiquement ce design au site public. Les réservations, commandes et autres fonctionnalités métiers ne sont pas créées par ce modèle.</p>
</div>

<style>
.ws-preview{max-width:1500px;margin:0 auto;color:#172033}.ws-preview *{box-sizing:border-box}.ws-preview-toolbar{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-bottom:22px}.ws-preview-kicker{font-size:11px;letter-spacing:1.6px;font-weight:800;color:#4f46e5}.ws-preview-toolbar h1{font-size:clamp(23px,3vw,30px);margin:7px 0}.ws-preview-toolbar p{margin:0;color:#64748b;font-size:14px}.ws-preview-toolbar-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.ws-preview-toolbar-actions form{margin:0}.ws-preview-return,.ws-preview-install{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:11px 16px;border-radius:9px;font-size:13px;font-weight:750;text-decoration:none}.ws-preview-return{border:1px solid #dbe2ea;background:#fff;color:#334155}.ws-preview-install{border:0;background:#4f46e5;color:#fff;cursor:pointer}.ws-preview-install:hover{background:#4338ca}.ws-preview-device-switch{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:18px}.ws-preview-device{padding:9px 15px;border:1px solid #dbe2ea;border-radius:9px;background:#fff;color:#475569;font:inherit;font-size:12px;font-weight:700;cursor:pointer}.ws-preview-device.is-active{background:#eef2ff;color:#4338ca;border-color:#a5b4fc}.ws-preview-stage{padding:20px;background:#e9edf4;border:1px solid #dbe2ea;border-radius:18px;overflow-x:auto}.ws-preview-browser{width:100%;max-width:100%;margin:0 auto;background:#fff;border:1px solid #dbe2ea;border-radius:13px;overflow:hidden;box-shadow:0 16px 40px rgba(15,23,42,.1);transition:max-width .25s}.ws-preview-browser[data-device=tablet]{max-width:768px}.ws-preview-browser[data-device=mobile]{max-width:390px}.ws-preview-browser-bar{display:flex;align-items:center;gap:12px;padding:11px 14px;background:#f8fafc;border-bottom:1px solid #e2e8f0}.ws-preview-dots{display:flex;gap:6px}.ws-preview-dots i{display:block;width:9px;height:9px;border-radius:50%;background:#fb7185}.ws-preview-dots i:nth-child(2){background:#fbbf24}.ws-preview-dots i:nth-child(3){background:#4ade80}.ws-preview-address{flex:1;text-align:center;background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:5px 8px;color:#94a3b8;font-size:10px}.ws-preview-site{font-family:Arial,sans-serif;background:#fff}.ws-preview-site-header{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:21px 5%}.ws-preview-brand{display:flex;align-items:center;gap:10px;font-size:14px;color:var(--preview-dark)}.ws-preview-brand-icon{display:grid;place-items:center;width:39px;height:39px;border-radius:10px;background:var(--preview-accent);font-size:22px}.ws-preview-nav{display:flex;gap:16px;flex-wrap:wrap;color:#475569;font-size:11px}.ws-preview-hero{display:grid;grid-template-columns:1.2fr .8fr;gap:20px;align-items:center;min-height:380px;padding:65px 6%;background:var(--preview-dark);overflow:hidden}.ws-preview-eyebrow{color:#f8dfa4;letter-spacing:1.5px;font-size:10px;font-weight:800}.ws-preview-hero h2{font-size:clamp(27px,3.4vw,49px);line-height:1.13;color:#fff;margin:15px 0}.ws-preview-hero p{font-size:14px;color:#d9e1ee;line-height:1.8;max-width:510px}.ws-preview-cta{display:inline-flex;gap:10px;margin-top:16px;padding:13px 19px;background:var(--preview-accent);border-radius:8px;color:#fff;font-weight:700;font-size:12px}.ws-preview-hero-art{position:relative;display:grid;place-items:center;min-height:250px;border:1px solid rgba(255,255,255,.17);border-radius:23px;background:rgba(255,255,255,.07)}.ws-preview-art-circle{display:grid;place-items:center;width:190px;height:190px;border-radius:50%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.16)}.ws-preview-art-circle span{font-size:90px}.ws-preview-art-badge{position:absolute;bottom:15px;right:10px;padding:10px 12px;border-radius:8px;background:#fff;color:var(--preview-dark);font-size:11px;font-weight:800;box-shadow:0 5px 20px rgba(0,0,0,.1)}.ws-preview-section{padding:65px 6%}.ws-preview-section-title{text-align:center;margin-bottom:30px}.ws-preview-section-title>span{color:var(--preview-accent);font-size:10px;font-weight:800;letter-spacing:1.5px}.ws-preview-section-title h3{font-size:29px;color:var(--preview-dark);margin:12px 0}.ws-preview-section-title p{font-size:13px;color:#64748b}.ws-preview-cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:17px}.ws-preview-demo-card{padding:23px;border:1px solid #e2e8f0;border-radius:13px}.ws-preview-card-number{font-size:24px;color:var(--preview-accent);font-weight:800}.ws-preview-demo-card h4{font-size:17px;margin:14px 0 8px;color:var(--preview-dark)}.ws-preview-demo-card p{font-size:12px;color:#64748b;line-height:1.7}.ws-preview-card-link{font-size:11px;color:var(--preview-accent);font-weight:800}.ws-preview-contact{display:flex;justify-content:space-between;align-items:center;gap:18px;padding:34px 6%;background:#f8fafc}.ws-preview-contact h3{margin:0 0 8px;font-size:23px;color:var(--preview-dark)}.ws-preview-contact p{margin:0;font-size:12px;color:#64748b;line-height:1.7}.ws-preview-contact>span{flex-shrink:0;background:var(--preview-accent);color:#fff;padding:12px 16px;border-radius:8px;font-size:12px;font-weight:700}.ws-preview-footer{display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;padding:23px 6%;background:var(--preview-dark);color:#fff;font-size:11px}.ws-preview-note{font-size:12px;color:#64748b;line-height:1.7;margin-top:16px}.ws-preview-browser[data-device=tablet] .ws-preview-nav,.ws-preview-browser[data-device=mobile] .ws-preview-nav{display:none}.ws-preview-browser[data-device=mobile] .ws-preview-hero{grid-template-columns:1fr;padding:35px 7%;min-height:auto}.ws-preview-browser[data-device=mobile] .ws-preview-hero-art{min-height:155px}.ws-preview-browser[data-device=mobile] .ws-preview-art-circle{width:120px;height:120px}.ws-preview-browser[data-device=mobile] .ws-preview-art-circle span{font-size:60px}.ws-preview-browser[data-device=mobile] .ws-preview-cards{grid-template-columns:1fr}.ws-preview-browser[data-device=mobile] .ws-preview-section{padding:40px 6%}.ws-preview-browser[data-device=mobile] .ws-preview-contact{flex-direction:column;align-items:flex-start}.ws-preview-browser[data-device=tablet] .ws-preview-cards{grid-template-columns:repeat(2,minmax(0,1fr))}@media(max-width:800px){.ws-preview-hero{grid-template-columns:1fr;padding:45px 6%}.ws-preview-hero-art{min-height:175px}.ws-preview-cards{grid-template-columns:1fr}.ws-preview-nav{display:none}.ws-preview-contact{flex-direction:column;align-items:flex-start}}@media(max-width:600px){.ws-preview-stage{padding:8px}.ws-preview-toolbar-actions,.ws-preview-toolbar-actions form,.ws-preview-return,.ws-preview-install{width:100%}.ws-preview-device{padding:9px;font-size:11px}.ws-preview-section{padding:40px 6%}.ws-preview-hero h2{font-size:29px}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const browser = document.getElementById('wsPreviewBrowser');
    const buttons = document.querySelectorAll('.ws-preview-device');
    if (!browser) return;
    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            browser.dataset.device = button.dataset.device;
            buttons.forEach(function (item) {
                const active = item === button;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        });
    });
});
</script>
@endsection
