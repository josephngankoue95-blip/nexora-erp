@extends('layouts.school')
@section('title', 'Notes des élèves')
@section('page-title', 'Notes des élèves')
@section('page-description', 'Saisie et consultation des résultats par évaluation')
@push('styles')
    @vite('resources/css/school-grades.css')
    
@endpush
@section('content')
@php
    $readonly = (bool) ($year->is_archived || $evaluation->is_locked);
    $filled = collect($students)->filter(fn ($student) => isset($grades[$student->id]) && $grades[$student->id] !== null && $grades[$student->id] !== '')->count();
@endphp
<div class="grades-page grades-modern">
    <header class="grades-hero">
        <div><span class="grades-eyebrow">PÉDAGOGIE · SAISIE DES NOTES</span><h1>{{ $evaluation->title }}</h1><p>{{ $class->name }} · {{ $year->name }} · Barème /{{ $evaluation->max_score }}</p></div>
        <a class="grades-btn grades-btn-light" href="{{ route('company.school.grades.index', ['year_id' => $year->id, 'class_id' => $class->id]) }}">← Retour aux évaluations</a>
    </header>

    <div class="grades-stats grades-stats-three">
        <div class="grades-stat"><span>Élèves inscrits</span><strong>{{ $students->count() }}</strong></div>
        <div class="grades-stat"><span>Notes renseignées</span><strong>{{ $filled }} / {{ $students->count() }}</strong></div>
        <div class="grades-stat"><span>Statut de saisie</span><strong>{{ $readonly ? 'Lecture seule' : 'Modifiable' }}</strong></div>
    </div>

    @if($readonly)
        <div class="grades-warning"><strong>Évaluation en lecture seule.</strong> {{ $year->is_archived ? 'Cette année scolaire est archivée.' : 'Cette évaluation est verrouillée.' }} Les notes ne peuvent plus être modifiées.</div>
    @else
        <div class="grades-info grades-entry-tip"><span class="grades-info-icon">ⓘ</span><div><strong>Conseil de saisie</strong><p>Entrez une note entre 0 et {{ $evaluation->max_score }}. Laissez la case vide si la note n'est pas encore disponible.</p></div></div>
    @endif

    <section class="grades-panel">
        <div class="grades-panel-head"><div><span class="grades-step">LISTE DES ÉLÈVES</span><h2>Notes de {{ $class->name }}</h2><p>Les notes sont enregistrées individuellement pour cette évaluation.</p></div><span class="grades-count">{{ $students->count() }} élève(s)</span></div>
        <form method="POST" action="{{ route('company.school.grades.evaluations.save', $evaluation->id) }}">
            @csrf
            @method('PUT')
            <div class="grades-table-wrap"><table class="grades-table grades-entry-table"><thead><tr><th>N°</th><th>Matricule</th><th>Élève</th><th>Note /{{ $evaluation->max_score }}</th></tr></thead><tbody>
                @forelse($students as $s)
                    <tr>
                        <td class="grades-row-number">{{ $loop->iteration }}</td>
                        <td><span class="grades-matricule">{{ $s->student_number }}</span></td>
                        <td><div class="grades-student"><span class="grades-student-avatar">{{ mb_strtoupper(mb_substr($s->last_name ?: $s->first_name, 0, 1)) }}</span><strong>{{ $s->last_name }} {{ $s->first_name }}</strong></div></td>
                        <td><div class="grades-note-control"><input type="number" inputmode="decimal" step="0.01" min="0" max="{{ $evaluation->max_score }}" name="grades[{{ $s->id }}]" value="{{ old('grades.'.$s->id, $grades[$s->id] ?? '') }}" aria-label="Note de {{ $s->last_name }} {{ $s->first_name }}" placeholder="—" @disabled($readonly)><span>/ {{ $evaluation->max_score }}</span></div></td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="grades-empty"><span>♙</span><strong>Aucun élève inscrit</strong><p>Vérifiez les inscriptions de cette classe.</p></div></td></tr>
                @endforelse
            </tbody></table></div>
            @if(!$readonly && $students->isNotEmpty())
                <div class="grades-savebar"><p>Vérifiez les notes avant de valider l'enregistrement.</p><button class="grades-btn grades-btn-primary" type="submit">Enregistrer les notes</button></div>
            @endif
        </form>
    </section>
    <a class="grades-back-link" href="{{ route('company.school.grades.index', ['year_id' => $year->id, 'class_id' => $class->id]) }}">← Retour à la liste des évaluations</a>
</div>
@endsection
