@extends('company.school.principal.layouts.app')
@section('title', 'Matières')
@section('content')
<section class="welcome"><div><span class="eyebrow">GESTION PÉDAGOGIQUE</span><h1>Matières enseignées</h1><p>Consultation des matières et de leurs coefficients.</p></div></section>
<section class="panel"><div class="academic-table-wrap"><table class="academic-table"><thead><tr><th>Code</th><th>Matière</th><th>Groupe</th><th>Coefficient</th><th>Statut</th></tr></thead><tbody>
@forelse($subjects as $subject)<tr><td>{{ $subject->code ?: '—' }}</td><td>{{ $subject->name }}</td><td>{{ $subject->group?->name ?? '—' }}</td><td>{{ number_format((float) $subject->coefficient,2,',',' ') }}</td><td><span class="status {{ $subject->is_active ? 'status-active' : 'status-inactive' }}">{{ $subject->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
@empty<tr><td colspan="5" class="empty-state">Aucune matière enregistrée.</td></tr>@endforelse
</tbody></table></div><div class="pagination-container">{{ $subjects->links() }}</div></section>
@endsection
