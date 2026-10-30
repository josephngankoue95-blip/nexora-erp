
@extends('layouts.ecommerce')

@section('title', 'Détail de l’avis')

@section('content')
<div class="ec-reviews-page">

    <div class="ec-review-detail-header">
        <div>
            <a href="{{ route('company.ecommerce.reviews.index') }}"
               class="ec-back-link">
                ← Retour aux avis
            </a>
            <h1>Détail de l’avis</h1>
            <p>Consultez le commentaire et gérez sa publication.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="ec-alert ec-alert-success">{{ session('success') }}</div>
    @endif

    <div class="ec-review-detail-grid">
        <section class="ec-review-detail-card">
            <div class="ec-detail-card-heading">
                <h2>Commentaire du client</h2>
                <span class="ec-status ec-status-{{ $review->status }}">
                    @switch($review->status)
                        @case('published') Publié @break
                        @case('rejected') Refusé @break
                        @default En attente
                    @endswitch
                </span>
            </div>

            <div class="ec-detail-rating">
                <span class="ec-stars">
                    {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}
                </span>
                <strong>{{ $review->rating }}/5</strong>
            </div>

            <h3 class="ec-review-title">{{ $review->title ?: 'Avis sans titre' }}</h3>

            <div class="ec-review-comment">
                {{ $review->comment }}
            </div>

            <div class="ec-review-meta">
                <span>Reçu le {{ $review->created_at?->format('d/m/Y à H:i') }}</span>
                @if($review->published_at)
                    <span>Publié le {{ $review->published_at->format('d/m/Y à H:i') }}</span>
                @endif
            </div>
        </section>

        <aside class="ec-review-detail-card">
            <h2>Informations client</h2>

            <div class="ec-customer-profile">
                <span class="ec-avatar ec-avatar-large">
                    {{ mb_strtoupper(mb_substr($review->customer_name, 0, 1)) }}
                </span>
                <div>
                    <strong>{{ $review->customer_name }}</strong>
                    <small>Client ayant laissé un avis</small>
                </div>
            </div>

            <div class="ec-info-row">
                <span>E-mail</span>
                <strong>{{ $review->customer_email ?: 'Non renseigné' }}</strong>
            </div>

            <div class="ec-info-row">
                <span>Produit ID</span>
                <strong>{{ $review->product_id ?: 'Non associé' }}</strong>
            </div>

            <div class="ec-detail-divider"></div>

            <h2>Modération</h2>

            <form method="POST"
                  action="{{ route('company.ecommerce.reviews.status', $review) }}"
                  class="ec-moderation-form">
                @csrf
                @method('PATCH')

                <label for="status">Statut de l’avis</label>
                <select name="status" id="status" required>
                    <option value="pending" @selected($review->status === 'pending')>En attente</option>
                    <option value="published" @selected($review->status === 'published')>Publié</option>
                    <option value="rejected" @selected($review->status === 'rejected')>Refusé</option>
                </select>

                <button type="submit" class="ec-btn ec-btn-primary ec-btn-full">
                    Enregistrer le statut
                </button>
            </form>

            <form method="POST"
                  action="{{ route('company.ecommerce.reviews.destroy', $review) }}"
                  onsubmit="return confirm('Confirmer la suppression définitive de cet avis ?')"
                  class="ec-delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="ec-btn ec-btn-danger ec-btn-full">
                    Supprimer cet avis
                </button>
            </form>
        </aside>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ecommerce-reviews.css') }}">
@endpush
