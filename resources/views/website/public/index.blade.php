@extends('website.public.layout')
@section('title', $company->name . ' — Accueil')
@section('meta_description', $home?->excerpt ?: ('Bienvenue sur le site de ' . $company->name))
@section('content')
@php
    // Un constructeur vide ne remplace jamais le site existant.
    $builderBlocks = $home?->builder_content;
    $hasBuilder = is_array($builderBlocks) && count($builderBlocks) > 0;
@endphp
@if($hasBuilder)
    @include('website.public.blocks', ['builderBlocks' => $builderBlocks])
@else
    @php
        $themeKey = $settings?->theme_key ?? 'services';
        $themeCopy = [
            'immobilier' => ['kicker'=>'IMMOBILIER','title'=>'Votre prochain projet immobilier commence ici','subtitle'=>'Découvrez nos biens et notre accompagnement immobilier.','section'=>'Nos biens et services','intro'=>'Explorez nos offres et nos prestations immobilières.','icon'=>'⌂'],
            'ecole' => ['kicker'=>'ÉDUCATION','title'=>'Grandir, apprendre et réussir ensemble','subtitle'=>'Découvrez notre établissement, nos formations et nos admissions.','section'=>'La vie de notre établissement','intro'=>'Toutes les informations utiles aux familles et aux élèves.','icon'=>'✦'],
            'services' => ['kicker'=>'SERVICES PROFESSIONNELS','title'=>'Des solutions adaptées à vos ambitions','subtitle'=>'Découvrez notre expertise, nos services et nos réalisations.','section'=>'Nos domaines d’expertise','intro'=>'Explorez nos services et découvrez notre entreprise.','icon'=>'✦'],
            'commerce' => ['kicker'=>'COMMERCE','title'=>'Bienvenue dans notre univers','subtitle'=>'Découvrez nos produits et les services de notre commerce.','section'=>'Nos produits et services','intro'=>'Parcourez les différentes rubriques de notre boutique.','icon'=>'◈'],
            'stock' => ['kicker'=>'DISTRIBUTION','title'=>'Votre partenaire en approvisionnement','subtitle'=>'Explorez notre catalogue et nos solutions de distribution.','section'=>'Notre offre commerciale','intro'=>'Retrouvez notre catalogue et nos services.','icon'=>'▣'],
            'hotel' => ['kicker'=>'HÔTELLERIE','title'=>'Un séjour pensé pour votre confort','subtitle'=>'Découvrez nos chambres, nos services et notre établissement.','section'=>'Découvrez notre hôtel','intro'=>'Chambres, prestations et informations pratiques.','icon'=>'✧'],
            'restaurant' => ['kicker'=>'RESTAURATION','title'=>'Le plaisir de se retrouver autour d’une table','subtitle'=>'Découvrez notre carte, nos spécialités et notre ambiance.','section'=>'Notre univers culinaire','intro'=>'Explorez notre carte et nos spécialités.','icon'=>'✳'],
            'clinique' => ['kicker'=>'SANTÉ','title'=>'Votre santé au cœur de nos priorités','subtitle'=>'Découvrez notre clinique et nos spécialités médicales.','section'=>'Nos services de santé','intro'=>'Informations sur notre équipe et nos prestations.','icon'=>'✚'],
            'hopital' => ['kicker'=>'HÔPITAL','title'=>'Des soins et un accompagnement de qualité','subtitle'=>'Découvrez nos départements et services hospitaliers.','section'=>'Nos services hospitaliers','intro'=>'Accédez aux informations de notre établissement.','icon'=>'✚'],
            'pharmacie' => ['kicker'=>'PHARMACIE','title'=>'À vos côtés pour votre bien-être','subtitle'=>'Découvrez notre pharmacie et nos conseils santé.','section'=>'Nos espaces et services','intro'=>'Informations pratiques, produits et conseils.','icon'=>'✚'],
            'location-vehicules' => ['kicker'=>'LOCATION AUTOMOBILE','title'=>'La liberté de voyager à votre rythme','subtitle'=>'Découvrez notre flotte et nos conditions de location.','section'=>'Nos offres de location','intro'=>'Véhicules, tarifs et conditions de location.','icon'=>'➜'],
            'ressources-humaines' => ['kicker'=>'RESSOURCES HUMAINES','title'=>'Les talents au service de votre réussite','subtitle'=>'Découvrez nos solutions RH et nos offres de recrutement.','section'=>'Nos solutions RH','intro'=>'Services, recrutement et opportunités.','icon'=>'♧'],
            'projets' => ['kicker'=>'GESTION DE PROJETS','title'=>'Ensemble, donnons vie à vos projets','subtitle'=>'Découvrez nos projets, réalisations et partenaires.','section'=>'Nos projets et réalisations','intro'=>'Parcourez nos activités et nos résultats.','icon'=>'▤'],
            'maintenance' => ['kicker'=>'MAINTENANCE','title'=>'Des équipements fiables, des activités sereines','subtitle'=>'Découvrez nos prestations et domaines d’intervention.','section'=>'Nos prestations techniques','intro'=>'Nos domaines d’intervention et services.','icon'=>'⚙'],
            'documents' => ['kicker'=>'GESTION DOCUMENTAIRE','title'=>'Simplifiez la gestion de vos documents','subtitle'=>'Découvrez nos solutions de gestion documentaire et d’archivage.','section'=>'Nos solutions documentaires','intro'=>'Découvrez nos offres et notre expertise.','icon'=>'▤'],
            'site-web' => ['kicker'=>'AGENCE DIGITALE','title'=>'Votre présence digitale commence ici','subtitle'=>'Découvrez nos services de création web et nos réalisations.','section'=>'Nos solutions digitales','intro'=>'Création web, accompagnement et réalisations.','icon'=>'◇'],
            'elearning' => ['kicker'=>'FORMATION EN LIGNE','title'=>'Apprenez aujourd’hui, progressez demain','subtitle'=>'Découvrez nos formations, nos cours et nos formateurs.','section'=>'Nos formations et ressources','intro'=>'Explorez nos programmes de formation.','icon'=>'✦'],
        ];
        $copy = $themeCopy[$themeKey] ?? $themeCopy['services'];
        $heroTitle = $settings?->hero_title ?: (($home && $home->title !== 'Accueil') ? $home->title : $copy['title']);
        $heroSubtitle = $settings?->hero_subtitle ?: ($home?->excerpt ?: $copy['subtitle']);
        $heroImage = $settings?->hero_url;
        $otherPages = $pages->where('slug', '!=', 'accueil');
    @endphp
    <section class="ws-hero ws-hero-custom ws-hero-{{ $themeKey }}" @if($heroImage)style="background-image:linear-gradient(rgba(15,23,42,.75),rgba(15,23,42,.55)),url('{{ $heroImage }}');"@endif>
        <div class="ws-container"><span class="ws-kicker">{{ $copy['kicker'] }}</span><h1>{{ $heroTitle }}</h1><p>{{ $heroSubtitle }}</p>
            @if($otherPages->isNotEmpty())<a class="ws-button" href="#pages">Découvrir notre site <span aria-hidden="true">→</span></a>@endif
        </div>
    </section>
    @if($home && filled($home->content))
        <section class="ws-container ws-content"><div class="ws-richtext">{!! \App\Support\WebsiteHtml::clean($home->content) !!}</div></section>
    @endif
    <section class="ws-container ws-section" id="pages"><div class="ws-section-heading"><span class="ws-kicker">{{ $copy['kicker'] }}</span><h2>{{ $copy['section'] }}</h2><p class="ws-section-intro">{{ $copy['intro'] }}</p></div>
        <div class="ws-cards">@forelse($otherPages as $page)
            <article class="ws-card"><span class="ws-card-icon" aria-hidden="true">{{ $copy['icon'] }}</span><h3>{{ $page->title }}</h3><p>{{ $page->excerpt ?: 'En savoir plus sur cette rubrique.' }}</p><a href="{{ route('website.public.show', ['company'=>$company->slug,'slug'=>$page->slug]) }}">Lire la suite →</a></article>
        @empty<p class="ws-empty">Les pages de notre site seront bientôt disponibles.</p>@endforelse</div>
    </section>
@endif
@endsection
