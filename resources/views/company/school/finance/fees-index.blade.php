@extends('layouts.school')
@section('title','Tarifs scolaires')
@section('page-title','Tarifs scolaires')
@section('content')
@include('company.school.finance.styles')
<div class="sf-page">
 <div class="sf-heading"><div><h2>Configuration des frais scolaires</h2><p>Tarifs par année scolaire et par niveau · Montants en FCFA</p></div><a class="sf-btn sf-btn-primary" href="{{ route('company.school.fees.create') }}">+ Nouveau tarif</a></div>
 <div class="sf-panel"><h3>Filtres</h3><form method="GET" class="sf-grid">
 <label class="sf-field"><span>Année scolaire</span><select name="year_id"><option value="">Toutes les années</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(request('year_id')==$year->id)>{{ $year->name }}</option>@endforeach</select></label>
 <label class="sf-field"><span>Section</span><select name="section_id"><option value="">Toutes les sections</option>@foreach($levels->pluck('section')->filter()->unique('id') as $section)<option value="{{ $section->id }}" @selected(request('section_id')==$section->id)>{{ $section->name }}</option>@endforeach</select></label>
 <label class="sf-field"><span>Niveau</span><select name="level_id"><option value="">Tous les niveaux</option>@foreach($levels as $level)<option value="{{ $level->id }}" @selected(request('level_id')==$level->id)>{{ $level->name }}</option>@endforeach</select></label>
 <div class="sf-actions"><button class="sf-btn sf-btn-primary">Filtrer</button><a class="sf-btn" href="{{ route('company.school.fees.index') }}">Réinitialiser</a></div></form></div>
 <div class="sf-panel"><h3>Liste des tarifs ({{ $fees->total() }})</h3><div class="sf-scroll"><table class="sf-table"><thead><tr><th>Année</th><th>Section</th><th>Niveau</th><th>Inscription</th><th>Tranche 1</th><th>Tranche 2</th><th>Tranche 3</th><th>Total</th><th>Actions</th></tr></thead><tbody>
 @forelse($fees as $fee)<tr><td>{{ $fee->year?->name }}</td><td>{{ $fee->level?->section?->name }}</td><td><span class="sf-tag">{{ $fee->level?->name }}</span></td>@foreach(['registration_fee','installment_1','installment_2','installment_3'] as $key)<td class="sf-money">{{ number_format($fee->$key,0,',',' ') }}</td>@endforeach<td class="sf-money">{{ number_format($fee->registration_fee+$fee->installment_1+$fee->installment_2+$fee->installment_3,0,',',' ') }}</td><td><div class="sf-inline"><a class="sf-btn" href="{{ route('company.school.fees.edit',$fee->id) }}">Modifier</a><form method="POST" action="{{ route('company.school.fees.destroy',$fee->id) }}" onsubmit="return confirm('Supprimer ce tarif ?')">@csrf @method('DELETE')<button class="sf-btn sf-btn-danger">Supprimer</button></form></div></td></tr>
 @empty<tr><td colspan="9" class="sf-empty">Aucun tarif configuré.</td></tr>@endforelse</tbody></table></div><div style="margin-top:15px">{{ $fees->links() }}</div></div>
</div>
@endsection
