@extends('layouts.superadmin')

@section('title', 'Paramètres')

@section('content')

<div class="sa-settings">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="sa-settings-header">

        <div class="sa-settings-header-content">

            <div class="sa-settings-breadcrumb">
                <span>Administration</span>
                <i data-lucide="chevron-right"></i>
                <strong>Paramètres</strong>
            </div>

            <h1>Paramètres de la plateforme</h1>

            <p>
                Gérez l'identité, l'apparence et la configuration
                globale de votre plateforme ERP SaaS.
            </p>

        </div>

        <div class="sa-settings-header-icon">
            <i data-lucide="settings-2"></i>
        </div>

    </div>


    {{-- =========================================================
         ALERTS
    ========================================================== --}}

    @if(session('success'))

        <div class="sa-settings-alert success">

            <div class="sa-alert-icon">
                <i data-lucide="check-circle-2"></i>
            </div>

            <div class="sa-alert-content">
                <strong>Modification enregistrée</strong>
                <span>{{ session('success') }}</span>
            </div>

            <button type="button" class="sa-alert-close">
                <i data-lucide="x"></i>
            </button>

        </div>

    @endif


    @if($errors->any())

        <div class="sa-settings-alert error">

            <div class="sa-alert-icon">
                <i data-lucide="alert-circle"></i>
            </div>

            <div class="sa-alert-content">

                <strong>Une erreur est survenue</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

            <button type="button" class="sa-alert-close">
                <i data-lucide="x"></i>
            </button>

        </div>

    @endif


    {{-- =========================================================
         LAYOUT
    ========================================================== --}}
    <div class="sa-settings-layout">


        {{-- =====================================================
             SIDEBAR PARAMÈTRES
        ====================================================== --}}
        <aside class="sa-settings-nav">

            <div class="sa-settings-nav-title">

                <span class="sa-nav-title-icon">
                    <i data-lucide="sliders-horizontal"></i>
                </span>

                <span>Configuration</span>

            </div>


            <button
                type="button"
                class="sa-settings-nav-item active"
                data-settings-tab="general"
            >

                <span class="sa-nav-item-icon">
                    <i data-lucide="building-2"></i>
                </span>

                <span class="sa-nav-item-text">
                    <strong>Général</strong>
                    <small>Identité de la plateforme</small>
                </span>

                <i
                    data-lucide="chevron-right"
                    class="sa-nav-item-arrow"
                ></i>

            </button>


            <button
                type="button"
                class="sa-settings-nav-item"
                data-settings-tab="appearance"
            >

                <span class="sa-nav-item-icon">
                    <i data-lucide="palette"></i>
                </span>

                <span class="sa-nav-item-text">
                    <strong>Apparence</strong>
                    <small>Couleurs et thème</small>
                </span>

                <i
                    data-lucide="chevron-right"
                    class="sa-nav-item-arrow"
                ></i>

            </button>


            <button
                type="button"
                class="sa-settings-nav-item"
                data-settings-tab="communication"
            >

                <span class="sa-nav-item-icon">
                    <i data-lucide="messages-square"></i>
                </span>

                <span class="sa-nav-item-text">
                    <strong>Communication</strong>
                    <small>Coordonnées officielles</small>
                </span>

                <i
                    data-lucide="chevron-right"
                    class="sa-nav-item-arrow"
                ></i>

            </button>


            <button
                type="button"
                class="sa-settings-nav-item"
                data-settings-tab="security"
            >

                <span class="sa-nav-item-icon">
                    <i data-lucide="shield-check"></i>
                </span>

                <span class="sa-nav-item-text">
                    <strong>Sécurité</strong>
                    <small>Protection de la plateforme</small>
                </span>

                <i
                    data-lucide="chevron-right"
                    class="sa-nav-item-arrow"
                ></i>

            </button>


            <button
                type="button"
                class="sa-settings-nav-item"
                data-settings-tab="maintenance"
            >

                <span class="sa-nav-item-icon">
                    <i data-lucide="wrench"></i>
                </span>

                <span class="sa-nav-item-text">
                    <strong>Maintenance</strong>
                    <small>État de la plateforme</small>
                </span>

                <i
                    data-lucide="chevron-right"
                    class="sa-nav-item-arrow"
                ></i>

            </button>


            <div class="sa-settings-nav-info">

                <div class="sa-settings-nav-info-icon">
                    <i data-lucide="info"></i>
                </div>

                <div>
                    <strong>Paramètres globaux</strong>

                    <p>
                        Ces paramètres s'appliquent à toute la
                        plateforme et non à une entreprise.
                    </p>
                </div>

            </div>

        </aside>


        {{-- =====================================================
             CONTENU
        ====================================================== --}}
        <main class="sa-settings-content">


            {{-- =================================================
                 FORMULAIRE PRINCIPAL
            ================================================== --}}
            <form
                id="platformSettingsForm"
                action="{{ route('superadmin.settings.update') }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf
                @method('PUT')


                {{-- =================================================
                     GÉNÉRAL
                ================================================== --}}
                <section
                    class="sa-settings-panel active"
                    data-settings-panel="general"
                >

                    <div class="sa-panel-heading">

                        <div class="sa-panel-heading-icon">
                            <i data-lucide="building-2"></i>
                        </div>

                        <div>
                            <h2>Informations générales</h2>

                            <p>
                                Configurez l'identité principale
                                de votre plateforme.
                            </p>
                        </div>

                    </div>


                    {{-- IDENTITÉ --}}
                    <div class="sa-settings-card">

                        <div class="sa-card-heading">

                            <div class="sa-card-heading-icon">
                                <i data-lucide="badge"></i>
                            </div>

                            <div>
                                <h3>Identité de la plateforme</h3>

                                <p>
                                    Ces informations sont utilisées
                                    dans l'administration et l'interface
                                    de la plateforme.
                                </p>
                            </div>

                        </div>


                        <div class="sa-form-grid">

                            <div class="sa-form-group">

                                <label for="platform_name">
                                    Nom de la plateforme
                                    <span>*</span>
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="type"></i>

                                    <input
                                        type="text"
                                        id="platform_name"
                                        name="platform_name"
                                        value="{{ old('platform_name', $settings['platform_name']) }}"
                                        placeholder="ERP SaaS"
                                        maxlength="150"
                                        required
                                    >

                                </div>

                            </div>


                            <div class="sa-form-group">

                                <label for="platform_slogan">
                                    Slogan
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="quote"></i>

                                    <input
                                        type="text"
                                        id="platform_slogan"
                                        name="platform_slogan"
                                        value="{{ old('platform_slogan', $settings['platform_slogan']) }}"
                                        placeholder="Votre gestion simplifiée"
                                        maxlength="255"
                                    >

                                </div>

                            </div>


                            <div class="sa-form-group">

                                <label for="currency">
                                    Devise
                                    <span>*</span>
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="coins"></i>

                                    <input
                                        type="text"
                                        id="currency"
                                        name="currency"
                                        value="{{ old('currency', $settings['currency']) }}"
                                        placeholder="XAF"
                                        maxlength="10"
                                        required
                                    >

                                </div>

                            </div>


                            <div class="sa-form-group">

                                <label for="currency_symbol">
                                    Symbole monétaire
                                    <span>*</span>
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="badge-dollar-sign"></i>

                                    <input
                                        type="text"
                                        id="currency_symbol"
                                        name="currency_symbol"
                                        value="{{ old('currency_symbol', $settings['currency_symbol']) }}"
                                        placeholder="FCFA"
                                        maxlength="20"
                                        required
                                    >

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- LOGO --}}
                    <div class="sa-settings-card">

                        <div class="sa-card-heading">

                            <div class="sa-card-heading-icon">
                                <i data-lucide="image"></i>
                            </div>

                            <div>
                                <h3>Logo de la plateforme</h3>

                                <p>
                                    Le logo sera affiché dans
                                    l'administration Super Admin.
                                </p>
                            </div>

                        </div>


                        <div class="sa-logo-upload">

                            <div class="sa-logo-preview">

                                @if(!empty($settings['platform_logo']))

                                    <img
                                        src="{{ asset('storage/' . $settings['platform_logo']) }}"
                                        alt="Logo de la plateforme"
                                    >

                                @else

                                    <div class="sa-logo-placeholder">
                                        <i data-lucide="image"></i>
                                    </div>

                                @endif

                            </div>


                            <div class="sa-logo-upload-info">

                                <h4>Logo actuel</h4>

                                <p>
                                    Formats acceptés : JPG, PNG,
                                    WEBP ou SVG.
                                </p>

                                <p>
                                    Taille maximale : 2 Mo.
                                </p>

                                <label
                                    for="platform_logo"
                                    class="sa-upload-button"
                                >
                                    <i data-lucide="upload"></i>
                                    Choisir un logo
                                </label>

                                <input
                                    type="file"
                                    id="platform_logo"
                                    name="platform_logo"
                                    accept=".jpg,.jpeg,.png,.webp,.svg"
                                    hidden
                                >

                                <div
                                    id="logoFileName"
                                    class="sa-selected-file"
                                ></div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         BOUTON SAUVEGARDE
                    ================================================== --}}
                    <div class="sa-panel-actions">

                        <button
                            type="submit"
                            class="sa-save-button"
                        >
                            <i data-lucide="save"></i>
                            Enregistrer les modifications
                        </button>

                    </div>

                </section>


                {{-- =================================================
                     APPARENCE
                ================================================== --}}
                <section
                    class="sa-settings-panel"
                    data-settings-panel="appearance"
                >

                    <div class="sa-panel-heading">

                        <div class="sa-panel-heading-icon">
                            <i data-lucide="palette"></i>
                        </div>

                        <div>
                            <h2>Apparence</h2>

                            <p>
                                Personnalisez les couleurs et le thème
                                de votre plateforme.
                            </p>
                        </div>

                    </div>


                    {{-- COULEURS --}}
                    <div class="sa-settings-card">

                        <div class="sa-card-heading">

                            <div class="sa-card-heading-icon">
                                <i data-lucide="paintbrush"></i>
                            </div>

                            <div>
                                <h3>Couleurs</h3>

                                <p>
                                    Définissez les couleurs principales
                                    de l'administration.
                                </p>
                            </div>

                        </div>


                        <div class="sa-form-grid">


                            {{-- PRIMARY --}}
                            <div class="sa-form-group">

                                <label for="primary_color">
                                    Couleur principale
                                </label>

                                <div class="sa-color-control">

                                    <input
                                        type="color"
                                        id="primary_color_picker"
                                        value="{{ old('primary_color', $settings['primary_color']) }}"
                                    >

                                    <input
                                        type="text"
                                        id="primary_color"
                                        name="primary_color"
                                        value="{{ old('primary_color', $settings['primary_color']) }}"
                                        maxlength="7"
                                        pattern="^#[0-9A-Fa-f]{6}$"
                                    >

                                </div>

                            </div>


                            {{-- SECONDARY --}}
                            <div class="sa-form-group">

                                <label for="secondary_color">
                                    Couleur secondaire
                                </label>

                                <div class="sa-color-control">

                                    <input
                                        type="color"
                                        id="secondary_color_picker"
                                        value="{{ old('secondary_color', $settings['secondary_color']) }}"
                                    >

                                    <input
                                        type="text"
                                        id="secondary_color"
                                        name="secondary_color"
                                        value="{{ old('secondary_color', $settings['secondary_color']) }}"
                                        maxlength="7"
                                        pattern="^#[0-9A-Fa-f]{6}$"
                                    >

                                </div>

                            </div>


                            {{-- SIDEBAR --}}
                            <div class="sa-form-group">

                                <label for="sidebar_color">
                                    Couleur de la sidebar
                                </label>

                                <div class="sa-color-control">

                                    <input
                                        type="color"
                                        id="sidebar_color_picker"
                                        value="{{ old('sidebar_color', $settings['sidebar_color']) }}"
                                    >

                                    <input
                                        type="text"
                                        id="sidebar_color"
                                        name="sidebar_color"
                                        value="{{ old('sidebar_color', $settings['sidebar_color']) }}"
                                        maxlength="7"
                                        pattern="^#[0-9A-Fa-f]{6}$"
                                    >

                                </div>

                            </div>


                            {{-- SIDEBAR TEXT --}}
                            <div class="sa-form-group">

                                <label for="sidebar_text_color">
                                    Couleur du texte sidebar
                                </label>

                                <div class="sa-color-control">

                                    <input
                                        type="color"
                                        id="sidebar_text_color_picker"
                                        value="{{ old('sidebar_text_color', $settings['sidebar_text_color']) }}"
                                    >

                                    <input
                                        type="text"
                                        id="sidebar_text_color"
                                        name="sidebar_text_color"
                                        value="{{ old('sidebar_text_color', $settings['sidebar_text_color']) }}"
                                        maxlength="7"
                                        pattern="^#[0-9A-Fa-f]{6}$"
                                    >

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- THÈME --}}
                    <div class="sa-settings-card">

                        <div class="sa-card-heading">

                            <div class="sa-card-heading-icon">
                                <i data-lucide="sun-moon"></i>
                            </div>

                            <div>
                                <h3>Mode d'affichage</h3>

                                <p>
                                    Choisissez le thème de l'administration.
                                </p>
                            </div>

                        </div>


                        <div class="sa-theme-options">

                            <label class="sa-theme-option">

                                <input
                                    type="radio"
                                    name="theme_mode"
                                    value="light"
                                    {{ old('theme_mode', $settings['theme_mode']) === 'light' ? 'checked' : '' }}
                                >

                                <span class="sa-theme-card">

                                    <span class="sa-theme-icon">
                                        <i data-lucide="sun"></i>
                                    </span>

                                    <span class="sa-theme-text">
                                        <strong>Clair</strong>
                                        <small>Interface claire</small>
                                    </span>

                                </span>

                            </label>


                            <label class="sa-theme-option">

                                <input
                                    type="radio"
                                    name="theme_mode"
                                    value="dark"
                                    {{ old('theme_mode', $settings['theme_mode']) === 'dark' ? 'checked' : '' }}
                                >

                                <span class="sa-theme-card">

                                    <span class="sa-theme-icon">
                                        <i data-lucide="moon"></i>
                                    </span>

                                    <span class="sa-theme-text">
                                        <strong>Sombre</strong>
                                        <small>Interface sombre</small>
                                    </span>

                                </span>

                            </label>


                            <label class="sa-theme-option">

                                <input
                                    type="radio"
                                    name="theme_mode"
                                    value="system"
                                    {{ old('theme_mode', $settings['theme_mode']) === 'system' ? 'checked' : '' }}
                                >

                                <span class="sa-theme-card">

                                    <span class="sa-theme-icon">
                                        <i data-lucide="monitor"></i>
                                    </span>

                                    <span class="sa-theme-text">
                                        <strong>Système</strong>
                                        <small>Selon votre appareil</small>
                                    </span>

                                </span>

                            </label>

                        </div>

                    </div>


                    {{-- APERÇU --}}
                    <div class="sa-settings-card">

                        <div class="sa-card-heading">

                            <div class="sa-card-heading-icon">
                                <i data-lucide="eye"></i>
                            </div>

                            <div>
                                <h3>Aperçu</h3>

                                <p>
                                    Aperçu en direct des couleurs sélectionnées.
                                </p>
                            </div>

                        </div>


                        <div
                            class="sa-live-preview"
                            id="appearancePreview"
                        >

                            <div
                                class="sa-preview-sidebar"
                                id="previewSidebar"
                            >

                                <div class="sa-preview-brand">

                                    <div class="sa-preview-logo">
                                        <i data-lucide="layers-3"></i>
                                    </div>

                                    <strong id="previewPlatformName">
                                        {{ $settings['platform_name'] ?: 'ERP SaaS' }}
                                    </strong>

                                </div>


                                <div class="sa-preview-menu">

                                    <div class="sa-preview-menu-item active">
                                        <i data-lucide="layout-dashboard"></i>
                                        Tableau de bord
                                    </div>

                                    <div class="sa-preview-menu-item">
                                        <i data-lucide="building-2"></i>
                                        Entreprises
                                    </div>

                                    <div class="sa-preview-menu-item">
                                        <i data-lucide="users"></i>
                                        Utilisateurs
                                    </div>

                                    <div class="sa-preview-menu-item">
                                        <i data-lucide="settings"></i>
                                        Paramètres
                                    </div>

                                </div>

                            </div>


                            <div class="sa-preview-main">

                                <div class="sa-preview-topbar">

                                    <span>Administration</span>

                                    <span class="sa-preview-user">
                                        <i data-lucide="user-circle"></i>
                                        Super Admin
                                    </span>

                                </div>


                                <div class="sa-preview-body">

                                    <h4>Aperçu de la plateforme</h4>

                                    <div class="sa-preview-cards">

                                        <div class="sa-preview-stat">
                                            <span></span>
                                            <strong></strong>
                                            <small></small>
                                        </div>

                                        <div class="sa-preview-stat">
                                            <span></span>
                                            <strong></strong>
                                            <small></small>
                                        </div>

                                        <div class="sa-preview-stat">
                                            <span></span>
                                            <strong></strong>
                                            <small></small>
                                        </div>

                                    </div>

                                    <button
                                        type="button"
                                        class="sa-preview-button"
                                        id="previewButton"
                                    >
                                        Exemple de bouton
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- FAVICON --}}
                    <div class="sa-settings-card">

                        <div class="sa-card-heading">

                            <div class="sa-card-heading-icon">
                                <i data-lucide="bookmark"></i>
                            </div>

                            <div>
                                <h3>Favicon</h3>

                                <p>
                                    Icône affichée dans l'onglet
                                    du navigateur.
                                </p>
                            </div>

                        </div>


                        <div class="sa-favicon-upload">

                            <div class="sa-favicon-preview">

                                @if(!empty($settings['favicon']))

                                    <img
                                        src="{{ asset('storage/' . $settings['favicon']) }}"
                                        alt="Favicon"
                                    >

                                @else

                                    <i data-lucide="globe-2"></i>

                                @endif

                            </div>


                            <div class="sa-favicon-info">

                                <label
                                    for="favicon"
                                    class="sa-upload-button"
                                >
                                    <i data-lucide="upload"></i>
                                    Choisir une favicon
                                </label>

                                <input
                                    type="file"
                                    id="favicon"
                                    name="favicon"
                                    accept=".png,.ico,.jpg,.jpeg"
                                    hidden
                                >

                                <p>
                                    PNG, ICO, JPG ou JPEG.
                                    Taille maximale : 1 Mo.
                                </p>

                                <div
                                    id="faviconFileName"
                                    class="sa-selected-file"
                                ></div>

                            </div>

                        </div>

                    </div>


                    <div class="sa-panel-actions">

                        <button
                            type="submit"
                            class="sa-save-button"
                        >
                            <i data-lucide="save"></i>
                            Enregistrer les modifications
                        </button>

                    </div>

                </section>


                {{-- =================================================
                     COMMUNICATION
                ================================================== --}}
                <section
                    class="sa-settings-panel"
                    data-settings-panel="communication"
                >

                    <div class="sa-panel-heading">

                        <div class="sa-panel-heading-icon">
                            <i data-lucide="messages-square"></i>
                        </div>

                        <div>
                            <h2>Communication</h2>

                            <p>
                                Configurez les coordonnées officielles
                                de la plateforme.
                            </p>
                        </div>

                    </div>


                    <div class="sa-settings-card">

                        <div class="sa-card-heading">

                            <div class="sa-card-heading-icon">
                                <i data-lucide="contact"></i>
                            </div>

                            <div>
                                <h3>Coordonnées administratives</h3>

                                <p>
                                    Ces informations peuvent être utilisées
                                    dans les communications de la plateforme.
                                </p>
                            </div>

                        </div>


                        <div class="sa-form-grid">

                            <div class="sa-form-group">

                                <label for="admin_email">
                                    Adresse e-mail
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="mail"></i>

                                    <input
                                        type="email"
                                        id="admin_email"
                                        name="admin_email"
                                        value="{{ old('admin_email', $settings['admin_email']) }}"
                                        placeholder="admin@erp-saas.com"
                                    >

                                </div>

                            </div>


                            <div class="sa-form-group">

                                <label for="admin_phone">
                                    Téléphone
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="phone"></i>

                                    <input
                                        type="text"
                                        id="admin_phone"
                                        name="admin_phone"
                                        value="{{ old('admin_phone', $settings['admin_phone']) }}"
                                        placeholder="+237 6XX XXX XXX"
                                    >

                                </div>

                            </div>


                            <div class="sa-form-group full">

                                <label for="address">
                                    Adresse
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="map-pin"></i>

                                    <input
                                        type="text"
                                        id="address"
                                        name="address"
                                        value="{{ old('address', $settings['address']) }}"
                                        placeholder="Adresse de l'administration"
                                    >

                                </div>

                            </div>


                            <div class="sa-form-group">

                                <label for="city">
                                    Ville
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="building"></i>

                                    <input
                                        type="text"
                                        id="city"
                                        name="city"
                                        value="{{ old('city', $settings['city']) }}"
                                        placeholder="Yaoundé"
                                    >

                                </div>

                            </div>


                            <div class="sa-form-group">

                                <label for="country">
                                    Pays
                                </label>

                                <div class="sa-input-icon">

                                    <i data-lucide="flag"></i>

                                    <input
                                        type="text"
                                        id="country"
                                        name="country"
                                        value="{{ old('country', $settings['country']) }}"
                                        placeholder="Cameroun"
                                    >

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="sa-panel-actions">

                        <button
                            type="submit"
                            class="sa-save-button"
                        >
                            <i data-lucide="save"></i>
                            Enregistrer les modifications
                        </button>

                    </div>

                </section>


                {{-- =================================================
                     SÉCURITÉ
                ================================================== --}}
                <section
                    class="sa-settings-panel"
                    data-settings-panel="security"
                >

                    <div class="sa-panel-heading">

                        <div class="sa-panel-heading-icon">
                            <i data-lucide="shield-check"></i>
                        </div>

                        <div>
                            <h2>Sécurité</h2>

                            <p>
                                Configuration de la sécurité
                                de la plateforme.
                            </p>
                        </div>

                    </div>


                    <div class="sa-coming-soon">

                        <div class="sa-coming-soon-icon">
                            <i data-lucide="shield"></i>
                        </div>

                        <h3>Configuration de sécurité</h3>

                        <p>
                            Cette section sera utilisée pour les fonctions
                            avancées de sécurité : authentification à deux
                            facteurs, gestion des sessions, restrictions
                            d'accès et autres protections.
                        </p>

                        <span>
                            Bientôt disponible
                        </span>

                    </div>

                </section>


                {{-- =================================================
                     MAINTENANCE
                ================================================== --}}
                <section
                    class="sa-settings-panel"
                    data-settings-panel="maintenance"
                >

                    <div class="sa-panel-heading">

                        <div class="sa-panel-heading-icon">
                            <i data-lucide="wrench"></i>
                        </div>

                        <div>
                            <h2>Maintenance</h2>

                            <p>
                                État général et outils de maintenance
                                de la plateforme.
                            </p>
                        </div>

                    </div>


                    <div class="sa-maintenance-grid">

                        <div class="sa-maintenance-card">

                            <div class="sa-maintenance-icon">
                                <i data-lucide="database"></i>
                            </div>

                            <div>
                                <h3>Base de données</h3>

                                <p>
                                    Base de données opérationnelle.
                                </p>
                            </div>

                            <span class="sa-status active">
                                Opérationnelle
                            </span>

                        </div>


                        <div class="sa-maintenance-card">

                            <div class="sa-maintenance-icon">
                                <i data-lucide="server"></i>
                            </div>

                            <div>
                                <h3>Application</h3>

                                <p>
                                    Application ERP SaaS en ligne.
                                </p>
                            </div>

                            <span class="sa-status active">
                                En ligne
                            </span>

                        </div>


                        <div class="sa-maintenance-card">

                            <div class="sa-maintenance-icon">
                                <i data-lucide="activity"></i>
                            </div>

                            <div>
                                <h3>Services</h3>

                                <p>
                                    Services de la plateforme disponibles.
                                </p>
                            </div>

                            <span class="sa-status active">
                                Opérationnels
                            </span>

                        </div>

                    </div>


                    <div class="sa-maintenance-notice">

                        <i data-lucide="info"></i>

                        <div>
                            <strong>Mode maintenance</strong>

                            <p>
                                La gestion du mode maintenance sera ajoutée
                                dans une prochaine étape.
                            </p>
                        </div>

                    </div>

                </section>

            </form>


            {{-- =====================================================
                 SUPPRESSION LOGO
                 FORMULAIRE INDÉPENDANT
            ====================================================== --}}
            @if(!empty($settings['platform_logo']))

                <form
                    action="{{ route('superadmin.settings.remove-logo') }}"
                    method="POST"
                    class="sa-hidden-form"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        id="removeLogoButton"
                    >
                        Supprimer le logo
                    </button>

                </form>

            @endif


            {{-- =====================================================
                 SUPPRESSION FAVICON
                 FORMULAIRE INDÉPENDANT
            ====================================================== --}}
            @if(!empty($settings['favicon']))

                <form
                    action="{{ route('superadmin.settings.remove-favicon') }}"
                    method="POST"
                    class="sa-hidden-form"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        id="removeFaviconButton"
                    >
                        Supprimer la favicon
                    </button>

                </form>

            @endif

        </main>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | LUCIDE
    |--------------------------------------------------------------------------
    */

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }


    /*
    |--------------------------------------------------------------------------
    | TABS
    |--------------------------------------------------------------------------
    */

    const tabs = document.querySelectorAll(
        '[data-settings-tab]'
    );

    const panels = document.querySelectorAll(
        '[data-settings-panel]'
    );


    tabs.forEach(tab => {

        tab.addEventListener('click', function () {

            const target = this.dataset.settingsTab;

            tabs.forEach(item => {
                item.classList.remove('active');
            });

            panels.forEach(panel => {
                panel.classList.remove('active');
            });

            this.classList.add('active');

            const panel = document.querySelector(
                `[data-settings-panel="${target}"]`
            );

            if (panel) {
                panel.classList.add('active');
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | COULEURS
    |--------------------------------------------------------------------------
    */

    const colorFields = [
        'primary',
        'secondary',
        'sidebar',
        'sidebar_text'
    ];


    colorFields.forEach(name => {

        const picker = document.getElementById(
            `${name}_color_picker`
        );

        const input = document.getElementById(
            `${name}_color`
        );

        if (!picker || !input) {
            return;
        }


        picker.addEventListener('input', function () {

            input.value = this.value;

            updatePreview();

        });


        input.addEventListener('input', function () {

            let value = this.value.trim();

            if (
                value.length === 6 &&
                !value.startsWith('#')
            ) {
                value = '#' + value;
                this.value = value;
            }

            if (
                /^#[0-9A-Fa-f]{6}$/.test(value)
            ) {

                picker.value = value;

                updatePreview();

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | NOM PLATEFORME
    |--------------------------------------------------------------------------
    */

    const platformNameInput =
        document.getElementById('platform_name');

    const previewPlatformName =
        document.getElementById('previewPlatformName');


    if (
        platformNameInput &&
        previewPlatformName
    ) {

        platformNameInput.addEventListener(
            'input',
            function () {

                previewPlatformName.textContent =
                    this.value || 'ERP SaaS';

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | APERÇU
    |--------------------------------------------------------------------------
    */

    function updatePreview() {

        const primary =
            document.getElementById('primary_color')?.value;

        const secondary =
            document.getElementById('secondary_color')?.value;

        const sidebar =
            document.getElementById('sidebar_color')?.value;

        const sidebarText =
            document.getElementById('sidebar_text_color')?.value;


        const previewSidebar =
            document.getElementById('previewSidebar');

        const previewButton =
            document.getElementById('previewButton');


        if (previewSidebar && sidebar) {
            previewSidebar.style.background = sidebar;
        }


        if (previewSidebar && sidebarText) {
            previewSidebar.style.color = sidebarText;
        }


        if (previewButton && primary) {
            previewButton.style.background = primary;
        }


        document
            .querySelectorAll('.sa-preview-menu-item.active')
            .forEach(item => {

                if (primary) {
                    item.style.background = primary;
                }

            });


        document
            .querySelectorAll('.sa-preview-logo')
            .forEach(item => {

                if (primary) {
                    item.style.background = primary;
                }

            });


        document
            .querySelectorAll('.sa-preview-topbar')
            .forEach(item => {

                if (secondary) {
                    item.style.borderColor = secondary;
                }

            });

    }


    updatePreview();


    /*
    |--------------------------------------------------------------------------
    | LOGO
    |--------------------------------------------------------------------------
    */

    const logoInput =
        document.getElementById('platform_logo');

    const logoFileName =
        document.getElementById('logoFileName');


    if (logoInput && logoFileName) {

        logoInput.addEventListener(
            'change',
            function () {

                if (this.files.length > 0) {

                    logoFileName.textContent =
                        'Fichier sélectionné : ' +
                        this.files[0].name;

                } else {

                    logoFileName.textContent = '';

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FAVICON
    |--------------------------------------------------------------------------
    */

    const faviconInput =
        document.getElementById('favicon');

    const faviconFileName =
        document.getElementById('faviconFileName');


    if (faviconInput && faviconFileName) {

        faviconInput.addEventListener(
            'change',
            function () {

                if (this.files.length > 0) {

                    faviconFileName.textContent =
                        'Fichier sélectionné : ' +
                        this.files[0].name;

                } else {

                    faviconFileName.textContent = '';

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ALERTES
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.sa-alert-close')
        .forEach(button => {

            button.addEventListener(
                'click',
                function () {

                    const alert =
                        this.closest('.sa-settings-alert');

                    if (alert) {
                        alert.remove();
                    }

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION SUPPRESSION LOGO
    |--------------------------------------------------------------------------
    */

    const removeLogoButton =
        document.getElementById('removeLogoButton');

    if (removeLogoButton) {

        removeLogoButton.addEventListener(
            'click',
            function (event) {

                if (
                    !confirm(
                        'Voulez-vous vraiment supprimer le logo de la plateforme ?'
                    )
                ) {
                    event.preventDefault();
                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION SUPPRESSION FAVICON
    |--------------------------------------------------------------------------
    */

    const removeFaviconButton =
        document.getElementById('removeFaviconButton');

    if (removeFaviconButton) {

        removeFaviconButton.addEventListener(
            'click',
            function (event) {

                if (
                    !confirm(
                        'Voulez-vous vraiment supprimer la favicon ?'
                    )
                ) {
                    event.preventDefault();
                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    const settingsForm =
        document.getElementById('platformSettingsForm');


    if (settingsForm) {

        settingsForm.addEventListener(
            'submit',
            function () {

                const buttons =
                    this.querySelectorAll(
                        '.sa-save-button'
                    );


                buttons.forEach(button => {

                    button.disabled = true;

                    button.innerHTML = `
                        <i data-lucide="loader-circle"></i>
                        Enregistrement...
                    `;

                });


                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

            }
        );

    }

});

</script>

@endpush