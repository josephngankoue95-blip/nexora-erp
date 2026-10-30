<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Patients') - NEXORA Médical</title>

    @vite([
        'resources/css/medical-patients.css',
        'resources/js/medical-patients.js'
    ])
</head>

<body>

<div class="mp-app">

    {{-- MENU LATÉRAL --}}
    <aside class="mp-sidebar">

        <div class="mp-brand">
            <span class="mp-logo">✚</span>

            <div>
                <strong>NEXORA</strong>
                <small>Gestion médicale</small>
            </div>
        </div>

        <nav class="mp-nav">

            @php
                $medicalDashboard = session(
                    'medical_dashboard',
                    'company.clinic.dashboard'
                );

                if (!in_array($medicalDashboard, [
                    'company.clinic.dashboard',
                    'company.hospital.dashboard'
                ], true)) {
                    $medicalDashboard = 'company.clinic.dashboard';
                }
            @endphp

            {{-- Retour au dashboard --}}
            <a href="{{ route($medicalDashboard) }}"
               class="mp-back-dashboard">
                <span>←</span>
                Retour au tableau de bord
            </a>

            <!-- {{-- Espaces de travail --}}
            <a href="{{ route('company.modules.index') }}">
                Espaces de travail
            </a> -->

            {{-- Patients --}}
            <a href="{{ route('company.medical.patients.index') }}"
               class="{{ request()->routeIs('company.medical.patients.*') ? 'active' : '' }}">
                Patients
            </a>

            <a href="{{ route('company.medical.appointments.index') }}"
            class="{{ request()->routeIs('company.medical.appointments.*') ? 'active' : '' }}">
                Rendez-vous
            </a>

            <a href="{{ route('company.medical.practitioners.index') }}"
            class="{{ request()->routeIs('company.medical.practitioners.*') ? 'active' : '' }}">
                Professionnels de santé
            </a>

            <a href="{{ route('company.medical.consultations.index') }}"
            class="{{ request()->routeIs('company.medical.consultations.*') ? 'active' : '' }}">
                Consultations
            </a>

        </nav>

    </aside>

    {{-- CONTENU PRINCIPAL --}}
    <div class="mp-main">

        {{-- EN-TÊTE --}}
        <header class="mp-header">

            <div>
                <h1>@yield('page-title', 'Patients')</h1>
                <p>Gestion centralisée des patients</p>
            </div>

            <div class="mp-user">
                {{ auth()->user()->name }}
            </div>

        </header>

        {{-- CONTENU --}}
        <main class="mp-content">

            {{-- Messages de succès --}}
            @if(session('success'))
                <div class="mp-alert mp-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Messages d'erreur --}}
            @if($errors->any())
                <div class="mp-alert mp-alert-error">
                    <strong>Veuillez corriger les erreurs :</strong>

                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Affichage des vues enfants --}}
            @yield('content')

        </main>

    </div>

</div>

</body>
</html>