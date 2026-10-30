@extends('company.school.principal.layouts.app')
@section('title', 'Tableau de bord')
@section('content')
<section class="welcome"><div><span class="eyebrow">PILOTAGE PÉDAGOGIQUE</span><h1>Tableau de bord du Proviseur</h1><p>Bienvenue dans votre espace de supervision scolaire.</p></div><div class="year">{{ $year->name ?? 'Aucune année active' }}</div></section>
<section class="stats"><article class="stat"><span>Élèves inscrits</span><strong>{{ number_format($stats['students'] ?? 0,0,',',' ') }}</strong><small>Année scolaire active</small></article><article class="stat"><span>Classes</span><strong>{{ number_format($stats['classes'] ?? 0,0,',',' ') }}</strong><small>Année scolaire active</small></article><article class="stat"><span>Enseignants actifs</span><strong>{{ number_format($stats['teachers'] ?? 0,0,',',' ') }}</strong><small>Établissement</small></article></section>
<section class="panel"><h2>Vue d'ensemble</h2><p>Les rubriques Classes, Matières et Affectations permettent de consulter les informations pédagogiques de votre établissement.</p></section>
@endsection
