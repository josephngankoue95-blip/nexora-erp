<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', $company->name)">
    <title>@yield('title', $company->name)</title>

    @php
        $siteSettings = $settings ?? null;
        $themeKey = $siteSettings?->theme_key ?: 'services';
        $theme = \App\Support\WebsiteTheme::find($themeKey);
        $primaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', $theme['primary'] ?? '') ? $theme['primary'] : '#2563eb';
        $secondaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', $theme['secondary'] ?? '') ? $theme['secondary'] : '#172033';
        $surfaceColor = preg_match('/^#[0-9a-fA-F]{6}$/', $theme['surface'] ?? '') ? $theme['surface'] : '#f6f8fc';
        $logoUrl = $siteSettings?->logo_url;
        $homeUrl = route('website.public.index', ['company' => $company->slug]);
        $footerText = $siteSettings?->footer_text ?: '© ' . date('Y') . ' ' . $company->name . '. Tous droits réservés.';

        // Ajouter les pages publiées et visibles dans le menu, sans supprimer les liens personnalisés.
        $publishedMenuPages = \App\Models\WebsitePage::query()
            ->where('company_id', $company->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $automaticNavigation = $publishedMenuPages->map(function ($page) use ($company, $homeUrl) {
            return [
                'label' => $page->title,
                'url' => $page->slug === 'accueil'
                    ? $homeUrl
                    : route('website.public.show', [
                        'company' => $company->slug,
                        'slug' => $page->slug,
                    ]),
            ];
        });

        $customNavigation = collect($navigation ?? [])->map(function ($item) {
            return [
                'label' => data_get($item, 'label', 'Lien'),
                'url' => data_get($item, 'url', '#'),
            ];
        });

        // Les liens personnalisés conservent leur priorité ; pas de doublon si l'URL existe déjà.
        $publicNavigation = $customNavigation
            ->concat($automaticNavigation)
            ->unique(fn ($item) => rtrim($item['url'], '/'))
            ->values();

    @endphp

    @vite([
        'resources/css/app.css',
        'resources/css/website-public.css',
        'resources/css/website-buttons.css',
        'resources/css/website-builder-public.css',
        'resources/js/website-builder-public.js',
        'resources/js/app.js'
    ])

    <style>
        :root {
            --ws-primary: {{ $primaryColor }};
            --ws-secondary: {{ $secondaryColor }};
            --ws-surface: {{ $surfaceColor }};
            --ws-dark: #172033;
            --ws-muted: #64748b;
            --ws-border: #e2e8f0;
        }
        body.ws-theme { margin: 0; color: var(--ws-dark); background: var(--ws-surface); }
        body.ws-theme .ws-header { background: rgba(255,255,255,.98); border-bottom: 1px solid var(--ws-border); box-shadow: 0 2px 14px rgba(15,23,42,.035); }
        body.ws-theme .ws-nav { min-height: 82px; }
        body.ws-theme .ws-brand { display: inline-flex; align-items: center; gap: 12px; min-width: 0; text-decoration: none; color: #14213d; }
        body.ws-theme .ws-brand-logo { width: 46px; height: 46px; object-fit: contain; border-radius: 11px; flex: none; }
        body.ws-theme .ws-brand-icon { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 12px; background: var(--ws-primary); color: #fff; flex: none; }
        body.ws-theme .ws-brand-name { font-size: 18px; font-weight: 800; overflow-wrap: anywhere; }
        body.ws-theme .ws-menu a { transition: color .18s ease; }
        body.ws-theme .ws-menu a:hover,
        body.ws-theme .ws-menu a.is-active { color: var(--ws-primary); }
        body.ws-theme .ws-menu a:focus-visible,
        body.ws-theme .ws-menu-button:focus-visible,
        body.ws-theme .ws-brand:focus-visible { outline: 2px solid var(--ws-primary); outline-offset: 4px; }

        body.ws-theme .ws-hero-custom { position: relative; background-color: var(--ws-secondary); background-image: linear-gradient(118deg, var(--ws-secondary), var(--ws-primary)); background-size: cover; background-position: center; padding: clamp(78px, 8vw, 118px) 0; min-height: 420px; display: flex; align-items: center; color: #fff; }
        body.ws-theme .ws-hero-custom .ws-container { position: relative; z-index: 1; }
        body.ws-theme .ws-hero-custom .ws-kicker { color: #fff; opacity: .88; }
        body.ws-theme .ws-hero-custom h1 { color: #fff; max-width: 800px; margin: 17px 0 20px; font-size: clamp(36px, 5vw, 62px); line-height: 1.12; letter-spacing: -.035em; overflow-wrap: anywhere; }
        body.ws-theme .ws-hero-custom p { max-width: 660px; color: #f1f5f9; font-size: clamp(16px, 1.7vw, 19px); line-height: 1.7; }
        body.ws-theme .ws-hero-custom .ws-button { display: inline-flex; align-items: center; gap: 12px; background: var(--ws-primary); color: #fff; border: 1px solid rgba(255,255,255,.15); padding: 14px 22px; border-radius: 11px; font-weight: 750; box-shadow: 0 8px 20px rgba(15,23,42,.12); transition: transform .2s ease, filter .2s ease; }
        body.ws-theme .ws-hero-custom .ws-button:hover { transform: translateY(-2px); filter: brightness(1.08); }
        body.ws-theme .ws-section { padding-block: clamp(55px, 7vw, 90px); }
        body.ws-theme .ws-section-heading { margin-bottom: 32px; }
        body.ws-theme .ws-section-heading .ws-kicker { color: var(--ws-primary); }
        body.ws-theme .ws-section-heading h2 { color: var(--ws-secondary); letter-spacing: -.025em; line-height: 1.2; }
        body.ws-theme .ws-section-heading p { color: var(--ws-muted); }
        body.ws-theme .ws-cards { gap: 24px; }
        body.ws-theme .ws-card { background: #fff; border: 1px solid var(--ws-border); border-top: 4px solid var(--ws-primary); border-radius: 17px; padding: 28px; box-shadow: 0 8px 28px rgba(15,23,42,.055); transition: transform .2s ease, box-shadow .2s ease; }
        body.ws-theme .ws-card:hover { transform: translateY(-4px); box-shadow: 0 15px 34px rgba(15,23,42,.10); }
        body.ws-theme .ws-card h3 { color: var(--ws-secondary); }
        body.ws-theme .ws-card-icon,
        body.ws-theme .ws-card a { color: var(--ws-primary); }
        body.ws-theme .ws-card p { color: var(--ws-muted); }
        body.ws-theme .ws-page-banner { background: var(--ws-surface); }
        body.ws-theme .ws-breadcrumb { color: var(--ws-primary); }
        body.ws-theme .ws-richtext { overflow-wrap: anywhere; min-width: 0; }
        body.ws-theme .ws-richtext img,
        body.ws-theme .ws-richtext video,
        body.ws-theme .ws-richtext iframe { max-width: 100%; height: auto; }
        body.ws-theme .ws-richtext img { border-radius: 12px; }
        body.ws-theme .ws-richtext a.site-btn { display: inline-flex; max-width: 100%; }
        body.ws-theme .ws-footer { background: var(--ws-secondary); color: #fff; padding: 30px 0; }
        body.ws-theme .ws-footer-inner { align-items: center; }

        /* Variations légères sans casser les pages éditées */
        body.ws-theme-hotel .ws-hero-custom,
        body.ws-theme-immobilier .ws-hero-custom { min-height: 520px; }
        body.ws-theme-restaurant .ws-hero-custom,
        body.ws-theme-commerce .ws-hero-custom { min-height: 460px; }
        body.ws-theme-ecole .ws-hero-custom,
        body.ws-theme-elearning .ws-hero-custom { min-height: 450px; }
        body.ws-theme-site-web .ws-card,
        body.ws-theme-projets .ws-card { border-radius: 22px; }
        body.ws-theme-restaurant .ws-card { border-top-width: 5px; }

        @media (max-width: 650px) {
            body.ws-theme .ws-nav { min-height: 70px; }
            body.ws-theme .ws-brand-logo,
            body.ws-theme .ws-brand-icon { width: 39px; height: 39px; }
            body.ws-theme .ws-brand-name { font-size: 15px; }
            body.ws-theme .ws-hero-custom { min-height: auto; padding: 70px 0 80px; }
            body.ws-theme .ws-hero-custom h1 { font-size: clamp(32px, 9vw, 45px); }
            body.ws-theme .ws-card { padding: 23px; }
            body.ws-theme .ws-footer-inner { gap: 8px; }
        }
    </style>
    @stack('styles')
</head>
<body class="ws-theme ws-theme-{{ $themeKey }}">
    <header class="ws-header">
        <div class="ws-container ws-nav">
            <a class="ws-brand" href="{{ $homeUrl }}" aria-label="Accueil - {{ $company->name }}">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo de {{ $company->name }}" class="ws-brand-logo">
                @else
                    <span class="ws-brand-icon">{{ mb_strtoupper(mb_substr($company->name, 0, 1)) }}</span>
                @endif
                <span class="ws-brand-name">{{ $company->name }}</span>
            </a>
            <button type="button" class="ws-menu-button" id="wsMenuButton" aria-controls="wsMenu" aria-expanded="false" aria-label="Ouvrir le menu">☰ <span>Menu</span></button>
            <nav class="ws-menu" id="wsMenu" aria-label="Navigation principale">
                @foreach($publicNavigation as $menuItem)
                    @php
                        $menuUrl = $menuItem['url'] ?? '#';
                        $menuLabel = $menuItem['label'] ?? 'Lien';
                        $isActive = url()->current() === $menuUrl;
                        $isExternal = filter_var($menuUrl, FILTER_VALIDATE_URL) && parse_url($menuUrl, PHP_URL_HOST) !== request()->getHost();
                    @endphp
                    <a href="{{ $menuUrl }}" class="{{ $isActive ? 'is-active' : '' }}" @if($isActive) aria-current="page" @endif @if($isExternal) target="_blank" rel="noopener noreferrer" @endif>{{ $menuLabel }}</a>
                @endforeach
            </nav>
        </div>
    </header>
    <main id="main-content">@yield('content')</main>
    <footer class="ws-footer">
        <div class="ws-container ws-footer-inner">
            <span>{{ $footerText }}</span>
            @if(filled($company->city))<span>{{ $company->city }}</span>@endif
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('wsMenuButton');
            const menu = document.getElementById('wsMenu');
            if (!button || !menu) return;
            function closeMenu() {
                menu.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
                button.setAttribute('aria-label', 'Ouvrir le menu');
            }
            button.addEventListener('click', function () {
                const isOpen = menu.classList.toggle('is-open');
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                button.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
            });
            menu.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', closeMenu); });
            document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeMenu(); });
            document.addEventListener('click', function (event) {
                if (!menu.contains(event.target) && !button.contains(event.target)) closeMenu();
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
