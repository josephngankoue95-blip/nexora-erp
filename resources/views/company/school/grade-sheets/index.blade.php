@extends('layouts.school')

@section('title', 'Fiches vierges de notes')

@push('styles')
    @vite('resources/css/school-grades.css')
@endpush

@section('content')
<div class="grade-sheet-page">
    <header class="grade-sheet-header">
        <div>
            <h1>Fiches vierges de notes</h1>
            <p>Préparez des feuilles de relevé à remplir manuellement par les enseignants.</p>
        </div>
    </header>

    @if($errors->any())
        <div class="grade-sheet-error">{{ $errors->first() }}</div>
    @endif

    <section class="grade-sheet-panel">
        <form action="{{ route('company.school.grade-sheets.index') }}" method="GET" class="grade-sheet-form">
            <div class="grade-sheet-field">
                <label for="year_id">Année scolaire</label>
                <select name="year_id" id="year_id" onchange="this.form.submit()" required>
                    <option value="">Sélectionner une année</option>
                    @foreach($years as $item)
                        <option value="{{ $item->id }}" @selected($year && $year->id == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grade-sheet-field">
                <label for="class_id">Classe</label>
                <select name="class_id" id="class_id" onchange="this.form.submit()" required>
                    <option value="">Sélectionner une classe</option>
                    @foreach($classes as $item)
                        <option value="{{ $item->id }}" @selected($class && $class->id == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grade-sheet-field">
                <label for="assignment_id">Matière et enseignant affecté</label>
                <select name="assignment_id" id="assignment_id" required>
                    <option value="">Sélectionner une affectation</option>
                    @foreach($assignments as $item)
                        <option value="{{ $item->id }}" @selected($assignment && $assignment->id == $item->id)>
                            {{ $item->subject_name }} — {{ $item->teacher_last_name }} {{ $item->teacher_first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grade-sheet-field">
                <label for="period_id">Période d'évaluation</label>
                <select name="period_id" id="period_id" required>
                    <option value="">Sélectionner une période</option>
                    @foreach($periods as $item)
                        <option value="{{ $item->id }}" @selected($period && $period->id == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grade-sheet-field">
                <label for="columns">Colonnes de notes vierges</label>
                <select name="columns" id="columns">
                    @foreach([3, 4, 5, 6] as $count)
                        <option value="{{ $count }}" @selected(request('columns', 4) == $count)>{{ $count }} colonnes</option>
                    @endforeach
                </select>
            </div>
            <div class="grade-sheet-actions">
                <button type="submit" formaction="{{ route('company.school.grade-sheets.print') }}" formtarget="_blank" class="grade-sheet-button" @disabled(!$year || !$class || $assignments->isEmpty() || $periods->isEmpty())>
                    Ouvrir la fiche imprimable
                </button>
            </div>
        </form>
        @if($class && $assignments->isEmpty())
            <p class="grade-sheet-hint">Aucune affectation active et configurée pour cette classe. Vérifiez les affectations des enseignants.</p>
        @endif
        @if($year && $periods->isEmpty())
            <p class="grade-sheet-hint">Aucune période d'évaluation n'est configurée pour cette année.</p>
        @endif
    </section>
</div>
@endsection
