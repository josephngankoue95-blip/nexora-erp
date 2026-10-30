@extends('company.school.principal.layouts.app')
@section('title', 'Classes')
@section('content')
<section class="welcome"><div><span class="eyebrow">GESTION PÉDAGOGIQUE</span><h1>Classes de l'établissement</h1><p>Consultation des classes de l'année scolaire active.</p></div><div class="year">{{ $year->name ?? 'Aucune année active' }}</div></section>
<section class="panel"><div class="academic-table-wrap"><table class="academic-table"><thead><tr><th>Classe</th><th>Niveau</th><th>Capacité</th></tr></thead><tbody>
@forelse($classes as $classe)<tr><td>{{ $classe->name }}</td><td>{{ $classe->level?->name ?? '—' }}</td><td>{{ $classe->capacity ?? '—' }}</td></tr>
@empty<tr><td colspan="3" class="empty-state">Aucune classe disponible pour cette année.</td></tr>@endforelse
</tbody></table></div><div class="pagination-container">{{ $classes->links() }}</div></section>
@endsection
