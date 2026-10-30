@extends('layouts.company')

@section('title', 'Permissions utilisateur')

@section('content')

<div class="user-permissions-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="page-header">

        <div class="page-header-content">

            <div class="page-title-icon">
                🔐
            </div>

            <div>
                <h1>Permissions utilisateur</h1>

                <p>
                    Configurez précisément les accès de cet utilisateur.
                </p>
            </div>

        </div>

        <a
            href="{{ route('company.users.index') }}"
            class="btn btn-secondary"
        >
            ← Retour
        </a>

    </div>


    {{-- =========================================================
         UTILISATEUR
    ========================================================== --}}

    <div class="permission-user-card">

        <div class="permission-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div class="permission-user-info">

            <div class="permission-user-main">

                <h2>
                    {{ $user->name }}
                </h2>

                <p>
                    {{ $user->email }}
                </p>

            </div>

            <span class="role-badge">
                {{ $user->roleLabel() }}
            </span>

        </div>

    </div>


    {{-- =========================================================
         INFORMATION
    ========================================================== --}}

    <div class="permission-info">

        <div class="permission-info-icon">
            ℹ️
        </div>

        <div>
            <strong>Comment fonctionnent les permissions ?</strong>

            <p>
                <strong>Par défaut</strong> signifie que l'utilisateur
                conserve les permissions définies par son rôle.
                Vous pouvez également forcer une permission à être
                <strong>autorisée</strong> ou <strong>refusée</strong>
                uniquement pour cet utilisateur.
            </p>
        </div>

    </div>


    {{-- =========================================================
         LÉGENDE
    ========================================================== --}}

    <div class="permission-legend">

        <div class="legend-item">

            <span class="legend-dot default"></span>

            <div>
                <strong>Par défaut</strong>

                <small>
                    Permission héritée du rôle
                </small>
            </div>

        </div>


        <div class="legend-item">

            <span class="legend-dot allow"></span>

            <div>
                <strong>Autorisé</strong>

                <small>
                    Accès forcé pour cet utilisateur
                </small>
            </div>

        </div>


        <div class="legend-item">

            <span class="legend-dot deny"></span>

            <div>
                <strong>Refusé</strong>

                <small>
                    Accès interdit pour cet utilisateur
                </small>
            </div>

        </div>

    </div>


    {{-- =========================================================
         FORMULAIRE
    ========================================================== --}}

    <form
        method="POST"
        action="{{ route('company.users.permissions.update', $user) }}"
        id="permissions-form"
    >

        @csrf

        @method('PUT')


        {{-- =====================================================
             PERMISSIONS
        ====================================================== --}}

        <div class="permissions-container">

            @forelse($permissions as $module => $modulePermissions)

                <div
                    class="permission-module"
                    data-module="{{ $module }}"
                >

                    {{-- =================================================
                         HEADER MODULE
                    ================================================== --}}

                    <div class="permission-module-header">

                        <div class="module-title">

                            <div class="module-icon">

                                @switch($module)

                                    @case('products')
                                        📦
                                        @break

                                    @case('categories')
                                        🏷️
                                        @break

                                    @case('stock')
                                        📊
                                        @break

                                    @case('clients')
                                        👥
                                        @break

                                    @case('suppliers')
                                        🚚
                                        @break

                                    @case('sales')
                                        🛒
                                        @break

                                    @case('purchases')
                                        🧾
                                        @break

                                    @case('payments')
                                        💳
                                        @break

                                    @case('finance')
                                        💰
                                        @break

                                    @case('reports')
                                        📈
                                        @break

                                    @case('services')
                                        🛠️
                                        @break

                                    @case('hotel')
                                        🏨
                                        @break

                                    @case('restaurant')
                                        🍽️
                                        @break

                                    @case('school')
                                        🎓
                                        @break

                                    @case('clinic')
                                        🩺
                                        @break

                                    @case('hospital')
                                        🏥
                                        @break

                                    @case('pharmacy')
                                        💊
                                        @break

                                    @case('ecommerce')
                                        🛍️
                                        @break

                                    @case('vehicle-rental')
                                        🚗
                                        @break

                                    @case('real-estate')
                                        🏠
                                        @break

                                    @case('hr')
                                        👨‍💼
                                        @break

                                    @case('projects')
                                        📋
                                        @break

                                    @case('maintenance')
                                        🔧
                                        @break

                                    @case('documents')
                                        📄
                                        @break

                                    @default
                                        📁

                                @endswitch

                            </div>


                            <div>

                                <h3>

                                    @switch($module)

                                        @case('products')
                                            Produits
                                            @break

                                        @case('categories')
                                            Catégories
                                            @break

                                        @case('stock')
                                            Stock
                                            @break

                                        @case('clients')
                                            Clients
                                            @break

                                        @case('suppliers')
                                            Fournisseurs
                                            @break

                                        @case('sales')
                                            Ventes
                                            @break

                                        @case('purchases')
                                            Achats
                                            @break

                                        @case('payments')
                                            Paiements
                                            @break

                                        @case('finance')
                                            Finances
                                            @break

                                        @case('reports')
                                            Rapports
                                            @break

                                        @case('services')
                                            Prestations
                                            @break

                                        @case('hotel')
                                            Hôtel
                                            @break

                                        @case('restaurant')
                                            Restaurant
                                            @break

                                        @case('school')
                                            Établissement scolaire
                                            @break

                                        @case('clinic')
                                            Clinique
                                            @break

                                        @case('hospital')
                                            Hôpital
                                            @break

                                        @case('pharmacy')
                                            Pharmacie
                                            @break

                                        @case('ecommerce')
                                            E-commerce
                                            @break

                                        @case('vehicle-rental')
                                            Location de véhicules
                                            @break

                                        @case('real-estate')
                                            Immobilier
                                            @break

                                        @case('hr')
                                            Ressources humaines
                                            @break

                                        @case('projects')
                                            Projets
                                            @break

                                        @case('maintenance')
                                            Maintenance
                                            @break

                                        @case('documents')
                                            Documents
                                            @break

                                        @default
                                            {{ ucfirst(str_replace('-', ' ', $module)) }}

                                    @endswitch

                                </h3>

                                <span>
                                    {{ $modulePermissions->count() }}
                                    permission(s)
                                </span>

                            </div>

                        </div>


                        {{-- =================================================
                             ACTIONS RAPIDES DU MODULE
                        ================================================== --}}

                        <div class="module-actions">

                            <button
                                type="button"
                                class="module-action-btn"
                                data-module-action="allow"
                                data-module="{{ $module }}"
                            >
                                Tout autoriser
                            </button>

                            <button
                                type="button"
                                class="module-action-btn deny"
                                data-module-action="deny"
                                data-module="{{ $module }}"
                            >
                                Tout refuser
                            </button>

                            <button
                                type="button"
                                class="module-action-btn default"
                                data-module-action="default"
                                data-module="{{ $module }}"
                            >
                                Réinitialiser
                            </button>

                        </div>

                    </div>


                    {{-- =================================================
                         LISTE DES PERMISSIONS
                    ================================================== --}}

                    <div class="permission-list">

                        @foreach($modulePermissions as $permission)

                            @php

                                $custom = $userPermissions->get(
                                    $permission->id
                                );

                                $state = $custom
                                    ? ($custom->allowed ? 'allow' : 'deny')
                                    : 'default';

                            @endphp


                            <div
                                class="permission-item {{ $state }}"
                                data-permission="{{ $permission->id }}"
                                data-module="{{ $module }}"
                            >

                                {{-- =================================================
                                     TEXTE
                                ================================================== --}}

                                <div class="permission-text">

                                    <div class="permission-name-row">

                                        <strong>
                                            {{ $permission->label }}
                                        </strong>

                                        @if($state === 'allow')

                                            <span class="state-badge allow">
                                                Autorisé
                                            </span>

                                        @elseif($state === 'deny')

                                            <span class="state-badge deny">
                                                Refusé
                                            </span>

                                        @else

                                            <span class="state-badge default">
                                                Par défaut
                                            </span>

                                        @endif

                                    </div>

                                    <small>
                                        {{ $permission->name }}
                                    </small>

                                </div>


                                {{-- =================================================
                                     OPTIONS
                                ================================================== --}}

                                <div class="permission-options">


                                    {{-- PAR DÉFAUT --}}

                                    <label class="permission-radio">

                                        <input
                                            type="radio"
                                            name="permissions[{{ $permission->id }}][state]"
                                            value="default"
                                            {{ $state === 'default' ? 'checked' : '' }}
                                        >

                                        <span class="permission-option default">
                                            <span class="option-icon">
                                                ↺
                                            </span>

                                            Par défaut
                                        </span>

                                    </label>


                                    {{-- AUTORISER --}}

                                    <label class="permission-radio">

                                        <input
                                            type="radio"
                                            name="permissions[{{ $permission->id }}][state]"
                                            value="allow"
                                            {{ $state === 'allow' ? 'checked' : '' }}
                                        >

                                        <span class="permission-option allow">
                                            <span class="option-icon">
                                                ✓
                                            </span>

                                            Autoriser
                                        </span>

                                    </label>


                                    {{-- REFUSER --}}

                                    <label class="permission-radio">

                                        <input
                                            type="radio"
                                            name="permissions[{{ $permission->id }}][state]"
                                            value="deny"
                                            {{ $state === 'deny' ? 'checked' : '' }}
                                        >

                                        <span class="permission-option deny">
                                            <span class="option-icon">
                                                ×
                                            </span>

                                            Refuser
                                        </span>

                                    </label>

                                </div>


                                {{-- =================================================
                                     DONNÉES ENVOYÉES AU CONTRÔLEUR
                                ================================================== --}}

                                <input
                                    type="hidden"
                                    name="permissions[{{ $permission->id }}][id]"
                                    value="{{ $permission->id }}"
                                    class="permission-id"
                                >

                                <input
                                    type="hidden"
                                    name="permissions[{{ $permission->id }}][allowed]"
                                    value="{{ $state === 'allow' ? '1' : '0' }}"
                                    class="permission-allowed"
                                >

                            </div>

                        @endforeach

                    </div>

                </div>

            @empty

                <div class="permissions-empty">

                    <div class="empty-icon">
                        🔒
                    </div>

                    <h3>
                        Aucune permission disponible
                    </h3>

                    <p>
                        Aucun module actuellement activé pour votre entreprise
                        ne possède de permission configurable.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- =========================================================
             ACTIONS
        ========================================================== --}}

        <div class="permissions-actions">

            <a
                href="{{ route('company.users.index') }}"
                class="btn btn-secondary"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="btn btn-primary"
                id="save-permissions"
            >
                <span class="save-icon">💾</span>

                <span>
                    Enregistrer les permissions
                </span>
            </button>

        </div>

    </form>

</div>


{{-- =============================================================
     CSS
============================================================= --}}

<style>

.user-permissions-page {

    --permission-primary: #315bea;
    --permission-primary-dark: #2448c9;

    --permission-success: #16a34a;
    --permission-success-bg: #ecfdf3;

    --permission-danger: #dc2626;
    --permission-danger-bg: #fef2f2;

    --permission-default: #64748b;
    --permission-default-bg: #f8fafc;

    --permission-border: #e2e8f0;
    --permission-text: #0f172a;
    --permission-muted: #64748b;

    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 10px 0 40px;
}


/* =========================================================
   HEADER
========================================================= */

.user-permissions-page .page-header {

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 24px;
}


.user-permissions-page .page-header-content {

    display: flex;
    align-items: center;

    gap: 15px;
}


.user-permissions-page .page-title-icon {

    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 14px;

    background: linear-gradient(
        135deg,
        #315bea,
        #2448c9
    );

    color: #fff;

    font-size: 23px;

    box-shadow:
        0 8px 20px rgba(49, 91, 234, .20);
}


.user-permissions-page .page-header h1 {

    margin: 0;

    color: var(--permission-text);

    font-size: 27px;
    font-weight: 750;

    letter-spacing: -.4px;
}


.user-permissions-page .page-header p {

    margin: 5px 0 0;

    color: var(--permission-muted);

    font-size: 14px;
}


/* =========================================================
   BOUTONS
========================================================= */

.user-permissions-page .btn {

    min-height: 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    padding: 0 17px;

    border-radius: 9px;

    border: 1px solid transparent;

    text-decoration: none;

    font-size: 14px;
    font-weight: 650;

    cursor: pointer;

    transition:
        background .2s ease,
        border-color .2s ease,
        color .2s ease,
        transform .2s ease,
        box-shadow .2s ease;
}


.user-permissions-page .btn:hover {

    transform: translateY(-1px);
}


.user-permissions-page .btn-secondary {

    background: #fff;

    border-color: var(--permission-border);

    color: #334155;
}


.user-permissions-page .btn-secondary:hover {

    background: #f8fafc;

    border-color: #cbd5e1;
}


.user-permissions-page .btn-primary {

    background: var(--permission-primary);

    color: #fff;

    box-shadow:
        0 6px 15px rgba(49, 91, 234, .20);
}


.user-permissions-page .btn-primary:hover {

    background: var(--permission-primary-dark);

    box-shadow:
        0 9px 20px rgba(49, 91, 234, .25);
}


/* =========================================================
   CARTE UTILISATEUR
========================================================= */

.user-permissions-page .permission-user-card {

    display: flex;
    align-items: center;

    gap: 16px;

    padding: 20px 22px;

    margin-bottom: 18px;

    background: #fff;

    border: 1px solid var(--permission-border);

    border-radius: 14px;

    box-shadow:
        0 4px 16px rgba(15, 23, 42, .04);
}


.user-permissions-page .permission-avatar {

    width: 54px;
    height: 54px;

    flex: 0 0 54px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: linear-gradient(
        135deg,
        #315bea,
        #2448c9
    );

    color: #fff;

    font-size: 21px;
    font-weight: 750;

    text-transform: uppercase;
}


.user-permissions-page .permission-user-info {

    flex: 1;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;
}


.user-permissions-page .permission-user-info h2 {

    margin: 0;

    color: var(--permission-text);

    font-size: 18px;
    font-weight: 700;
}


.user-permissions-page .permission-user-info p {

    margin: 4px 0 0;

    color: var(--permission-muted);

    font-size: 13px;
}


.user-permissions-page .role-badge {

    display: inline-flex;
    align-items: center;

    padding: 7px 11px;

    border-radius: 8px;

    background: #eff6ff;

    color: #1d4ed8;

    font-size: 12px;
    font-weight: 700;

    white-space: nowrap;
}


/* =========================================================
   INFO
========================================================= */

.user-permissions-page .permission-info {

    display: flex;

    gap: 13px;

    padding: 15px 17px;

    margin-bottom: 18px;

    border: 1px solid #bfdbfe;

    border-radius: 12px;

    background: #eff6ff;

    color: #1e3a8a;
}


.user-permissions-page .permission-info-icon {

    font-size: 18px;

    flex: 0 0 auto;
}


.user-permissions-page .permission-info strong {

    font-size: 13px;
}


.user-permissions-page .permission-info p {

    margin: 4px 0 0;

    font-size: 13px;

    line-height: 1.6;
}


/* =========================================================
   LÉGENDE
========================================================= */

.user-permissions-page .permission-legend {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 12px;

    margin-bottom: 20px;
}


.user-permissions-page .legend-item {

    display: flex;
    align-items: center;

    gap: 10px;

    padding: 13px 15px;

    background: #fff;

    border: 1px solid var(--permission-border);

    border-radius: 11px;
}


.user-permissions-page .legend-dot {

    width: 10px;
    height: 10px;

    flex: 0 0 10px;

    border-radius: 50%;
}


.user-permissions-page .legend-dot.default {

    background: #94a3b8;
}


.user-permissions-page .legend-dot.allow {

    background: var(--permission-success);
}


.user-permissions-page .legend-dot.deny {

    background: var(--permission-danger);
}


.user-permissions-page .legend-item strong {

    display: block;

    color: #334155;

    font-size: 12px;
}


.user-permissions-page .legend-item small {

    display: block;

    margin-top: 2px;

    color: var(--permission-muted);

    font-size: 11px;
}


/* =========================================================
   MODULE
========================================================= */

.user-permissions-page .permission-module {

    margin-bottom: 18px;

    overflow: hidden;

    background: #fff;

    border: 1px solid var(--permission-border);

    border-radius: 14px;

    box-shadow:
        0 4px 14px rgba(15, 23, 42, .035);
}


.user-permissions-page .permission-module-header {

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 17px 18px;

    background: #f8fafc;

    border-bottom: 1px solid var(--permission-border);
}


.user-permissions-page .module-title {

    display: flex;
    align-items: center;

    gap: 12px;

    min-width: 0;
}


.user-permissions-page .module-icon {

    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 40px;

    border-radius: 10px;

    background: #fff;

    border: 1px solid var(--permission-border);

    font-size: 18px;
}


.user-permissions-page .module-title h3 {

    margin: 0;

    color: var(--permission-text);

    font-size: 15px;
    font-weight: 720;
}


.user-permissions-page .module-title span {

    display: block;

    margin-top: 3px;

    color: var(--permission-muted);

    font-size: 11px;
}


/* =========================================================
   ACTIONS MODULE
========================================================= */

.user-permissions-page .module-actions {

    display: flex;
    align-items: center;

    gap: 6px;

    flex-wrap: wrap;

    justify-content: flex-end;
}


.user-permissions-page .module-action-btn {

    min-height: 32px;

    padding: 0 9px;

    border: 1px solid #dbe3ef;

    border-radius: 7px;

    background: #fff;

    color: #475569;

    font-size: 11px;
    font-weight: 650;

    cursor: pointer;

    transition:
        background .2s ease,
        color .2s ease,
        border-color .2s ease;
}


.user-permissions-page .module-action-btn:hover {

    background: #eff6ff;

    border-color: #bfdbfe;

    color: #1d4ed8;
}


.user-permissions-page .module-action-btn.deny:hover {

    background: #fef2f2;

    border-color: #fecaca;

    color: #dc2626;
}


.user-permissions-page .module-action-btn.default:hover {

    background: #f8fafc;

    border-color: #cbd5e1;

    color: #475569;
}


/* =========================================================
   LISTE PERMISSIONS
========================================================= */

.user-permissions-page .permission-list {

    width: 100%;
}


.user-permissions-page .permission-item {

    position: relative;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    min-height: 74px;

    padding: 13px 18px;

    border-bottom: 1px solid #edf1f6;

    transition:
        background .2s ease,
        border-color .2s ease;
}


.user-permissions-page .permission-item:last-child {

    border-bottom: 0;
}


.user-permissions-page .permission-item:hover {

    background: #fafcff;
}


/* =========================================================
   TEXTE PERMISSION
========================================================= */

.user-permissions-page .permission-text {

    min-width: 0;

    flex: 1;
}


.user-permissions-page .permission-name-row {

    display: flex;
    align-items: center;

    gap: 8px;

    flex-wrap: wrap;
}


.user-permissions-page .permission-text strong {

    color: #1e293b;

    font-size: 13px;
    font-weight: 650;
}


.user-permissions-page .permission-text small {

    display: block;

    margin-top: 3px;

    color: #94a3b8;

    font-family:
        ui-monospace,
        SFMono-Regular,
        Menlo,
        Monaco,
        Consolas,
        monospace;

    font-size: 10px;
}


/* =========================================================
   BADGES
========================================================= */

.user-permissions-page .state-badge {

    display: inline-flex;
    align-items: center;

    padding: 3px 7px;

    border-radius: 5px;

    font-size: 9px;
    font-weight: 750;

    text-transform: uppercase;

    letter-spacing: .3px;
}


.user-permissions-page .state-badge.default {

    background: #f1f5f9;

    color: #64748b;
}


.user-permissions-page .state-badge.allow {

    background: var(--permission-success-bg);

    color: var(--permission-success);
}


.user-permissions-page .state-badge.deny {

    background: var(--permission-danger-bg);

    color: var(--permission-danger);
}


/* =========================================================
   OPTIONS
========================================================= */

.user-permissions-page .permission-options {

    display: flex;
    align-items: center;

    gap: 6px;

    flex-shrink: 0;
}


.user-permissions-page .permission-radio {

    position: relative;

    cursor: pointer;
}


.user-permissions-page .permission-radio input {

    position: absolute;

    width: 1px;
    height: 1px;

    opacity: 0;

    pointer-events: none;
}


.user-permissions-page .permission-option {

    min-height: 34px;

    display: inline-flex;
    align-items: center;

    justify-content: center;

    gap: 5px;

    padding: 0 10px;

    border: 1px solid #dbe3ef;

    border-radius: 7px;

    background: #fff;

    color: #64748b;

    font-size: 11px;
    font-weight: 650;

    transition:
        background .2s ease,
        color .2s ease,
        border-color .2s ease,
        box-shadow .2s ease;
}


.user-permissions-page .option-icon {

    font-size: 12px;
    font-weight: 800;
}


.user-permissions-page .permission-radio input:checked
+ .permission-option.default {

    background: #f1f5f9;

    border-color: #94a3b8;

    color: #475569;

    box-shadow:
        0 0 0 2px rgba(100, 116, 139, .08);
}


.user-permissions-page .permission-radio input:checked
+ .permission-option.allow {

    background: var(--permission-success-bg);

    border-color: #86efac;

    color: var(--permission-success);

    box-shadow:
        0 0 0 2px rgba(22, 163, 74, .08);
}


.user-permissions-page .permission-radio input:checked
+ .permission-option.deny {

    background: var(--permission-danger-bg);

    border-color: #fca5a5;

    color: var(--permission-danger);

    box-shadow:
        0 0 0 2px rgba(220, 38, 38, .08);
}


/* =========================================================
   ÉTAT GLOBAL DE LA LIGNE
========================================================= */

.user-permissions-page .permission-item.allow {

    background: rgba(236, 253, 243, .35);
}


.user-permissions-page .permission-item.deny {

    background: rgba(254, 242, 242, .35);
}


.user-permissions-page .permission-item.default {

    background: #fff;
}


/* =========================================================
   EMPTY
========================================================= */

.user-permissions-page .permissions-empty {

    padding: 60px 25px;

    text-align: center;

    background: #fff;

    border: 1px solid var(--permission-border);

    border-radius: 14px;
}


.user-permissions-page .empty-icon {

    width: 58px;
    height: 58px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin: 0 auto 15px;

    border-radius: 50%;

    background: #f1f5f9;

    font-size: 23px;
}


.user-permissions-page .permissions-empty h3 {

    margin: 0;

    color: #334155;

    font-size: 17px;
}


.user-permissions-page .permissions-empty p {

    max-width: 500px;

    margin: 7px auto 0;

    color: var(--permission-muted);

    font-size: 13px;

    line-height: 1.6;
}


/* =========================================================
   ACTIONS FINALES
========================================================= */

.user-permissions-page .permissions-actions {

    position: sticky;

    bottom: 12px;

    z-index: 10;

    display: flex;
    align-items: center;
    justify-content: flex-end;

    gap: 10px;

    padding: 14px 16px;

    margin-top: 20px;

    background: rgba(255, 255, 255, .94);

    border: 1px solid var(--permission-border);

    border-radius: 12px;

    box-shadow:
        0 10px 30px rgba(15, 23, 42, .10);

    backdrop-filter: blur(10px);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1000px) {

    .user-permissions-page .permission-module-header {

        align-items: flex-start;

        flex-direction: column;
    }

    .user-permissions-page .module-actions {

        justify-content: flex-start;
    }

    .user-permissions-page .permission-item {

        align-items: flex-start;

        flex-direction: column;

        gap: 12px;
    }

    .user-permissions-page .permission-options {

        width: 100%;
    }

}


@media (max-width: 700px) {

    .user-permissions-page {

        padding-bottom: 25px;
    }


    .user-permissions-page .page-header {

        align-items: flex-start;

        flex-direction: column;
    }


    .user-permissions-page .page-header .btn {

        width: 100%;
    }


    .user-permissions-page .permission-user-card {

        align-items: flex-start;
    }


    .user-permissions-page .permission-user-info {

        align-items: flex-start;

        flex-direction: column;

        gap: 9px;
    }


    .user-permissions-page .permission-legend {

        grid-template-columns: 1fr;
    }


    .user-permissions-page .permission-module-header {

        padding: 14px;
    }


    .user-permissions-page .permission-item {

        padding: 14px;
    }


    .user-permissions-page .permission-options {

        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 5px;
    }


    .user-permissions-page .permission-option {

        width: 100%;

        padding: 0 5px;

        font-size: 10px;
    }


    .user-permissions-page .module-actions {

        width: 100%;

        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }


    .user-permissions-page .module-action-btn {

        width: 100%;
    }


    .user-permissions-page .permissions-actions {

        position: static;

        flex-direction: column;
    }


    .user-permissions-page .permissions-actions .btn {

        width: 100%;
    }

}


@media (max-width: 450px) {

    .user-permissions-page .page-title-icon {

        width: 45px;
        height: 45px;

        font-size: 19px;
    }


    .user-permissions-page .page-header h1 {

        font-size: 22px;
    }


    .user-permissions-page .permission-avatar {

        width: 45px;
        height: 45px;

        flex-basis: 45px;
    }


    .user-permissions-page .permission-options {

        grid-template-columns: 1fr;
    }


    .user-permissions-page .permission-option {

        min-height: 38px;
    }


    .user-permissions-page .module-actions {

        grid-template-columns: 1fr;
    }

}

</style>


{{-- =============================================================
     JAVASCRIPT
============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const items = document.querySelectorAll(
        '.permission-item'
    );

    const form = document.getElementById(
        'permissions-form'
    );

    const saveButton = document.getElementById(
        'save-permissions'
    );


    /*
    |--------------------------------------------------------------------------
    | Mettre à jour une permission
    |--------------------------------------------------------------------------
    */

    function updatePermission(item) {

        const selected = item.querySelector(
            'input[type="radio"]:checked'
        );

        if (!selected) {
            return;
        }


        const state = selected.value;


        /*
        |--------------------------------------------------------------------------
        | Classe visuelle
        |--------------------------------------------------------------------------
        */

        item.classList.remove(
            'default',
            'allow',
            'deny'
        );

        item.classList.add(state);


        /*
        |--------------------------------------------------------------------------
        | Champs cachés
        |--------------------------------------------------------------------------
        */

        const idInput = item.querySelector(
            '.permission-id'
        );

        const allowedInput = item.querySelector(
            '.permission-allowed'
        );


        /*
        |--------------------------------------------------------------------------
        | Par défaut
        |--------------------------------------------------------------------------
        |
        | Aucun enregistrement personnalisé ne doit être envoyé.
        |
        */

        if (state === 'default') {

            idInput.disabled = true;

            allowedInput.disabled = true;

        } else {

            /*
            |--------------------------------------------------------------------------
            | Permission personnalisée
            |--------------------------------------------------------------------------
            */

            idInput.disabled = false;

            allowedInput.disabled = false;

            allowedInput.value =
                state === 'allow'
                    ? '1'
                    : '0';
        }


        /*
        |--------------------------------------------------------------------------
        | Badge
        |--------------------------------------------------------------------------
        */

        const badge = item.querySelector(
            '.state-badge'
        );

        if (!badge) {
            return;
        }


        badge.classList.remove(
            'default',
            'allow',
            'deny'
        );

        badge.classList.add(state);


        if (state === 'allow') {

            badge.textContent = 'Autorisé';

        } else if (state === 'deny') {

            badge.textContent = 'Refusé';

        } else {

            badge.textContent = 'Par défaut';
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Initialisation
    |--------------------------------------------------------------------------
    */

    items.forEach(function (item) {

        const radios = item.querySelectorAll(
            'input[type="radio"]'
        );


        radios.forEach(function (radio) {

            radio.addEventListener(
                'change',
                function () {

                    updatePermission(item);

                }
            );

        });


        updatePermission(item);

    });


    /*
    |--------------------------------------------------------------------------
    | Actions rapides par module
    |--------------------------------------------------------------------------
    */

    const moduleActions =
        document.querySelectorAll(
            '[data-module-action]'
        );


    moduleActions.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                const module =
                    button.dataset.module;

                const action =
                    button.dataset.moduleAction;


                const moduleItems =
                    document.querySelectorAll(
                        '.permission-item[data-module="' +
                        module +
                        '"]'
                    );


                moduleItems.forEach(function (item) {

                    const radio =
                        item.querySelector(
                            'input[type="radio"][value="' +
                            action +
                            '"]'
                        );


                    if (!radio) {
                        return;
                    }


                    radio.checked = true;

                    updatePermission(item);

                });

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Empêcher le double envoi
    |--------------------------------------------------------------------------
    */

    if (form && saveButton) {

        form.addEventListener(
            'submit',
            function () {

                saveButton.disabled = true;

                saveButton.style.opacity = '.7';

                saveButton.style.cursor =
                    'not-allowed';

                saveButton.querySelector(
                    'span:last-child'
                ).textContent =
                    'Enregistrement...';

            }
        );

    }

});

</script>

@endsection