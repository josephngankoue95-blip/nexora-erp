@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')

<div class="superadmin-dashboard">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="dashboard-header">

        <div>
            <span class="dashboard-kicker">
                Administration
            </span>

            <h1>
                Dashboard Super Admin
            </h1>

            <p>
                Vue globale de votre plateforme ERP SaaS.
            </p>
        </div>

        <div class="dashboard-date">
            <span class="date-label">Aujourd'hui</span>

            <strong>
                {{ now()->translatedFormat('d F Y') }}
            </strong>
        </div>

    </div>


    {{-- =========================================================
        STATISTIQUES
    ========================================================== --}}

    <div class="dashboard-stats">

        {{-- ENTREPRISES --}}

        <div class="stat-card stat-card-blue">

            <div class="stat-icon">
                🏢
            </div>

            <div class="stat-content">

                <span>
                    Entreprises
                </span>

                <strong
                    class="counter"
                    data-value="{{ $totalCompanies }}"
                >
                    0
                </strong>

                <small>
                    Total des entreprises
                </small>

            </div>

        </div>


        {{-- ENTREPRISES ACTIVES --}}

        <div class="stat-card stat-card-green">

            <div class="stat-icon">
                ✓
            </div>

            <div class="stat-content">

                <span>
                    Entreprises actives
                </span>

                <strong
                    class="counter"
                    data-value="{{ $activeCompanies }}"
                >
                    0
                </strong>

                <small>
                    Entreprises opérationnelles
                </small>

            </div>

        </div>


        {{-- ENTREPRISES INACTIVES --}}

        <div class="stat-card stat-card-orange">

            <div class="stat-icon">
                ⏸
            </div>

            <div class="stat-content">

                <span>
                    Entreprises inactives
                </span>

                <strong
                    class="counter"
                    data-value="{{ $inactiveCompanies }}"
                >
                    0
                </strong>

                <small>
                    Entreprises désactivées
                </small>

            </div>

        </div>


        {{-- UTILISATEURS --}}

        <div class="stat-card stat-card-purple">

            <div class="stat-icon">
                👥
            </div>

            <div class="stat-content">

                <span>
                    Utilisateurs
                </span>

                <strong
                    class="counter"
                    data-value="{{ $totalUsers }}"
                >
                    0
                </strong>

                <small>
                    Tous les utilisateurs
                </small>

            </div>

        </div>

    </div>


    {{-- =========================================================
        GRAPHIQUES
    ========================================================== --}}

    <div class="dashboard-charts">

        {{-- ÉVOLUTION ENTREPRISES --}}

        <div class="dashboard-panel chart-panel">

            <div class="panel-header">

                <div>
                    <h2>
                        Évolution des entreprises
                    </h2>

                    <p>
                        Nouvelles entreprises sur les 6 derniers mois
                    </p>
                </div>

                <span class="panel-badge">
                    6 mois
                </span>

            </div>

            <div class="chart-wrapper">
                <canvas id="companiesChart"></canvas>
            </div>

        </div>


        {{-- RÉPARTITION --}}

        <div class="dashboard-panel chart-panel small-chart">

            <div class="panel-header">

                <div>
                    <h2>
                        État des entreprises
                    </h2>

                    <p>
                        Situation actuelle
                    </p>
                </div>

            </div>

            <div class="donut-wrapper">
                <canvas id="companyStatusChart"></canvas>
            </div>

        </div>

    </div>


    {{-- =========================================================
        UTILISATEURS
    ========================================================== --}}

    <div class="dashboard-charts">

        <div class="dashboard-panel chart-panel">

            <div class="panel-header">

                <div>
                    <h2>
                        Évolution des utilisateurs
                    </h2>

                    <p>
                        Nouveaux utilisateurs sur les 6 derniers mois
                    </p>
                </div>

                <span class="panel-badge">
                    6 mois
                </span>

            </div>

            <div class="chart-wrapper">
                <canvas id="usersChart"></canvas>
            </div>

        </div>


        {{-- DISTRIBUTION UTILISATEURS --}}

        <div class="dashboard-panel chart-panel small-chart">

            <div class="panel-header">

                <div>
                    <h2>
                        Utilisateurs
                    </h2>

                    <p>
                        Répartition par type
                    </p>
                </div>

            </div>

            <div class="donut-wrapper">
                <canvas id="userDistributionChart"></canvas>
            </div>

        </div>

    </div>


    {{-- =========================================================
        DERNIÈRES ACTIVITÉS
    ========================================================== --}}

    <div class="dashboard-columns">


        {{-- ENTREPRISES RÉCENTES --}}

        <div class="dashboard-panel">

            <div class="panel-header">

                <div>
                    <h2>
                        Entreprises récentes
                    </h2>

                    <p>
                        Dernières entreprises enregistrées
                    </p>
                </div>

                <a href="{{ route('superadmin.companies.index') }}"
                   class="panel-link">

                    Voir tout →

                </a>

            </div>


            <div class="recent-list">

                @forelse($recentCompanies as $company)

                    <div class="recent-item">

                        <div class="recent-avatar company-avatar">

                            @if($company->logo)

                                <img
                                    src="{{ asset('storage/' . $company->logo) }}"
                                    alt="{{ $company->name }}"
                                >

                            @else

                                {{ strtoupper(substr($company->name, 0, 2)) }}

                            @endif

                        </div>


                        <div class="recent-info">

                            <strong>
                                {{ $company->name }}
                            </strong>

                            <span>
                                {{ $company->email ?? 'Aucun email' }}
                            </span>

                        </div>


                        <div>

                            @if($company->is_active)

                                <span class="status-badge status-active">
                                    Active
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Inactive
                                </span>

                            @endif

                        </div>

                    </div>

                @empty

                    <div class="empty-state">
                        Aucune entreprise enregistrée.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- UTILISATEURS RÉCENTS --}}

        <div class="dashboard-panel">

            <div class="panel-header">

                <div>
                    <h2>
                        Utilisateurs récents
                    </h2>

                    <p>
                        Derniers comptes créés
                    </p>
                </div>

                <span class="panel-link">
                    {{ $totalUsers }} utilisateurs
                </span>

            </div>


            <div class="recent-list">

                @forelse($recentUsers as $user)

                    <div class="recent-item">

                        <div class="recent-avatar user-avatar">

                            {{ strtoupper(substr($user->name, 0, 2)) }}

                        </div>


                        <div class="recent-info">

                            <strong>
                                {{ $user->name }}
                            </strong>

                            <span>
                                {{ $user->email }}
                            </span>

                        </div>


                        <div class="user-role">

                            @if($user->role === 'super_admin')

                                <span class="role-badge role-super">
                                    Super Admin
                                </span>

                            @else

                                <span class="role-badge">
                                    {{ $user->role }}
                                </span>

                            @endif

                        </div>

                    </div>

                @empty

                    <div class="empty-state">
                        Aucun utilisateur enregistré.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    CHART.JS
========================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | COMPTEURS ANIMÉS
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.counter').forEach(counter => {

        const target = Number(counter.dataset.value || 0);

        let current = 0;

        const duration = 1200;

        const start = performance.now();

        function animate(time) {

            const progress = Math.min(
                (time - start) / duration,
                1
            );

            const eased =
                1 - Math.pow(1 - progress, 3);

            current = Math.floor(target * eased);

            counter.textContent =
                current.toLocaleString('fr-FR');

            if (progress < 1) {
                requestAnimationFrame(animate);
            }

        }

        requestAnimationFrame(animate);

    });


    /*
    |--------------------------------------------------------------------------
    | DONNÉES
    |--------------------------------------------------------------------------
    */

    const companyEvolution =
        @json($companyEvolution);

    const userEvolution =
        @json($userEvolution);

    const companyStatus =
        @json($companyStatus);

    const userDistribution =
        @json($userDistribution);


    /*
    |--------------------------------------------------------------------------
    | GRAPHIQUE ENTREPRISES
    |--------------------------------------------------------------------------
    */

    const companiesCanvas =
        document.getElementById('companiesChart');

    if (companiesCanvas) {

        new Chart(companiesCanvas, {

            type: 'line',

            data: {

                labels: companyEvolution.map(
                    item => item.month
                ),

                datasets: [{

                    label: 'Nouvelles entreprises',

                    data: companyEvolution.map(
                        item => item.count
                    ),

                    borderWidth: 3,

                    tension: 0.4,

                    fill: true,

                    pointRadius: 4,

                    pointHoverRadius: 7

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                animation: {
                    duration: 1600,
                    easing: 'easeOutQuart'
                },

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }

                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | ENTREPRISES ACTIVES / INACTIVES
    |--------------------------------------------------------------------------
    */

    const companyStatusCanvas =
        document.getElementById('companyStatusChart');

    if (companyStatusCanvas) {

        new Chart(companyStatusCanvas, {

            type: 'doughnut',

            data: {

                labels: [
                    'Actives',
                    'Inactives'
                ],

                datasets: [{

                    data: [
                        companyStatus.active,
                        companyStatus.inactive
                    ],

                    borderWidth: 0,

                    hoverOffset: 8

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '72%',

                animation: {
                    animateRotate: true,
                    duration: 1400
                },

                plugins: {

                    legend: {
                        position: 'bottom'
                    }

                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | GRAPHIQUE UTILISATEURS
    |--------------------------------------------------------------------------
    */

    const usersCanvas =
        document.getElementById('usersChart');

    if (usersCanvas) {

        new Chart(usersCanvas, {

            type: 'bar',

            data: {

                labels: userEvolution.map(
                    item => item.month
                ),

                datasets: [{

                    label: 'Nouveaux utilisateurs',

                    data: userEvolution.map(
                        item => item.count
                    ),

                    borderRadius: 8,

                    borderWidth: 0

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                animation: {
                    duration: 1400
                },

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }

                    }

                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | RÉPARTITION UTILISATEURS
    |--------------------------------------------------------------------------
    */

    const userDistributionCanvas =
        document.getElementById('userDistributionChart');

    if (userDistributionCanvas) {

        new Chart(userDistributionCanvas, {

            type: 'doughnut',

            data: {

                labels: [
                    'Utilisateurs entreprises',
                    'Super Admins'
                ],

                datasets: [{

                    data: [
                        userDistribution.company_users,
                        userDistribution.super_admins
                    ],

                    borderWidth: 0,

                    hoverOffset: 8

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '72%',

                animation: {
                    duration: 1400
                },

                plugins: {

                    legend: {
                        position: 'bottom'
                    }

                }

            }

        });

    }

});

</script>

@endsection