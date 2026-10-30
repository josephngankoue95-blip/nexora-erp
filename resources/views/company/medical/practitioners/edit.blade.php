@extends('company.medical.patients.layout')

@section('title', 'Modifier un professionnel')
@section('page-title', 'Modifier un professionnel')

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
            <h2>Modifier : {{ $practitioner->full_name }}</h2>
            <p>Mettez à jour les informations du professionnel.</p>
        </div>
    </div>

    @include('company.medical.practitioners.form')

</div>

@endsection