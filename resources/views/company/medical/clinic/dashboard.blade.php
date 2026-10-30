<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tableau de bord Clinique - NEXORA</title>

    @vite([
        'resources/css/medical-dashboard.css',
        'resources/js/medical-dashboard.js'
    ])
</head>

<body>

<div class="medical-app">

    {{-- MENU LATÉRAL --}}
    <aside class="medical-sidebar" id="medicalSidebar">

        <div class="medical-brand">
            <div class="brand-icon">✚</div>

            <div>
                <strong>NEXORA</strong>
                <small>Gestion Clinique</small>
            </div>
        </div>

        <div class="medical-sidebar-label">
            ESPACE MÉDICAL
        </div>

        <nav class="medical-nav">

            <a href="{{ route('company.clinic.dashboard') }}"
               class="medical-nav-link {{ request()->routeIs('company.clinic.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">▦</span>
                Tableau de bord
            </a>

            <div class="nav-heading">GESTION MÉDICALE</div>

            <a href="{{ route('company.medical.patients.index') }}"
               class="medical-nav-link">
                <span class="nav-icon">♙</span>
                Patients
            </a>

            <a href="{{ route('company.medical.appointments.index') }}"
               class="medical-nav-link">
                <span class="nav-icon">▣</span>
                Rendez-vous
            </a>

            <a href="{{ route('company.medical.practitioners.index') }}"
            class="medical-nav-link">
                <span class="nav-icon">♙</span>
                Professionnels de santé
            </a>

            <a href="{{ route('company.medical.consultations.index') }}"
            class="medical-nav-link">
                <span class="nav-icon">✚</span>
                Consultations
            </a>

            <span class="medical-nav-link nav-disabled">
                <span class="nav-icon">▤</span>
                Dossiers médicaux
                <small>Bientôt</small>
            </span>

            <span class="medical-nav-link nav-disabled">
                <span class="nav-icon">▧</span>
                Ordonnances
                <small>Bientôt</small>
            </span>

            <span class="medical-nav-link nav-disabled">
                <span class="nav-icon">◈</span>
                Laboratoire
                <small>Bientôt</small>
            </span>

            <span class="medical-nav-link nav-disabled">
                <span class="nav-icon">▥</span>
                Pharmacie
                <small>Bientôt</small>
            </span>

            <div class="nav-heading">ADMINISTRATION</div>

            <span class="medical-nav-link nav-disabled">
                <span class="nav-icon">▤</span>
                Facturation
                <small>Bientôt</small>
            </span>

            <span class="medical-nav-link nav-disabled">
                <span class="nav-icon">▥</span>
                Rapports
                <small>Bientôt</small>
            </span>

            <span class="medical-nav-link nav-disabled">
                <span class="nav-icon">⚙</span>
                Paramètres
                <small>Bientôt</small>
            </span>

        </nav>

        <div class="medical-sidebar-footer">
            <a href="{{ route('company.modules.index') }}"
               class="back-link">
                <span>←</span>
                Retour aux modules
            </a>
        </div>

    </aside>

    {{-- FOND MOBILE --}}
    <div class="medical-overlay" id="medicalOverlay"></div>

    {{-- CONTENU PRINCIPAL --}}
    <div class="medical-main">

        <header class="medical-header">

            <div class="header-left">

                <button type="button"
                        class="menu-toggle"
                        id="medicalMenuToggle"
                        aria-label="Ouvrir le menu"
                        aria-controls="medicalSidebar"
                        aria-expanded="false">
                    ☰
                </button>

                <div>
                    <div class="header-breadcrumb">
                        Espace médical / Clinique
                    </div>

                    <h1>Tableau de bord</h1>
                    <p>{{ $company->name }}</p>
                </div>

            </div>

            <div class="header-right">
                <div class="header-date">
                    {{ now()->translatedFormat('d F Y') }}
                </div>

                <div class="header-user">
                    <div class="user-avatar">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <span>{{ auth()->user()->name }}</span>
                </div>
            </div>

        </header>

        <main class="medical-content">

            {{-- BIENVENUE --}}
            <section class="welcome-panel">

                <div class="welcome-content">
                    <span class="eyebrow">NEXORA CLINIQUE</span>

                    <h2>Bienvenue dans votre espace médical</h2>

                    <p>
                        Retrouvez vos patients, vos rendez-vous et
                        les principales activités de votre clinique.
                    </p>

                    <div class="welcome-actions">
                        <a href="{{ route('company.medical.patients.create') }}"
                           class="welcome-btn welcome-btn-primary">
                            + Nouveau patient
                        </a>

                        <a href="{{ route('company.medical.appointments.create') }}"
                           class="welcome-btn welcome-btn-outline">
                            + Rendez-vous
                        </a>
                    </div>
                </div>

                <div class="welcome-symbol" aria-hidden="true">
                    ✚
                </div>

            </section>

            {{-- STATISTIQUES --}}
            <section class="medical-stats">

                <a href="{{ route('company.medical.patients.index') }}"
                   class="stat-card">
                    <div class="stat-top">
                        <span>Patients enregistrés</span>
                        <div class="stat-icon stat-blue">♙</div>
                    </div>

                    <strong>{{ number_format($stats['patients'], 0, ',', ' ') }}</strong>
                    <small>Total des patients de la clinique</small>
                </a>

                <a href="{{ route('company.medical.appointments.index', ['date' => today()->toDateString()]) }}"
                   class="stat-card">
                    <div class="stat-top">
                        <span>Rendez-vous du jour</span>
                        <div class="stat-icon stat-orange">▣</div>
                    </div>

                    <strong>{{ number_format($stats['appointments'], 0, ',', ' ') }}</strong>
                    <small>{{ now()->format('d/m/Y') }}</small>
                </a>

                <a href="{{ route('company.medical.appointments.index') }}"
                   class="stat-card">
                    <div class="stat-top">
                        <span>Rendez-vous à venir</span>
                        <div class="stat-icon stat-green">◷</div>
                    </div>

                    <strong>{{ number_format($stats['upcoming'], 0, ',', ' ') }}</strong>
                    <small>Programmés ou confirmés</small>
                </a>

                <a href="{{ route('company.medical.appointments.index', ['status' => 'completed']) }}"
                   class="stat-card">
                    <div class="stat-top">
                        <span>Rendez-vous terminés</span>
                        <div class="stat-icon stat-purple">✓</div>
                    </div>

                    <strong>{{ number_format($stats['completed'], 0, ',', ' ') }}</strong>
                    <small>Total des rendez-vous terminés</small>
                </a>

            </section>

            {{-- RENDEZ-VOUS DU JOUR --}}
            <section class="medical-panel">

                <div class="panel-heading">
                    <div>
                        <h3>Rendez-vous du jour</h3>
                        <p>Planning des patients attendus aujourd'hui</p>
                    </div>

                    <a href="{{ route('company.medical.appointments.index') }}"
                       class="panel-link">
                        Voir tous les rendez-vous
                    </a>
                </div>

                <div class="medical-table-wrap">
                    <table class="medical-table">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <th>Patient</th>
                                <th>Professionnel</th>
                                <th>Motif</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($recentAppointments as $appointment)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $appointment->scheduled_at->format('H:i') }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $appointment->patient?->full_name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $appointment->practitioner?->name ?? 'Non affecté' }}
                                    </td>

                                    <td>
                                        {{ $appointment->reason ?: '—' }}
                                    </td>

                                    <td>
                                        @php
                                            $statusLabels = [
                                                'scheduled' => 'Programmé',
                                                'confirmed' => 'Confirmé',
                                                'completed' => 'Terminé',
                                                'cancelled' => 'Annulé',
                                                'no_show' => 'Absent',
                                            ];
                                        @endphp

                                        <span class="appointment-badge status-{{ $appointment->status }}">
                                            {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('company.medical.appointments.show', $appointment) }}"
                                           class="table-action">
                                            Consulter
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="table-empty">
                                        Aucun rendez-vous programmé aujourd'hui.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </section>

            {{-- ACCÈS RAPIDES --}}
            <section class="medical-panel">

                <div class="panel-heading">
                    <div>
                        <h3>Modules médicaux</h3>
                        <p>Accédez aux fonctionnalités de votre clinique</p>
                    </div>
                </div>

                <div class="feature-grid">

                    <a href="{{ route('company.medical.patients.index') }}"
                       class="feature-card">
                        <span class="feature-icon">♙</span>
                        <strong>Gestion des patients</strong>
                        <small class="feature-available">Disponible</small>
                    </a>

                    <a href="{{ route('company.medical.appointments.index') }}"
                       class="feature-card">
                        <span class="feature-icon">▣</span>
                        <strong>Rendez-vous médicaux</strong>
                        <small class="feature-available">Disponible</small>
                    </a>

                    @foreach([
                        'Consultations',
                        'Dossiers médicaux',
                        'Ordonnances',
                        'Laboratoire',
                        'Pharmacie',
                        'Facturation',
                        'Rapports'
                    ] as $feature)

                        <div class="feature-card feature-disabled">
                            <span class="feature-icon">✚</span>
                            <strong>{{ $feature }}</strong>
                            <small>Prochainement</small>
                        </div>

                    @endforeach

                </div>

            </section>

        </main>

    </div>

</div>

</body>
</html>