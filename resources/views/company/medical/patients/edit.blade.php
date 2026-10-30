@extends('company.medical.patients.layout')

@section('title', 'Modifier un patient')
@section('page-title', 'Modifier le patient')

@section('content')

<div class="mp-panel mp-form-panel">

    <h2>{{ $patient->full_name }}</h2>

    <p class="mp-muted">
        Matricule : {{ $patient->patient_number }}
    </p>

    <form method="POST"
          action="{{ route('company.medical.patients.update', $patient) }}">

        @csrf
        @method('PUT')

        @include('company.medical.patients.form')

    </form>

</div>

@endsection