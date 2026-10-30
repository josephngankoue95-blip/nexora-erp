<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion — ERP SaaS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        body {
            overflow-x: hidden;
        }


        /* =====================================================
           PAGE
        ===================================================== */

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

            background:
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, .35), transparent 35%),
                radial-gradient(circle at 90% 80%, rgba(99, 102, 241, .30), transparent 35%),
                linear-gradient(135deg, #07111f, #0f1d35 50%, #101827);

            position: relative;
            overflow: hidden;
        }


        /* =====================================================
           FOND ANIMÉ
        ===================================================== */

        .animated-background {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .animated-background span {
            position: absolute;
            display: block;

            width: 80px;
            height: 80px;

            background: rgba(255, 255, 255, .06);

            border: 1px solid rgba(255, 255, 255, .08);

            border-radius: 20px;

            backdrop-filter: blur(5px);

            animation: floating 18s linear infinite;
        }

        .animated-background span:nth-child(1) {
            left: 5%;
            top: 15%;
            width: 90px;
            height: 90px;
            animation-duration: 20s;
        }

        .animated-background span:nth-child(2) {
            left: 25%;
            top: 70%;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            animation-duration: 24s;
        }

        .animated-background span:nth-child(3) {
            left: 48%;
            top: 10%;
            width: 60px;
            height: 60px;
            animation-duration: 17s;
        }

        .animated-background span:nth-child(4) {
            left: 70%;
            top: 60%;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            animation-duration: 27s;
        }

        .animated-background span:nth-child(5) {
            left: 88%;
            top: 15%;
            width: 70px;
            height: 70px;
            animation-duration: 19s;
        }

        .animated-background span:nth-child(6) {
            left: 55%;
            top: 85%;
            width: 100px;
            height: 100px;
            animation-duration: 22s;
        }


        @keyframes floating {

            0% {
                transform:
                    translateY(0)
                    rotate(0deg);
            }

            50% {
                transform:
                    translateY(-80px)
                    rotate(180deg);
            }

            100% {
                transform:
                    translateY(0)
                    rotate(360deg);
            }
        }


        /* =====================================================
           CONTAINER PRINCIPAL
        ===================================================== */

        .login-container {
            width: min(1100px, 92%);
            min-height: 650px;

            position: relative;
            z-index: 2;

            display: grid;
            grid-template-columns: 1fr 1fr;

            background: rgba(255, 255, 255, .96);

            border-radius: 28px;

            overflow: hidden;

            box-shadow:
                0 35px 90px rgba(0, 0, 0, .35),
                0 10px 30px rgba(0, 0, 0, .15);
        }


        /* =====================================================
           COMPARTIMENT GAUCHE
        ===================================================== */

        .login-presentation {
            position: relative;

            padding: 60px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            color: white;

            background:
                linear-gradient(
                    145deg,
                    rgba(15, 23, 42, .97),
                    rgba(30, 64, 175, .94)
                );

            overflow: hidden;
        }


        .login-presentation::before {
            content: "";

            position: absolute;

            width: 400px;
            height: 400px;

            border-radius: 50%;

            background: rgba(59, 130, 246, .25);

            top: -180px;
            right: -150px;

            filter: blur(10px);

            animation: pulseCircle 7s ease-in-out infinite;
        }


        .login-presentation::after {
            content: "";

            position: absolute;

            width: 300px;
            height: 300px;

            border-radius: 50%;

            background: rgba(99, 102, 241, .20);

            bottom: -150px;
            left: -100px;

            filter: blur(10px);

            animation: pulseCircle 9s ease-in-out infinite reverse;
        }


        @keyframes pulseCircle {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.25);
            }
        }


        .presentation-content {
            position: relative;
            z-index: 2;
        }


        /* =====================================================
           LOGO
        ===================================================== */

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;

            margin-bottom: 50px;
        }


        .brand-logo {
            width: 58px;
            height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 16px;

            background: rgba(255, 255, 255, .15);

            border: 1px solid rgba(255, 255, 255, .2);

            font-size: 18px;
            font-weight: 800;

            backdrop-filter: blur(10px);
        }


        .brand-name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .5px;
        }


        /* =====================================================
           TEXTE PRESENTATION
        ===================================================== */

        .login-presentation h2 {
            font-size: 42px;

            line-height: 1.15;

            margin-bottom: 22px;

            letter-spacing: -1px;
        }


        .login-presentation h2 span {
            color: #93c5fd;
        }


        .presentation-description {
            max-width: 450px;

            color: rgba(255, 255, 255, .78);

            font-size: 16px;

            line-height: 1.7;

            margin-bottom: 35px;
        }


        /* =====================================================
           FEATURES
        ===================================================== */

        .features {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }


        .feature {
            display: flex;
            align-items: center;
            gap: 14px;

            color: rgba(255, 255, 255, .9);

            font-size: 14px;
        }


        .feature-icon {
            width: 34px;
            height: 34px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;

            background: rgba(255, 255, 255, .12);

            color: #bfdbfe;

            font-size: 16px;
        }


        /* =====================================================
           COMPARTIMENT DROIT
        ===================================================== */

        .login-card {
            padding: 60px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            background: white;
        }


        .login-header {
            margin-bottom: 35px;
        }


        .login-header h1 {
            color: #111827;

            font-size: 32px;

            margin-bottom: 8px;

            letter-spacing: -.7px;
        }


        .login-header p {
            color: #6b7280;

            font-size: 14px;
        }


        /* =====================================================
           ALERT
        ===================================================== */

        .alert {
            padding: 13px 15px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        .alert-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }


        /* =====================================================
           FORMULAIRE
        ===================================================== */

        .form-group {
            margin-bottom: 22px;
        }


        .form-group label {
            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 600;

            color: #374151;
        }


        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="text"] {
            width: 100%;

            height: 50px;

            padding: 0 15px;

            border: 1px solid #d1d5db;

            border-radius: 11px;

            background: #f9fafb;

            color: #111827;

            font-size: 14px;

            outline: none;

            transition: .25s ease;
        }


        .form-group input:focus {
            background: white;

            border-color: #3b82f6;

            box-shadow:
                0 0 0 4px rgba(59, 130, 246, .10);
        }


        .form-group input::placeholder {
            color: #9ca3af;
        }


        /* =====================================================
           PASSWORD
        ===================================================== */

        .password-wrapper {
            position: relative;
        }


        .password-wrapper input {
            padding-right: 50px !important;
        }


        .password-toggle {
            position: absolute;

            right: 8px;
            top: 50%;

            transform: translateY(-50%);

            width: 38px;
            height: 38px;

            border: none;

            background: transparent;

            cursor: pointer;

            border-radius: 8px;

            font-size: 17px;

            transition: .2s;
        }


        .password-toggle:hover {
            background: #f3f4f6;
        }


        /* =====================================================
           ERREURS
        ===================================================== */

        .error-message {
            display: block;

            margin-top: 7px;

            color: #dc2626;

            font-size: 12px;
        }


        /* =====================================================
           REMEMBER
        ===================================================== */

        .remember-row {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 10px;

            margin-bottom: 28px;

            font-size: 13px;
        }


        .remember-row label {
            display: flex;
            align-items: center;

            gap: 8px;

            color: #6b7280;

            cursor: pointer;
        }


        .remember-row input[type="checkbox"] {
            width: 15px;
            height: 15px;

            accent-color: #2563eb;

            cursor: pointer;
        }


        .remember-row a {
            color: #2563eb;

            text-decoration: none;

            font-weight: 600;
        }


        .remember-row a:hover {
            text-decoration: underline;
        }


        /* =====================================================
           BOUTON
        ===================================================== */

        .login-button {
            width: 100%;

            height: 52px;

            border: none;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #4f46e5
                );

            color: white;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 8px 20px rgba(37, 99, 235, .25);

            transition: all .25s ease;
        }


        .login-button:hover {
            transform: translateY(-2px);

            box-shadow:
                0 12px 28px rgba(37, 99, 235, .35);
        }


        .login-button:active {
            transform: translateY(0);
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .login-footer {
            margin-top: 35px;

            text-align: center;
        }


        .login-footer p {
            color: #9ca3af;

            font-size: 12px;
        }


        /* =====================================================
           RESPONSIVE TABLETTE
        ===================================================== */

        @media (max-width: 850px) {

            .login-container {
                grid-template-columns: 1fr;

                max-width: 550px;

                min-height: auto;
            }


            .login-presentation {
                padding: 45px;

                min-height: 330px;
            }


            .login-presentation h2 {
                font-size: 34px;
            }


            .brand {
                margin-bottom: 30px;
            }


            .features {
                display: grid;

                grid-template-columns: 1fr 1fr;
            }


            .login-card {
                padding: 45px;
            }
        }


        /* =====================================================
           RESPONSIVE MOBILE
        ===================================================== */

        @media (max-width: 550px) {

            .login-page {
                padding: 15px;
            }


            .login-container {
                width: 100%;

                border-radius: 20px;
            }


            .login-presentation {
                padding: 35px 25px;

                min-height: auto;
            }


            .login-presentation h2 {
                font-size: 29px;
            }


            .presentation-description {
                font-size: 14px;

                margin-bottom: 25px;
            }


            .features {
                grid-template-columns: 1fr;
            }


            .login-card {
                padding: 35px 25px;
            }


            .login-header h1 {
                font-size: 27px;
            }


            .remember-row {
                align-items: flex-start;

                flex-direction: column;
            }
        }


        /* =====================================================
           PETITS ECRANS
        ===================================================== */

        @media (max-width: 380px) {

            .login-presentation {
                padding: 28px 20px;
            }


            .login-card {
                padding: 30px 20px;
            }


            .login-presentation h2 {
                font-size: 26px;
            }
        }

        
/* =====================================================
   NEXORA ERP — CORRECTION SCROLL RESPONSIVE
===================================================== */

/* Autoriser le défilement vertical */
html,
body {
    width: 100%;
    min-height: 100%;
    height: auto;
    overflow-x: hidden;
    overflow-y: auto;
}

/* Page de connexion */
.login-page {
    min-height: 100vh;
    min-height: 100dvh;
    height: auto;

    overflow-x: hidden;
    overflow-y: auto;

    padding: 30px 15px;
}

/* Conserver les animations en arrière-plan */
.animated-background {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
}

/* Tablettes et mobiles */
@media (max-width: 850px) {

    .login-page {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;

        min-height: 100vh;
        min-height: 100dvh;
        height: auto;

        padding: 25px 15px;

        overflow-y: auto;
        overflow-x: hidden;
    }

    .login-container {
        width: 100%;
        max-width: 550px;
        min-height: 0;
        height: auto;

        display: grid;
        grid-template-columns: 1fr;

        margin: 0 auto;
    }

    .login-presentation,
    .login-card {
        height: auto;
        min-height: 0;
    }
}

/* Smartphones */
@media (max-width: 550px) {

    .login-page {
        padding: 15px 12px 25px;
    }

    .login-container {
        width: 100%;
        max-width: 100%;
        border-radius: 20px;
    }

    .login-presentation {
        padding: 30px 25px;
    }

    .login-card {
        padding: 30px 25px;
    }
}


    </style>
</head>


<body class="login-page">


    <!-- =====================================================
         FOND ANIMÉ
    ====================================================== -->

    <div class="animated-background">

        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>

    </div>



    <!-- =====================================================
         CONTAINER
    ====================================================== -->

    <div class="login-container">


        <!-- =================================================
             COMPARTIMENT GAUCHE
        ================================================== -->

        <div class="login-presentation">

            <div class="presentation-content">


                <!-- LOGO -->

                <div class="brand">

                    <div class="brand-logo">
                        ERP
                    </div>

                    <div class="brand-name">
                        NEXORA
                    </div>

                </div>


                <!-- TITRE -->

                <h2>
                    Gérez votre entreprise
                    <span>plus simplement.</span>
                </h2>


                <p class="presentation-description">

                    Une plateforme moderne et centralisée
                    pour piloter vos activités, vos équipes,
                    vos clients et vos opérations depuis un
                    seul espace.

                </p>


                <!-- FEATURES -->

                <div class="features">

                    <div class="feature">

                        <div class="feature-icon">
                            ✓
                        </div>

                        <span>
                            Gestion centralisée de votre activité
                        </span>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            ✓
                        </div>

                        <span>
                            Suivi des utilisateurs et des équipes
                        </span>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            ✓
                        </div>

                        <span>
                            Données sécurisées et accessibles
                        </span>

                    </div>


                </div>

            </div>

        </div>



        <!-- =================================================
             COMPARTIMENT DROIT
        ================================================== -->

        <div class="login-card">


            <!-- HEADER -->

            <div class="login-header">

                <h1>
                    Bienvenue 👋
                </h1>

                <p>
                    Connectez-vous à votre espace de gestion
                </p>

            </div>



            <!-- STATUS -->

            @if (session('status'))

                <div class="alert alert-success">

                    {{ session('status') }}

                </div>

            @endif



            <!-- FORMULAIRE -->

            <form method="POST" action="{{ route('login') }}">

                @csrf


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Adresse email
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="exemple@email.com"
                    >

                    @error('email')

                        <span class="error-message">
                            {{ $message }}
                        </span>

                    @enderror

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Mot de passe
                    </label>


                    <div class="password-wrapper">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Votre mot de passe"
                        >


                        <button
                            type="button"
                            id="togglePassword"
                            class="password-toggle"
                            aria-label="Afficher le mot de passe"
                        >
                            👁
                        </button>

                    </div>


                    @error('password')

                        <span class="error-message">
                            {{ $message }}
                        </span>

                    @enderror

                </div>



                <!-- REMEMBER -->

                <div class="remember-row">

                    <label>

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        <span>
                            Se souvenir de moi
                        </span>

                    </label>


                    @if (Route::has('password.request'))

                        <a href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>

                    @endif

                </div>



                <!-- BUTTON -->

                <button
                    type="submit"
                    class="login-button"
                >
                    Se connecter
                </button>

            </form>



            <!-- FOOTER -->

            <div class="login-footer">

                <p>
                    © {{ date('Y') }} ERP Nexora — Tous droits réservés
                </p>

            </div>


        </div>

    </div>



    <!-- =====================================================
         PASSWORD TOGGLE
    ====================================================== -->

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const password =
                document.getElementById('password');

            const toggle =
                document.getElementById('togglePassword');


            if (password && toggle) {

                toggle.addEventListener('click', function () {

                    if (password.type === 'password') {

                        password.type = 'text';

                        toggle.textContent = '🙈';

                        toggle.setAttribute(
                            'aria-label',
                            'Masquer le mot de passe'
                        );

                    } else {

                        password.type = 'password';

                        toggle.textContent = '👁';

                        toggle.setAttribute(
                            'aria-label',
                            'Afficher le mot de passe'
                        );

                    }

                });

            }

        });

    </script>

</body>

</html>
