@extends('layouts.school')

@section('title', 'Cartes scolaires')
@section('page-title', 'Cartes scolaires')

@push('styles')
    @vite('resources/css/school-cards.css')
@endpush

@section('content')
<div class="cards-page">
    <div class="cards-hero">
        <div>
            <span class="cards-eyebrow">GESTION SCOLAIRE · IDENTIFICATION</span>
            <h1>Cartes scolaires</h1>
            <p>Cartes recto uniquement : recherchez un élève ou imprimez une classe entière.</p>
        </div>
        <div class="cards-hero-icon" aria-hidden="true">▣</div>
    </div>

    <form method="GET" action="{{ route('company.school.cards.index') }}" class="cards-panel cards-filters">
        <label>Année scolaire
            <select name="year_id" onchange="this.form.submit()">
                @foreach($years as $y)
                    <option value="{{ $y->id }}" @selected($year && $year->id == $y->id)>{{ $y->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Classe
            <select name="class_id">
                <option value="">Toutes les classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected($classId == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Recherche
            <input type="search" name="q" value="{{ $search }}" placeholder="Nom, prénom ou matricule">
        </label>
        <button class="cards-btn cards-btn-primary" type="submit">Rechercher</button>
        <a class="cards-btn cards-btn-muted" href="{{ route('company.school.cards.index') }}">Réinitialiser</a>
    </form>

    @if($year && $classId)
        <div class="cards-toolbar">
            <span>Impression recto des cartes de la classe sélectionnée (maximum 200 élèves)</span>
            <div>
                <a target="_blank" rel="noopener" class="cards-btn cards-btn-muted"
                   href="{{ route('company.school.cards.print', ['year_id' => $year->id, 'class_id' => $classId]) }}">Aperçu / Imprimer</a>
                <a class="cards-btn cards-btn-primary"
                   href="{{ route('company.school.cards.print', ['year_id' => $year->id, 'class_id' => $classId, 'pdf' => 1]) }}">Exporter PDF</a>
            </div>
        </div>
    @endif

    <section class="cards-panel">
        <div class="cards-section-head">
            <h2>Liste des élèves</h2>
            <span>{{ $students ? $students->total() : 0 }} élève(s)</span>
        </div>
        <div class="cards-table-wrap">
            <table class="cards-table">
                <thead>
                    <tr><th>Élève</th><th>Matricule</th><th>Classe</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($students ?? [] as $s)
                        <tr>
                            <td><strong>{{ $s->last_name }} {{ $s->first_name }}</strong></td>
                            <td>{{ $s->student_number }}</td>
                            <td>{{ $s->class_name }}</td>
                            <td class="cards-actions">
                                <a target="_blank" rel="noopener" class="cards-btn cards-btn-muted"
                                   href="{{ route('company.school.cards.print', ['year_id' => $year->id, 'class_id' => $s->school_class_id, 'student_id' => $s->id]) }}">Aperçu</a>
                                <a class="cards-btn cards-btn-primary"
                                   href="{{ route('company.school.cards.print', ['year_id' => $year->id, 'class_id' => $s->school_class_id, 'student_id' => $s->id, 'pdf' => 1]) }}">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="cards-empty">Aucun élève trouvé pour ces critères.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students && $students->hasPages())
            <div class="cards-pagination">{{ $students->links() }}</div>
        @endif
    </section>
</div>
@endsection
