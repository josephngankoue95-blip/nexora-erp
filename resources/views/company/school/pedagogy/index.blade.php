@extends('layouts.school')
@section('title', 'Gestion pédagogique')
@section('page-title', 'Gestion pédagogique')

@php
    $labels = [
        'teachers' => 'Enseignants',
        'subject-groups' => 'Groupes de matières',
        'subjects' => 'Matières',
        'assignments' => 'Affectations',
    ];
    $title = $labels[$type] ?? 'Gestion pédagogique';
    $base = 'company.school.' . $type;
    $total = method_exists($items, 'total') ? $items->total() : $items->count();
    $onPage = $items->count();
    $routeExists = \Illuminate\Support\Facades\Route::has($base . '.create');
@endphp

@push('styles')
<style>
.pedagogy-page{--pd-navy:#142e52;--pd-blue:#2563eb;--pd-border:#e2e8f0;--pd-muted:#64748b;--pd-bg:#f8fafc;color:#172b4d}
.pedagogy-page *{box-sizing:border-box}
.pedagogy-hero{background:linear-gradient(115deg,#132e53,#1e4f87);color:#fff;border-radius:17px;padding:25px 27px;display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;margin-bottom:19px;box-shadow:0 10px 24px rgba(18,47,85,.13)}
.pedagogy-eyebrow{font-size:11px;letter-spacing:1.3px;text-transform:uppercase;color:#b9d5fb;font-weight:800;margin-bottom:7px}
.pedagogy-hero h2{font-size:25px;letter-spacing:-.7px;line-height:1.2;margin:0 0 7px;color:#fff}
.pedagogy-hero p{font-size:12.5px;color:#d5e4f8;margin:0;max-width:650px}
.pedagogy-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 13px;border-radius:9px;text-decoration:none;font-weight:750;font-size:12px;border:1px solid transparent;cursor:pointer;white-space:nowrap;transition:background .15s,transform .15s}
.pedagogy-btn:hover{transform:translateY(-1px)}
.pedagogy-primary{background:#fff;color:#183e6b}.pedagogy-primary:hover{background:#e8f2ff}
.pedagogy-assign{background:#eef2ff;color:#4338ca}.pedagogy-edit{background:#eaf3ff;color:#1d4ed8}.pedagogy-delete{background:#fff1f2;color:#be123c}
.pedagogy-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;margin-bottom:19px}
.pedagogy-stat{background:#fff;border:1px solid var(--pd-border);border-radius:13px;padding:16px 18px;box-shadow:0 3px 12px rgba(20,46,82,.035)}
.pedagogy-stat-label{font-size:11px;font-weight:700;color:var(--pd-muted);margin-bottom:7px}.pedagogy-stat-number{font-size:25px;font-weight:850;color:var(--pd-navy);line-height:1.1}.pedagogy-stat-hint{font-size:10px;color:#94a3b8;margin-top:6px}
.pedagogy-panel{background:#fff;border:1px solid var(--pd-border);border-radius:15px;overflow:hidden;box-shadow:0 5px 22px rgba(20,46,82,.045)}
.pedagogy-panel-head{padding:20px 21px 16px;border-bottom:1px solid var(--pd-border)}
.pedagogy-panel-head h3{font-size:16px;color:var(--pd-navy);margin:0 0 4px}.pedagogy-panel-head p{font-size:12px;color:var(--pd-muted);margin:0}
.pedagogy-toolbar{display:grid;grid-template-columns:minmax(210px,2fr) minmax(145px,1fr) minmax(125px,1fr) auto;gap:11px;align-items:end;padding:17px 21px;background:#fbfcff;border-bottom:1px solid var(--pd-border)}
.pedagogy-field{display:flex;flex-direction:column;gap:6px;min-width:0}.pedagogy-field label{font-size:11px;color:#475569;font-weight:750}.pedagogy-field input,.pedagogy-field select{width:100%;height:41px;padding:0 12px;border:1px solid #cbd5e1;border-radius:9px;background:#fff;color:#172b4d;font-size:12px;outline:0}.pedagogy-field input:focus,.pedagogy-field select:focus{border-color:#3b82f6;box-shadow:0 0 0 3px #dbeafe}.pedagogy-reset{height:41px;background:#edf2f8;color:#334155;border:0}
.pedagogy-table-wrap{overflow-x:auto}.pedagogy-table{width:100%;min-width:790px;border-collapse:collapse}.pedagogy-table th,.pedagogy-table td{padding:14px 16px;text-align:left;border-bottom:1px solid #eaf0f6;font-size:12px;vertical-align:middle}.pedagogy-table th{background:#f4f7fc;color:#51627c;font-size:10.5px;text-transform:uppercase;letter-spacing:.45px;font-weight:850}.pedagogy-table tbody tr:hover{background:#f8fbff}.pedagogy-table tbody tr:last-child td{border-bottom:0}
.pedagogy-id{font-weight:750;color:#64748b}.pedagogy-name{font-weight:800;color:#18385f;font-size:12.5px}.pedagogy-muted{color:#64748b}.pedagogy-pill{display:inline-block;border-radius:100px;background:#eef4fd;color:#245287;padding:5px 9px;font-size:10px;font-weight:800}
.pedagogy-actions{display:flex;justify-content:flex-end;flex-wrap:wrap;gap:7px}.pedagogy-actions form{margin:0}.pedagogy-actions .pedagogy-btn{padding:8px 10px;font-size:11px}
.pedagogy-empty{text-align:center!important;padding:35px 18px!important;color:#64748b!important}.pedagogy-pagination{padding:17px 21px;border-top:1px solid var(--pd-border)}.pedagogy-footer-info{font-size:11px;color:#64748b;padding:13px 21px;border-top:1px solid #eef2f7}.pedagogy-flash{margin:0 0 15px;padding:12px 15px;border-radius:10px;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;font-size:12px}.pedagogy-alert{margin:0 0 15px;padding:12px 15px;border-radius:10px;background:#fff1f2;color:#be123c;border:1px solid #fecdd3;font-size:12px}
@media(max-width:1050px){.pedagogy-toolbar{grid-template-columns:repeat(2,minmax(0,1fr))}.pedagogy-reset{width:100%}}
@media(max-width:640px){.pedagogy-hero{padding:21px 18px}.pedagogy-hero h2{font-size:21px}.pedagogy-hero .pedagogy-btn{width:100%}.pedagogy-stats{grid-template-columns:1fr 1fr;gap:9px}.pedagogy-stat{padding:13px}.pedagogy-stat-number{font-size:22px}.pedagogy-stats .pedagogy-stat:last-child{grid-column:1/-1}.pedagogy-toolbar{grid-template-columns:1fr;padding:15px}.pedagogy-panel-head{padding:16px}.pedagogy-pagination{padding:14px}}
</style>
@endpush

@section('content')
<div class="pedagogy-page" id="pedagogyPage">
    <div class="pedagogy-hero">
        <div>
            <div class="pedagogy-eyebrow">NEXORA · Gestion scolaire</div>
            <h2>{{ $title }}</h2>
            <p>Consultez, recherchez et gérez les {{ mb_strtolower($title) }} de votre établissement.</p>
        </div>
        @if($routeExists)
            <a href="{{ route($base . '.create') }}" class="pedagogy-btn pedagogy-primary">＋ Ajouter {{ $type === 'teachers' ? 'un enseignant' : ($type === 'assignments' ? 'une affectation' : 'un élément') }}</a>
        @endif
    </div>

    @if(session('success'))
        <div class="pedagogy-flash" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="pedagogy-alert" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="pedagogy-stats">
        <div class="pedagogy-stat"><div class="pedagogy-stat-label">Total {{ mb_strtolower($title) }}</div><div class="pedagogy-stat-number">{{ number_format($total, 0, ',', ' ') }}</div><div class="pedagogy-stat-hint">Enregistrements au total</div></div>
        <div class="pedagogy-stat"><div class="pedagogy-stat-label">Éléments sur cette page</div><div class="pedagogy-stat-number">{{ $onPage }}</div><div class="pedagogy-stat-hint">Avant filtrage local</div></div>
        <div class="pedagogy-stat"><div class="pedagogy-stat-label">Résultats visibles</div><div class="pedagogy-stat-number" id="pedagogyVisibleCount">{{ $onPage }}</div><div class="pedagogy-stat-hint">Après recherche et filtres</div></div>
    </div>

    <section class="pedagogy-panel">
        <div class="pedagogy-panel-head">
            <h3>Liste des {{ mb_strtolower($title) }}</h3>
            <p>Recherchez par nom, matricule, code, statut ou identifiant selon la rubrique.</p>
        </div>
        <div class="pedagogy-toolbar" role="search" aria-label="Filtres pédagogiques">
            <div class="pedagogy-field">
                <label for="pedagogySearch">Recherche</label>
                <input type="search" id="pedagogySearch" placeholder="Rechercher un nom, matricule, code, ID..." autocomplete="off">
            </div>
            <div class="pedagogy-field">
                <label for="pedagogyFilter">{{ $type === 'teachers' ? 'Statut' : 'Type de recherche' }}</label>
                @if($type === 'teachers')
                    <select id="pedagogyFilter"><option value="all">Tous les statuts</option><option value="active">Actif / Active</option><option value="inactive">Inactif / Inactive</option></select>
                @else
                    <select id="pedagogyFilter"><option value="all">Tous les champs</option><option value="name">Désignation</option><option value="info">Informations</option><option value="id">Identifiant</option></select>
                @endif
            </div>
            <div class="pedagogy-field">
                <label for="pedagogySort">Trier par</label>
                <select id="pedagogySort"><option value="default">Ordre initial</option><option value="name-asc">Nom A → Z</option><option value="name-desc">Nom Z → A</option><option value="id-desc">ID décroissant</option></select>
            </div>
            <button class="pedagogy-btn pedagogy-reset" type="button" id="pedagogyReset">Réinitialiser</button>
        </div>
        <div class="pedagogy-table-wrap">
            <table class="pedagogy-table" id="pedagogyTable">
                <thead><tr><th>ID</th><th>Désignation</th><th>Informations</th><th style="text-align:right">Actions</th></tr></thead>
                <tbody id="pedagogyTableBody">
                @forelse($items as $item)
                    @php
                        $designation = $type === 'teachers'
                            ? trim(($item->last_name ?? '') . ' ' . ($item->first_name ?? ''))
                            : ($item->name ?? 'Affectation #' . $item->id);
                        $information = match($type) {
                            'teachers' => ($item->matricule ?? '—') . ' — ' . ($item->status ?? '—'),
                            'subjects' => $item->code ?? 'Sans code',
                            'assignments' => 'Enseignant #' . ($item->teacher_id ?? '—') . ' · Matière #' . ($item->subject_id ?? '—') . ' · Classe #' . ($item->class_id ?? '—'),
                            default => $item->code ?? '—',
                        };
                        $status = mb_strtolower(trim((string) ($item->status ?? '')));
                    @endphp
                    <tr class="pedagogy-data-row" data-id="{{ $item->id }}" data-name="{{ $designation }}" data-info="{{ $information }}" data-status="{{ $status }}" data-index="{{ $loop->index }}">
                        <td><span class="pedagogy-id">#{{ $item->id }}</span></td>
                        <td><span class="pedagogy-name">{{ $designation }}</span></td>
                        <td><span class="pedagogy-pill">{{ $information }}</span></td>
                        <td>
                            <div class="pedagogy-actions">
                                @if($type === 'teachers' && \Illuminate\Support\Facades\Route::has('company.school.assignments.teacher'))
                                    <a class="pedagogy-btn pedagogy-assign" href="{{ route('company.school.assignments.teacher', $item->id) }}">Affectations</a>
                                @endif
                                <a class="pedagogy-btn pedagogy-edit" href="{{ route($base . '.edit', $item->id) }}">Modifier</a>
                                <form method="POST" action="{{ route($base . '.destroy', $item->id) }}" onsubmit="return confirm('Confirmer la suppression ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="pedagogy-btn pedagogy-delete" type="submit">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="pedagogy-empty">Aucun enregistrement.</td></tr>
                @endforelse
                <tr id="pedagogyNoMatches" hidden><td colspan="4" class="pedagogy-empty">Aucun résultat ne correspond à votre recherche sur cette page.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="pedagogy-footer-info">Recherche et tri appliqués uniquement aux éléments de la page courante. Utilisez la pagination pour consulter les autres pages.</div>
        @if(method_exists($items, 'links'))
            <div class="pedagogy-pagination">{{ $items->links() }}</div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('pedagogyPage');
    if (!root) return;
    const search = root.querySelector('#pedagogySearch');
    const filter = root.querySelector('#pedagogyFilter');
    const sort = root.querySelector('#pedagogySort');
    const reset = root.querySelector('#pedagogyReset');
    const tbody = root.querySelector('#pedagogyTableBody');
    const rows = Array.from(tbody.querySelectorAll('.pedagogy-data-row'));
    const count = root.querySelector('#pedagogyVisibleCount');
    const empty = root.querySelector('#pedagogyNoMatches');
    const normalize = value => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    const collator = new Intl.Collator('fr', {numeric: true, sensitivity: 'base'});
    function refresh() {
        const query = normalize(search.value.trim());
        const field = filter.value;
        const sorted = [...rows].sort((a, b) => {
            if (sort.value === 'name-asc') return collator.compare(a.dataset.name, b.dataset.name);
            if (sort.value === 'name-desc') return collator.compare(b.dataset.name, a.dataset.name);
            if (sort.value === 'id-desc') return Number(b.dataset.id) - Number(a.dataset.id);
            return Number(a.dataset.index) - Number(b.dataset.index);
        });
        let visible = 0;
        sorted.forEach(row => {
            tbody.insertBefore(row, empty);
            const data = row.dataset;
            const target = field === 'name' ? data.name : field === 'info' ? data.info : field === 'id' ? data.id : [data.id, data.name, data.info].join(' ');
            const matchesSearch = normalize(target).includes(query);
            const status = normalize(data.status);
            const matchesStatus = field === 'all' || !root.querySelector('#pedagogyFilter option[value="active"]') || (field === 'active' ? ['active','actif','enabled','1'].includes(status) : ['inactive','inactif','disabled','0'].includes(status));
            const show = matchesSearch && matchesStatus;
            row.hidden = !show;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        count.textContent = visible;
        empty.hidden = visible > 0 || rows.length === 0;
        empty.style.display = visible > 0 || rows.length === 0 ? 'none' : '';
    }
    [search, filter, sort].forEach(el => el.addEventListener(el === search ? 'input' : 'change', refresh));
    reset.addEventListener('click', () => { search.value = ''; filter.value = 'all'; sort.value = 'default'; refresh(); });
    refresh();
})();
</script>
@endpush
