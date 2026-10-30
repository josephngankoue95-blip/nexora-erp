
@extends('layouts.website')

@section('title', 'Tableau de bord du site web')

@section('page-title', 'Mon site web')

@section('page-description', 'Gérez les pages, le contenu et l’apparence de votre site.')

@section('content')
<div class="website-dashboard">
    <header class="website-header">
        <div>
            <span class="website-eyebrow">ESPACE DIGITAL</span>
            <h1>Site Web</h1>
            <p>Créez, personnalisez et gérez la présence en ligne de votre entreprise.</p>
        </div>

        <div class="website-header-icon">🌐</div>
    </header>

    <section class="website-intro">
        <div class="website-intro-icon">✦</div>
        <div>
            <h2>Bienvenue dans votre espace Web</h2>
            <p>
                Préparez votre site vitrine, présentez vos services,
                publiez vos actualités et centralisez vos informations.
            </p>
        </div>
    </section>

    <section class="website-section">
        <div class="website-section-heading">
            <div>
                <h2>Gestion du site</h2>
                <p>Les outils essentiels pour construire votre site Internet.</p>
            </div>
        </div>

        <a href="{{ route('company.website.menus.index') }}">
            Gérer les menus du site
        </a>

        <div class="website-grid">
            <article class="website-card">
                <div class="website-card-icon blue">▤</div>
                <h3>Pages du site</h3>
                <p>Accueil, À propos, services, contact et pages personnalisées.</p>
                <span class="website-status">À configurer</span>
            </article>

            <article class="website-card">
                <div class="website-card-icon purple">☷</div>
                <h3>Menus et navigation</h3>
                <p>Organisez les liens et les rubriques visibles par vos visiteurs.</p>
                <span class="website-status">À configurer</span>
            </article>

            <article class="website-card">
                <div class="website-card-icon orange">✎</div>
                <h3>Articles et actualités</h3>
                <p>Publiez des annonces, des nouvelles et des contenus éditoriaux.</p>
                <span class="website-status">À configurer</span>
            </article>

            <article class="website-card">
                <div class="website-card-icon green">◈</div>
                <h3>Identité visuelle</h3>
                <p>Préparez votre logo, vos couleurs, vos coordonnées et votre thème.</p>
                <span class="website-status">À configurer</span>
            </article>

            <article class="website-card">
                <div class="website-card-icon pink">⌕</div>
                <h3>Référencement SEO</h3>
                <p>Configurez les titres, descriptions et informations de référencement.</p>
                <span class="website-status">À configurer</span>
            </article>

            <article class="website-card">
                <div class="website-card-icon teal">✉</div>
                <h3>Messages des visiteurs</h3>
                <p>Préparez la réception des demandes de contact et de devis.</p>
                <span class="website-status">À configurer</span>
            </article>
        </div>
    </section>

    <!-- <section class="website-next-step">
        <div>
            <h2>Votre site, votre image</h2>
            <p>
                La prochaine étape consistera à créer les pages et à enregistrer
                leur contenu en base de données, avec une prévisualisation avant publication.
            </p>
        </div>
        <span class="website-next-icon">🚀</span>
    </section> -->
</div>

<style>
.website-dashboard {
    --web-primary: #315bea;
    --web-text: #172033;
    --web-muted: #64748b;
    color: var(--web-text);
    padding: 8px 0 30px;
}

.website-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 26px;
}

.website-eyebrow {
    color: var(--web-primary);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.website-header h1 {
    margin: 8px 0;
    font-size: clamp(26px, 3vw, 34px);
    font-weight: 800;
    letter-spacing: -1px;
}

.website-header p,
.website-section-heading p {
    color: var(--web-muted);
    margin: 0;
    line-height: 1.7;
}

.website-header-icon {
    display: grid;
    place-items: center;
    width: 68px;
    height: 68px;
    flex-shrink: 0;
    border-radius: 20px;
    background: #eaf0ff;
    font-size: 32px;
}

.website-intro {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 24px;
    margin-bottom: 34px;
    border: 1px solid #dce6ff;
    border-radius: 18px;
    background: linear-gradient(120deg, #f5f8ff, #ffffff);
}

.website-intro-icon {
    display: grid;
    place-items: center;
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    border-radius: 13px;
    background: var(--web-primary);
    color: white;
    font-size: 22px;
}

.website-intro h2,
.website-section-heading h2,
.website-next-step h2 {
    margin: 0 0 8px;
    font-size: 18px;
    font-weight: 750;
}

.website-intro p,
.website-next-step p {
    color: var(--web-muted);
    margin: 0;
    line-height: 1.7;
}

.website-section-heading {
    margin-bottom: 18px;
}

.website-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.website-card {
    min-width: 0;
    padding: 22px;
    border: 1px solid #e6eaf0;
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 5px 18px rgba(15, 23, 42, .035);
    transition: transform .2s ease, box-shadow .2s ease;
}

.website-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 26px rgba(15, 23, 42, .08);
}

.website-card-icon {
    display: grid;
    place-items: center;
    width: 46px;
    height: 46px;
    margin-bottom: 17px;
    border-radius: 14px;
    font-size: 25px;
}

.website-card-icon.blue { background: #eaf0ff; color: #315bea; }
.website-card-icon.purple { background: #f1eaff; color: #7c3aed; }
.website-card-icon.orange { background: #fff0df; color: #c56a13; }
.website-card-icon.green { background: #e1f8ec; color: #16834a; }
.website-card-icon.pink { background: #ffe8f0; color: #c02663; }
.website-card-icon.teal { background: #ddf8f5; color: #0f766e; }

.website-card h3 {
    margin: 0 0 9px;
    font-size: 16px;
    font-weight: 750;
}

.website-card p {
    min-height: 66px;
    margin: 0 0 18px;
    color: var(--web-muted);
    font-size: 13px;
    line-height: 1.7;
}

.website-status {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    background: #f1f5f9;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
}

.website-next-step {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 28px;
    padding: 24px;
    border-radius: 18px;
    background: #172554;
    color: #fff;
}

.website-next-step p {
    max-width: 720px;
    color: #dbe5ff;
    font-size: 13px;
}

.website-next-icon {
    font-size: 35px;
}

@media (max-width: 1000px) {
    .website-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .website-header {
        align-items: flex-start;
    }

    .website-header-icon {
        width: 52px;
        height: 52px;
        border-radius: 15px;
        font-size: 25px;
    }

    .website-intro,
    .website-next-step {
        padding: 18px;
    }

    .website-grid {
        grid-template-columns: 1fr;
    }

    .website-card p {
        min-height: auto;
    }
}
</style>
@endsection
