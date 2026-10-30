@extends('layouts.school')



@section('title', 'Absences et présences')



@vite(['resources/css/school-attendance.css', 'resources/js/school-attendance.js'])



@section('content')

@php

    $totalStudents = $class ? $students->count() : 0;

    $savedCount = $class ? $entries->count() : 0;

    $presentCount = $class ? $entries->where('status', 'present')->count() : 0;

    $absentCount = $class ? $entries->where('status', 'absent')->count() : 0;

    $lateCount = $class ? $entries->where('status', 'late')->count() : 0;

    $excusedCount = $class ? $entries->where('status', 'excused')->count() : 0;

    $readOnly = $year && $year->is_archived;

@endphp

<div class="sa-page">

    <div class="sa-heading">

        <div class="sa-heading-copy">

            <div class="sa-eyebrow"><span class="sa-eyebrow-dot"></span> VIE SCOLAIRE <span class="sa-eyebrow-separator">/</span> SUIVI DES ÉLÈVES</div>

            <h1>Absences et présences</h1>

            <p>Suivez l'assiduité des élèves, effectuez l'appel et consultez l'historique de vos classes.</p>

        </div>

        <div class="sa-page-actions">

            <a class="sa-btn sa-btn-justifications" href="{{ route('company.school.attendance.justifications.index') }}">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3h8l4 4v14H4V3h4Z"/><path d="M16 3v5h5M8 13h8M8 17h5"/></svg>

                Justificatifs d'absence

            </a>

        </div>

        <div class="sa-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 9h18M8 14l2 2 5-5"/></svg></div>

    </div>



    @if(session('success'))

        <div class="sa-alert sa-alert-success" role="status">✓ {{ session('success') }}</div>

    @endif

    @if($errors->any())

        <div class="sa-alert sa-alert-error" role="alert"><strong>Veuillez corriger les erreurs :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>

    @endif



    <section class="sa-panel sa-filter-panel" aria-labelledby="sa-filter-title">

        <div class="sa-section-head"><div><span class="sa-section-kicker">SÉLECTION</span><h2 id="sa-filter-title">Choisir une classe et une date</h2></div><span class="sa-head-note">Les données sont propres à votre établissement</span></div>

        <form method="GET" action="{{ route('company.school.attendance.index') }}" class="sa-filters">

            <label class="sa-field"><span>Année scolaire</span><select name="year_id" id="sa-year" required>

                @foreach($years as $item)<option value="{{ $item->id }}" @selected($year && $year->id == $item->id)>{{ $item->name ?? $item->id }}</option>@endforeach

            </select></label>

            <label class="sa-field"><span>Classe</span><select name="class_id" required><option value="">Sélectionner une classe</option>

                @foreach($classes as $item)<option value="{{ $item->id }}" @selected($class && $class->id == $item->id)>{{ $item->name }}</option>@endforeach

            </select></label>

            <label class="sa-field sa-date-field"><span>Date de l'appel</span><input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" required></label>

            <button type="submit" class="sa-btn sa-btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg> Afficher l'appel</button>

        </form>

    </section>



    @if($class)

        <div class="sa-stats" aria-label="Statistiques de l'appel enregistré">

            <div class="sa-stat sa-stat-total"><div class="sa-stat-top"><span>Élèves inscrits</span><span class="sa-stat-symbol">♙</span></div><strong>{{ $totalStudents }}</strong><small>Dans la classe {{ $class->name }}</small></div>

            <div class="sa-stat sa-stat-present"><div class="sa-stat-top"><span>Présents</span><span class="sa-stat-symbol">✓</span></div><strong>{{ $presentCount }}</strong><small>Appel enregistré</small></div>

            <div class="sa-stat sa-stat-absent"><div class="sa-stat-top"><span>Absents</span><span class="sa-stat-symbol">!</span></div><strong>{{ $absentCount }}</strong><small>Non justifiés</small></div>

            <div class="sa-stat sa-stat-late"><div class="sa-stat-top"><span>Retards</span><span class="sa-stat-symbol">◷</span></div><strong>{{ $lateCount }}</strong><small>{{ $excusedCount }} absence(s) excusée(s)</small></div>

        </div>



        <section class="sa-panel sa-roll-panel" aria-labelledby="sa-roll-title">

            <div class="sa-section-head sa-roll-heading">

                <div><span class="sa-section-kicker">FEUILLE DE PRÉSENCE</span><h2 id="sa-roll-title">Appel — {{ $class->name }}</h2><p>{{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('l d F Y') }} <span class="sa-bullet">•</span> {{ $totalStudents }} élève(s)</p></div>

                <div class="sa-heading-actions"><span class="sa-pill {{ $readOnly ? 'sa-pill-archived' : ($savedCount ? 'sa-pill-saved' : 'sa-pill-pending') }}">{{ $readOnly ? 'Année archivée' : ($savedCount ? 'Appel enregistré' : 'Appel à effectuer') }}</span></div>

            </div>

            @if($readOnly)<div class="sa-inline-warning">Cette année scolaire est archivée. Les informations sont disponibles en consultation uniquement.</div>@endif

            <form method="POST" action="{{ route('company.school.attendance.store') }}" id="sa-attendance-form">

                @csrf

                <input type="hidden" name="school_year_id" value="{{ $year->id }}">

                <input type="hidden" name="school_class_id" value="{{ $class->id }}">

                <input type="hidden" name="attendance_date" value="{{ $date }}">

                <div class="sa-table-toolbar"><div class="sa-toolbar-label">Liste des élèves <span>{{ $totalStudents }}</span></div><label class="sa-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg><input type="search" id="sa-student-search" placeholder="Rechercher un élève…" aria-label="Rechercher un élève"></label></div>

                <div class="sa-table-scroll"><table class="sa-table sa-student-table"><thead><tr><th scope="col">ÉLÈVE</th><th scope="col">STATUT DE PRÉSENCE</th><th scope="col">RETARD (MIN)</th><th scope="col">MOTIF / OBSERVATION</th></tr></thead><tbody>

                    @forelse($students as $student)

                        @php($entry = $entries->get($student->id))

                        @php($status = old('attendance.'.$student->id.'.status', $entry->status ?? 'present'))

                        <tr class="sa-student-row" data-student="{{ mb_strtolower($student->last_name.' '.$student->first_name.' '.$student->student_number) }}">

                            <td><div class="sa-student"><span class="sa-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($student->last_name,0,1).mb_substr($student->first_name,0,1)) }}</span><div><strong>{{ $student->last_name }} {{ $student->first_name }}</strong><small>{{ $student->student_number }}</small></div></div></td>

                            <td><select class="sa-status" name="attendance[{{ $student->id }}][status]" @disabled($readOnly) aria-label="Statut de {{ $student->last_name }} {{ $student->first_name }}">

                                @foreach(['present'=>'Présent','absent'=>'Absent','late'=>'Retard','excused'=>'Absence excusée'] as $value=>$label)<option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>@endforeach

                            </select></td>

                            <td><input class="sa-delay" type="number" min="0" max="1440" name="attendance[{{ $student->id }}][minutes_late]" value="{{ old('attendance.'.$student->id.'.minutes_late', $entry->minutes_late ?? '') }}" placeholder="0" @disabled($readOnly) aria-label="Minutes de retard de {{ $student->last_name }}"></td>

                            <td><input class="sa-reason" type="text" maxlength="500" name="attendance[{{ $student->id }}][reason]" value="{{ old('attendance.'.$student->id.'.reason', $entry->reason ?? '') }}" placeholder="Ajouter une observation…" @disabled($readOnly) aria-label="Observation pour {{ $student->last_name }}"></td>

                        </tr>

                    @empty<tr><td colspan="4" class="sa-empty">Aucun élève inscrit dans cette classe.</td></tr>@endforelse

                    <tr class="sa-search-empty" hidden><td colspan="4" class="sa-empty">Aucun élève ne correspond à la recherche.</td></tr>

                </tbody></table></div>

                @if($students->isNotEmpty() && !$readOnly)

                    <div class="sa-form-bottom"><label class="sa-field sa-notes"><span>Note générale <small>(facultatif)</small></span><textarea name="notes" maxlength="2000" rows="3" placeholder="Informations complémentaires sur l'appel du jour…">{{ old('notes', $session->notes ?? '') }}</textarea></label><div class="sa-submit-area"><span class="sa-submit-hint">Vérifiez les statuts avant d'enregistrer.</span><button type="submit" class="sa-btn sa-btn-primary sa-save-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 3h12l4 4v14H3V3h2Z"/><path d="M7 3v6h10V3M7 21v-8h10v8"/></svg> Enregistrer l'appel</button></div></div>

                @endif

            </form>

        </section>



        <section class="sa-panel sa-history-panel" aria-labelledby="sa-history-title"><div class="sa-section-head"><div><span class="sa-section-kicker">SUIVI</span><h2 id="sa-history-title">Historique des appels</h2><p>Retrouvez les appels précédemment enregistrés pour cette classe.</p></div></div>

            <div class="sa-table-scroll"><table class="sa-table sa-history-table"><thead><tr><th scope="col">DATE</th><th scope="col">ABSENCES</th><th scope="col">RETARDS</th><th scope="col">ACTION</th></tr></thead><tbody>

                @forelse($history as $item)<tr><td><strong>{{ \Illuminate\Support\Carbon::parse($item->attendance_date)->format('d/m/Y') }}</strong></td><td><span class="sa-count sa-count-red">{{ $item->absent_count }}</span></td><td><span class="sa-count sa-count-amber">{{ $item->late_count }}</span></td><td><a class="sa-history-link" href="{{ route('company.school.attendance.index', ['year_id'=>$year->id,'class_id'=>$class->id,'date'=>$item->attendance_date]) }}">Consulter <span aria-hidden="true">→</span></a></td></tr>

                @empty<tr><td colspan="4" class="sa-empty"><span class="sa-empty-icon">▤</span><strong>Aucun appel enregistré</strong><span>L'historique apparaîtra ici après votre premier enregistrement.</span></td></tr>@endforelse

            </tbody></table></div>

        </section>

    @else

        <div class="sa-start"><span class="sa-start-icon">▤</span><h2>Prêt à effectuer l'appel ?</h2><p>Choisissez une année scolaire, une classe et une date, puis cliquez sur « Afficher l'appel ».</p></div>

    @endif

</div>

@endsection
