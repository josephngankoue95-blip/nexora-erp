
@extends('layouts.ecommerce')

@section('title', 'Promotions et codes de réduction')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ecommerce-promotions.css') }}">
@endpush

@section('content')
<div class="promo-page">

    <header class="promo-header">
        <div>
            <span class="promo-eyebrow">E-COMMERCE / MARKETING</span>
            <h1>Promotions et réductions</h1>
            <p>Créez et gérez les offres commerciales de votre boutique.</p>
        </div>

        <a href="{{ route('company.ecommerce.promotions.create') }}"
           class="promo-btn promo-btn-primary">
            + Nouvelle promotion
        </a>
    </header>

    @if(session('success'))
        <div class="promo-alert promo-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="promo-alert promo-alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="promo-stats">
        <article class="promo-stat-card">
            <span>Total des promotions</span>
            <strong>{{ $promotions->total() }}</strong>
            <small>Enregistrées dans cette entreprise</small>
        </article>

        <article class="promo-stat-card">
            <span>Promotions actives</span>
            <strong>
                {{ $promotions->getCollection()->where('is_active', true)->count() }}
            </strong>
            <small>Sur la page affichée</small>
        </article>

        <article class="promo-stat-card">
            <span>Utilisations enregistrées</span>
            <strong>
                {{ $promotions->getCollection()->sum('usage_count') }}
            </strong>
            <small>Sur la page affichée</small>
        </article>
    </section>

    <section class="promo-panel">
        <div class="promo-panel-heading">
            <div>
                <h2>Liste des promotions</h2>
                <p>Consultez et gérez les codes de réduction.</p>
            </div>
        </div>

        <form method="GET"
              action="{{ route('company.ecommerce.promotions.index') }}"
              class="promo-filters">

            <input type="search"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Rechercher un nom ou un code…">

            <select name="status">
                <option value="">Tous les statuts</option>
                <option value="active" @selected(request('status') === 'active')>
                    Actives
                </option>
                <option value="inactive" @selected(request('status') === 'inactive')>
                    Désactivées
                </option>
            </select>

            <button type="submit" class="promo-btn promo-btn-primary">
                Filtrer
            </button>

            <a href="{{ route('company.ecommerce.promotions.index') }}"
               class="promo-btn promo-btn-light">
                Réinitialiser
            </a>
        </form>

        <div class="promo-table-wrapper">
            <table class="promo-table">
                <thead>
                    <tr>
                        <th>Promotion</th>
                        <th>Réduction</th>
                        <th>Commande minimale</th>
                        <th>Utilisations</th>
                        <th>Période</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($promotions as $promotion)
                    <tr>
                        <td>
                            <div class="promo-name">
                                <strong>{{ $promotion->name }}</strong>
                                <span>
                                    {{ $promotion->code ?: 'Sans code' }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <strong>
                                @if($promotion->type === 'percentage')
                                    {{ number_format((float) $promotion->value, 2, ',', ' ') }} %
                                @else
                                    {{ number_format((float) $promotion->value, 0, ',', ' ') }}
                                    FCFA
                                @endif
                            </strong>
                        </td>

                        <td>
                            {{ number_format((float) $promotion->minimum_order_amount, 0, ',', ' ') }}
                            FCFA
                        </td>

                        <td>
                            {{ $promotion->usage_count }}
                            /
                            {{ $promotion->usage_limit ?? '∞' }}
                        </td>

                        <td>
                            <div class="promo-period">
                                <span>
                                    Début :
                                    {{ $promotion->starts_at?->format('d/m/Y H:i') ?? 'Immédiat' }}
                                </span>
                                <span>
                                    Fin :
                                    {{ $promotion->ends_at?->format('d/m/Y H:i') ?? 'Sans limite' }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="promo-badge {{ $promotion->is_active ? 'is-active' : 'is-inactive' }}">
                                {{ $promotion->is_active ? 'Active' : 'Désactivée' }}
                            </span>
                        </td>

                        <td>
                            <div class="promo-actions">
                                <a class="promo-action-link"
                                   href="{{ route('company.ecommerce.promotions.edit', $promotion) }}">
                                    Modifier
                                </a>

                                <form method="POST"
                                      action="{{ route('company.ecommerce.promotions.toggle', $promotion) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="promo-action-button">
                                        {{ $promotion->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('company.ecommerce.promotions.destroy', $promotion) }}"
                                      onsubmit="return confirm('Confirmer la suppression de cette promotion ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="promo-action-button is-danger">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="promo-empty">
                            <strong>Aucune promotion trouvée</strong>
                            <p>Créez votre première promotion pour commencer.</p>
                            <a href="{{ route('company.ecommerce.promotions.create') }}"
                               class="promo-btn promo-btn-primary">
                                Créer une promotion
                            </a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="promo-pagination">
            {{ $promotions->withQueryString()->links() }}
        </div>
    </section>
</div>
@endsection
