<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Modules') — NEXORA
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background: #f5f7fb;

            color: #172033;
        }

        a {
            color: inherit;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .module-launcher-layout {
            min-height: 100vh;

            display: flex;
            flex-direction: column;
        }

        .module-launcher-main {
            flex: 1;

            width: 100%;

            padding: 35px;

            display: flex;
            justify-content: center;
        }

        .module-launcher-container {
            width: 100%;
            max-width: 1450px;
        }
        .modules-company {
display: flex;
align-items: center;
gap: 12px;
}

.modules-company-info {
display: flex;
flex-direction: column;
min-width: 0;
}

.modules-company-info strong {
font-size: 15px;
font-weight: 700;
color: #111827;
}

.modules-company-info span {
margin-top: 2px;
font-size: 13px;
color: #6b7280;
}

.modules-logout-form {
margin-left: 8px;
}

.modules-logout-btn {
display: inline-flex;
align-items: center;
justify-content: center;
gap: 7px;
height: 38px;
padding: 0 13px;
border: 1px solid #e5e7eb;
border-radius: 9px;
background: #ffffff;
color: #dc2626;
font-size: 13px;
font-weight: 600;
cursor: pointer;
transition: all 0.2s ease;
}

.modules-logout-btn svg {
width: 17px;
height: 17px;
}

.modules-logout-btn:hover {
background: #fef2f2;
border-color: #fecaca;
color: #b91c1c;
}

@media (max-width: 700px) {
.modules-company {
flex-wrap: wrap;
justify-content: flex-end;
}

.modules-logout-form {
    margin-left: 0;
}

.modules-logout-btn span {
    display: none;
}

.modules-logout-btn {
    width: 38px;
    padding: 0;
}

}



        /* ============================================================
           TOP BAR
        ============================================================ */

        .module-launcher-topbar {
            height: 72px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 35px;

            background: #ffffff;

            border-bottom: 1px solid #e8ecf3;
        }

        .module-launcher-brand {
            display: flex;
            align-items: center;

            gap: 12px;

            text-decoration: none;
        }

        .module-launcher-brand-icon {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #315bea,
                    #2448c9
                );

            color: #ffffff;

            border-radius: 11px;

            font-size: 20px;
        }

        .module-launcher-brand-text {
            display: flex;
            flex-direction: column;
        }

        .module-launcher-brand-text strong {
            font-size: 15px;
            font-weight: 800;

            color: #172033;
        }

        .module-launcher-brand-text span {
            margin-top: 2px;

            font-size: 11px;

            color: #64748b;
        }


        /* ============================================================
           USER
        ============================================================ */

        .module-launcher-user {
            display: flex;
            align-items: center;

            gap: 10px;
        }

        .module-launcher-user-avatar {
            width: 38px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #eff4ff;

            color: #315bea;

            border-radius: 50%;

            font-size: 13px;
            font-weight: 800;
        }

        .module-launcher-user-info {
            display: flex;
            flex-direction: column;
        }

        .module-launcher-user-info strong {
            font-size: 13px;

            color: #172033;
        }

        .module-launcher-user-info span {
            margin-top: 2px;

            font-size: 11px;

            color: #64748b;
        }


        /* ============================================================
           FOOTER
        ============================================================ */

        .module-launcher-footer {
            padding: 18px 35px;

            text-align: center;

            border-top: 1px solid #e8ecf3;

            background: #ffffff;

            color: #94a3b8;

            font-size: 12px;
        }


        /* ============================================================
           MOBILE
        ============================================================ */

        @media (max-width: 700px) {

            .module-launcher-topbar {
                height: 64px;

                padding: 0 18px;
            }

            .module-launcher-brand-icon {
                width: 36px;
                height: 36px;
            }

            .module-launcher-user-info {
                display: none;
            }

            .module-launcher-main {
                padding: 20px 15px;
            }

            .module-launcher-footer {
                padding: 15px;
            }

        }

    </style>

    @stack('styles')

</head>

<body>

<div class="module-launcher-layout">


    {{-- ============================================================
         TOP BAR
    ============================================================= --}}

    <header class="module-launcher-topbar">

        <a
            href="{{ route('company.modules.index') }}"
            class="module-launcher-brand"
        >

            <span class="module-launcher-brand-icon">
                ▦
            </span>

            <span class="module-launcher-brand-text">

                <strong>
                    NEXORA ERP
                </strong>

                <span>
                    Applications
                </span>

            </span>

        </a>


        {{-- UTILISATEUR --}}

        @auth

            <div class="module-launcher-user">

                <div class="module-launcher-user-avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name,
                            0,
                            1
                        )
                    ) }}

                </div>

                <div class="module-launcher-user-info">

                    <strong>
                        {{ auth()->user()->name }}
                    </strong>

                    <span>
                        {{ $company->name ?? 'Entreprise' }}
                    </span>

                </div>
                <form method="POST" action="{{ route('logout') }}" class="modules-logout-form">
        @csrf

        <button type="submit" class="modules-logout-btn" title="Se déconnecter">
            <i data-lucide="log-out"></i>
            <span>Déconnexion</span>
        </button>
    </form>

            </div>

        @endauth

    </header>


    {{-- ============================================================
         CONTENT
    ============================================================= --}}

    <main class="module-launcher-main">

        <div class="module-launcher-container">

            @yield('content')

        </div>

    </main>


    {{-- ============================================================
         FOOTER
    ============================================================= --}}

    <footer class="module-launcher-footer">

        ERP SaaS
        <span>•</span>
        Sélection des applications

    </footer>

</div>

@stack('scripts')

</body>

</html>