@extends('layouts.school')
@section('title', 'Nouvelle inscription')
@section('page-title', 'Nouvelle inscription scolaire')
@section('page-description', 'Affectation d’un élève à une classe pour une année scolaire')
@section('content')
<div class="en-page en-form-page">
    <header class="en-heading"><div><span class="en-eyebrow">ADMINISTRATION SCOLAIRE</span><h2>Inscrire un élève</h2><p>Choisissez l'élève, l'année scolaire et la classe correspondante.</p></div><a class="en-btn en-outline" href="{{ route('company.school.enrollments.index') }}">← Retour aux inscriptions</a></header>
    <form action="{{ route('company.school.enrollments.store') }}" method="POST">@csrf
        <section class="en-panel"><div class="en-panel-head"><div><h3>01 · Informations de l'inscription</h3><p>Les champs marqués d'un astérisque sont obligatoires.</p></div></div>
            <div class="en-grid en-form-grid">
                <label class="en-field"><span>Élève *</span><select name="school_student_id" required><option value="">Sélectionner un élève</option>@foreach($students as $student)<option value="{{ $student->id }}" @selected(old('school_student_id')==$student->id)>{{ $student->student_number }} — {{ $student->last_name }} {{ $student->first_name }}</option>@endforeach</select>@error('school_student_id')<small class="en-error">{{ $message }}</small>@enderror</label>
                <label class="en-field"><span>Année scolaire *</span><select name="school_year_id" id="enCreateYear" required><option value="">Sélectionner l'année</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(old('school_year_id', $years->firstWhere('is_active', true)?->id)==$year->id)>{{ $year->name }}{{ $year->is_active ? ' — Active' : '' }}</option>@endforeach</select>@error('school_year_id')<small class="en-error">{{ $message }}</small>@enderror</label>
                <label class="en-field"><span>Classe *</span><select name="school_class_id" id="enCreateClass" required><option value="">Sélectionner une classe</option>@foreach($classes as $class)<option value="{{ $class->id }}" data-year="{{ $class->school_year_id }}" @selected(old('school_class_id')==$class->id)>{{ $class->level?->section?->name }} · {{ $class->level?->name }} · {{ $class->name }}</option>@endforeach</select>@error('school_class_id')<small class="en-error">{{ $message }}</small>@enderror</label>
                <label class="en-field"><span>Date d'inscription *</span><input type="date" name="enrolled_on" value="{{ old('enrolled_on', date('Y-m-d')) }}" required>@error('enrolled_on')<small class="en-error">{{ $message }}</small>@enderror</label>
                <label class="en-field"><span>Statut *</span><select name="status" required><option value="enrolled" @selected(old('status','enrolled')==='enrolled')>Inscrit</option><option value="transferred" @selected(old('status')==='transferred')>Transféré</option><option value="withdrawn" @selected(old('status')==='withdrawn')>Retiré</option></select>@error('status')<small class="en-error">{{ $message }}</small>@enderror</label>
            </div>
        </section>
        <div class="en-actions en-form-actions"><a class="en-btn en-outline" href="{{ route('company.school.enrollments.index') }}">Annuler</a><button class="en-btn en-primary" type="submit">Enregistrer l'inscription</button></div>
    </form>
</div>
@include('company.school.enrollments.styles')
@push('scripts')
<script>
(function(){const year=document.getElementById('enCreateYear'),cl=document.getElementById('enCreateClass');if(!year||!cl)return;function refresh(){for(const option of cl.options){if(option.value)option.hidden=option.dataset.year!==year.value;}if(cl.selectedOptions[0]?.hidden)cl.value='';}year.addEventListener('change',refresh);refresh();})();
</script>
@endpush
@endsection
