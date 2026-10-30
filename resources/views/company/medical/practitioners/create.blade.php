@extends('company.medical.patients.layout')

@section('title', 'Nouveau professionnel')
@section('page-title', 'Ajouter un professionnel')

@section('content')

<div class="mp-toolbar">
    <a href="{{ route('company.medical.practitioners.index') }}"
       class="mp-btn mp-btn-light">
        ← Retour aux professionnels
    </a>
</div>

<div class="mp-panel mp-form-panel">

    <div class="mp-panel-heading">
        <div>
            <h2>Nouveau professionnel de santé</h2>
            <p>Renseignez les informations du professionnel.</p>
        </div>
    </div>

    @include('company.medical.practitioners.form')

</div>

@endsection