@extends('layouts.school')
@section('title', 'Rapports des absences')
@vite('resources/css/school-attendance-reports.css')
@section('content')
<div class="sar-page">
    <header class="sar-header"><div><span class="sar-kicker">VIE SCOLAIRE / ANALYSE</span><h1>Rapports des absences</h1><p>Suivez l'assiduité et exportez les appels enregistrés.</p></div><a class="sar-link" href="{{ route('company.school.attendance.index') }}">← Retour aux appels</a></header>
    @if($errors->any())<div class="sar-error">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <section class="sar-panel"><form class="sar-filters" method="GET" action="{{ route('company.school.attendance.reports.index') }}">
        <label>Année scolaire<select name="year_id">@foreach($years as $year)<option value="{{ $year->id }}" @selected($yearId == $year->id)>{{ $year->name ?? $year->id }}</option>@endforeach</select></label>
        <label>Classe<select name="class_id"><option value="">Toutes les classes autorisées</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name }}</option>@endforeach</select></label>
        <label>Du<input type="date" name="from" value="{{ $from }}" required></label><label>Au<input type="date" name="to" value="{{ $to }}" required></label>
        <button class="sar-btn" type="submit">Appliquer</button>
    </form></section>
    <section class="sar-stats">
        <article><span>Enregistrements</span><strong>{{ $total }}</strong><small>Statuts saisis</small></article>
        <article class="present"><span>Présences</span><strong>{{ $summary->get('present', 0) }}</strong><small>Appels « présent »</small></article>
        <article class="absent"><span>Absences</span><strong>{{ $summary->get('absent', 0) }}</strong><small>Non excusées</small></article>
        <article class="late"><span>Retards</span><strong>{{ $summary->get('late', 0) }}</strong><small>Arrivées tardives</small></article>
        <article class="excused"><span>Excusées</span><strong>{{ $summary->get('excused', 0) }}</strong><small>Statut d'appel</small></article>
    </section>
    <section class="sar-panel"><div class="sar-panel-head"><div><span class="sar-kicker">HISTORIQUE</span><h2>Détail des présences</h2><p>Les chiffres portent sur les appels enregistrés, et non sur tous les jours ouvrables.</p></div><div class="sar-actions"><a class="sar-link" href="{{ route('company.school.attendance.reports.csv', request()->query()) }}">Exporter CSV (Excel)</a><button type="button" class="sar-btn sar-print" onclick="window.print()">Imprimer / PDF</button></div></div>
    <div class="sar-table-wrap"><table><thead><tr><th>Date</th><th>Classe</th><th>Élève</th><th>Statut</th><th>Retard</th><th>Justificatif</th></tr></thead><tbody>
        @forelse($rows as $row)<tr><td>{{ \Illuminate\Support\Carbon::parse($row->attendance_date)->format('d/m/Y') }}</td><td>{{ $row->class_name }}</td><td><strong>{{ $row->last_name }} {{ $row->first_name }}</strong><small>{{ $row->student_number }}</small></td><td><span class="sar-status sar-{{ $row->status }}">{{ ['present'=>'Présent','absent'=>'Absent','late'=>'Retard','excused'=>'Excusé'][$row->status] ?? $row->status }}</span></td><td>{{ $row->minutes_late === null ? '—' : $row->minutes_late.' min' }}</td><td>{{ ['pending'=>'En attente','approved'=>'Validé','rejected'=>'Refusé'][$row->justification_status] ?? '—' }}</td></tr>
        @empty<tr><td colspan="6" class="sar-empty">Aucun appel enregistré pour les filtres sélectionnés.</td></tr>@endforelse
    </tbody></table></div><div class="sar-pagination">{{ $rows->links() }}</div></section>
</div>
@endsection
