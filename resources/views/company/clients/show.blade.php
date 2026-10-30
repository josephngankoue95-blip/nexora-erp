@extends('layouts.company')

@section('title', $client->name)
@section('page-title', $client->name)

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                FICHE CLIENT
            </span>

            <h1>{{ $client->name }}</h1>

            <p>
                Informations et coordonnées du client.
            </p>

        </div>


        <div class="module-header-actions">

            <a
                href="{{ route('company.clients.edit', $client->id) }}"
                class="module-btn module-btn-primary"
            >
                Modifier
            </a>

            <a
                href="{{ route('company.clients.index') }}"
                class="module-btn module-btn-light"
            >
                ← Retour
            </a>

        </div>

    </div>


    <div class="module-card">

        <div class="module-card-header">

            <h2>
                Coordonnées
            </h2>

        </div>


        <div class="details-grid">

            <div>

                <span>
                    Nom / raison sociale
                </span>

                <strong>
                    {{ $client->name }}
                </strong>

            </div>


            <div>

                <span>
                    Téléphone
                </span>

                <strong>
                    {{ $client->phone ?? '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Email
                </span>

                <strong>
                    {{ $client->email ?? '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Adresse
                </span>

                <strong>
                    {{ $client->address ?? '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Ville
                </span>

                <strong>
                    {{ $client->city ?? '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Pays
                </span>

                <strong>
                    {{ $client->country }}
                </strong>

            </div>


            <div>

                <span>
                    N° contribuable
                </span>

                <strong>
                    {{ $client->tax_number ?? '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Statut
                </span>

                @if($client->is_active)

                    <strong class="status-active">
                        Actif
                    </strong>

                @else

                    <strong class="status-inactive">
                        Inactif
                    </strong>

                @endif

            </div>

        </div>

    </div>


    <div class="module-card">

        <div class="module-card-header">

            <h2>
                Notes
            </h2>

        </div>


        <div class="product-description">

            @if($client->notes)

                {!! nl2br(e($client->notes)) !!}

            @else

                <span>
                    Aucune note.
                </span>

            @endif

        </div>

    </div>


    {{-- FUTUR : HISTORIQUE DES VENTES --}}

    <div class="module-card">

        <div class="module-card-header">

            <h2>
                Historique commercial
            </h2>

            <span>
                Disponible avec le module Ventes.
            </span>

        </div>

        <div class="empty-state">

            <strong>
                Aucune vente pour le moment
            </strong>

            <p>
                L'historique des ventes de ce client apparaîtra ici.
            </p>

        </div>

    </div>

</div>

@endsection