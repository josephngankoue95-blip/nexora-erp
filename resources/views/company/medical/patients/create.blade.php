@extends('company.medical.patients.layout')

@section('title', 'Nouveau patient')
@section('page-title', 'Enregistrer un patient')

@section('content')

<div class="mp-panel mp-form-panel">

    <h2>Informations du patient</h2>

    <p class="mp-muted">
        Le matricule sera généré automatiquement à l'enregistrement.
    </p>

    <form method="POST"
          action="{{ route('company.medical.patients.store') }}">

        @csrf

        @include('company.medical.patients.form')

    </form>

</div>

@endsection