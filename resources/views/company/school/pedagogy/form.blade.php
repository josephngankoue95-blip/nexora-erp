@extends('layouts.school')
@section('title', 'Gestion pédagogique')
@section('page-title', 'Gestion pédagogique')
@section('content')
@php
    $edit = $item->exists;
    $fields = [
        'teachers' => ['matricule'=>'Matricule','first_name'=>'Prénom','last_name'=>'Nom','email'=>'Adresse e-mail','phone'=>'Téléphone','speciality'=>'Spécialité','qualification'=>'Diplôme / Qualification','recruitment_date'=>'Date de recrutement','status'=>'Statut'],
        'subject-groups' => ['section_id'=>'Section','name'=>'Nom du groupe','code'=>'Code','description'=>'Description'],
        // Le groupe et le coefficient sont configurés au niveau de chaque classe.
        'subjects' => ['section_id'=>'Section','name'=>'Nom de la matière','code'=>'Code','is_active'=>'Actif'],
        'assignments' => ['teacher_id'=>'Enseignant','subject_id'=>'Matière','class_id'=>'Classe','school_year_id'=>'Année scolaire','is_active'=>'Actif'],
    ];
    $selects = ['section_id'=>$sections ?? collect(),'teacher_id'=>$teachers ?? collect(),'subject_id'=>$subjects ?? collect(),'class_id'=>$classes ?? collect(),'school_year_id'=>$years ?? collect()];
    $titles = ['teachers'=>'Enseignant','subject-groups'=>'Groupe de matières','subjects'=>'Matière','assignments'=>'Affectation pédagogique'];
    $formTitle = $titles[$type] ?? 'Élément';
    $base = 'company.school.' . $type;
    $action = $edit ? route($base . '.update', $item->id) : route($base . '.store');
@endphp
<style>
.pedagogy-form-page{max-width:1050px;margin:0 auto;padding-bottom:25px}.pedagogy-form-header{margin-bottom:22px}.pedagogy-form-header h2{margin:0 0 8px;color:#0f172a}.pedagogy-form-header p{color:#64748b;margin:0}.pedagogy-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:15px;padding:25px}.pedagogy-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.pedagogy-form-field{display:flex;flex-direction:column;gap:8px;min-width:0}.pedagogy-form-field label{font-weight:650;color:#334155;font-size:13px}.pedagogy-form-control{width:100%;min-height:44px;padding:10px 12px;border:1px solid #cbd5e1;border-radius:9px;background:#fff;color:#0f172a;font-size:14px;box-sizing:border-box}.pedagogy-form-control:focus{outline:2px solid #a5b4fc;outline-offset:1px}.pedagogy-form-error{font-size:12px;color:#dc2626}.pedagogy-form-actions{display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;margin-top:26px;border-top:1px solid #e2e8f0;padding-top:20px}.pedagogy-form-btn{padding:11px 18px;border-radius:9px;text-decoration:none;font-weight:650;font-size:13px;cursor:pointer;border:1px solid #cbd5e1}.pedagogy-form-save{background:#4f46e5;color:#fff;border-color:#4f46e5}.pedagogy-form-cancel{background:#f8fafc;color:#334155}.pedagogy-form-alert{padding:12px 16px;margin-bottom:20px;background:#fef2f2;color:#991b1b;border-radius:9px}
@media(max-width:768px){.pedagogy-form-grid{grid-template-columns:1fr}.pedagogy-form-card{padding:17px}}@media(max-width:480px){.pedagogy-form-actions{flex-direction:column-reverse}.pedagogy-form-btn{width:100%;text-align:center}}
</style>
<div class="pedagogy-form-page">
    <div class="pedagogy-form-header">
        <h2>{{ $edit ? 'Modifier' : 'Ajouter' }} — {{ $formTitle }}</h2>
        <p>{{ $edit ? 'Modifiez les informations puis enregistrez.' : 'Renseignez les informations pour créer un nouvel enregistrement.' }}</p>
        @if($type === 'subjects')<p>Le groupe et le coefficient seront définis dans « Classes → Configurer les matières ».</p>@endif
    </div>
    <div class="pedagogy-form-card">
        @if($errors->any())
            <div class="pedagogy-form-alert"><strong>Veuillez corriger les erreurs :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ $action }}">
            @csrf
            @if($edit) @method('PUT') @endif
            <div class="pedagogy-form-grid">
                @foreach(($fields[$type] ?? []) as $field => $label)
                    @php
                        $value = old($field, $item->{$field} ?? null);
                        if($field === 'recruitment_date' && $value instanceof \DateTimeInterface) $value = $value->format('Y-m-d');
                    @endphp
                    <div class="pedagogy-form-field">
                        <label for="field-{{ $field }}">{{ $label }}</label>
                        @if(array_key_exists($field, $selects))
                            <select id="field-{{ $field }}" name="{{ $field }}" class="pedagogy-form-control">
                                <option value="">-- Sélectionner --</option>
                                @foreach($selects[$field] as $option)
                                    @php $optionLabel = $field === 'teacher_id' ? trim(($option->last_name ?? '').' '.($option->first_name ?? '')) : ($option->name ?? $option->label ?? $option->id); @endphp
                                    <option value="{{ $option->id }}" @selected((string)$value === (string)$option->id)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @elseif($field === 'status')
                            <select id="field-status" name="status" class="pedagogy-form-control">
                                @foreach(['active'=>'Actif','inactive'=>'Inactif','suspended'=>'Suspendu'] as $statusValue => $statusLabel)
                                    <option value="{{ $statusValue }}" @selected((string)old('status',$item->status ?? 'active') === (string)$statusValue)>{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                        @elseif($field === 'is_active')
                            <select id="field-is_active" name="is_active" class="pedagogy-form-control">
                                <option value="1" @selected((string)$value === '1' || ($value === null && !$edit))>Oui</option>
                                <option value="0" @selected((string)$value === '0')>Non</option>
                            </select>
                        @elseif($field === 'description')
                            <textarea id="field-description" name="description" rows="4" class="pedagogy-form-control">{{ $value }}</textarea>
                        @else
                            <input id="field-{{ $field }}" name="{{ $field }}" type="{{ match($field){'recruitment_date'=>'date','email'=>'email','phone'=>'tel',default=>'text'} }}" value="{{ $value }}" class="pedagogy-form-control">
                        @endif
                        @error($field)<small class="pedagogy-form-error">{{ $message }}</small>@enderror
                    </div>
                @endforeach
            </div>
            <div class="pedagogy-form-actions">
                <a href="{{ route($base . '.index') }}" class="pedagogy-form-btn pedagogy-form-cancel">Annuler</a>
                <button type="submit" class="pedagogy-form-btn pedagogy-form-save">{{ $edit ? 'Enregistrer les modifications' : 'Enregistrer' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
