@extends('layouts.company')

@section('title', 'Nouveau fournisseur | Gestion')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>
            <span class="module-kicker">
                FOURNISSEURS
            </span>

            <h1 class="module-title">
                Nouveau fournisseur
            </h1>

            <p class="module-description">
                Ajoutez un nouveau fournisseur à votre entreprise.
            </p>
        </div>

        <a href="{{ route('company.suppliers.index') }}"
           class="module-btn module-btn-secondary">
            ← Retour
        </a>

    </div>


    <form method="POST"
          action="{{ route('company.suppliers.store') }}"
          class="module-card">

        @csrf

        @include('company.suppliers.form')

    </form>

</div>

@endsection