@extends('layouts.school')
@section('title', 'Saisie des notes')
@section('page-title', 'Saisie des notes')
@section('page-description', 'Configuration des périodes, évaluations et suivi des notes')
@push('styles')
    @vite('resources/css/school-grades.css')
 
@endpush
@section('content')
<div class="grades-page grades-modern">
    <header class="grades-hero">
        <div>
            <span class="grades-eyebrow">PÉDAGOGIE · ÉVALUATIONS</span>
            <h1>Saisie des notes</h1>
            <p>Organisez les évaluations de votre établissement et accédez rapidement aux notes des élèves.</p>
        </div>
        @if(\Illuminate\Support\Facades\Route::has('company.school.bulletins.index'))
            <a class="grades-btn grades-btn-light" href="{{ route('company.school.bulletins.index', ['year_id' => $year?->id, 'class_id' => $class?->id]) }}">Voir les bulletins <span aria-hidden="true">↗</span></a>
        @endif
    </header>

    <div class="grades-stats">
        <div class="grades-stat"><span>Année scolaire</span><strong>{{ $year?->name ?? 'Non sélectionnée' }}</strong></div>
        <div class="grades-stat"><span>Classe</span><strong>{{ $class?->name ?? 'Toutes les classes' }}</strong></div>
        <div class="grades-stat"><span>Évaluations</span><strong>{{ $class ? $evaluations->count() : '—' }}</strong></div>
        <div class="grades-stat"><span>Mode de notation</span><strong>{{ $config ? ($config->mode === 'sequences' ? '6 séquences' : '3 trimestres') : 'Non configuré' }}</strong></div>
    </div>

    <section class="grades-panel">
        <div class="grades-panel-head"><div><span class="grades-step">01 · FILTRES</span><h2>Sélection de la classe</h2><p>Choisissez l'année scolaire, puis la classe concernée.</p></div></div>
        <form method="GET" action="{{ route('company.school.grades.index') }}" class="grades-form-grid grades-filter-grid">
            <label class="grades-field">Année scolaire
                <select name="year_id" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y->id }}" @selected($year && $year->id == $y->id)>{{ $y->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grades-field">Classe
                <select name="class_id" onchange="this.form.submit()">
                    <option value="">Choisir une classe</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected($class && $class->id == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </label>
            <button class="grades-btn grades-btn-primary" type="submit">Afficher</button>
        </form>
    </section>

    @if($year)
        <section class="grades-panel">
            <div class="grades-panel-head"><div><span class="grades-step">02 · CONFIGURATION</span><h2>Mode d'évaluation</h2><p>Paramétrage pour l'année scolaire {{ $year->name }}.</p></div><span class="grades-pill {{ $year->is_archived ? 'grades-pill-muted' : 'grades-pill-success' }}">{{ $year->is_archived ? 'Année archivée' : 'Année modifiable' }}</span></div>
            <div class="grades-info"><span class="grades-info-icon">ⓘ</span><div><strong>Mode actuel</strong><p>{{ $config ? ($config->mode === 'sequences' ? '6 séquences réparties en 3 trimestres' : '3 trimestres avec notation directe') : 'Aucun mode configuré pour cette année.' }}</p></div></div>
            @unless($year->is_archived)
                <form method="POST" action="{{ route('company.school.grades.configure') }}" class="grades-form-grid grades-config-grid">
                    @csrf
                    <input type="hidden" name="school_year_id" value="{{ $year->id }}">
                    <label class="grades-field">Choisir le mode
                        <select name="mode" required>
                            <option value="sequences" @selected(old('mode', $config?->mode ?? 'sequences') === 'sequences')>6 séquences / 3 trimestres</option>
                            <option value="trimesters" @selected(old('mode', $config?->mode ?? '') === 'trimesters')>3 trimestres directs</option>
                        </select>
                    </label>
                    <button class="grades-btn grades-btn-primary" type="submit">Enregistrer le mode</button>
                </form>
            @endunless
        </section>
    @endif

    @if($class && $config && !$year->is_archived)
        <section class="grades-panel">
            <div class="grades-panel-head"><div><span class="grades-step">03 · CRÉATION</span><h2>Créer une évaluation</h2><p>Définissez la matière, la période, le barème et le poids de l'évaluation.</p></div></div>
            <form method="POST" action="{{ route('company.school.grades.evaluations.store') }}" class="grades-form-grid grades-create-grid">
                @csrf
                <input type="hidden" name="school_year_id" value="{{ $year->id }}">
                <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                <label class="grades-field">Matière
                    <select name="school_class_subject_id" required>
                        <option value="">Choisir une matière</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @selected(old('school_class_subject_id') == $s->id)>{{ $s->name }} (coef. {{ $s->coefficient }})</option>
                        @endforeach
                    </select>
                </label>
                <label class="grades-field">Période
                    <select name="school_evaluation_period_id" required>
                        <option value="">Choisir une période</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" @selected(old('school_evaluation_period_id') == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grades-field grades-title-field">Nom de l'évaluation
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Ex. : Devoir n° 1" required maxlength="255">
                </label>
                <label class="grades-field">Barème
                    <input type="number" name="max_score" value="{{ old('max_score', 20) }}" step="0.01" min="0.01" required>
                </label>
                <label class="grades-field">Poids
                    <input type="number" name="weight" value="{{ old('weight', 1) }}" step="0.01" min="0.01" required>
                </label>
                <div class="grades-form-actions"><button class="grades-btn grades-btn-primary" type="submit">+ Créer l'évaluation</button></div>
            </form>
        </section>
    @endif

    @if($class)
        <section class="grades-panel">
            <div class="grades-panel-head"><div><span class="grades-step">04 · SUIVI</span><h2>Évaluations de {{ $class->name }}</h2><p>Consultez les évaluations et accédez à la saisie des notes.</p></div><span class="grades-count">{{ $evaluations->count() }} évaluation(s)</span></div>
            <div class="grades-table-wrap"><table class="grades-table"><thead><tr><th>Évaluation</th><th>Matière</th><th>Période</th><th>Barème</th><th>Action</th></tr></thead><tbody>
                @forelse($evaluations as $e)
                    <tr><td><strong>{{ $e->title }}</strong></td><td>{{ $e->subject_name }}</td><td><span class="grades-tag">{{ $e->period_name }}</span></td><td><span class="grades-score-badge">/{{ $e->max_score }}</span></td><td><a class="grades-btn grades-btn-outline grades-btn-small" href="{{ route('company.school.grades.evaluations.edit', $e->id) }}">{{ $year->is_archived ? 'Consulter' : 'Saisir les notes' }} →</a></td></tr>
                @empty
                    <tr><td colspan="5"><div class="grades-empty"><span>▤</span><strong>Aucune évaluation</strong><p>Créez une évaluation pour commencer la saisie des notes.</p></div></td></tr>
                @endforelse
            </tbody></table></div>
        </section>
    @endif
</div>
@endsection
