
<header class="topbar">

    <button type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Afficher le menu"
            aria-expanded="false"
            aria-controls="principalSidebar">
        ☰
    </button>

    <span>Direction de l'établissement</span>

    <span class="account">
        {{ auth()->user()->name ?? 'Proviseur' }}
    </span>

</header>
