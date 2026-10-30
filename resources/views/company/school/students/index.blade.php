@extends('layouts.school')

@section('title', 'Gestion des élèves')

@section('page-title', 'Gestion des élèves')

@section('page-description', 'Répertoire des élèves et suivi des inscriptions')

@section('content')

<div class="students-page">

    <div class="students-heading">

        <div><span class="students-eyebrow">ADMINISTRATION SCOLAIRE</span><h2>Liste des élèves</h2><p>Consultez, recherchez et gérez les élèves de votre établissement.</p></div>

        <a class="students-button students-primary" href="{{ route('company.school.students.create') }}">＋ Nouvel élève</a>

    </div>

    <section class="students-panel" aria-label="Recherche et filtres">

        <div class="students-panel-title"><div><h3>Rechercher un élève</h3><p>Affinez la liste selon l'année, la section, le niveau ou la classe.</p></div><span class="students-count">{{ $students->total() }} élève(s)</span></div>

        <form method="GET" action="{{ route('company.school.students.index') }}">

            <div class="students-filters">

                <label class="students-field students-search"><span>Recherche</span><input name="q" type="search" placeholder="Nom, prénom ou matricule" value="{{ request('q') }}"></label>

                <label class="students-field"><span>Année scolaire</span><select name="year_id" id="filterYear"><option value="">Toutes les années</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(request('year_id')  ==  $year->id)>{{ $year->name }}</option>@endforeach</select></label>

                <label class="students-field"><span>Section</span><select name="section_id" id="filterSection"><option value="">Toutes les sections</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected(request('section_id')  ==  $section->id)>{{ $section->name }}</option>@endforeach</select></label>

                <label class="students-field"><span>Niveau</span><select name="level_id" id="filterLevel"><option value="">Tous les niveaux</option>@foreach($levels as $level)<option data-section="{{ $level->school_section_id }}" value="{{ $level->id }}" @selected(request('level_id')  ==  $level->id)>{{ $level->name }}</option>@endforeach</select></label>

                <label class="students-field"><span>Classe</span><select name="class_id" id="filterClass"><option value="">Toutes les classes</option>@foreach($classes as $class)<option data-level="{{ $class->school_level_id }}" data-year="{{ $class->school_year_id }}" value="{{ $class->id }}" @selected(request('class_id')  ==  $class->id)>{{ $class->name }}</option>@endforeach</select></label>

                <label class="students-field"><span>Trier par</span><select name="sort"><option value="name" @selected(request('sort', 'name') === 'name')>Nom (A–Z)</option><option value="matricule" @selected(request('sort') === 'matricule')>Matricule</option><option value="recent" @selected(request('sort') === 'recent')>Plus récents</option></select></label>

            </div>

            <div class="students-filter-actions"><button class="students-button students-primary" type="submit">Appliquer les filtres</button><a class="students-button students-outline" href="{{ route('company.school.students.index') }}">Réinitialiser</a></div>

        </form>

    </section>

    <section class="students-panel students-tools-panel" aria-label="Import et export Excel">

        <div class="students-panel-title"><div><h3>Importation et exportation</h3><p>Gérez vos listes d'élèves au format Excel.</p></div></div>

        <div class="students-tools-grid">

            <div class="students-tool"><div><strong>Exporter la liste</strong><p>Exportez les élèves correspondant aux filtres actifs.</p></div><a class="students-button students-outline" href="{{ route('company.school.students.export', request()->query()) }}">↓ Exporter Excel</a></div>

            <div class="students-tool"><div><strong>Importer des élèves</strong><p>Utilisez le modèle pour préparer vos données.</p></div><a class="students-template" href="{{ route('company.school.students.template') }}">Télécharger le modèle Excel</a><form method="POST" enctype="multipart/form-data" action="{{ route('company.school.students.import') }}" class="students-import">@csrf<input aria-label="Fichier Excel des élèves" type="file" name="file" accept=".xlsx,.xls,.csv" required><button class="students-button students-primary" type="submit">Importer</button></form></div>

        </div>

    </section>

    <section class="students-panel students-list-panel" aria-label="Liste des élèves">

        <div class="students-panel-title"><div><h3>Répertoire des élèves</h3><p>{{ $students->total() }} résultat(s) au total</p></div></div>

        <div class="students-table-scroll">

            <table class="students-table students-table-detailed">

                <thead><tr>

                    <th>Photo</th><th>Matricule</th><th>Nom et prénom</th><th>Sexe</th><th>Naissance</th>

                    <th>Section</th><th>Niveau</th><th>Classe</th><th>Année scolaire</th>

                    <th>Parent / Tuteur</th><th>Téléphone</th><th class="students-actions-col">Actions</th>

                </tr></thead>

                <tbody>

                @forelse($students as $student)

                    @php

                        // Privilégier l'inscription correspondant aux filtres ; sinon la plus récente.

                        $currentEnrollment = $student->enrollments->first(function ($enrollment) {

                            $schoolClass = $enrollment->schoolClass;

                            return (!request()->filled('year_id') || (int)$enrollment->school_year_id === (int)request('year_id'))

                                && (!request()->filled('class_id') || (int)$enrollment->school_class_id === (int)request('class_id'))

                                && (!request()->filled('level_id') || (int)($schoolClass?->school_level_id) === (int)request('level_id'))

                                && (!request()->filled('section_id') || (int)($schoolClass?->level?->school_section_id) === (int)request('section_id'));

                        });

                        $currentEnrollment ??= $student->enrollments->first();

                        $studentClass = $currentEnrollment?->schoolClass;

                        $studentLevel = $studentClass?->level;

                        $studentSection = $studentLevel?->section;

                    @endphp

                    <tr>

                        <td>

                            @if($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo))

                                <img class="students-avatar-photo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($student->photo) }}" alt="Photo de {{ $student->last_name }}" loading="lazy">

                            @else

                                <span class="students-initial">{{ mb_strtoupper(mb_substr($student->last_name ?: 'E', 0, 1)) }}</span>

                            @endif

                        </td>

                        <td><span class="students-matricule">{{ $student->student_number }}</span></td>

                        <td><div class="students-identity"><div><strong>{{ $student->last_name }} {{ $student->first_name }}</strong><small>{{ $student->place_of_birth ?: 'Lieu non renseigné' }}</small></div></div></td>

                        <td>{{ $student->gender === 'M' ? 'Masculin' : ($student->gender === 'F' ? 'Féminin' : '—') }}</td>

                        <td>{{ $student->date_of_birth?->format('d/m/Y') ?? '—' }}</td>

                        <td>{{ $studentSection?->name ?? '—' }}</td>

                        <td>{{ $studentLevel?->name ?? '—' }}</td>

                        <td><span class="students-class-chip">{{ $studentClass?->name ?? 'Non inscrit' }}</span></td>

                        <td>{{ $currentEnrollment?->year?->name ?? '—' }}</td>

                        <td>{{ $student->guardian_name ?: '—' }}</td>

                        <td>{{ $student->guardian_phone ?: '—' }}</td>

                        <td><div class="students-row-actions"><a class="students-edit" href="{{ route('company.school.students.edit', $student) }}">Modifier</a><form method="POST" action="{{ route('company.school.students.destroy', $student) }}" onsubmit="return confirm('Supprimer cet élève ?')">@csrf @method('DELETE')<button type="submit" class="students-delete">Supprimer</button></form></div></td>

                    </tr>

                @empty

                    <tr><td colspan="12"><div class="students-empty">Aucun élève ne correspond aux critères sélectionnés.</div></td></tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($students->hasPages())<div class="students-pagination">{{ $students->links() }}</div>@endif

    </section>

</div>

<style>

.students-page{max-width:1380px;margin:0 auto;color:#17223b}.students-heading{display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap;margin:0 0 24px}.students-eyebrow{font-size:11px;letter-spacing:1.2px;font-weight:800;color:#4f46e5}.students-heading h2{font-size:26px;margin:6px 0 5px;letter-spacing:-.6px}.students-heading p,.students-panel-title p,.students-tool p{color:#66738b;font-size:13px;margin:5px 0 0}.students-button{display:inline-flex;align-items:center;justify-content:center;gap:7px;border-radius:9px;padding:11px 17px;font-size:13px;font-weight:700;border:1px solid transparent;white-space:nowrap;cursor:pointer;text-decoration:none}.students-primary{background:#4f46e5;color:#fff}.students-primary:hover{background:#3730a3}.students-outline{background:#fff;color:#3730a3;border-color:#d9def0}.students-outline:hover{background:#f5f4ff}.students-panel{background:#fff;border:1px solid #e5eaf3;border-radius:15px;padding:22px;margin-bottom:20px;box-shadow:0 3px 16px #17223b07}.students-panel-title{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:19px}.students-panel-title h3{font-size:16px;margin:0}.students-count{background:#eef2ff;color:#4338ca;border-radius:20px;padding:7px 11px;font-size:12px;font-weight:800}.students-filters{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.students-field{display:flex;flex-direction:column;gap:7px;min-width:0}.students-field>span{font-size:12px;font-weight:700;color:#42516c}.students-field input,.students-field select{width:100%;min-width:0;min-height:43px;padding:10px 12px;border:1px solid #dbe2ee;border-radius:9px;background:#fff;color:#17223b;font-size:13px;outline:none}.students-field input:focus,.students-field select:focus{border-color:#6366f1;box-shadow:0 0 0 3px #6366f119}.students-filter-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:19px}.students-tools-grid{display:grid;grid-template-columns:1fr 1.3fr;gap:16px}.students-tool{border:1px solid #e5eaf3;background:#fafbff;border-radius:11px;padding:17px;display:flex;flex-direction:column;align-items:flex-start;gap:13px;min-width:0}.students-tool strong{font-size:14px}.students-template{font-size:12px;color:#4f46e5;font-weight:700;text-decoration:underline}.students-import{display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%}.students-import input{flex:1;min-width:180px;max-width:100%;font-size:12px;color:#42516c}.students-table-scroll{overflow-x:auto}.students-list-panel{padding-bottom:12px}.students-table{width:100%;border-collapse:collapse;min-width:620px;text-align:left}.students-table th{background:#f4f6fb;color:#52617b;text-transform:uppercase;letter-spacing:.5px;font-size:11px;padding:13px 15px;font-weight:800}.students-table td{padding:15px;border-bottom:1px solid #edf0f5;font-size:13px;vertical-align:middle}.students-table tr:last-child td{border-bottom:0}.students-matricule{font-size:12px;font-weight:800;color:#4338ca;background:#eef2ff;padding:7px 9px;border-radius:7px;white-space:nowrap}.students-identity{display:flex;align-items:center;gap:11px}.students-initial{width:36px;height:36px;display:grid;place-items:center;flex:none;border-radius:10px;background:#e8e9ff;color:#4338ca;font-weight:800}.students-identity strong{display:block;font-size:13px}.students-identity small{display:block;color:#8290a6;margin-top:3px}.students-row-actions{display:flex;gap:8px;align-items:center;justify-content:flex-end}.students-row-actions form{margin:0}.students-edit,.students-delete{border-radius:7px;padding:8px 11px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;white-space:nowrap}.students-edit{background:#eff6ff;color:#1d4ed8;border:1px solid #dbeafe}.students-delete{background:#fff1f2;color:#be123c;border:1px solid #ffe4e6}.students-actions-col{text-align:right}.students-empty{text-align:center;padding:32px 10px;color:#66738b}.students-pagination{padding-top:16px;border-top:1px solid #edf0f5;margin-top:8px}@media(max-width:1050px){.students-filters{grid-template-columns:repeat(2,minmax(0,1fr))}.students-tools-grid{grid-template-columns:1fr}}@media(max-width:600px){.students-heading h2{font-size:22px}.students-panel{padding:15px}.students-filters{grid-template-columns:1fr}.students-filter-actions .students-button{flex:1}.students-row-actions{justify-content:flex-start}.students-heading>.students-button{width:100%}}

.students-avatar-photo{width:42px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #dbe2ee}.students-table-detailed{min-width:1510px}.students-table-detailed td{white-space:nowrap}.students-table-detailed td:nth-child(3){min-width:210px;white-space:normal}.students-class-chip{display:inline-flex;background:#f0fdf4;color:#166534;border:1px solid #dcfce7;padding:6px 9px;border-radius:7px;font-weight:700;font-size:12px}.students-table-detailed thead th{position:sticky;top:0;z-index:1}.students-table-scroll{max-width:100%;overflow-x:auto;scrollbar-width:thin}


/* Interface modernisée, cohérente avec la gestion scolaire */
.students-page{--st-primary:#1d4ed8;--st-dark:#123c72;--st-border:#e1e8f1;--st-muted:#64748b;max-width:1480px}
.students-heading{padding:8px 0 2px}.students-eyebrow{color:var(--st-primary)}
.students-heading h2{font-weight:800;color:#102d51;letter-spacing:-.7px}
.students-panel{border-color:var(--st-border);box-shadow:0 7px 24px rgba(16,45,81,.045);border-radius:16px}
.students-panel-title h3{color:#17355a;font-weight:800}
.students-primary{background:var(--st-primary);box-shadow:0 3px 9px rgba(29,78,216,.14)}
.students-primary:hover{background:#1e40af}.students-outline{color:var(--st-primary)}
.students-count{background:#eaf2ff;color:#1744a0}
.students-field input:focus,.students-field select:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.12)}
.students-tools-grid{grid-template-columns:1fr 1.3fr}
.students-tool{background:#f8faff;border-color:#e1e9f5}
.students-table-scroll{border:1px solid #e6edf5;border-radius:12px}
.students-table thead th{background:#eef4fc;color:#355171;white-space:nowrap}
.students-table tbody tr:hover{background:#f8fbff}
.students-table td{padding:12px 14px}
.students-avatar-photo,.students-initial{width:42px;height:48px;border-radius:9px;display:block;object-fit:cover}
.students-initial{display:grid;place-items:center;background:#eaf1ff;color:#1d4ed8}
.students-identity strong{color:#17355a;line-height:1.45}
.students-matricule{background:#eaf2ff;color:#1d4ed8}
.students-row-actions{gap:7px}.students-edit{color:#1d4ed8}
.students-table-detailed{min-width:1510px}
@media(max-width:1050px){.students-tools-grid{grid-template-columns:1fr}}
@media(max-width:600px){.students-panel{padding:15px}.students-heading>.students-button{width:100%}.students-tools-grid{gap:12px}.students-table td{padding:11px}}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const section = document.getElementById('filterSection');
    const level = document.getElementById('filterLevel');
    const schoolClass = document.getElementById('filterClass');
    const year = document.getElementById('filterYear');
    if (!section || !level || !schoolClass || !year) return;

    function refresh() {
        const sectionId = section.value;
        const yearId = year.value;
        for (const option of level.options) {
            if (!option.value) continue;
            option.hidden = Boolean(sectionId && option.dataset.section !== sectionId);
        }
        if (level.selectedOptions[0]?.hidden) level.value = '';
        const levelId = level.value;
        for (const option of schoolClass.options) {
            if (!option.value) continue;
            const belongsToSection = !sectionId || Array.from(level.options).some(
                item => item.value === option.dataset.level && item.dataset.section === sectionId
            );
            option.hidden = !belongsToSection
                || Boolean(levelId && option.dataset.level !== levelId)
                || Boolean(yearId && option.dataset.year !== yearId);
        }
        if (schoolClass.selectedOptions[0]?.hidden) schoolClass.value = '';
    }
    [section, level, year].forEach(element => element.addEventListener('change', refresh));
    refresh();
});
</script>

@endsection
