@extends('layouts.company')

@section('title', 'Modifier fournisseur | Gestion')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>
            <span class="module-kicker">
                FOURNISSEURS
            </span>

            <h1 class="module-title">
                Modifier le fournisseur
            </h1>

            <p class="module-description">
                {{ $supplier->name }}
            </p>
        </div>

        <a href="{{ route('company.suppliers.index') }}"
           class="module-btn module-btn-secondary">
            ← Retour
        </a>

    </div>


    <form
        method="POST"
        action="{{ route('company.suppliers.update', $supplier) }}"
        class="module-card"
    >

        @csrf
        @method('PUT')

        @include('company.suppliers.form')

    </form>

</div>

@endsection