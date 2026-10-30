<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>@yield('title', 'Espace Proviseur') — NEXORA</title>
@vite(['resources/css/school-principal.css', 'resources/js/school-principal.js'])</head>
<body><div class="shell">
@include('company.school.principal.partials.sidebar')
<div class="main"><header class="topbar"><button class="sidebar-toggle" id="sidebarToggle" type="button" aria-controls="principalSidebar" aria-expanded="false" aria-label="Ouvrir le menu">☰</button><span>Direction de l'établissement</span><span class="account">{{ auth()->user()->name ?? 'Proviseur' }}</span></header>
<main class="page-content">@yield('content')</main></div></div>
<div class="sidebar-overlay" id="sidebarOverlay" hidden></div>
</body></html>
