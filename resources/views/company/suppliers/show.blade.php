@extends('layouts.company')

@section('title', $supplier->name . ' | Fournisseur')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                FOURNISSEUR
            </span>

            <h1 class="module-title">
                {{ $supplier->name }}
            </h1>

            <p class="module-description">
                Fiche complète du fournisseur.
            </p>

        </div>

        <div class="module-actions">

            <a href="{{ route('company.suppliers.edit', $supplier) }}"
               class="module-btn module-btn-primary">
                Modifier
            </a>

            <a href="{{ route('company.suppliers.index') }}"
               class="module-btn module-btn-secondary">
                ← Retour
            </a>

        </div>

    </div>


    <div class="module-detail-grid">

        <div class="module-card">

            <div class="module-card-header">
                <h2>Informations générales</h2>
            </div>

            <div class="module-detail-list">

                <div>
                    <span>Nom</span>
                    <strong>{{ $supplier->name }}</strong>
                </div>

                <div>
                    <span>Téléphone</span>
                    <strong>{{ $supplier->phone ?: '—' }}</strong>
                </div>

                <div>
                    <span>Email</span>
                    <strong>{{ $supplier->email ?: '—' }}</strong>
                </div>

                <div>
                    <span>N° contribuable</span>
                    <strong>{{ $supplier->tax_number ?: '—' }}</strong>
                </div>

                <div>
                    <span>Statut</span>

                    @if($supplier->is_active)

                        <strong class="module-text-success">
                            Actif
                        </strong>

                    @else

                        <strong class="module-text-danger">
                            Inactif
                        </strong>

                    @endif

                </div>

            </div>

        </div>


        <div class="module-card">

            <div class="module-card-header">
                <h2>Localisation</h2>
            </div>

            <div class="module-detail-list">

                <div>
                    <span>Adresse</span>
                    <strong>{{ $supplier->address ?: '—' }}</strong>
                </div>

                <div>
                    <span>Ville</span>
                    <strong>{{ $supplier->city ?: '—' }}</strong>
                </div>

                <div>
                    <span>Pays</span>
                    <strong>{{ $supplier->country }}</strong>
                </div>

            </div>

        </div>


        <div class="module-card module-detail-full">

            <div class="module-card-header">
                <h2>Notes</h2>
            </div>

            <div class="module-detail-notes">

                {!! nl2br(e($supplier->notes ?: 'Aucune note.')) !!}

            </div>

        </div>

    </div>

</div>

@endsection