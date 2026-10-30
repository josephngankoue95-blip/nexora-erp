
@extends('layouts.ecommerce')

@section('title', 'Avis clients')

@section('content')
<div class="ec-reviews-page">

    <div class="ec-reviews-header">
        <div>
            <span class="ec-eyebrow">E-COMMERCE / CLIENTS</span>
            <h1>Avis clients</h1>
            <p>Consultez, modérez et gérez les avis laissés sur votre boutique.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="ec-alert ec-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="ec-alert ec-alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="ec-review-stats">
        <div class="ec-stat-card">
            <span>Total des avis</span>
            <strong>{{ $counts['all'] }}</strong>
            <small>Tous les avis reçus</small>
        </div>

        <div class="ec-stat-card">
            <span>En attente</span>
            <strong>{{ $counts['pending'] }}</strong>
            <small>À modérer</small>
        </div>

        <div class="ec-stat-card">
            <span>Avis publiés</span>
            <strong>{{ $counts['published'] }}</strong>
            <small>Visibles sur la boutique</small>
        </div>

        <div class="ec-stat-card ec-stat-rating">
            <span>Note moyenne</span>
            <strong>{{ number_format($averageRating, 1, ',', ' ') }} / 5</strong>
            <small class="ec-stars">★★★★★</small>
        </div>
    </div>

    <div class="ec-review-panel">
        <form method="GET"
              action="{{ route('company.ecommerce.reviews.index') }}"
              class="ec-review-filters">

            <div class="ec-filter-search">
                <label for="review-search">Rechercher</label>
                <input
                    type="search"
                    id="review-search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Client, e-mail, titre ou commentaire..."
                >
            </div>

            <div class="ec-filter-field">
                <label for="review-status">Statut</label>
                <select id="review-status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="pending" @selected(request('status') === 'pending')>
                        En attente
                    </option>
                    <option value="published" @selected(request('status') === 'published')>
                        Publiés
                    </option>
                    <option value="rejected" @selected(request('status') === 'rejected')>
                        Refusés
                    </option>
                </select>
            </div>

            <div class="ec-filter-field">
                <label for="review-rating">Note</label>
                <select id="review-rating" name="rating">
                    <option value="">Toutes les notes</option>
                    @for($rating = 5; $rating >= 1; $rating--)
                        <option value="{{ $rating }}"
                            @selected((string) request('rating') === (string) $rating)>
                            {{ $rating }} étoile(s)
                        </option>
                    @endfor
                </select>
            </div>

            <div class="ec-filter-actions">
                <button type="submit" class="ec-btn ec-btn-primary">
                    Filtrer
                </button>

                <a href="{{ route('company.ecommerce.reviews.index') }}"
                   class="ec-btn ec-btn-light">
                    Réinitialiser
                </a>
            </div>
        </form>

        <div class="ec-table-heading">
            <div>
                <h2>Liste des avis</h2>
                <p>Les avis les plus récents apparaissent en premier.</p>
            </div>
            <span class="ec-results-count">
                {{ $reviews->total() }} résultat(s)
            </span>
        </div>

        @if($reviews->isEmpty())
            <div class="ec-empty-state">
                <div class="ec-empty-icon">☆</div>
                <h3>Aucun avis trouvé</h3>
                <p>Les avis clients apparaîtront ici lorsqu'ils seront enregistrés.</p>
            </div>
        @else
            <div class="ec-review-table-wrapper">
                <table class="ec-review-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Évaluation</th>
                            <th>Commentaire</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="ec-text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($reviews as $review)
                            <tr>
                                <td>
                                    <div class="ec-customer-cell">
                                        <span class="ec-avatar">
                                            {{ mb_strtoupper(mb_substr($review->customer_name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <strong>{{ $review->customer_name }}</strong>
                                            <small>{{ $review->customer_email ?: 'E-mail non renseigné' }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="ec-rating">
                                        <span class="ec-stars">
                                            {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}
                                        </span>
                                        <small>{{ $review->rating }}/5</small>
                                    </div>
                                </td>

                                <td>
                                    <div class="ec-comment-cell">
                                        <strong>{{ $review->title ?: 'Avis client' }}</strong>
                                        <p>{{ \Illuminate\Support\Str::limit($review->comment, 90) }}</p>
                                    </div>
                                </td>

                                <td>
                                    @php
                                        $statusLabels = [
                                            'pending' => 'En attente',
                                            'published' => 'Publié',
                                            'rejected' => 'Refusé',
                                        ];
                                    @endphp

                                    <span class="ec-status ec-status-{{ $review->status }}">
                                        {{ $statusLabels[$review->status] ?? $review->status }}
                                    </span>
                                </td>

                                <td>
                                    <span class="ec-date">
                                        {{ $review->created_at?->format('d/m/Y') }}
                                    </span>
                                    <small class="ec-date-time">
                                        {{ $review->created_at?->format('H:i') }}
                                    </small>
                                </td>

                                <td>
                                    <div class="ec-row-actions">
                                        <a
                                            href="{{ route('company.ecommerce.reviews.show', $review) }}"
                                            class="ec-action-link"
                                        >Voir</a>

                                        @if($review->status !== 'published')
                                            <form method="POST"
                                                  action="{{ route('company.ecommerce.reviews.status', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="published">
                                                <button class="ec-action-button ec-action-publish" type="submit">
                                                    Publier
                                                </button>
                                            </form>
                                        @endif

                                        @if($review->status !== 'rejected')
                                            <form method="POST"
                                                  action="{{ route('company.ecommerce.reviews.status', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button class="ec-action-button ec-action-reject" type="submit">
                                                    Refuser
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST"
                                              action="{{ route('company.ecommerce.reviews.destroy', $review) }}"
                                              onsubmit="return confirm('Voulez-vous vraiment supprimer cet avis ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="ec-action-button ec-action-delete" type="submit">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ec-pagination">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ecommerce-reviews.css') }}">
@endpush
