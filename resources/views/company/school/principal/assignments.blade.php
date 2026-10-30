@extends('company.school.principal.layouts.app')
@section('title', 'Affectations')
@section('content')
<section class="welcome"><div><span class="eyebrow">GESTION PÉDAGOGIQUE</span><h1>Affectations des enseignants</h1><p>Consultation des affectations de l'année scolaire active.</p></div><div class="year">{{ $year->name ?? 'Aucune année active' }}</div></section>
<section class="panel"><div class="academic-table-wrap"><table class="academic-table"><thead><tr><th>Enseignant</th><th>Matière</th><th>Classe</th></tr></thead><tbody>
@forelse($assignments as $assignment)<tr><td>{{ $assignment->teacher
    ? trim($assignment->teacher->last_name . ' ' . $assignment->teacher->first_name)
    : '—' }}
</td>

<td>{{ $assignment->subject?->name ?? '—' }}

</td>
<td>{{ $assignment->schoolClass?->name ?? '—' }}

</td>
</tr>
@empty<tr><td colspan="3" class="empty-state">Aucune affectation active pour cette année.</td></tr>@endforelse
</tbody></table></div><div class="pagination-container">{{ $assignments->links() }}</div></section>
@endsection
