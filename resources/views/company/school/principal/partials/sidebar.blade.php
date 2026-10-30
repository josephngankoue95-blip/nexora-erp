<aside class="sidebar" id="principalSidebar"><div class="brand"><span class="brand-icon">N</span><span>NEXORA<small>GESTION SCOLAIRE</small></span></div><div class="nav-label">ESPACE PROVISEUR</div>
<nav class="principal-nav">

    <a class="nav-link {{ request()->routeIs('company.school.principal.dashboard') ? 'active' : '' }}"
       href="{{ route('company.school.principal.dashboard') }}">
        Tableau de bord
    </a>

    <a class="nav-link {{ request()->routeIs('company.school.principal.classes.*') ? 'active' : '' }}"
       href="{{ route('company.school.principal.classes.index') }}">
        Classes
    </a>

    <a class="nav-link {{ request()->routeIs('company.school.principal.subjects.*') ? 'active' : '' }}"
       href="{{ route('company.school.principal.subjects.index') }}">
        Matières
    </a>

    <a class="nav-link {{ request()->routeIs('company.school.principal.assignments.*') ? 'active' : '' }}"
       href="{{ route('company.school.principal.assignments.index') }}">
        Affectations
    </a>

    <a
        href="{{ route('company.school.principal.teachers.index') }}"
        class="nav-link {{ request()->routeIs('company.school.principal.teachers.*') ? 'active' : '' }}"
    >
        Enseignants
    </a>

    <a
        href="{{ route('company.school.principal.results.index') }}"
        class="nav-link {{ request()->routeIs('company.school.principal.results.*') ? 'active' : '' }}"
    >
        Résultats scolaires
    </a>

    <a
        href="{{ route('company.school.principal.results.by-class') }}"
        class="nav-link {{ request()->routeIs('company.school.principal.results.by-class') ? 'active' : '' }}"
    >
        Notes par classe
    </a>

    <a
        href="{{ route('company.school.principal.results.ranking') }}"
        class="nav-link {{ request()->routeIs('company.school.principal.results.ranking') ? 'active' : '' }}"
    >
        Classement des élèves
    </a>

    <a
        href="{{ route('company.school.principal.bulletins.index') }}"
        class="nav-link {{ request()->routeIs('company.school.principal.bulletins.*') ? 'active' : '' }}"
    >
        Bulletins scolaires
    </a>

    <a
        href="{{ route('company.school.principal.tuition.index') }}"
        class="nav-link {{ request()->routeIs('company.school.principal.tuition.*') ? 'active' : '' }}"
    >
        Supervision de la scolarité
    </a>

</nav>

<div class="nav-note">Consultation pédagogique de l'établissement.</div><form method="POST" action="{{ route('logout') }}" class="logout">@csrf<button type="submit">Déconnexion</button></form></aside>
