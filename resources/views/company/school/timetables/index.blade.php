@extends('layouts.school')
@section('title', 'Emplois du temps')
@section('page-title', 'Emplois du temps')
@section('page-description', 'Planification des cours et détection des conflits')
@push('styles') @vite('resources/css/school-timetables.css') @endpush
@section('content')
<div class="tt-page">
    <div class="tt-panel">
        <h2>Choisir une classe et une année</h2>
        <form method="GET" action="{{ route('company.school.timetables.index') }}" class="tt-grid">
            <label>Année scolaire<select name="year_id" required>@foreach($years as $year)<option value="{{ $year->id }}" @selected($yearId === (int) $year->id)>{{ $year->name ?? $year->label ?? $year->id }}</option>@endforeach</select></label>
            <label>Classe<select name="class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId === (int) $class->id)>{{ $class->name }}</option>@endforeach</select></label>
            <button type="submit">Afficher</button>
        </form>
    </div>
    @if(!$valid)
        <div class="tt-panel">Créez une année scolaire et une classe pour commencer.</div>
    @else
        <div class="tt-panel">
            <div class="tt-heading"><h2>Planning hebdomadaire</h2><a class="tt-link" target="_blank" href="{{ route('company.school.timetables.print', ['year_id' => $yearId, 'class_id' => $classId]) }}">Version imprimable</a></div>
            <div class="tt-scroll"><table><thead><tr><th>Jour</th><th>Horaire</th><th>Matière</th><th>Salle</th><th>Remarque</th><th></th></tr></thead><tbody>
                @php $days = [1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi']; @endphp
                @forelse($slots as $slot)
                    <tr><td>{{ $days[$slot->weekday] ?? $slot->weekday }}</td><td>{{ substr($slot->starts_at, 0, 5) }} – {{ substr($slot->ends_at, 0, 5) }}</td><td>{{ $slot->subject_name }}</td><td>{{ $slot->room_name ?? '—' }}</td><td>{{ $slot->note ?? '—' }}</td><td><form method="POST" action="{{ route('company.school.timetables.destroy', $slot->id) }}" onsubmit="return confirm('Supprimer ce créneau ?')">@csrf @method('DELETE')<button class="tt-danger">Supprimer</button></form></td></tr>
                @empty<tr><td colspan="6">Aucun cours planifié pour cette classe.</td></tr>@endforelse
            </tbody></table></div>
        </div>
        <div class="tt-panel"><h2>Ajouter un cours</h2>
            <form method="POST" action="{{ route('company.school.timetables.store') }}" class="tt-grid">@csrf
                <input type="hidden" name="year_id" value="{{ $yearId }}"><input type="hidden" name="class_id" value="{{ $classId }}">
                <label>Matière<select name="subject_id" required><option value="">Choisir</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>@endforeach</select></label>
                <label>Jour<select name="weekday" required>@foreach($days as $dayId=>$day)<option value="{{ $dayId }}" @selected(old('weekday') == $dayId)>{{ $day }}</option>@endforeach</select></label>
                <label>Début<input type="time" name="starts_at" value="{{ old('starts_at', '08:00') }}" required></label>
                <label>Fin<input type="time" name="ends_at" value="{{ old('ends_at', '09:00') }}" required></label>
                <label>Salle<select name="room_id"><option value="">Sans salle</option>@foreach($rooms as $room)<option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->name }}</option>@endforeach</select></label>
                <label>Remarque<input name="note" maxlength="255" value="{{ old('note') }}" placeholder="Facultatif"></label>
                <button type="submit">Enregistrer le cours</button>
            </form>
            <p class="tt-muted">Les chevauchements d'horaires pour une classe ou une salle sont automatiquement refusés. L'affectation des enseignants sera raccordée à votre structure existante lors de la prochaine étape.</p>
        </div>
        <div class="tt-panel"><h2>Ajouter une salle</h2>
            <form method="POST" action="{{ route('company.school.timetables.rooms.store') }}" class="tt-grid">@csrf
                <label>Nom de la salle<input name="name" required maxlength="120" placeholder="Ex. Salle A1"></label>
                <label>Capacité<input type="number" min="1" name="capacity" placeholder="Facultatif"></label>
                <button type="submit">Ajouter la salle</button>
            </form>
        </div>
    @endif
</div>
@endsection
