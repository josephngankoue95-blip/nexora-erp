@extends('layouts.school')
@section('title', 'Inscriptions scolaires')
@section('page-title', 'Inscriptions scolaires')
@section('page-description', 'Suivi des inscriptions par année, section, niveau et classe')
@section('content')
<div class="en-page">
    <header class="en-heading">
        <div><span class="en-eyebrow">ADMINISTRATION SCOLAIRE</span><h2>Liste des inscriptions</h2><p>Retrouvez les élèves inscrits et suivez leur situation scolaire.</p></div>
        <a class="en-btn en-primary" href="{{ route('company.school.enrollments.create') }}">＋ Nouvelle inscription</a>
    </header>
    <section class="en-panel">
        <div class="en-panel-head"><div><h3>Rechercher et filtrer</h3><p>Affinez les résultats selon l'établissement et l'année scolaire.</p></div><span class="en-count">{{ $enrollments->total() }} inscription(s)</span></div>
        <form method="GET" action="{{ route('company.school.enrollments.index') }}">
            <div class="en-grid">
                <label class="en-field"><span>Élève / matricule</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Nom, prénom ou matricule"></label>
                <label class="en-field"><span>Année scolaire</span><select name="year_id" id="enYear"><option value="">Toutes les années</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(request('year_id') == $year->id)>{{ $year->name }}</option>@endforeach</select></label>
                <label class="en-field"><span>Section</span><select name="section_id" id="enSection"><option value="">Toutes les sections</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>{{ $section->name }}</option>@endforeach</select></label>
                <label class="en-field"><span>Niveau</span><select name="level_id" id="enLevel"><option value="">Tous les niveaux</option>@foreach($levels as $level)<option data-section="{{ $level->school_section_id }}" value="{{ $level->id }}" @selected(request('level_id') == $level->id)>{{ $level->name }}</option>@endforeach</select></label>
                <label class="en-field"><span>Classe</span><select name="class_id" id="enClass"><option value="">Toutes les classes</option>@foreach($classes as $class)<option data-level="{{ $class->school_level_id }}" data-year="{{ $class->school_year_id }}" value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>@endforeach</select></label>
                <label class="en-field"><span>Statut</span><select name="status"><option value="">Tous les statuts</option><option value="enrolled" @selected(request('status')==='enrolled')>Inscrit</option><option value="transferred" @selected(request('status')==='transferred')>Transféré</option><option value="withdrawn" @selected(request('status')==='withdrawn')>Retiré</option></select></label>
                <label class="en-field"><span>Tri</span><select name="sort"><option value="recent" @selected(request('sort','recent')==='recent')>Inscriptions récentes</option><option value="oldest" @selected(request('sort')==='oldest')>Plus anciennes</option><option value="name" @selected(request('sort')==='name')>Nom (A–Z)</option></select></label>
            </div>
            <div class="en-actions"><button class="en-btn en-primary" type="submit">Appliquer les filtres</button><a class="en-btn en-outline" href="{{ route('company.school.enrollments.index') }}">Réinitialiser</a></div>
        </form>
    </section>
    <section class="en-panel en-table-panel">
        <div class="en-panel-head"><div><h3>Répertoire des inscriptions</h3><p>{{ $enrollments->total() }} résultat(s) · {{ $enrollments->perPage() }} par page</p></div></div>
        <div class="en-scroll"><table class="en-table"><thead><tr><th>Matricule</th><th>Élève</th><th>Section</th><th>Niveau</th><th>Classe</th><th>Année scolaire</th><th>Date d'inscription</th><th>Statut</th><th>Tuteur</th><th>Téléphone</th></tr></thead><tbody>
        @forelse($enrollments as $enrollment)
            @php $student = $enrollment->student; $schoolClass = $enrollment->schoolClass; $level = $schoolClass?->level; @endphp
            <tr>
                <td><span class="en-number">{{ $student?->student_number ?? '—' }}</span></td>
                <td><div class="en-person"><span class="en-avatar">{{ mb_strtoupper(mb_substr($student?->last_name ?: 'E',0,1)) }}</span><div><strong>{{ $student?->last_name }} {{ $student?->first_name }}</strong><small>{{ $student?->gender === 'M' ? 'Masculin' : ($student?->gender === 'F' ? 'Féminin' : '—') }}</small></div></div></td>
                <td>{{ $level?->section?->name ?? '—' }}</td><td>{{ $level?->name ?? '—' }}</td><td><span class="en-class">{{ $schoolClass?->name ?? '—' }}</span></td>
                <td>{{ $enrollment->year?->name ?? '—' }}</td><td>{{ $enrollment->enrolled_on?->format('d/m/Y') ?? '—' }}</td>
                <td><span class="en-status en-status-{{ $enrollment->status }}">{{ ['enrolled'=>'Inscrit','transferred'=>'Transféré','withdrawn'=>'Retiré'][$enrollment->status] ?? $enrollment->status }}</span></td>
                <td>{{ $student?->guardian_name ?: '—' }}</td><td>{{ $student?->guardian_phone ?: '—' }}</td>
            </tr>
        @empty<tr><td colspan="10" class="en-empty">Aucune inscription trouvée pour ces critères.</td></tr>@endforelse
        </tbody></table></div>
        @if($enrollments->hasPages())<div class="en-pagination">{{ $enrollments->links() }}</div>@endif
    </section>
</div>
@include('company.school.enrollments.styles')
@push('scripts')
<script>
(function(){const section=document.getElementById('enSection'),level=document.getElementById('enLevel'),cl=document.getElementById('enClass'),year=document.getElementById('enYear');if(!section||!level||!cl||!year)return;function refresh(){const s=section.value,y=year.value;for(const o of level.options){if(o.value)o.hidden=!!s&&o.dataset.section!==s;}if(level.selectedOptions[0]?.hidden)level.value='';const l=level.value;for(const o of cl.options){if(!o.value)continue;const matchesSection=!s||Array.from(level.options).some(x=>x.value===o.dataset.level&&x.dataset.section===s);o.hidden=!matchesSection||(!!l&&o.dataset.level!==l)||(!!y&&o.dataset.year!==y);}if(cl.selectedOptions[0]?.hidden)cl.value='';}section.addEventListener('change',refresh);level.addEventListener('change',refresh);year.addEventListener('change',refresh);refresh();})();
</script>
@endpush
@endsection
