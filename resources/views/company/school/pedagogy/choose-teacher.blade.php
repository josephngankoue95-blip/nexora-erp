@extends('layouts.school')
@section('title','Choisir un enseignant')
@section('page-title','Nouvelle affectation')
@section('content')
<div class="school-card" style="max-width:700px;margin:auto;padding:25px">
<h2>Choisir un enseignant</h2><p>Les affectations sont gérées depuis la fiche de chaque enseignant.</p>
<div style="display:grid;gap:10px">
@forelse($teachers as $teacher)
<a style="padding:12px;border:1px solid #e2e8f0;border-radius:8px;color:#4338ca;text-decoration:none" href="{{ route('company.school.assignments.teacher',$teacher->id) }}">{{ $teacher->last_name }} {{ $teacher->first_name }} →</a>
@empty <p>Aucun enseignant actif.</p> @endforelse
</div></div>
@endsection
