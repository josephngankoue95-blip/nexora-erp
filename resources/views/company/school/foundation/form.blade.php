@extends('layouts.school')
@php
$names=['years'=>'Année scolaire','sections'=>'Section','levels'=>'Niveau','classes'=>'Classe'];
$editing=$record->exists;
$fields=[
'years'=>[['name','Nom de l’année','text','Ex. 2026-2027'],['starts_on','Date de début','date',''],['ends_on','Date de fin','date','']],
'sections'=>[['code','Code','text','Ex. FR'],['name','Nom de la section','text','Ex. Francophone'],['is_active','Section active','checkbox','']],
'levels'=>[['school_section_id','Section','section',''],['name','Nom du niveau','text','Ex. 6e ou Form 1'],['sort_order','Ordre d’affichage','number',''],['is_exam_class','Classe d’examen','checkbox','']],
'classes'=>[['school_year_id','Année scolaire','year',''],['school_level_id','Niveau','level',''],['name','Nom de la classe','text','Ex. 6e A'],['capacity','Capacité (facultatif)','number','']]
];
@endphp
@section('title',($editing?'Modifier ':'Ajouter ').$names[$kind])
@section('page-title',($editing?'Modifier ':'Ajouter ').$names[$kind])
@section('page-description','Configuration de votre établissement')
@section('content')
<style>
.foundation-form{max-width:740px;margin:auto}.foundation-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:26px;box-shadow:0 4px 18px #17223b08}.foundation-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.foundation-form-field{display:flex;flex-direction:column;gap:7px}.foundation-form-field label{font-size:13px;font-weight:700;color:#334155}.foundation-form-field input:not([type=checkbox]),.foundation-form-field select{width:100%;min-height:44px;padding:10px 12px;border:1px solid #cbd5e1;border-radius:10px;background:#fff;font:inherit;font-size:14px;color:#17223b}.foundation-form-field input:focus,.foundation-form-field select:focus{outline:2px solid #c7d2fe;border-color:#6366f1}.foundation-form-field .check{display:flex;align-items:center;gap:9px;margin-top:12px}.foundation-form-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:28px}.foundation-form-actions button,.foundation-form-actions a{border:0;border-radius:10px;padding:12px 18px;font-size:13px;font-weight:700;text-decoration:none;cursor:pointer}.foundation-form-actions button{background:#4f46e5;color:#fff}.foundation-form-actions a{background:#f1f5f9;color:#334155}.foundation-form-hint{font-size:12px;color:#64748b;margin-bottom:20px}@media(max-width:620px){.foundation-form-grid{grid-template-columns:1fr}.foundation-form-card{padding:18px}}
</style>
<div class="foundation-form"><div class="foundation-form-card"><p class="foundation-form-hint">Renseignez les informations puis enregistrez.</p>
<form method="POST" action="{{ $editing ? route('company.school.'.$kind.'.update',$record->id) : route('company.school.'.$kind.'.store') }}">
@csrf @if($editing) @method('PUT') @endif
<div class="foundation-form-grid">
@foreach($fields[$kind] as [$key,$label,$type,$placeholder])
@php $value=old($key,$record->{$key}); if($value instanceof \Carbon\CarbonInterface) $value=$value->format('Y-m-d'); @endphp
<div class="foundation-form-field">
@if($type==='checkbox')
<label class="check"><input type="hidden" name="{{ $key }}" value="0"><input type="checkbox" name="{{ $key }}" value="1" {{ old($key,$record->exists ? $record->{$key} : ($key==='is_active')) ? 'checked' : '' }}> {{ $label }}</label>
@else
<label for="field-{{ $key }}">{{ $label }}</label>
@if($type==='section')<select id="field-{{ $key }}" name="{{ $key }}" required><option value="">Sélectionner une section</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected((string)$value===(string)$section->id)>{{ $section->name }}</option>@endforeach</select>
@elseif($type==='year')<select id="field-{{ $key }}" name="{{ $key }}" required><option value="">Sélectionner une année</option>@foreach($years as $year)@unless($year->is_archived)<option value="{{ $year->id }}" @selected((string)$value===(string)$year->id)>{{ $year->name }}{{ $year->is_active ? ' (active)' : '' }}</option>@endunless @endforeach</select>
@elseif($type==='level')<select id="field-{{ $key }}" name="{{ $key }}" required><option value="">Sélectionner un niveau</option>@foreach($levels as $level)<option value="{{ $level->id }}" @selected((string)$value===(string)$level->id)>{{ $level->section?->name }} — {{ $level->name }}</option>@endforeach</select>
@else<input id="field-{{ $key }}" name="{{ $key }}" type="{{ $type }}" value="{{ $value ?? ($key==='sort_order' ? 0 : '') }}" placeholder="{{ $placeholder }}" {{ $key==='capacity' ? '' : 'required' }} {{ $type==='number' ? 'min=0' : '' }}>@endif
@endif
@error($key)<small style="color:#be123c">{{ $message }}</small>@enderror
</div>
@endforeach
</div>
<div class="foundation-form-actions"><button type="submit">{{ $editing?'Enregistrer les modifications':'Créer' }}</button><a href="{{ route('company.school.'.$kind.'.index') }}">Annuler</a></div>
</form></div></div>
@endsection
