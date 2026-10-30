<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>

        @yield('title', 'Gestion scolaire') —

        {{ auth()->user()?->company?->schoolSetting?->school_name ?: (auth()->user()?->company?->name ?? 'Établissement') }}

    </title>

    <style>

        :root {

            --school-primary: #4f46e5;

            --school-primary-dark: #3730a3;

            --school-bg: #f4f6fb;

            --school-text: #17223b;

            --school-muted: #66738b;

            --school-line: #e5eaf3;

            --school-sidebar: #111b34;

            --school-radius: 14px;

        }

        * { box-sizing: border-box; }

        html, body {

            margin: 0;

            min-height: 100%;

            font-family: Inter, ui-sans-serif, system-ui,

                -apple-system, "Segoe UI", Arial, sans-serif;

            color: var(--school-text);

            background: var(--school-bg);

        }

        a {

            text-decoration: none;

            color: inherit;

        }

        button, input, select { font: inherit; }

        button { cursor: pointer; }

        .school-shell {

            display: flex;

            min-height: 100vh;

        }

        .school-sidebar {

            width: 268px;

            flex: none;

            background: var(--school-sidebar);

            color: #d9e3fa;

            display: flex;

            flex-direction: column;

            position: sticky;

            top: 0;

            height: 100vh;

            overflow-y: auto;

            z-index: 30;

        }

        .school-brand {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 23px 19px;

            border-bottom: 1px solid #ffffff1b;

        }

        .school-mark {

            display: grid;

            place-items: center;

            width: 43px;

            height: 43px;

            border-radius: 12px;

            background: linear-gradient(135deg, #6366f1, #2563eb);

            font-size: 23px;

        }

        .school-brand strong {

            display: block;

            color: #fff;

            font-size: 16px;

        }

        .school-brand small {

            display: block;

            color: #9fb1d4;

            font-size: 11px;

            margin-top: 3px;

        }

        .school-establishment {

            margin: 17px 13px;

            padding: 12px;

            background: #ffffff0d;

            border: 1px solid #ffffff1c;

            border-radius: 12px;

            display: flex;

            align-items: center;

            gap: 10px;

        }

        .school-establishment img {

            width: 37px;

            height: 37px;

            object-fit: contain;

            border-radius: 8px;

            background: #fff;

        }

        .school-establishment strong {

            font-size: 12px;

            color: #fff;

            display: block;

        }

        .school-establishment small {

            font-size: 11px;

            color: #a8b7d4;

            display: block;

            margin-top: 4px;

        }

        .school-nav {

            padding: 7px 12px 25px;

            flex: 1;

        }

        .school-nav-heading {

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 1.1px;

            color: #8093b8;

            margin: 21px 10px 9px;

            text-transform: uppercase;

        }

        .school-nav a {

            display: flex;

            align-items: center;

            gap: 11px;

            padding: 11px 12px;

            margin: 3px 0;

            border-radius: 9px;

            font-size: 12.5px;

            color: #d9e3fa;

            transition: background .15s, color .15s;

        }

        .school-nav a:hover,

        .school-nav a.active {

            background: #6366f12c;

            color: #fff;

        }

        .school-nav a.active {

            box-shadow: inset 3px 0 0 #818cf8;

        }

        .school-nav .nav-icon {

            font-size: 17px;

            width: 23px;

            text-align: center;

            flex: none;

        }

        .school-nav-footer {

            padding: 13px;

            border-top: 1px solid #ffffff1b;

        }

        .school-nav-footer a {

            display: block;

            border: 1px solid #ffffff25;

            border-radius: 9px;

            padding: 11px;

            text-align: center;

            font-size: 12px;

        }

        .school-main {

            min-width: 0;

            flex: 1;

        }

        .school-topbar {

            height: 76px;

            background: #fff;

            border-bottom: 1px solid var(--school-line);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 28px;

            gap: 18px;

            position: sticky;

            top: 0;

            z-index: 20;

        }

        .school-topbar h1 {

            margin: 0;

            font-size: 19px;

            letter-spacing: -.4px;

        }

        .school-topbar p {

            margin: 4px 0 0;

            font-size: 12px;

            color: var(--school-muted);

        }

        .school-topbar-actions {

            display: flex;

            align-items: center;

            gap: 14px;

        }

        .school-user {

            display: flex;

            align-items: center;

            gap: 9px;

        }

        .school-avatar {

            width: 38px;

            height: 38px;

            display: grid;

            place-items: center;

            border-radius: 50%;

            background: #e8e9ff;

            color: #4338ca;

            font-weight: 800;

        }

        .school-user strong {

            display: block;

            font-size: 12px;

        }

        .school-user small {

            display: block;

            font-size: 11px;

            color: var(--school-muted);

            margin-top: 3px;

        }

        .school-content {

            padding: 26px 28px;

            max-width: 1700px;

            margin: auto;

        }

        .school-flash {

            padding: 14px 17px;

            border-radius: 11px;

            margin-bottom: 18px;

            background: #ecfdf5;

            color: #166534;

            border: 1px solid #bbf7d0;

        }

        .school-errors {

            padding: 14px 17px;

            border-radius: 11px;

            margin-bottom: 18px;

            background: #fef2f2;

            color: #991b1b;

            border: 1px solid #fecaca;

        }

        .school-card {

            background: #fff;

            border: 1px solid var(--school-line);

            border-radius: var(--school-radius);

            padding: 20px;

            box-shadow: 0 4px 18px #17223b08;

        }

        .school-menu-toggle {

            display: none;

            background: #f1f3ff;

            border: 0;

            border-radius: 9px;

            padding: 9px 12px;

            font-size: 20px;

        }

        .school-overlay { display: none; }

        .school-back {

            font-size: 12px;

            color: #4f46e5;

            font-weight: 700;

        }

        @media (max-width: 1000px) {

            .school-sidebar {

                position: fixed;

                left: 0;

                transform: translateX(-100%);

                transition: transform .2s;

            }

            .school-shell.sidebar-open .school-sidebar {

                transform: translateX(0);

            }

            .school-shell.sidebar-open .school-overlay {

                display: block;

                position: fixed;

                inset: 0;

                background: #07142b8a;

                z-index: 25;

            }

            .school-menu-toggle { display: block; }

            .school-topbar { padding: 0 18px; }

            .school-content { padding: 20px 16px; }

        }

        @media (max-width: 600px) {

            .school-topbar { height: 68px; }

            .school-topbar h1 { font-size: 16px; }

            .school-topbar p { font-size: 11px; }

            .school-user div:last-child { display: none; }

            .school-content { padding: 16px 12px; }

            .school-avatar { width: 34px; height: 34px; }

        }

        /* Logo officiel de l'établissement */

        .school-establishment-logo {

            width: 46px;

            height: 46px;

            min-width: 46px;

            border-radius: 10px;

            background: #fff;

            display: grid;

            place-items: center;

            padding: 4px;

            overflow: hidden;

        }

        .school-establishment-logo img {

            width: 100%;

            height: 100%;

            object-fit: contain;

            display: block;

            background: transparent;

        }

        .school-logo-placeholder {

            place-items: center;

            font-size: 22px;

        }

        .school-establishment-info { min-width: 0; }

        .school-establishment-info strong { overflow-wrap: anywhere; }

        .school-motto { font-style: italic; color: #a5b4fc !important; }

    </style>

    @stack('styles')

</head>

<body>

@php

    $schoolUser = auth()->user();

    $schoolCompany = $schoolUser?->company;

    $schoolRole = $schoolUser?->role;

    // Identité de l'école propre à l'entreprise connectée.

    $schoolSettings = $schoolCompany?->schoolSetting;

    $schoolName = $schoolSettings?->school_name ?: ($schoolCompany?->name ?: 'Établissement');

    $schoolMotto = $schoolSettings?->motto;

    // Certains anciens uploads peuvent enregistrer un préfixe storage/ ou public/.

    // On teste chaque candidat sur le disque public avant de construire son URL.

    $schoolLogoUrl = null;

    $logoSources = [$schoolSettings?->logo, $schoolCompany?->logo];

    foreach ($logoSources as $logoSource) {

        if (!is_string($logoSource) || trim($logoSource) === '') {

            continue;

        }

        $rawLogoPath = str_replace('\\\\', '/', trim($logoSource));

        $rawLogoPath = preg_replace('#^(?:https?://[^/]+/)+#i', '', $rawLogoPath);

        $rawLogoPath = ltrim($rawLogoPath, '/');

        $candidates = [$rawLogoPath];

        $candidate = $rawLogoPath;

        foreach (['public/storage/', 'storage/app/public/', 'storage/', 'public/'] as $prefix) {

            if (str_starts_with($candidate, $prefix)) {

                $candidate = substr($candidate, strlen($prefix));

                $candidates[] = $candidate;

            }

        }

        foreach (array_unique($candidates) as $candidatePath) {

            if (

                $candidatePath !== '' &&

                !str_contains($candidatePath, '../') &&

                \Illuminate\Support\Facades\Storage::disk('public')->exists($candidatePath)

            ) {

                $schoolLogoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($candidatePath);

                break 2;

            }

        }

    }

    $schoolAdmin = in_array($schoolRole, [

        'companyadmin',

        'school_admin',

    ], true);

    $schoolSupervisor = in_array($schoolRole, [

        'school_principal',

        'school_director',

    ], true);

    $schoolTeacher = $schoolRole === 'school_teacher';

    $schoolBursar = $schoolRole === 'school_bursar';

    $schoolDiscipline = in_array($schoolRole, [

        'school_supervisor',

        'school_sector_supervisor',

    ], true);

    $schoolStudent = $schoolRole === 'school_student';

    $schoolParent = $schoolRole === 'school_parent';

    $schoolLibrarian = $schoolRole === 'school_librarian';

    $schoolItems = [

        [

            'label' => 'Tableau de bord',

            'route' => 'company.school.dashboard',
            'permission' => 'school.dashboard.view',

            'icon' => '▦',

            'roles' => ['all'],

        ],

        [

            'heading' => 'Administration',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Années scolaires',

            'route' => 'company.school.years.index',

            'icon' => '▤',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Sections',

            'route' => 'company.school.sections.index',

            'icon' => '◈',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Niveaux',

            'route' => 'company.school.levels.index',

            'icon' => '▧',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Classes',

            'route' => 'company.school.classes.index',
            'permission' => 'school.classes.manage',

            'icon' => '▥',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'label' => 'Gestion des élèves',

            'route' => 'company.school.students.index',
            'permission' => 'school.students.view',

            'icon' => '♙',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'label' => 'Inscriptions scolaires',

            'route' => 'company.school.enrollments.index',
            'permission' => 'school.enrollments.manage',

            'icon' => '▤',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'label' => 'Cartes scolaires',

            'route' => 'company.school.cards.index',

            'icon' => '▣',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'label' => 'Matières',

            'route' => 'company.school.subjects.index',

            'icon' => '▣',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'label' => 'Enseignants et affectations',

            'route' => 'company.school.teachers.index',
            'permission' => 'school.teachers.manage',

            'icon' => '♧',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'heading' => 'Pédagogie et vie scolaire',

            'roles' => [

                'admin', 'supervisor', 'teacher',

                'discipline', 'student', 'parent',

            ],

        ],

        [

            'label' => 'Saisie des notes',

            'route' => 'company.school.grades.index',
            'permission' => 'school.grades.view',

            'icon' => '✎',

            'roles' => ['admin', 'supervisor', 'teacher'],

        ],

        [

            'label' => 'Fiches vierges de notes',

            'route' => 'company.school.grade-sheets.index',

            'icon' => '▤',

            'roles' => ['admin', 'supervisor', 'teacher'],

        ],

        [

            'label' => "Tableaux d'honneur",

            'route' => 'company.school.honors.index',

            'icon' => '★',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'label' => 'Bulletins et relevés',

            'route' => 'company.school.reports.index',
            'permission' => 'school.reports.view',

            'icon' => '▧',

            'roles' => [

                'admin', 'supervisor', 'teacher',

                'student', 'parent',

            ],

        ],

        [

            'label' => 'Emplois du temps',

            'route' => 'company.school.timetables.index',

            'icon' => '▦',

            'roles' => [

                'admin', 'supervisor', 'teacher',

                'discipline', 'student',

            ],

        ],

        [

            'label' => 'Absences et discipline',

            'route' => 'company.school.attendance.index',

            'icon' => '◷',

            'roles' => ['admin', 'supervisor', 'discipline'],

        ],

        [

            'label' => 'Conseils de classe',

            'route' => 'company.school.councils.index',

            'icon' => '⚑',

            'roles' => ['admin', 'supervisor'],

        ],

        [

            'label' => 'Épreuves et examens',

            'route' => 'company.school.exams.index',

            'icon' => '▤',

            'roles' => ['admin', 'teacher', 'student'],

        ],

        [

            'heading' => 'Services et finances',

            'roles' => [

                'admin', 'supervisor', 'bursar',

                'student', 'parent', 'librarian',

            ],

        ],

        [

            'label' => 'Tarifs scolaires',

            'route' => 'company.school.fees.index',

            'icon' => '▤',

            'roles' => ['admin', 'bursar'],

        ],

        [

            'label' => 'Scolarité et paiements',

            'route' => 'company.school.tuition.index',
            'permission' => 'school.tuition.manage',

            'icon' => '◉',

            'roles' => ['admin', 'supervisor', 'bursar', 'parent'],

        ],

        [

            'label' => 'Rapports financiers',

            'route' => 'company.school.finance-reports.index',

            'icon' => '▥',

            'roles' => ['admin', 'bursar'],

        ],

        [

            'label' => 'Transport scolaire',

            'route' => 'company.school.transport.index',

            'icon' => '▰',

            'roles' => ['admin', 'bursar'],

        ],

        [

            'label' => 'Requêtes',

            'route' => 'company.school.requests.index',

            'icon' => '☷',

            'roles' => ['admin', 'bursar', 'student'],

        ],

        [

            'label' => 'Bibliothèque',

            'route' => 'company.school.library.index',

            'icon' => '▣',

            'roles' => ['admin', 'librarian', 'student'],

        ],

        [

            'heading' => 'Configuration',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Utilisateurs',

            'route' => 'company.school.users.index',
            'permission' => 'school.users.manage',

            'icon' => '♙',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Gestion des accès',

            'route' => 'company.school.access.index',
            'permission' => 'school.access.manage',

            'icon' => '🔐',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Permissions scolaires',

            'route' => 'company.school.permissions.index',
            'permission' => 'school.permissions.manage',

            'icon' => '🔐',

            'roles' => ['admin'],

        ],

        [

            'label' => 'Paramètres de l’établissement',

            'route' => 'company.school.settings.index',
            'permission' => 'school.settings.manage',

            'icon' => '⚙',

            'roles' => ['admin', 'supervisor'],

        ],

    ];

    $schoolGroups = array_filter([

        $schoolAdmin ? 'admin' : null,

        $schoolSupervisor ? 'supervisor' : null,

        $schoolTeacher ? 'teacher' : null,

        $schoolBursar ? 'bursar' : null,

        $schoolDiscipline ? 'discipline' : null,

        $schoolStudent ? 'student' : null,

        $schoolParent ? 'parent' : null,

        $schoolLibrarian ? 'librarian' : null,

    ]);

@endphp

<div class="school-shell" id="schoolShell">

    <div

        class="school-overlay"

        id="schoolOverlay"

        aria-hidden="true">

    </div>

    <aside

        class="school-sidebar"

        id="schoolSidebar"

        aria-label="Navigation scolaire">

        <div class="school-brand">

            <span class="school-mark">🎓</span>

            <div>

                <strong>Gestion scolaire</strong>

                <small>ERP SaaS · Établissement</small>

            </div>

        </div>

        <div class="school-establishment">

            <div class="school-establishment-logo">

                @if($schoolLogoUrl)

                    <img src="{{ $schoolLogoUrl }}"

                         alt="Logo de {{ $schoolName }}"

                         loading="eager"

                         onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">

                    <span class="school-logo-placeholder" style="display:none;">🏫</span>

                @else

                    <span class="school-logo-placeholder">🏫</span>

                @endif

            </div>

            <div class="school-establishment-info">

                <strong>{{ $schoolName }}</strong>

                <small>{{ $schoolUser?->roleLabel() ?? 'Utilisateur' }}</small>

                @if($schoolMotto)

                    <small class="school-motto">{{ $schoolMotto }}</small>

                @endif

            </div>

        </div>

        <nav class="school-nav">

            @foreach($schoolItems as $item)

                @php

                    $visible = in_array('all', $item['roles'], true)

                        || count(array_intersect(

                            $schoolGroups,

                            $item['roles']

                        )) > 0;

                @endphp

                {{-- Une permission explicite peut restreindre davantage la visibilité par rôle. --}}
                @php
                    $permissionKey = $item['permission'] ?? null;
                    if ($visible && $permissionKey && $schoolUser) {
                        $visible = app(\App\Services\SchoolPermissionService::class)
                            ->allowed($schoolUser, $permissionKey);
                    }
                @endphp
                @continue(!$visible)

                @if(isset($item['heading']))

                    <div class="school-nav-heading">

                        {{ $item['heading'] }}

                    </div>

                @elseif(\Illuminate\Support\Facades\Route::has($item['route']))

                    @php

                        $routePrefix = preg_replace(

                            '/\\\\.index$/',

                            '',

                            $item['route']

                        );

                        $active = request()->routeIs($item['route'])

                            || request()->routeIs($routePrefix . '.*');

                    @endphp

                    <a

                        href="{{ route($item['route']) }}"

                        class="{{ $active ? 'active' : '' }}"

                        @if($active) aria-current="page" @endif>

                        <span class="nav-icon">

                            {{ $item['icon'] }}

                        </span>

                        {{ $item['label'] }}

                    </a>

                @endif

            @endforeach

        </nav>

        <div class="school-nav-footer">

            @if(

                \Illuminate\Support\Facades\Route::has('company.modules.index')

                && $schoolAdmin

            )

                <a href="{{ route('company.modules.index') }}">

                    ← Retour aux applications ERP

                </a>

            @endif

        </div>

    </aside>

    <main class="school-main">

        <header class="school-topbar">

            <div style="display:flex;align-items:center;gap:12px;min-width:0">

                <button

                    class="school-menu-toggle"

                    id="schoolMenuToggle"

                    aria-label="Ouvrir le menu"

                    aria-expanded="false"

                    aria-controls="schoolSidebar"

                    type="button">

                    ☰

                </button>

                <div>

                    <h1>

                        @yield('page-title', 'Tableau de bord')

                    </h1>

                    <p>

                        @yield(

                            'page-description',

                            'Gestion et suivi de votre établissement'

                        )

                    </p>

                </div>

            </div>

            <div class="school-topbar-actions">

                @if(\Illuminate\Support\Facades\Route::has('profile.edit'))

                    <a

                        class="school-back"

                        href="{{ route('profile.edit') }}">

                        Mon profil

                    </a>

                @endif

                <div class="school-user">

                    <span class="school-avatar">

                        {{ mb_strtoupper(mb_substr($schoolUser?->name ?? 'U', 0, 1)) }}

                    </span>

                    <div>

                        <strong>{{ $schoolUser?->name }}</strong>

                        <small>{{ $schoolUser?->roleLabel() }}</small>

                    </div>

                </div>

            </div>

        </header>

        <div class="school-content">

            @if(session('success'))

                <div class="school-flash" role="status">

                    {{ session('success') }}

                </div>

            @endif

            @if($errors->any())

                <div class="school-errors" role="alert">

                    <strong>

                        Veuillez corriger les erreurs suivantes :

                    </strong>

                    <ul>

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            @yield('content')

        </div>

    </main>

</div>

<script>

    (function () {

        const shell = document.getElementById('schoolShell');

        const toggle = document.getElementById('schoolMenuToggle');

        const overlay = document.getElementById('schoolOverlay');

        function setOpen(open) {

            shell.classList.toggle('sidebar-open', open);

            toggle?.setAttribute('aria-expanded', String(open));

        }

        toggle?.addEventListener('click', () => {

            setOpen(!shell.classList.contains('sidebar-open'));

        });

        overlay?.addEventListener('click', () => {

            setOpen(false);

        });

        document.addEventListener('keydown', (event) => {

            if (event.key === 'Escape') {

                setOpen(false);

            }

        });

    })();

</script>

@stack('scripts')

</body>

</html>
